<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // expenses already exists from phase 2 - just add missing columns
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()
                    ->comment('Manajer yang menyetujui pengeluaran')->after('user_id');
            }
            if (!Schema::hasColumn('expenses', 'receipt_url')) {
                $table->string('receipt_url')->nullable()->after('status')
                    ->comment('Foto/scan bukti pengeluaran via Cloudinary');
            }
            if (!Schema::hasColumn('expenses', 'journal_entry_id')) {
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete()
                    ->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['receipt_url', 'journal_entry_id']);
        });
    }
};
