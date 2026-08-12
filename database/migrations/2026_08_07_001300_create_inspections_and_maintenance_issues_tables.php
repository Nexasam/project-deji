<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id')->nullable();
            $table->uuid('operational_task_id')->nullable();
            $table->uuid('inspector_employee_id')->nullable();
            $table->foreignUuid('preceding_inspection_id')->nullable()->constrained('inspections')->restrictOnDelete();
            $table->string('inspection_type', 40);
            $table->string('result', 40)->default('pending');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'operational_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->foreign(['business_id', 'inspector_employee_id'])->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'property_id', 'result', 'completed_at']);
            $table->index(['inspector_employee_id', 'status']);
        });

        Schema::create('inspection_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('inspection_id')->constrained('inspections')->restrictOnDelete();
            $table->uuid('corrective_task_id')->nullable();
            $table->string('category', 80);
            $table->string('item_name');
            $table->text('expected_value')->nullable();
            $table->text('observed_value')->nullable();
            $table->string('result', 40);
            $table->string('severity', 20)->nullable();
            $table->text('notes')->nullable();
            $table->json('evidence')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'corrective_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->index(['inspection_id', 'sort_order']);
            $table->index(['business_id', 'result', 'severity']);
        });

        Schema::create('maintenance_issues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id')->nullable();
            $table->uuid('asset_id')->nullable();
            $table->uuid('inspection_id')->nullable();
            $table->uuid('operational_task_id')->nullable();
            $table->foreignUuid('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('assigned_employee_id')->nullable();
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('origin', 40);
            $table->string('category', 80);
            $table->string('priority', 20)->default('normal');
            $table->text('description');
            $table->text('diagnosis')->nullable();
            $table->text('resolution')->nullable();
            $table->decimal('estimated_cost', 19, 4)->nullable();
            $table->decimal('actual_cost', 19, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status', 40)->default('reported');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'asset_id'])->references(['business_id', 'id'])->on('assets')->restrictOnDelete();
            $table->foreign(['business_id', 'inspection_id'])->references(['business_id', 'id'])->on('inspections')->restrictOnDelete();
            $table->foreign(['business_id', 'operational_task_id'])->references(['business_id', 'id'])->on('operational_tasks')->restrictOnDelete();
            $table->foreign(['business_id', 'assigned_employee_id'])->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'property_id', 'status', 'priority']);
            $table->index(['assigned_employee_id', 'status', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_issues');
        Schema::dropIfExists('inspection_items');
        Schema::dropIfExists('inspections');
    }
};
