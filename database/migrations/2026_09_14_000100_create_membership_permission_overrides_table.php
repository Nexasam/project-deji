<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_permission_overrides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('business_membership_id');
            $table->foreignUuid('permission_id')->constrained('permissions')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->string('effect', 20);
            $table->text('reason');
            $table->foreignUuid('approved_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'business_membership_id'], 'membership_override_business_membership_fk')
                ->references(['business_id', 'id'])->on('business_memberships')->restrictOnDelete();
            $table->foreign(['business_id', 'property_id'], 'membership_override_business_property_fk')
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->index(['business_membership_id', 'permission_id', 'status'], 'membership_permission_lookup');
            $table->index(['property_id', 'effect', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_permission_overrides');
    }
};
