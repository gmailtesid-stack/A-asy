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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('status');
            $table->index('invoice_number');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('po_number');
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('so_number');
        });

        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->index('status');
            $table->index('transfer_number');
        });

        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->index('reference');
            $table->index('type');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('sku');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['sku']);
            $table->dropIndex(['status']);
        });

        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropIndex(['reference']);
            $table->dropIndex(['type']);
        });

        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['transfer_number']);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['so_number']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['po_number']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['invoice_number']);
        });
    }
};
