<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->string('template_key', 120);
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('trigger_type', 40)->default('manual');
            $table->string('trigger_event', 150)->nullable();
            $table->json('trigger_configuration')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'template_key', 'version']);
            $table->index(['business_id', 'is_active', 'status']);
            $table->index(['trigger_event', 'is_active']);
        });

        Schema::create('workflow_template_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workflow_template_id')->constrained('workflow_templates')->cascadeOnDelete();
            $table->string('step_key', 120);
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('step_order');
            $table->string('step_type', 40)->default('task');
            $table->string('task_type', 40)->nullable();
            $table->foreignUuid('responsible_role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignUuid('responsible_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->unsignedInteger('estimated_duration_minutes')->nullable();
            $table->unsignedInteger('sla_minutes')->nullable();
            $table->boolean('requires_verification')->default(false);
            $table->boolean('requires_approval')->default(false);
            $table->json('checklist_template')->nullable();
            $table->json('failure_configuration')->nullable();
            $table->json('escalation_configuration')->nullable();
            $table->json('configuration')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['workflow_template_id', 'step_order']);
            $table->unique(['workflow_template_id', 'step_key']);
        });

        Schema::table('workflow_runs', function (Blueprint $table) {
            $table->foreignUuid('workflow_template_id')->nullable()->after('business_id')->constrained('workflow_templates')->restrictOnDelete();
        });

        Schema::table('workflow_steps', function (Blueprint $table) {
            $table->foreignUuid('workflow_template_step_id')->nullable()->after('workflow_run_id')->constrained('workflow_template_steps')->restrictOnDelete();
        });

        Schema::table('operational_tasks', function (Blueprint $table) {
            $table->foreignUuid('workflow_run_id')->nullable()->after('booking_id')->constrained('workflow_runs')->restrictOnDelete();
            $table->foreignUuid('workflow_step_id')->nullable()->after('workflow_run_id')->constrained('workflow_steps')->restrictOnDelete();
            $table->unsignedInteger('estimated_duration_minutes')->nullable()->after('due_at');
            $table->timestamp('sla_due_at')->nullable()->after('due_at');
            $table->boolean('requires_verification')->default(false)->after('completed_at');
            $table->foreignUuid('verified_by')->nullable()->after('requires_verification')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_notes')->nullable()->after('verified_at');
            $table->text('manual_creation_reason')->nullable()->after('notes');
            $table->timestamp('escalated_at')->nullable()->after('next_recurrence_at');

            $table->index(['business_id', 'workflow_run_id']);
            $table->index(['business_id', 'sla_due_at', 'status']);
        });

        Schema::create('operational_task_dependencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('operational_task_id');
            $table->uuid('depends_on_task_id');
            $table->string('dependency_type', 40)->default('finish_to_start');
            $table->unsignedInteger('lag_minutes')->default(0);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'operational_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->foreign(['business_id', 'depends_on_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->unique(['operational_task_id', 'depends_on_task_id']);
            $table->index(['business_id', 'depends_on_task_id', 'status'], 'task_dependencies_predecessor_index');
        });

        Schema::create('operational_task_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('operational_task_id');
            $table->string('attachment_type', 40)->default('general');
            $table->string('disk', 80)->default('private');
            $table->string('path', 2048);
            $table->string('original_name')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('checksum', 128)->nullable();
            $table->text('caption')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'operational_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->index(['operational_task_id', 'attachment_type', 'status'], 'task_attachments_type_status_index');
        });

        Schema::create('operational_task_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('operational_task_id');
            $table->uuid('employee_id');
            $table->string('assignment_role', 40)->default('primary');
            $table->string('assignment_status', 40)->default('assigned');
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->text('release_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'operational_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->foreign(['business_id', 'employee_id'])->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->index(['operational_task_id', 'assignment_status']);
            $table->index(['business_id', 'employee_id', 'assignment_status'], 'task_assignments_employee_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_task_assignments');
        Schema::dropIfExists('operational_task_attachments');
        Schema::dropIfExists('operational_task_dependencies');

        Schema::table('operational_tasks', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'workflow_run_id']);
            $table->dropIndex(['business_id', 'sla_due_at', 'status']);
            $table->dropForeign(['workflow_run_id']);
            $table->dropForeign(['workflow_step_id']);
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['workflow_run_id', 'workflow_step_id', 'estimated_duration_minutes', 'sla_due_at', 'requires_verification', 'verified_by', 'verified_at', 'verification_notes', 'manual_creation_reason', 'escalated_at']);
        });

        Schema::table('workflow_steps', function (Blueprint $table) {
            $table->dropConstrainedForeignId('workflow_template_step_id');
        });
        Schema::table('workflow_runs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('workflow_template_id');
        });

        Schema::dropIfExists('workflow_template_steps');
        Schema::dropIfExists('workflow_templates');
    }
};
