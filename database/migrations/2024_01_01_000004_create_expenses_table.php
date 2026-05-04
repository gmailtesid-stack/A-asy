<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
                $table->string('category'); // Operational, Rent, Salary, etc
                $table->decimal('amount', 15, 2);
                $table->text('notes')->nullable();
                $table->string('status')->default('pending'); // pending, approved, rejected
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
