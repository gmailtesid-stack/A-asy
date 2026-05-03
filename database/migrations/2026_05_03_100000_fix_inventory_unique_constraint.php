<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaikan constraint UNIQUE pada tabel inventories.
 *
 * Masalah:
 * Constraint lama UNIQUE(outlet_id, product_id) tidak kompatibel dengan WMS
 * multi-gudang. Satu produk seharusnya bisa berada di beberapa gudang (warehouse)
 * meskipun gudang-gudang tersebut berada dalam outlet yang sama.
 *
 * Solusi:
 * Ganti constraint ke UNIQUE(warehouse_id, product_id) agar tiap kombinasi
 * produk+gudang hanya memiliki satu baris inventory record.
 * Juga tambahkan kolom location_id jika belum ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Hapus unique constraint lama (outlet_id + product_id)
            $table->dropUnique(['outlet_id', 'product_id']);

            // Tambahkan kolom location_id jika belum ada
            if (! Schema::hasColumn('inventories', 'location_id')) {
                $table->foreignId('location_id')
                    ->nullable()
                    ->after('warehouse_id')
                    ->constrained()
                    ->onDelete('set null');
            }

            // Buat unique constraint baru: warehouse_id + product_id
            $table->unique(['warehouse_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropUnique(['warehouse_id', 'product_id']);

            if (Schema::hasColumn('inventories', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropColumn('location_id');
            }

            // Kembalikan constraint lama
            $table->unique(['outlet_id', 'product_id']);
        });
    }
};
