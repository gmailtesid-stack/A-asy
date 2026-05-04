<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Subscription Plans (SaaS Tiering)
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Lite, Pro, Enterprise
            $table->decimal('price', 15, 2)->default(0);
            $table->json('features'); // ['pos', 'wms', 'accounting', 'payroll']
            $table->integer('max_outlets')->default(1);
            $table->integer('max_users')->default(3);
            $table->timestamps();
        });

        // 2. Companies (Tenants)
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_entity'); // PT, CV, Individual
            $table->string('registration_number')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('subscription_plan')->default('basic'); // basic, premium, enterprise
            $table->integer('max_products')->default(100);
            $table->timestamp('subscription_expires_at')->nullable();
            $table->string('currency')->default('IDR');
            $table->string('timezone')->default('UTC');
            $table->json('settings')->nullable(); // Module toggles, branding
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Marketplace Connections (Universal Hub)
        Schema::create('marketplace_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('marketplace_name'); // Shopee, Tokopedia, etc
            $table->string('connection_status')->default('active');
            $table->json('api_credentials'); // Encrypted keys/tokens
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });

        // 4. Dynamic Attributes (JSON) on Core Tables
        Schema::table('products', function (Blueprint $table) {
            $table->json('custom_attributes')->nullable()->after('description');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->json('custom_attributes')->nullable()->after('phone');
        });

        // 5. Upgrade Users for Multi-Tenancy & Localization
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->default('UTC')->after('is_active');
            }
            if (!Schema::hasColumn('users', 'locale')) {
                $table->string('locale')->default('id')->after('timezone');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_connections');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('subscription_plans');
        
        Schema::table('products', function (Blueprint $table) { $table->dropColumn('custom_attributes'); });
        Schema::table('customers', function (Blueprint $table) { $table->dropColumn('custom_attributes'); });
    }
};
