<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_calendar_connections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->string('provider', 80);
            $table->string('external_calendar_id', 191)->nullable();
            $table->string('sync_direction', 20)->default('bidirectional');
            $table->string('feed_url', 2048)->nullable();
            $table->text('credentials')->nullable();
            $table->string('sync_cursor', 500)->nullable();
            $table->string('sync_status', 40)->default('pending');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_imported_at')->nullable();
            $table->timestamp('last_exported_at')->nullable();
            $table->unsignedSmallInteger('consecutive_failure_count')->default(0);
            $table->text('last_error')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['provider', 'external_calendar_id']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'property_id', 'status']);
        });

        Schema::create('property_availability_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id')->nullable();
            $table->foreignUuid('external_calendar_connection_id')->nullable()->constrained('external_calendar_connections')->restrictOnDelete();
            $table->string('source_type', 40);
            $table->string('source_reference', 191)->nullable();
            $table->boolean('blocks_booking')->default(true);
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('validation_status', 40)->default('validated');
            $table->foreignUuid('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->string('block_state', 40)->default('active');
            $table->text('reason')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'property_id', 'block_state', 'starts_on', 'ends_on'], 'availability_overlap_lookup');
            $table->index(['business_id', 'property_id', 'blocks_booking', 'validation_status'], 'availability_block_validation_lookup');
            $table->index(['booking_id', 'block_state']);
        });

        Schema::create('booking_channel_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->string('provider', 80);
            $table->string('external_booking_id', 191);
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique(['provider', 'external_booking_id']);
            $table->index(['business_id', 'booking_id']);
        });

        Schema::create('booking_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->string('previous_status', 40)->nullable();
            $table->string('new_status', 40);
            $table->string('source', 40)->default('user');
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'occurred_at']);
        });

        Schema::create('booking_date_changes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->date('previous_arrival_date');
            $table->date('previous_departure_date');
            $table->date('new_arrival_date');
            $table->date('new_departure_date');
            $table->string('change_type', 40)->default('reschedule');
            $table->string('availability_status', 40)->default('pending');
            $table->text('reason')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('occurred_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'occurred_at']);
        });

        Schema::create('booking_interactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('interaction_type', 40);
            $table->string('direction', 20)->nullable();
            $table->string('channel', 40)->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_address', 191)->nullable();
            $table->string('summary')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->string('external_thread_id', 191)->nullable();
            $table->string('external_message_id', 191)->nullable();
            $table->string('delivery_status', 40)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'occurred_at']);
            $table->index(['interaction_type', 'channel']);
            $table->index(['business_id', 'booking_id', 'is_internal', 'occurred_at'], 'booking_interaction_visibility_lookup');
            $table->index(['delivery_status', 'failed_at']);
            $table->unique(['channel', 'external_message_id']);
        });

        Schema::create('guest_service_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->foreignUuid('guest_user_id')->constrained('users')->restrictOnDelete();
            $table->uuid('operational_task_id')->nullable();
            $table->uuid('assigned_employee_id')->nullable();
            $table->string('request_type', 40);
            $table->string('priority', 20)->default('normal');
            $table->text('description');
            $table->text('resolution')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('resolved_at')->nullable();
            $table->string('status', 40)->default('open');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'operational_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->foreign(['business_id', 'assigned_employee_id'])->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'status']);
            $table->index(['assigned_employee_id', 'status']);
        });

        Schema::create('booking_incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->foreignUuid('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('operational_task_id')->nullable();
            $table->foreignUuid('document_id')->nullable()->constrained('documents')->restrictOnDelete();
            $table->string('incident_type', 40);
            $table->string('severity', 20)->default('normal');
            $table->text('description');
            $table->decimal('financial_impact', 19, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->text('resolution')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamp('resolved_at')->nullable();
            $table->string('status', 40)->default('open');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'operational_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'booking_id', 'status']);
            $table->index(['severity', 'status']);
        });

        Schema::create('booking_check_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->string('identity_status', 40)->default('pending');
            $table->string('balance_status', 40)->default('pending');
            $table->string('security_deposit_status', 40)->default('not_required');
            $table->string('access_method', 40)->nullable();
            $table->string('access_reference')->nullable();
            $table->json('checklist')->nullable();
            $table->json('blocking_issues')->nullable();
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 40)->default('pending');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique('booking_id');
            $table->index(['business_id', 'status']);
        });

        Schema::create('booking_check_outs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->timestamp('actual_departure_at')->nullable();
            $table->string('access_return_status', 40)->default('pending');
            $table->string('room_condition', 40)->nullable();
            $table->string('damage_status', 40)->default('none');
            $table->text('damage_notes')->nullable();
            $table->string('deposit_release_status', 40)->default('not_required');
            $table->decimal('deposit_release_amount', 19, 4)->nullable();
            $table->text('handover_notes')->nullable();
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 40)->default('pending');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique('booking_id');
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_check_outs');
        Schema::dropIfExists('booking_check_ins');
        Schema::dropIfExists('booking_incidents');
        Schema::dropIfExists('guest_service_requests');
        Schema::dropIfExists('booking_interactions');
        Schema::dropIfExists('booking_date_changes');
        Schema::dropIfExists('booking_status_history');
        Schema::dropIfExists('booking_channel_links');
        Schema::dropIfExists('property_availability_blocks');
        Schema::dropIfExists('external_calendar_connections');
    }
};
