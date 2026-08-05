<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_check_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->string('identity_state', 40)->default('pending');
            $table->string('balance_state', 40)->default('pending');
            $table->string('deposit_state', 40)->default('pending');
            $table->string('access_state', 40)->default('pending');
            $table->string('access_reference', 191)->nullable();
            $table->json('checklist')->nullable();
            $table->json('blocking_issues')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->uuid('completed_by_employee_id')->nullable();
            $table->foreignUuid('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('pending');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'property_id', 'booking_id'])
                ->references(['business_id', 'property_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'completed_by_employee_id'])
                ->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->unique('booking_id');
            $table->index(['business_id', 'property_id', 'status']);
        });

        Schema::create('booking_check_outs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->timestamp('actual_departure_at')->nullable();
            $table->string('access_return_state', 40)->default('pending');
            $table->string('room_state', 40)->default('pending');
            $table->string('damage_state', 40)->default('pending');
            $table->string('deposit_state', 40)->default('pending');
            $table->decimal('released_amount', 19, 4)->default(0);
            $table->string('released_currency', 3)->nullable();
            $table->text('handover_notes')->nullable();
            $table->uuid('completed_by_employee_id')->nullable();
            $table->foreignUuid('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('pending');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'property_id', 'booking_id'])
                ->references(['business_id', 'property_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'completed_by_employee_id'])
                ->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->unique('booking_id');
            $table->index(['business_id', 'property_id', 'status']);
        });

        Schema::create('booking_interactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->uuid('guest_id')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('employee_id')->nullable();
            $table->string('interaction_type', 80);
            $table->string('direction', 20);
            $table->string('channel', 40);
            $table->string('summary');
            $table->longText('content')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->string('status', 40)->default('recorded');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'guest_id'])
                ->references(['business_id', 'id'])->on('guests')->restrictOnDelete();
            $table->foreign(['business_id', 'employee_id'])
                ->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'occurred_at'], 'booking_interaction_timeline_index');
            $table->index(['business_id', 'guest_id', 'occurred_at'], 'booking_interaction_guest_timeline_index');
            $table->index(['business_id', 'user_id', 'occurred_at'], 'booking_interaction_user_timeline_index');
            $table->index(['business_id', 'employee_id', 'occurred_at'], 'booking_interaction_employee_timeline_index');
        });

        Schema::create('guest_service_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->uuid('guest_id');
            $table->uuid('operational_task_id')->nullable();
            $table->uuid('assigned_employee_id')->nullable();
            $table->string('request_type', 80);
            $table->string('priority', 20)->default('normal');
            $table->text('description');
            $table->text('resolution')->nullable();
            $table->string('state', 40)->default('open');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'property_id', 'booking_id'])
                ->references(['business_id', 'property_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'guest_id'])
                ->references(['business_id', 'id'])->on('guests')->restrictOnDelete();
            $table->foreign(['business_id', 'operational_task_id'])
                ->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->foreign(['business_id', 'assigned_employee_id'])
                ->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->index(['business_id', 'guest_id', 'state', 'created_at'], 'service_request_guest_state_index');
            $table->index(['business_id', 'booking_id', 'state', 'created_at'], 'service_request_booking_state_index');
            $table->index(['business_id', 'assigned_employee_id', 'state', 'created_at'], 'service_request_assignee_state_index');
        });

        Schema::create('booking_incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->foreignUuid('reporter_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('reporter_employee_id')->nullable();
            $table->uuid('operational_task_id')->nullable();
            $table->uuid('document_id')->nullable();
            $table->string('incident_type', 80);
            $table->string('severity', 20)->default('medium');
            $table->text('description');
            $table->decimal('financial_impact', 19, 4)->default(0);
            $table->string('financial_currency', 3)->nullable();
            $table->text('resolution')->nullable();
            $table->timestamp('reported_at');
            $table->timestamp('resolved_at')->nullable();
            $table->string('state', 40)->default('open');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'property_id', 'booking_id'])
                ->references(['business_id', 'property_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'reporter_employee_id'])
                ->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->foreign(['business_id', 'operational_task_id'])
                ->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->foreign(['business_id', 'document_id'])
                ->references(['business_id', 'id'])->on('documents')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'reported_at'], 'booking_incident_timeline_index');
            $table->index(['business_id', 'property_id', 'state', 'severity'], 'booking_incident_property_state_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_incidents');
        Schema::dropIfExists('guest_service_requests');
        Schema::dropIfExists('booking_interactions');
        Schema::dropIfExists('booking_check_outs');
        Schema::dropIfExists('booking_check_ins');
    }
};
