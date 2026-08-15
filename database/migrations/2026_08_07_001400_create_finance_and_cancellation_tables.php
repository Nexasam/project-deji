<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->uuid('booking_id')->nullable();
            $table->foreignUuid('operational_task_id')->nullable()->constrained('operational_tasks')->restrictOnDelete();
            $table->foreignUuid('maintenance_issue_id')->nullable()->constrained('maintenance_issues')->restrictOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->foreignUuid('employee_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->string('reference', 100);
            $table->string('category', 80);
            $table->decimal('amount', 19, 4);
            $table->char('currency', 3);
            $table->string('payee')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_reference', 191)->nullable();
            $table->date('incurred_on');
            $table->date('due_on')->nullable();
            $table->date('paid_on')->nullable();
            $table->string('approval_status', 40)->default('pending');
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique(['business_id', 'reference']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'category', 'incurred_on']);
            $table->index(['business_id', 'approval_status', 'due_on']);
        });

        Schema::create('booking_financial_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->foreignUuid('payment_id')->nullable()->constrained('payments')->restrictOnDelete();
            $table->foreignUuid('expense_id')->nullable()->constrained('expenses')->restrictOnDelete();
            $table->string('allocation_type', 40);
            $table->string('direction', 20);
            $table->decimal('amount', 19, 4);
            $table->char('currency', 3);
            $table->date('recognized_on');
            $table->text('description')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'allocation_type'], 'financial_allocation_booking_type_idx');
            $table->index(['business_id', 'recognized_on', 'direction'], 'financial_allocation_recognition_idx');
        });

        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->string('name');
            $table->string('policy_type', 40);
            $table->text('description')->nullable();
            $table->json('rules');
            $table->boolean('is_default')->default(false);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->index(['business_id', 'property_id', 'status']);
            $table->index(['business_id', 'is_default', 'effective_from'], 'cancellation_policy_default_effective_idx');
        });

        Schema::create('booking_cancellations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->foreignUuid('cancellation_policy_id')->nullable()->constrained('cancellation_policies')->restrictOnDelete();
            $table->foreignUuid('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requester_type', 40);
            $table->text('reason')->nullable();
            $table->json('policy_snapshot');
            $table->decimal('refund_amount', 19, 4)->default(0);
            $table->decimal('cancellation_fee', 19, 4)->default(0);
            $table->char('currency', 3);
            $table->foreignUuid('refund_payment_id')->nullable()->constrained('payments')->restrictOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 40)->default('pending');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'status']);
            $table->index(['business_id', 'requested_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_cancellations');
        Schema::dropIfExists('cancellation_policies');
        Schema::dropIfExists('booking_financial_allocations');
        Schema::dropIfExists('expenses');
    }
};
