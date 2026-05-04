<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Outlets untuk Hierarki
        Schema::table('outlets', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(1)->after('id')->index();
            $table->unsignedBigInteger('branch_id')->default(1)->after('company_id')->index();
        });

        // 2. Tambahkan Tenancy & Sync Metadata ke Tabel-Tabel Utama
        $tables = [
            'users', 'products', 'inventories', 'transactions', 
            'customers', 'audit_logs', 'journal_entries', 'returns',
            'purchase_orders', 'grns', 'stock_transfers', 'stock_opnames',
            'warehouses', 'brands', 'suppliers', 'inventory_logs'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // ULID untuk Global Sync (Unique)
                if (!Schema::hasColumn($tableName, 'ulid')) {
                    $table->char('ulid', 26)->nullable()->unique()->after('id');
                }
                
                // Tenancy Hierarchy (di luar yang sudah ada outlet_id)
                if ($tableName !== 'users' && !Schema::hasColumn($tableName, 'company_id')) {
                    $table->unsignedBigInteger('company_id')->default(1)->index();
                }
                if ($tableName !== 'users' && !Schema::hasColumn($tableName, 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->default(1)->index();
                }

                // Conflict Resolution (Optimistic Locking)
                if (!Schema::hasColumn($tableName, 'version')) {
                    $table->unsignedInteger('version')->default(1);
                }

                // Sync Tracker
                if (!Schema::hasColumn($tableName, 'synced_at')) {
                    $table->timestamp('synced_at')->nullable();
                }

                // WMS specific
                if ($tableName === 'inventories' && !Schema::hasColumn($tableName, 'is_frozen')) {
                    $table->boolean('is_frozen')->default(false)->after('quantity');
                }
            });
        }

        // 3. Khusus User: Tambahkan role scope
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->default(1)->index();
            $table->unsignedBigInteger('company_id')->default(1)->index();
        });
    }

    public function down(): void
    {
        // Rollback logik (opsional, namun karena ini sistem ERP, biasanya kita tidak drop kolom di prod)
    }
};
