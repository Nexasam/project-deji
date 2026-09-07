<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_workspace_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('default_workspace', 80)->default('dashboard');
            $table->string('theme', 20)->default('system');
            $table->string('language', 10)->default('en');
            $table->string('timezone', 64)->default('UTC');
            $table->string('currency_display', 3)->nullable();
            $table->json('kpi_configuration')->nullable();
            $table->json('widget_configuration')->nullable();
            $table->json('notification_preferences')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'user_id'])
                ->references(['business_id', 'user_id'])
                ->on('business_memberships')
                ->restrictOnDelete();
            $table->unique(['business_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('dashboard_briefings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->date('briefing_date');
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->json('summary_data')->nullable();
            $table->json('supporting_metrics')->nullable();
            $table->string('generation_status', 40)->default('pending');
            $table->string('model_provider', 80)->nullable();
            $table->string('model_name', 120)->nullable();
            $table->string('model_version', 80)->nullable();
            $table->string('prompt_version', 80)->nullable();
            $table->unsignedInteger('generation_duration_ms')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'briefing_date']);
            $table->index(
                ['business_id', 'generation_status', 'briefing_date'],
                'dashboard_briefings_generation_date_idx'
            );
        });

        Schema::create('business_health_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->date('snapshot_date');
            $table->string('period_type', 30)->default('daily');
            $table->unsignedTinyInteger('score');
            $table->json('component_scores');
            $table->json('explanations')->nullable();
            $table->json('supporting_metrics')->nullable();
            $table->string('calculation_version', 80);
            $table->timestamp('calculated_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['business_id', 'period_type', 'snapshot_date'],
                'business_health_period_date_unique'
            );
            $table->index(['business_id', 'snapshot_date', 'score']);
        });

        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->uuid('booking_id')->nullable();
            $table->foreignUuid('domain_event_id')->nullable()->constrained('domain_events')->restrictOnDelete();
            $table->foreignUuid('workflow_run_id')->nullable()->constrained('workflow_runs')->restrictOnDelete();
            $table->string('recommendation_key', 191);
            $table->string('category', 80);
            $table->string('title');
            $table->text('summary');
            $table->longText('rationale')->nullable();
            $table->json('evidence')->nullable();
            $table->json('proposed_action')->nullable();
            $table->string('priority', 30)->default('normal');
            $table->string('recommendation_status', 40)->default('pending');
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->string('model_provider', 80)->nullable();
            $table->string('model_name', 120)->nullable();
            $table->string('model_version', 80)->nullable();
            $table->timestamp('generated_at');
            $table->timestamp('valid_until')->nullable();
            $table->foreignUuid('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignUuid('dismissed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dismissed_at')->nullable();
            $table->text('dismissal_reason')->nullable();
            $table->foreignUuid('executed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('executed_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique(['business_id', 'recommendation_key']);
            $table->index(
                ['business_id', 'recommendation_status', 'priority'],
                'ai_recommendations_status_priority_idx'
            );
            $table->index(['business_id', 'category', 'generated_at']);
            $table->index(['property_id', 'recommendation_status']);
            $table->index(['booking_id', 'recommendation_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
        Schema::dropIfExists('business_health_snapshots');
        Schema::dropIfExists('dashboard_briefings');
        Schema::dropIfExists('user_workspace_preferences');
    }
};
