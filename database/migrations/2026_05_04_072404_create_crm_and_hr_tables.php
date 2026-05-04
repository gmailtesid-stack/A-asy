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
        // 1. CRM: Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('outlet_id')->nullable()->constrained();
            $table->integer('loyalty_points')->default(0);
            $table->timestamps();
        });

        // 2. HR: Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained(); // Link to login account
            $table->string('employee_id')->unique(); // e.g., EMP001
            $table->string('name');
            $table->string('position')->nullable();
            $table->decimal('salary', 15, 2)->default(0);
            $table->date('joined_at');
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');
            $table->timestamps();
        });

        // 3. HR: Attendances
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('status')->default('present'); // present, sick, leave, alpha
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('customers');
    }
};
