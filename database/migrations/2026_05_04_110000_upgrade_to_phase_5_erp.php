<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Warehouse Zoning & Bin Mapping
        if (!Schema::hasTable('warehouse_zones')) {
            Schema::create('warehouse_zones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
                $table->string('code')->comment('e.g. Z-COLD, Z-DRY');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('warehouse_bins')) {
            Schema::create('warehouse_bins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('zone_id')->constrained('warehouse_zones')->cascadeOnDelete();
                $table->string('rack')->comment('e.g. A, B');
                $table->string('level')->comment('e.g. 1, 2, 3');
                $table->string('bin')->comment('e.g. 01, 02');
                $table->string('full_code')->unique()->comment('e.g. Z-DRY-A-1-01');
                $table->timestamps();
            });
        }

        // Add bin_id to inventories
        if (!Schema::hasColumn('inventories', 'bin_id')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->foreignId('bin_id')->nullable()->constrained('warehouse_bins')->nullOnDelete();
            });
        }

        // 2. Multi-Currency & Taxation for POS / OMS Transactions
        if (!Schema::hasColumn('transactions', 'currency')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('currency', 3)->default('IDR')->after('status');
                $table->decimal('exchange_rate', 15, 4)->default(1)->after('currency');
                $table->boolean('tax_included')->default(true)->after('exchange_rate');
                $table->decimal('tax_rate', 5, 2)->default(0)->comment('Percentage, e.g. 11.00')->after('tax_included');
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
            });
        }

        // 3. Landed Cost Tracking for Purchasing
        if (!Schema::hasColumn('purchase_orders', 'freight_cost')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->decimal('freight_cost', 15, 2)->default(0)->after('total_amount');
                $table->decimal('insurance_cost', 15, 2)->default(0)->after('freight_cost');
                $table->decimal('estimated_landed_cost', 15, 2)->default(0)->after('insurance_cost');
            });
        }

        if (!Schema::hasColumn('grns', 'actual_freight_cost')) {
            Schema::table('grns', function (Blueprint $table) {
                $table->decimal('actual_freight_cost', 15, 2)->default(0)->after('grn_number');
                $table->decimal('actual_insurance_cost', 15, 2)->default(0)->after('actual_freight_cost');
                $table->decimal('total_landed_cost', 15, 2)->default(0)->after('actual_insurance_cost');
            });
        }
    }

    public function down(): void
    {
        Schema::table('grns', function (Blueprint $table) {
            $table->dropColumn(['actual_freight_cost', 'actual_insurance_cost', 'total_landed_cost']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['freight_cost', 'insurance_cost', 'estimated_landed_cost']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate', 'tax_included', 'tax_rate', 'tax_amount']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['bin_id']);
            $table->dropColumn('bin_id');
        });

        Schema::dropIfExists('warehouse_bins');
        Schema::dropIfExists('warehouse_zones');
    }
};
