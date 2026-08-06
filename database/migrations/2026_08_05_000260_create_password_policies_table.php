<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->string('system_key', 100)->nullable()->unique();
            $table->string('name');
            $table->unsignedSmallInteger('minimum_length')->default(12);
            $table->boolean('requires_uppercase')->default(true);
            $table->boolean('requires_lowercase')->default(true);
            $table->boolean('requires_number')->default(true);
            $table->boolean('requires_symbol')->default(true);
            $table->unsignedSmallInteger('password_history_count')->default(5);
            $table->unsignedSmallInteger('maximum_age_days')->nullable();
            $table->unsignedSmallInteger('maximum_failed_attempts')->default(5);
            $table->unsignedSmallInteger('lockout_minutes')->default(30);
            $table->unsignedInteger('session_timeout_minutes')->default(120);
            $table->boolean('requires_mfa')->default(false);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'name']);
            $table->index(['business_id', 'status']);
        });

        Schema::create('user_password_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('password_hash');
            $table->timestamp('changed_at');
            $table->foreignUuid('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->timestamp('created_at')->nullable();

            $table->index(['user_id', 'status', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_password_histories');
        Schema::dropIfExists('password_policies');
    }
};
