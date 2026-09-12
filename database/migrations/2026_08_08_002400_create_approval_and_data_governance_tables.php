<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->string('workflow_key', 120);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('subject_type', 150);
            $table->unsignedSmallInteger('version')->default(1);
            $table->json('conditions')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['business_id', 'workflow_key', 'version']);
            $table->index(['business_id', 'subject_type', 'is_active']);
        });
        Schema::create('approval_workflow_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('approval_workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->unsignedSmallInteger('step_order');
            $table->string('name');
            $table->foreignUuid('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignUuid('permission_id')->nullable()->constrained('permissions')->nullOnDelete();
            $table->unsignedSmallInteger('required_approvals')->default(1);
            $table->decimal('minimum_amount', 14, 4)->nullable();
            $table->decimal('maximum_amount', 14, 4)->nullable();
            $table->string('currency', 3)->nullable();
            $table->unsignedInteger('escalate_after_minutes')->nullable();
            $table->json('conditions')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['approval_workflow_id', 'step_order']);
        });
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('approval_workflow_id')->constrained('approval_workflows')->restrictOnDelete();
            $table->string('reference', 100);
            $table->string('subject_type');
            $table->uuid('subject_id');
            $table->foreignUuid('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('request_status', 40)->default('pending');
            $table->unsignedSmallInteger('current_step_order')->nullable();
            $table->json('subject_snapshot');
            $table->text('request_reason')->nullable();
            $table->dateTime('submitted_at');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['business_id', 'reference']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['business_id', 'request_status', 'due_at']);
        });
        Schema::create('approval_request_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('approval_request_id')->constrained('approval_requests')->restrictOnDelete();
            $table->foreignUuid('approval_workflow_step_id')->nullable()->constrained('approval_workflow_steps')->restrictOnDelete();
            $table->unsignedSmallInteger('step_order');
            $table->string('name');
            $table->foreignUuid('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignUuid('permission_id')->nullable()->constrained('permissions')->nullOnDelete();
            $table->unsignedSmallInteger('required_approvals')->default(1);
            $table->unsignedSmallInteger('approval_count')->default(0);
            $table->string('step_status', 40)->default('pending');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('definition_snapshot')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['approval_request_id', 'step_order']);
        });
        Schema::create('approval_delegations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('approval_workflow_id')->nullable()->constrained('approval_workflows')->restrictOnDelete();
            $table->foreignUuid('delegator_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('delegate_user_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->text('reason')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignUuid('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['business_id', 'delegator_user_id', 'starts_at', 'ends_at'], 'approval_delegations_active_index');
        });
        Schema::create('approval_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('approval_request_id')->constrained('approval_requests')->restrictOnDelete();
            $table->foreignUuid('approval_request_step_id')->nullable()->constrained('approval_request_steps')->restrictOnDelete();
            $table->foreignUuid('approval_delegation_id')->nullable()->constrained('approval_delegations')->restrictOnDelete();
            $table->foreignUuid('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action_type', 40);
            $table->text('comments')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('acted_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['approval_request_id', 'acted_at']);
        });
        Schema::create('data_retention_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->string('policy_key', 120);
            $table->string('name');
            $table->string('data_category', 120);
            $table->unsignedInteger('retention_days')->nullable();
            $table->string('retention_trigger', 80)->default('record_created');
            $table->string('terminal_action', 40)->default('anonymize');
            $table->text('legal_basis')->nullable();
            $table->boolean('allow_legal_hold')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['business_id', 'policy_key']);
            $table->index(['data_category', 'is_active']);
        });
        Schema::create('data_retention_executions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('data_retention_policy_id')->constrained('data_retention_policies')->restrictOnDelete();
            $table->string('execution_status', 40)->default('pending');
            $table->dateTime('cutoff_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('records_examined')->default(0);
            $table->unsignedBigInteger('records_affected')->default(0);
            $table->unsignedBigInteger('records_held')->default(0);
            $table->json('errors')->nullable();
            $table->foreignUuid('executed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['execution_status', 'started_at']);
        });
        Schema::create('data_subject_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('reference', 100)->unique();
            $table->string('request_type', 40);
            $table->string('request_status', 40)->default('received');
            $table->string('requester_name');
            $table->string('requester_email');
            $table->timestamp('identity_verified_at')->nullable();
            $table->dateTime('received_at');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('resolution_summary')->nullable();
            $table->text('legal_hold_reason')->nullable();
            $table->json('request_data')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['business_id', 'request_status', 'due_at']);
        });
        Schema::table('audit_events', function (Blueprint $table) {
            $table->string('source_channel', 40)->nullable()->after('metadata');
            $table->string('actor_type', 40)->default('user')->after('source_channel');
            $table->uuid('correlation_id')->nullable()->after('actor_type');
            $table->string('request_id', 191)->nullable()->after('correlation_id');
            $table->string('device_identifier', 191)->nullable()->after('request_id');
            $table->index(['correlation_id', 'occurred_at']);
            $table->index(['source_channel', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::table('audit_events', function (Blueprint $table) {
            $table->dropIndex(['correlation_id', 'occurred_at']);
            $table->dropIndex(['source_channel', 'occurred_at']);
            $table->dropColumn(['source_channel', 'actor_type', 'correlation_id', 'request_id', 'device_identifier']);
        });
        Schema::dropIfExists('data_subject_requests');
        Schema::dropIfExists('data_retention_executions');
        Schema::dropIfExists('data_retention_policies');
        Schema::dropIfExists('approval_actions');
        Schema::dropIfExists('approval_delegations');
        Schema::dropIfExists('approval_request_steps');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_workflow_steps');
        Schema::dropIfExists('approval_workflows');
    }
};
