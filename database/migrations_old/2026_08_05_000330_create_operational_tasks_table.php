<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('property_id')
                ->constrained('properties')
                ->restrictOnDelete();
            $table->foreignUuid('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->restrictOnDelete();
            $table->foreignUuid('assigned_employee_id')
                ->nullable()
                ->constrained('employees')
                ->restrictOnDelete();
            $table->string('task_type', 80);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority', 20)->default('normal');
            $table->string('status', 40)->default('pending');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('checklist')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->json('recurrence_rule')->nullable();
            $table->string('creation_source', 40)->default('manual');
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

            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'status', 'due_at']);
            $table->index(['business_id', 'property_id', 'status', 'due_at']);
            $table->index(['business_id', 'booking_id', 'status']);
            $table->index(['business_id', 'assigned_employee_id', 'status', 'due_at'], 'tasks_assignee_status_due_index');
            $table->index(['business_id', 'task_type', 'status']);
            $table->index(['business_id', 'creation_source', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_tasks');
    }
};
