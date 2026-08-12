<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key', 80)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->nullable()->constrained('workspaces')->restrictOnDelete();
            $table->string('name', 150)->unique();
            $table->string('category', 80);
            $table->string('audience', 40);
            $table->string('module', 80);
            $table->string('action', 80);
            $table->text('description')->nullable();
            $table->string('risk_level', 20)->default('normal');
            $table->boolean('requires_audit')->default(true);
            $table->boolean('is_sensitive')->default(false);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['module', 'action', 'status']);
            $table->index(['workspace_id', 'status']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('parent_role_id')->nullable()->constrained('roles')->restrictOnDelete();
            $table->string('name');
            $table->string('slug', 100);
            $table->string('system_key', 100)->nullable()->unique();
            $table->string('scope', 40);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('hierarchy_level')->default(0);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_template')->default(false);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'slug']);
            $table->index(['scope', 'status']);
            $table->index(['business_id', 'hierarchy_level', 'status']);
            $table->index(['parent_role_id', 'status']);
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignUuid('role_id')->constrained('roles')->restrictOnDelete();
            $table->foreignUuid('permission_id')->constrained('permissions')->restrictOnDelete();
            $table->foreignUuid('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->primary(['role_id', 'permission_id']);
            $table->index('permission_id');
        });

        Schema::create('role_workspaces', function (Blueprint $table) {
            $table->foreignUuid('role_id')->constrained('roles')->restrictOnDelete();
            $table->foreignUuid('workspace_id')->constrained('workspaces')->restrictOnDelete();
            $table->string('access_level', 20)->default('full');
            $table->foreignUuid('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->primary(['role_id', 'workspace_id']);
            $table->index('workspace_id');
        });

        Schema::create('permission_separation_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->string('system_key', 150)->nullable()->unique();
            $table->foreignUuid('permission_id')->constrained('permissions')->restrictOnDelete();
            $table->foreignUuid('conflicting_permission_id')->constrained('permissions')->restrictOnDelete();
            $table->string('enforcement', 20)->default('block');
            $table->text('description');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['business_id', 'permission_id', 'conflicting_permission_id'],
                'permission_separation_rule_unique'
            );
            $table->index(['permission_id', 'status']);
            $table->index(['conflicting_permission_id', 'status'], 'conflicting_permission_status_index');
        });

        Schema::create('business_memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('membership_type', 40)->default('staff');
            $table->string('job_title')->nullable();
            $table->string('status', 40)->default('invited');
            $table->foreignUuid('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['business_id', 'membership_type', 'status']);
        });

        Schema::create('user_role_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('role_id')->constrained('roles')->restrictOnDelete();
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
            $table->index(['user_id', 'status', 'expires_at']);
        });

        Schema::create('user_business_contexts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('business_membership_id')->nullable()->constrained('business_memberships')->restrictOnDelete();
            $table->timestamp('switched_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['business_id', 'status']);
            $table->index(['business_membership_id', 'status'], 'business_context_membership_status_index');
        });

        Schema::create('membership_role_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('business_membership_id')->constrained('business_memberships')->restrictOnDelete();
            $table->foreignUuid('role_id')->constrained('roles')->restrictOnDelete();
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->restrictOnDelete();
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['business_membership_id', 'role_id', 'property_id'],
                'membership_role_property_unique'
            );
            $table->index(['business_id', 'role_id', 'status']);
            $table->index(['business_membership_id', 'status', 'expires_at'], 'membership_role_status_expiry_index');
            $table->index(['property_id', 'status']);
        });

        Schema::create('membership_permission_overrides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('business_membership_id')->constrained('business_memberships')->restrictOnDelete();
            $table->foreignUuid('permission_id')->constrained('permissions')->restrictOnDelete();
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->restrictOnDelete();
            $table->string('effect', 20);
            $table->text('reason');
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['business_membership_id', 'permission_id', 'status'], 'membership_permission_status_index');
            $table->index(['property_id', 'status']);
        });

        Schema::create('impersonation_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('platform_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('target_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('authorized_by')->constrained('users')->restrictOnDelete();
            $table->text('reason');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('authorized_at');
            $table->timestamp('expires_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('notification_acknowledged_at')->nullable();
            $table->text('termination_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['platform_user_id', 'status', 'started_at']);
            $table->index(['target_user_id', 'started_at']);
            $table->index(['business_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impersonation_sessions');
        Schema::dropIfExists('membership_permission_overrides');
        Schema::dropIfExists('membership_role_assignments');
        Schema::dropIfExists('user_business_contexts');
        Schema::dropIfExists('user_role_assignments');
        Schema::dropIfExists('business_memberships');
        Schema::dropIfExists('permission_separation_rules');
        Schema::dropIfExists('role_workspaces');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('workspaces');
    }
};
