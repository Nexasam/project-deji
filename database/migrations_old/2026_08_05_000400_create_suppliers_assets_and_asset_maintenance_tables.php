<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->string('name');
            $table->string('category', 80)->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->json('address')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'category', 'status']);
            $table->index(['business_id', 'name']);
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('property_id')->constrained('properties')->restrictOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->string('name');
            $table->string('category', 80);
            $table->string('serial_number', 191)->nullable();
            $table->date('purchase_date')->nullable();
            $table->json('warranty')->nullable();
            $table->string('condition', 40)->default('good');
            $table->decimal('replacement_value', 19, 4)->nullable();
            $table->char('replacement_currency', 3)->nullable();
            $table->json('maintenance_schedule')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['business_id', 'property_id', 'serial_number']);
            $table->index(['business_id', 'property_id', 'status']);
            $table->index(['business_id', 'category', 'status']);
            $table->index(['supplier_id', 'status']);
            $table->index(['business_id', 'condition']);
        });

        Schema::create('asset_maintenance_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('asset_id')->constrained('assets')->restrictOnDelete();
            $table->foreignUuid('operational_task_id')->nullable()->constrained('operational_tasks')->restrictOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->string('maintenance_type', 80);
            $table->text('description')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('condition_before', 40)->nullable();
            $table->string('condition_after', 40)->nullable();
            $table->decimal('cost', 19, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('status', 40)->default('scheduled');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['business_id', 'asset_id', 'status', 'scheduled_at'], 'asset_maintenance_status_schedule_index');
            $table->index(['business_id', 'status', 'scheduled_at']);
            $table->index(['operational_task_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance_records');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('suppliers');
    }
};
