<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add store credit to customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'store_credit')) {
                $table->decimal('store_credit', 15, 2)->default(0)->after('email');
            }
        });

        // 2. Returns Table (RMA Header)
        if (!Schema::hasTable('returns')) {
            Schema::create('returns', function (Blueprint $table) {
                $table->id();
                $table->string('return_number')->unique();
                $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
                $table->foreignId('processed_by')->constrained('users')->cascadeOnDelete();
                $table->enum('refund_method', ['cash', 'store_credit', 'exchange'])->default('cash');
                $table->decimal('total_refund', 15, 2)->default(0);
                $table->string('reason');
                $table->timestamps();
            });
        }

        // 3. Return Items
        if (!Schema::hasTable('return_items')) {
            Schema::create('return_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity');
                $table->decimal('refund_amount', 15, 2);
                $table->enum('condition', ['good', 'reject', 'service']);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('store_credit');
        });
    }
};
