<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->char('nationality', 2)->nullable();
            $table->text('identity_verification')->nullable();
            $table->string('preferred_language', 10)->default('en');
            $table->text('emergency_contact')->nullable();
            $table->json('marketing_preferences')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'email']);
            $table->index(['business_id', 'phone_number']);
            $table->index(['business_id', 'full_name']);
            $table->unique(['business_id', 'id']);
            $table->unique(['business_id', 'user_id']);
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('business_membership_id')
                ->nullable()
                ->constrained('business_memberships')
                ->nullOnDelete();
            $table->string('employee_number', 80);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('employment_status', 40)->default('active');
            $table->json('availability')->nullable();
            $table->text('emergency_contact')->nullable();
            $table->date('started_on')->nullable();
            $table->date('ended_on')->nullable();
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'employee_number']);
            $table->index(['business_id', 'employment_status']);
            $table->index(['business_id', 'department', 'employment_status']);
            $table->unique(['business_id', 'id']);
            $table->unique(['business_id', 'user_id']);
            $table->unique('business_membership_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('guests');
    }
};
