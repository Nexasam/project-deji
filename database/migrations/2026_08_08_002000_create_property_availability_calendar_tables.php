<?php

use App\Enums\PropertyAvailabilityDayState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_availability_days', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id')->nullable();
            $table->uuid('availability_block_id')->nullable();
            $table->date('availability_date');
            $table->string('availability_state', 30)->default(PropertyAvailabilityDayState::Held->value);
            $table->string('source_type', 40);
            $table->string('source_reference', 191)->nullable();
            $table->string('hold_token_hash', 128)->nullable();
            $table->foreignUuid('held_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hold_expires_at')->nullable();
            $table->string('active_key', 20)->nullable()->default('active');
            $table->timestamp('allocated_at');
            $table->timestamp('released_at')->nullable();
            $table->text('release_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'availability_block_id'])
                ->references(['business_id', 'id'])->on('property_availability_blocks')->restrictOnDelete();
            $table->unique(['property_id', 'availability_date', 'active_key'], 'property_active_availability_day_unique');
            $table->index(['business_id', 'property_id', 'availability_date', 'availability_state'], 'property_availability_calendar_lookup');
            $table->index(['availability_state', 'hold_expires_at', 'active_key'], 'availability_expired_hold_lookup');
            $table->index(['booking_id', 'active_key']);
            $table->index(['availability_block_id', 'active_key']);
        });

        Schema::create('external_calendar_sync_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('external_calendar_connection_id');
            $table->string('direction', 20);
            $table->string('trigger_type', 30)->default('scheduled');
            $table->string('sync_status', 40)->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('received_count')->default(0);
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('updated_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('conflict_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->string('cursor_before', 500)->nullable();
            $table->string('cursor_after', 500)->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'external_calendar_connection_id'])
                ->references(['business_id', 'id'])->on('external_calendar_connections')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'external_calendar_connection_id', 'created_at'], 'external_sync_connection_history');
            $table->index(['sync_status', 'started_at']);
        });

        Schema::create('external_calendar_sync_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('sync_run_id');
            $table->string('external_event_id', 191)->nullable();
            $table->string('operation', 30);
            $table->string('validation_status', 40)->default('pending');
            $table->string('result_status', 40)->default('pending');
            $table->string('source_type', 80)->nullable();
            $table->uuid('source_id')->nullable();
            $table->string('payload_hash', 128)->nullable();
            $table->json('payload')->nullable();
            $table->json('validation_errors')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'sync_run_id'])
                ->references(['business_id', 'id'])->on('external_calendar_sync_runs')->restrictOnDelete();
            $table->index(['sync_run_id', 'result_status']);
            $table->index(['business_id', 'external_event_id']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_calendar_sync_items');
        Schema::dropIfExists('external_calendar_sync_runs');
        Schema::dropIfExists('property_availability_days');
    }
};
