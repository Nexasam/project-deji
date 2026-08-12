<?php

use App\Enums\EmploymentStatus;
use App\Enums\OperationalTaskStatus;
use App\Enums\TaskGenerationSource;
use App\Enums\TaskPriority;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->string('name');
            $table->string('code', 80);
            $table->text('description')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'code']);
            $table->index(['business_id', 'status']);
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('business_membership_id');
            $table->foreignUuid('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('employee_code', 80);
            $table->string('employment_status', 40)->default(EmploymentStatus::Invited->value);
            $table->json('availability')->nullable();
            $table->json('emergency_contact')->nullable();
            $table->date('started_on')->nullable();
            $table->date('ended_on')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'business_membership_id'])
                ->references(['business_id', 'id'])
                ->on('business_memberships')
                ->restrictOnDelete();
            $table->unique('business_membership_id');
            $table->unique(['business_id', 'employee_code']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'department_id', 'employment_status']);
        });

        Schema::create('operational_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id')->nullable();
            $table->uuid('assigned_employee_id')->nullable();
            $table->uuid('parent_task_id')->nullable();
            $table->string('reference', 100);
            $table->string('title');
            $table->string('task_type', 40);
            $table->string('priority', 20)->default(TaskPriority::Normal->value);
            $table->string('status', 40)->default(OperationalTaskStatus::Pending->value);
            $table->timestamp('due_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('generation_source', 20)->default(TaskGenerationSource::Manual->value);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_rule')->nullable();
            $table->timestamp('next_recurrence_at')->nullable();
            $table->json('generation_metadata')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'assigned_employee_id'])
                ->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->foreign('parent_task_id')->references('id')->on('operational_tasks')->restrictOnDelete();
            $table->unique(['business_id', 'reference']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'property_id', 'status', 'due_at']);
            $table->index(['business_id', 'assigned_employee_id', 'status', 'due_at'], 'tasks_assignee_status_due_index');
            $table->index(['business_id', 'booking_id']);
            $table->index(['is_recurring', 'next_recurrence_at', 'status']);
        });

        Schema::create('operational_task_checklist_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('operational_task_id');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_completed')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'operational_task_id'])
                ->references(['business_id', 'id'])->on('operational_tasks')->cascadeOnDelete();

            $table->index(['operational_task_id', 'sort_order']);
            $table->index(['business_id', 'is_completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_task_checklist_items');
        Schema::dropIfExists('operational_tasks');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
    }
};
