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
        // 1. Chart of Accounts
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., 1101
            $table->string('name');           // e.g., Kas
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Journal Headers
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->string('reference')->nullable(); // e.g., INV-001
            $table->string('description');
            $table->foreignId('user_id')->constrained(); // Who recorded this
            $table->timestamps();
        });

        // 3. Journal Details (Double Entry)
        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
    }
};
