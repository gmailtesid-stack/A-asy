<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouses', 'photo')) {
                $table->string('photo')->nullable()->after('address');
            }
            if (!Schema::hasColumn('warehouses', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('photo');
            }
            if (!Schema::hasColumn('warehouses', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('warehouses', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('longitude');
            }
        });

        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'logo')) {
                $table->string('logo')->nullable()->after('address');
            }
            if (!Schema::hasColumn('suppliers', 'notes')) {
                $table->text('notes')->nullable()->after('logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumnIfExists('photo');
            $table->dropColumnIfExists('latitude');
            $table->dropColumnIfExists('longitude');
            $table->dropColumnIfExists('is_active');
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumnIfExists('logo');
            $table->dropColumnIfExists('notes');
        });
    }
};
