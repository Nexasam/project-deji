<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->uuid('booking_id')->nullable();
            $table->string('event_name', 150);
            $table->string('aggregate_type');
            $table->uuid('aggregate_id');
            $table->uuid('correlation_id')->nullable();
            $table->uuid('causation_id')->nullable();
            $table->string('idempotency_key', 191)->unique();
            $table->json('payload');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->string('publication_status', 40)->default('pending');
            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->index(['publication_status', 'next_attempt_at', 'occurred_at'], 'domain_events_publication_queue');
            $table->index(['aggregate_type', 'aggregate_id', 'occurred_at'], 'domain_events_aggregate_history');
            $table->index(['business_id', 'event_name', 'occurred_at']);
        });

        Schema::create('workflow_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('domain_event_id')->constrained('domain_events')->restrictOnDelete();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->uuid('booking_id')->nullable();
            $table->string('workflow_key', 120);
            $table->unsignedSmallInteger('workflow_version')->default(1);
            $table->string('idempotency_key', 191)->unique();
            $table->string('execution_status', 40)->default('pending');
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->index(['execution_status', 'next_retry_at']);
            $table->index(['business_id', 'workflow_key', 'created_at']);
        });

        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workflow_run_id')->constrained('workflow_runs')->restrictOnDelete();
            $table->unsignedSmallInteger('step_order');
            $table->string('step_key', 120);
            $table->string('handler_key', 150);
            $table->string('execution_status', 40)->default('pending');
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['workflow_run_id', 'step_order']);
            $table->index(['execution_status', 'next_retry_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
        Schema::dropIfExists('workflow_runs');
        Schema::dropIfExists('domain_events');
    }
};
