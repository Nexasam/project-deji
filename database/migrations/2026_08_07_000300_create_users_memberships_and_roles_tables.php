<?php

use App\Enums\BusinessMembershipStatus;
use App\Enums\IdentityVerificationStatus;
use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number', 32)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('nationality')->nullable();
            $table->string('identity_verification_status', 40)
                ->default(IdentityVerificationStatus::Unverified->value);
            $table->string('preferred_language', 10)->default('en');
            $table->json('emergency_contact')->nullable();
            $table->json('marketing_preferences')->nullable();
            $table->text('guest_notes')->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->string('status', 40)->default(UserStatus::Active->value);
            $table->unsignedSmallInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone_number');
            $table->index(['status', 'created_at']);
            $table->index(['identity_verification_status', 'status']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('business_memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('job_title')->nullable();
            $table->string('status', 40)
                ->default(BusinessMembershipStatus::Invited->value);
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'user_id']);
            $table->unique(['business_id', 'id']);
            $table->index(['user_id', 'status']);
            $table->index(['business_id', 'status']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('business_memberships');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
