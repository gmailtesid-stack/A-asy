<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'old_values')) {
                $table->json('old_values')->nullable()->after('description')
                    ->comment('Nilai lama sebelum perubahan (JSON)');
            }
            if (!Schema::hasColumn('audit_logs', 'new_values')) {
                $table->json('new_values')->nullable()->after('old_values')
                    ->comment('Nilai baru setelah perubahan (JSON)');
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('new_values');
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('audit_logs', 'url')) {
                $table->string('url')->nullable()->after('user_agent')
                    ->comment('Endpoint yang memicu perubahan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['old_values', 'new_values', 'ip_address', 'user_agent', 'url']);
        });
    }
};
