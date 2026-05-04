<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add virtual stock fields to inventories (safe, idempotent)
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'reserved_quantity')) {
                $table->unsignedInteger('reserved_quantity')->default(0)->after('quantity')
                    ->comment('Stok yang sudah dipesan (OMS) tapi belum diambil');
            }
            if (!Schema::hasColumn('inventories', 'reorder_point')) {
                $table->unsignedInteger('reorder_point')->default(0)->after('min_quantity')
                    ->comment('Titik pemesanan ulang otomatis untuk trigger draf PO');
            }
        });

        // Add batch/expiry tracking to inventory_logs (safe, idempotent)
        Schema::table('inventory_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_logs', 'batch_number')) {
                $table->string('batch_number')->nullable()->after('cost_price')
                    ->comment('Nomor batch/lot dari supplier');
            }
            if (!Schema::hasColumn('inventory_logs', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('batch_number')
                    ->comment('Tanggal kedaluwarsa produk dalam batch ini');
            }
            if (!Schema::hasColumn('inventory_logs', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('expiry_date')
                    ->comment('Nomor seri unit spesifik untuk produk bergaransi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['reserved_quantity', 'reorder_point']);
        });
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'expiry_date', 'serial_number']);
        });
    }
};
