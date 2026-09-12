<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('employee_id');
            $table->string('skill_key', 100);
            $table->string('name');
            $table->string('proficiency_level', 40)->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(['business_id', 'employee_id'])->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->unique(['employee_id', 'skill_key']);
            $table->index(['business_id', 'skill_key', 'status']);
        });

        Schema::create('employee_certifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('employee_id');
            $table->foreignUuid('document_id')->nullable()->constrained('documents')->restrictOnDelete();
            $table->string('name');
            $table->string('issuing_organization')->nullable();
            $table->string('certificate_number', 120)->nullable();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->string('verification_status', 40)->default('unverified');
            $table->timestamp('verified_at')->nullable();
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(['business_id', 'employee_id'])->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->index(['business_id', 'expires_on', 'verification_status'], 'employee_certifications_expiry_index');
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->string('sku', 100);
            $table->string('name');
            $table->string('category', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('unit_of_measure', 40)->default('unit');
            $table->decimal('default_reorder_level', 14, 4)->nullable();
            $table->decimal('default_reorder_quantity', 14, 4)->nullable();
            $table->decimal('unit_cost', 14, 4)->nullable();
            $table->string('currency', 3)->nullable();
            $table->boolean('is_trackable')->default(true);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['business_id', 'sku']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'category', 'status']);
        });

        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->string('code', 100);
            $table->string('name');
            $table->string('location_type', 40)->default('store');
            $table->text('description')->nullable();
            $table->json('address')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['business_id', 'code']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'property_id', 'status']);
        });

        Schema::create('inventory_stock_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('inventory_item_id');
            $table->uuid('inventory_location_id');
            $table->decimal('quantity_on_hand', 14, 4)->default(0);
            $table->decimal('quantity_reserved', 14, 4)->default(0);
            $table->decimal('reorder_level', 14, 4)->nullable();
            $table->decimal('reorder_quantity', 14, 4)->nullable();
            $table->timestamp('last_counted_at')->nullable();
            $table->foreignUuid('last_counted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'inventory_item_id'])->references(['business_id', 'id'])->on('inventory_items')->restrictOnDelete();
            $table->foreign(['business_id', 'inventory_location_id'])->references(['business_id', 'id'])->on('inventory_locations')->restrictOnDelete();
            $table->unique(['inventory_item_id', 'inventory_location_id'], 'inventory_stock_item_location_unique');
            $table->index(['business_id', 'quantity_on_hand']);
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('inventory_item_id');
            $table->uuid('source_location_id')->nullable();
            $table->uuid('destination_location_id')->nullable();
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->restrictOnDelete();
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings')->restrictOnDelete();
            $table->foreignUuid('operational_task_id')->nullable()->constrained('operational_tasks')->restrictOnDelete();
            $table->foreignUuid('employee_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->string('movement_type', 40);
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost', 14, 4)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('reference', 120)->nullable();
            $table->text('reason')->nullable();
            $table->dateTime('occurred_at');
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'inventory_item_id'])->references(['business_id', 'id'])->on('inventory_items')->restrictOnDelete();
            $table->foreign(['business_id', 'source_location_id'])->references(['business_id', 'id'])->on('inventory_locations')->restrictOnDelete();
            $table->foreign(['business_id', 'destination_location_id'])->references(['business_id', 'id'])->on('inventory_locations')->restrictOnDelete();
            $table->index(['business_id', 'inventory_item_id', 'occurred_at'], 'inventory_movements_item_time_index');
            $table->index(['business_id', 'movement_type', 'occurred_at']);
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('asset_id')->constrained('assets')->restrictOnDelete();
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->restrictOnDelete();
            $table->foreignUuid('employee_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings')->restrictOnDelete();
            $table->foreignUuid('operational_task_id')->nullable()->constrained('operational_tasks')->restrictOnDelete();
            $table->string('assignment_type', 40)->default('custody');
            $table->dateTime('assigned_at');
            $table->timestamp('expected_return_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('returned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['business_id', 'asset_id', 'returned_at']);
            $table->index(['business_id', 'property_id', 'status']);
            $table->index(['business_id', 'employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_stock_levels');
        Schema::dropIfExists('inventory_locations');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('employee_certifications');
        Schema::dropIfExists('employee_skills');
    }
};
