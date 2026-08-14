<?php

use App\Enums\UserRoleStatus;
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
            $table->string('key', 150)->unique();
            $table->string('category', 80);
            $table->string('action', 80);
            $table->text('description')->nullable();
            $table->string('risk_level', 20)->default('normal');
            $table->boolean('requires_audit')->default(true);
            $table->boolean('is_sensitive')->default(false);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'action', 'status']);
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
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('role_id')->constrained('roles')->restrictOnDelete();
            $table->foreignUuid('permission_id')->constrained('permissions')->restrictOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
            $table->index(['permission_id', 'status']);
        });

        Schema::create('role_workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('role_id')->constrained('roles')->restrictOnDelete();
            $table->foreignUuid('workspace_id')->constrained('workspaces')->restrictOnDelete();
            $table->string('access_level', 20)->default('full');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'workspace_id']);
            $table->index(['workspace_id', 'status']);
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('role_id')->constrained('roles')->restrictOnDelete();
            $table->foreignUuid('business_membership_id')->nullable()
                ->constrained('business_memberships')->restrictOnDelete();
            $table->string('scope_key', 36)->default('global');
            $table->string('status', 40)->default(UserRoleStatus::Active->value);
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id', 'scope_key']);
            $table->index(['user_id', 'status', 'expires_at']);
            $table->index(['business_membership_id', 'status']);
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

            $table->unique(['business_id', 'permission_id', 'conflicting_permission_id'], 'permission_separation_unique');
        });

        Schema::create('user_identities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('provider', 40);
            $table->string('provider_user_id', 191);
            $table->string('provider_email')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['provider', 'provider_user_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('user_mfa_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('method_type', 40);
            $table->string('label')->nullable();
            $table->text('secret')->nullable();
            $table->text('recovery_codes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'method_type', 'status']);
        });

        Schema::create('user_refresh_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token_hash', 128)->unique();
            $table->string('device_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->dateTime('expires_at');
            $table->timestamp('revoked_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'status', 'expires_at']);
        });

        Schema::create('password_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->string('system_key', 100)->nullable()->unique();
            $table->string('name');
            $table->unsignedSmallInteger('minimum_length')->default(12);
            $table->boolean('requires_uppercase')->default(true);
            $table->boolean('requires_lowercase')->default(true);
            $table->boolean('requires_number')->default(true);
            $table->boolean('requires_symbol')->default(false);
            $table->unsignedSmallInteger('password_history_count')->default(5);
            $table->unsignedSmallInteger('maximum_age_days')->nullable();
            $table->unsignedSmallInteger('maximum_failed_attempts')->default(5);
            $table->unsignedSmallInteger('lockout_minutes')->default(15);
            $table->unsignedSmallInteger('session_timeout_minutes')->default(60);
            $table->boolean('requires_mfa')->default(false);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_business_contexts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('business_membership_id')->nullable()->constrained('business_memberships')->restrictOnDelete();
            $table->foreignUuid('active_user_role_id')->nullable()->constrained('user_roles')->restrictOnDelete();
            $table->timestamp('switched_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['business_id', 'status']);
        });

        Schema::create('impersonation_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('platform_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('authorized_by')->constrained('users')->restrictOnDelete();
            $table->text('reason');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('started_at');
            $table->dateTime('authorized_at');
            $table->dateTime('expires_at');
            $table->timestamp('ended_at')->nullable();
            $table->text('termination_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['platform_user_id', 'status', 'started_at']);
            $table->index(['business_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impersonation_sessions');
        Schema::dropIfExists('user_business_contexts');
        Schema::dropIfExists('password_policies');
        Schema::dropIfExists('user_refresh_tokens');
        Schema::dropIfExists('user_mfa_methods');
        Schema::dropIfExists('user_identities');
        Schema::dropIfExists('permission_separation_rules');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_workspaces');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('workspaces');
    }
};
