<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'base_uom')) {
                // Unit of Measure
                $table->string('base_uom')->default('Pcs')->after('price');
                $table->string('purchase_uom')->default('Karton')->after('base_uom');
                $table->integer('conversion_rate')->default(1)->after('purchase_uom'); // 1 purchase_uom = X base_uom
                
                // Multi-price
                $table->decimal('wholesale_price', 15, 2)->nullable()->after('price');
                $table->decimal('member_price', 15, 2)->nullable()->after('wholesale_price');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'base_uom', 'purchase_uom', 'conversion_rate',
                'wholesale_price', 'member_price'
            ]);
        });
    }
};
