<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_onboarding_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->string('step_key', 80);
            $table->unsignedInteger('sequence');
            $table->string('state', 40)->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['business_id', 'step_key']);
            $table->index(['business_id', 'state', 'sequence']);
        });

        Schema::create('business_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->string('plan_key', 80);
            $table->string('state', 40);
            $table->string('provider', 80)->nullable();
            $table->string('provider_subscription_id', 191)->nullable();
            $table->string('provider_customer_id', 191)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('current_period_starts_at')->nullable();
            $table->timestamp('current_period_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['business_id', 'state', 'current_period_ends_at']);
            $table->index(['provider', 'provider_subscription_id']);
        });

        Schema::create('property_lifecycle_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('property_id')->constrained('properties')->restrictOnDelete();
            $table->string('event_type', 80);
            $table->string('previous_state', 40)->nullable();
            $table->string('new_state', 40)->nullable();
            $table->foreignUuid('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->string('status', 40)->default('recorded');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['business_id', 'property_id', 'occurred_at'], 'property_lifecycle_timeline_index');
            $table->index(['business_id', 'event_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_lifecycle_events');
        Schema::dropIfExists('business_subscriptions');
        Schema::dropIfExists('business_onboarding_steps');
    }
};
