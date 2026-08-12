<?php

use App\Enums\BusinessOnboardingStatus;
use App\Enums\BusinessStatus;
use App\Enums\BusinessVerificationStatus;
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
            $table->string('logo_disk', 40)->nullable();
            $table->string('logo_path', 2048)->nullable();
            $table->string('website_url', 2048)->nullable();
            $table->json('social_links')->nullable();
            $table->string('registration_number', 100)->nullable();
            $table->char('country_code', 2);
            $table->json('address')->nullable();
            $table->string('primary_contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->text('tax_information')->nullable();
            $table->string('business_type', 80);
            $table->string('timezone', 64)->default('UTC');
            $table->char('currency', 3);
            $table->string('subscription_plan', 80)->nullable();
            $table->string('onboarding_status', 40)
                ->default(BusinessOnboardingStatus::Registered->value);
            $table->timestamp('onboarding_started_at')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->string('verification_status', 40)
                ->default(BusinessVerificationStatus::Unverified->value);
            $table->string('status', 40)->default(BusinessStatus::Active->value);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['country_code', 'registration_number'],
                'businesses_country_registration_unique'
            );
            $table->index(['status', 'verification_status']);
            $table->index(['country_code', 'business_type']);
            $table->index(['subscription_plan', 'status']);
            $table->index(['onboarding_status', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
