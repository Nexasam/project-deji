<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_role_permission_settings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained('roles')->restrictOnDelete();
            $table->foreignUuid('permission_id')->constrained('permissions')->restrictOnDelete();
            $table->string('effect', 20)->default('allow');
            $table->text('reason')->nullable();
            $table->foreignUuid('configured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['business_id', 'role_id', 'permission_id'], 'business_role_permission_unique');
            $table->index(['business_id', 'role_id', 'effect', 'status'], 'business_role_permission_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_role_permission_settings');
    }
};
