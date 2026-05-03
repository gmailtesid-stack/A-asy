<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->after('brand_id')->constrained()->onDelete('set null');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('outlet_id')->constrained()->onDelete('cascade');
            // Drop unique constraint if it exists to allow multi-warehouse per product
            // $table->dropUnique(['outlet_id', 'product_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['brand_id', 'supplier_id']);
        });
    }
};
