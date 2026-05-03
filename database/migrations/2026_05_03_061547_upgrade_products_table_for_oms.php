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
        Schema::table('products', function (Blueprint $table) {
            $table->string('status', 20)->default('draft')->after('sku')->comment('live, draft, under_review, failed');
            $table->string('type', 20)->default('simple')->after('status')->comment('simple, variant, kit');
            $table->string('csku')->nullable()->after('type')->comment('Channel SKU for marketplace mapping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['status', 'type', 'csku']);
        });
    }
};
