<?php

use App\Enums\BookingDisputeStatus;
use App\Enums\BookingGuestType;
use App\Enums\FinancialDocumentStatus;
use App\Enums\FinancialDocumentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('guest_type', 20)->default(BookingGuestType::Adult->value);
            $table->boolean('is_primary')->default(false);
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->string('nationality')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('identity_verification_status', 40)->default('unverified');
            $table->json('preferences')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique(['booking_id', 'user_id']);
            $table->index(['business_id', 'booking_id', 'is_primary', 'status']);
            $table->index(['email', 'phone_number']);
        });

        Schema::create('booking_staff_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->uuid('employee_id');
            $table->string('assignment_role', 50)->default('booking_manager');
            $table->text('responsibilities')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamp('assigned_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('assignment_status', 40)->default('active');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'employee_id'])
                ->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'assignment_role', 'assignment_status'], 'booking_staff_booking_lookup');
            $table->index(['business_id', 'employee_id', 'assignment_status'], 'booking_staff_employee_lookup');
            $table->index(['booking_id', 'assignment_role', 'is_primary', 'assignment_status'], 'booking_primary_staff_lookup');
        });

        Schema::create('property_access_instructions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->string('title');
            $table->string('access_method', 40)->nullable();
            $table->text('instructions');
            $table->text('secret_payload')->nullable();
            $table->timestamp('effective_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['property_id', 'version']);
            $table->index(['business_id', 'property_id', 'status', 'effective_at'], 'property_access_current_lookup');
        });

        Schema::create('booking_financial_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->uuid('source_document_id')->nullable();
            $table->uuid('payment_id')->nullable();
            $table->string('document_number', 100);
            $table->string('document_type', 40)->default(FinancialDocumentType::Quotation->value);
            $table->string('document_status', 40)->default(FinancialDocumentStatus::Draft->value);
            $table->char('currency', 3);
            $table->decimal('subtotal_amount', 19, 4)->default(0);
            $table->decimal('discount_amount', 19, 4)->default(0);
            $table->decimal('tax_amount', 19, 4)->default(0);
            $table->decimal('total_amount', 19, 4)->default(0);
            $table->decimal('paid_amount', 19, 4)->default(0);
            $table->decimal('balance_amount', 19, 4)->default(0);
            $table->json('issuer_snapshot');
            $table->json('recipient_snapshot');
            $table->json('booking_snapshot');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('due_on')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->foreignUuid('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('void_reason')->nullable();
            $table->string('storage_disk', 40)->nullable();
            $table->string('storage_path', 2048)->nullable();
            $table->string('checksum', 128)->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'payment_id'])
                ->references(['business_id', 'id'])->on('payments')->restrictOnDelete();
            $table->foreign(['business_id', 'source_document_id'])
                ->references(['business_id', 'id'])->on('booking_financial_documents')->restrictOnDelete();
            $table->unique(['business_id', 'document_number']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'booking_id', 'document_type', 'document_status'], 'booking_financial_document_lookup');
            $table->index(['business_id', 'due_on', 'document_status']);
        });

        Schema::create('booking_financial_document_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('financial_document_id');
            $table->string('item_type', 40)->default('accommodation');
            $table->string('description');
            $table->decimal('quantity', 12, 4)->default(1);
            $table->decimal('unit_amount', 19, 4);
            $table->decimal('subtotal_amount', 19, 4);
            $table->decimal('discount_amount', 19, 4)->default(0);
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('tax_amount', 19, 4)->default(0);
            $table->decimal('total_amount', 19, 4);
            $table->json('metadata')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'financial_document_id'])
                ->references(['business_id', 'id'])->on('booking_financial_documents')->restrictOnDelete();
            $table->index(['financial_document_id', 'sort_order']);
            $table->index(['business_id', 'item_type', 'created_at']);
        });

        Schema::create('booking_payment_installments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->uuid('financial_document_id')->nullable();
            $table->unsignedSmallInteger('sequence');
            $table->string('purpose', 40);
            $table->decimal('amount', 19, 4);
            $table->decimal('paid_amount', 19, 4)->default(0);
            $table->char('currency', 3);
            $table->timestamp('due_at');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_status', 40)->default('pending');
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'financial_document_id'])
                ->references(['business_id', 'id'])->on('booking_financial_documents')->restrictOnDelete();
            $table->unique(['booking_id', 'sequence']);
            $table->index(['business_id', 'payment_status', 'due_at']);
        });

        Schema::create('booking_disputes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->uuid('booking_incident_id')->nullable();
            $table->uuid('financial_document_id')->nullable();
            $table->foreignUuid('payment_id')->nullable()->constrained('payments')->restrictOnDelete();
            $table->string('reference', 100);
            $table->string('dispute_type', 50);
            $table->string('opened_by_type', 40);
            $table->foreignUuid('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description');
            $table->decimal('disputed_amount', 19, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('dispute_status', 40)->default(BookingDisputeStatus::Open->value);
            $table->text('resolution')->nullable();
            $table->foreignUuid('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_incident_id'])->references(['business_id', 'id'])->on('booking_incidents')->restrictOnDelete();
            $table->foreign(['business_id', 'financial_document_id'])->references(['business_id', 'id'])->on('booking_financial_documents')->restrictOnDelete();
            $table->unique(['business_id', 'reference']);
            $table->index(['business_id', 'booking_id', 'dispute_status']);
            $table->index(['business_id', 'dispute_status', 'opened_at']);
        });

        Schema::create('business_automation_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->string('scope_key', 191)->default('business');
            $table->string('automation_key', 120);
            $table->string('trigger_event', 150);
            $table->string('workflow_key', 120);
            $table->boolean('is_enabled')->default(true);
            $table->json('configuration')->nullable();
            $table->unsignedSmallInteger('workflow_version')->default(1);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['business_id', 'scope_key', 'automation_key'], 'business_automation_scope_unique');
            $table->index(['business_id', 'trigger_event', 'is_enabled', 'status'], 'business_automation_trigger_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_automation_settings');
        Schema::dropIfExists('booking_disputes');
        Schema::dropIfExists('booking_payment_installments');
        Schema::dropIfExists('booking_financial_document_items');
        Schema::dropIfExists('booking_financial_documents');
        Schema::dropIfExists('property_access_instructions');
        Schema::dropIfExists('booking_staff_assignments');
        Schema::dropIfExists('booking_guests');
    }
};
