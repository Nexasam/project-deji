<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo_disk', 40);
            $table->string('logo_path', 2048);
            $table->string('website_url', 2048);
            $table->json('social_links');
            $table->string('registration_number', 100)->nullable();
            $table->char('country_code', 2);
            $table->json('address')->nullable();
            $table->foreignUuid('primary_contact_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('primary_contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number', 32)->nullable();
            // Encrypted casts produce ciphertext, so this must be TEXT rather than JSON.
            $table->text('tax_information')->nullable();
            $table->string('business_type', 80);
            $table->string('timezone', 64)->default('UTC');
            $table->char('currency', 3);
            $table->string('subscription_plan', 80)->nullable();
            $table->string('verification_status', 40)->default('unverified');
            $table->string('onboarding_status', 40)->default('registered');
            $table->timestamp('onboarding_started_at')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
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

            $table->unique(
                ['country_code', 'registration_number'],
                'businesses_country_registration_unique'
            );
            $table->index(['status', 'verification_status']);
            $table->index(['onboarding_status', 'status']);
            $table->index(['subscription_plan', 'status']);
            $table->index(['country_code', 'business_type']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
