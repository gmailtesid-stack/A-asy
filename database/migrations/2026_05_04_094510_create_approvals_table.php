<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type')->comment('Model yang membutuhkan approval: StockOpname, Expense, PurchaseOrder');
            $table->unsignedBigInteger('approvable_id');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable()->comment('Catatan dari approver');
            $table->decimal('amount_before', 15, 2)->nullable()->comment('Nilai/stok sebelum perubahan');
            $table->decimal('amount_after', 15, 2)->nullable()->comment('Nilai/stok setelah perubahan jika disetujui');
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['approvable_type', 'approvable_id']);
            $table->index(['status', 'requested_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
