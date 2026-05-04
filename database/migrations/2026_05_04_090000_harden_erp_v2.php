<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add cost_price to inventory_logs for FIFO support
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->nullable()->after('quantity_change');
        });

        // 2. Create archive table for audit logs
        Schema::create('audit_logs_archive', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('action');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs_archive');
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }
};
