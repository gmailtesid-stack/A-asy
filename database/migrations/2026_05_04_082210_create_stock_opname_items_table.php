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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_opname_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('recorded_quantity');
            $table->integer('physical_quantity');
            $table->integer('adjustment_quantity'); // physical - recorded
            $table->timestamps();

            $table->foreign('stock_opname_id')->references('id')->on('stock_opnames')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
