<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Upgrade Inventories for Intelligence & Freezing
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'is_frozen')) {
                $table->boolean('is_frozen')->default(false)->after('quantity');
            }
            if (!Schema::hasColumn('inventories', 'abc_category')) {
                $table->char('abc_category', 1)->default('C')->after('is_frozen');
            }
            if (!Schema::hasColumn('inventories', 'last_counted_at')) {
                $table->timestamp('last_counted_at')->nullable();
            }
        });

        // 2. Upgrade Stock Opnames for Blind & Scheduled Audit
        Schema::table('stock_opnames', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_opnames', 'type')) {
                $table->string('type')->default('daily'); // daily, monthly, annual
            }
            if (!Schema::hasColumn('stock_opnames', 'is_blind')) {
                $table->boolean('is_blind')->default(true);
            }
        });

        // 3. Upgrade Stock Opname Items for Double Check
        Schema::table('stock_opname_items', function (Blueprint $table) {
            $table->unsignedInteger('counter_1_qty')->nullable()->after('physical_quantity');
            $table->unsignedInteger('counter_2_qty')->nullable()->after('counter_1_qty');
            $table->unsignedBigInteger('counter_1_user_id')->nullable();
            $table->unsignedBigInteger('counter_2_user_id')->nullable();
            $table->string('verification_status')->default('pending'); // pending, recount, matched, discrepant
        });

        // 4. Table for Pending Adjustments during Frozen state
        Schema::create('pending_stock_adjustments', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('inventory_id');
            $blueprint->integer('quantity_change');
            $blueprint->string('reference_type'); // e.g. Transaction
            $blueprint->unsignedBigInteger('reference_id');
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_stock_adjustments');
    }
};
