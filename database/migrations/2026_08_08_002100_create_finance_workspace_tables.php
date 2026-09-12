<?php

use App\Enums\FinancialAccountType;
use App\Enums\FinancialTransactionDirection;
use App\Enums\FinancialTransactionType;
use App\Enums\RefundRequestStatus;
use App\Enums\RevenueRecognitionBasis;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('financial_accounts')) {
            Schema::create('financial_accounts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
                $table->string('code', 80);
                $table->string('name');
                $table->string('account_type', 40)->default(FinancialAccountType::Bank->value);
                $table->string('provider', 80)->nullable();
                $table->string('external_account_reference', 191)->nullable();
                $table->char('currency', 3);
                $table->decimal('opening_balance', 19, 4)->default(0);
                $table->date('opening_balance_date')->nullable();
                $table->boolean('is_default')->default(false);
                $table->string('status', 40)->default('active');
                $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['business_id', 'code']);
                $table->unique(['business_id', 'id']);
                $table->index(['business_id', 'account_type', 'currency', 'status'], 'financial_accounts_type_currency_idx');
                $table->index(['provider', 'external_account_reference'], 'financial_accounts_provider_ref_idx');
            });
        } else {
            Schema::table('financial_accounts', function (Blueprint $table) {
                if (! Schema::hasIndex('financial_accounts', 'financial_accounts_type_currency_idx')) {
                    $table->index(['business_id', 'account_type', 'currency', 'status'], 'financial_accounts_type_currency_idx');
                }

                if (! Schema::hasIndex('financial_accounts', 'financial_accounts_provider_ref_idx')) {
                    $table->index(['provider', 'external_account_reference'], 'financial_accounts_provider_ref_idx');
                }
            });
        }

        Schema::create('cost_centres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->string('code', 80);
            $table->string('name');
            $table->text('description')->nullable();
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

        Schema::create('tax_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->string('code', 80);
            $table->string('name');
            $table->string('tax_type', 40);
            $table->decimal('rate', 8, 4)->default(0);
            $table->boolean('is_inclusive')->default(false);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'code', 'effective_from']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'tax_type', 'status']);
        });

        Schema::create('expense_recurring_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->uuid('supplier_id')->nullable();
            $table->uuid('cost_centre_id')->nullable();
            $table->uuid('tax_category_id')->nullable();
            $table->string('name');
            $table->string('category', 80);
            $table->decimal('amount', 19, 4);
            $table->char('currency', 3);
            $table->string('frequency', 40);
            $table->unsignedSmallInteger('interval')->default(1);
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->date('next_occurrence_on')->nullable();
            $table->json('recurrence_rule')->nullable();
            $table->text('description')->nullable();
            $table->boolean('auto_submit_for_approval')->default(true);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'supplier_id'])->references(['business_id', 'id'])->on('suppliers')->restrictOnDelete();
            $table->foreign(['business_id', 'cost_centre_id'])->references(['business_id', 'id'])->on('cost_centres')->restrictOnDelete();
            $table->foreign(['business_id', 'tax_category_id'])->references(['business_id', 'id'])->on('tax_categories')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'next_occurrence_on', 'status'], 'expense_recurring_next_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->uuid('cost_centre_id')->nullable()->after('employee_id');
            $table->uuid('tax_category_id')->nullable()->after('cost_centre_id');
            $table->uuid('recurring_expense_schedule_id')->nullable()->after('tax_category_id');
            $table->decimal('subtotal_amount', 19, 4)->nullable()->after('amount');
            $table->decimal('tax_amount', 19, 4)->default(0)->after('subtotal_amount');
            $table->foreignUuid('submitted_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable()->after('submitted_by');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
            $table->foreign(['business_id', 'cost_centre_id'])->references(['business_id', 'id'])->on('cost_centres')->restrictOnDelete();
            $table->foreign(['business_id', 'tax_category_id'])->references(['business_id', 'id'])->on('tax_categories')->restrictOnDelete();
            $table->foreign(['business_id', 'recurring_expense_schedule_id'])->references(['business_id', 'id'])->on('expense_recurring_schedules')->restrictOnDelete();
            $table->index(['business_id', 'cost_centre_id', 'incurred_on']);
            $table->index(['recurring_expense_schedule_id', 'incurred_on']);
        });

        Schema::create('expense_approval_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('expense_id');
            $table->string('action', 40);
            $table->string('previous_status', 40)->nullable();
            $table->string('new_status', 40);
            $table->text('reason')->nullable();
            $table->dateTime('occurred_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'expense_id'])->references(['business_id', 'id'])->on('expenses')->restrictOnDelete();
            $table->index(['business_id', 'expense_id', 'occurred_at']);
        });

        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->char('base_currency', 3);
            $table->char('quote_currency', 3);
            $table->decimal('rate', 20, 10);
            $table->string('provider', 80);
            $table->dateTime('effective_at');
            $table->timestamp('expires_at')->nullable();
            $table->json('provider_metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['business_id', 'base_currency', 'quote_currency', 'provider', 'effective_at'], 'exchange_rate_source_unique');
            $table->index(['base_currency', 'quote_currency', 'effective_at'], 'exchange_rates_currency_date_idx');
        });

        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('financial_account_id')->nullable();
            $table->uuid('property_id')->nullable();
            $table->uuid('booking_id')->nullable();
            $table->uuid('payment_id')->nullable();
            $table->uuid('expense_id')->nullable();
            $table->uuid('tax_category_id')->nullable();
            $table->foreignUuid('exchange_rate_id')->nullable()->constrained('currency_exchange_rates')->restrictOnDelete();
            $table->uuid('reversed_transaction_id')->nullable();
            $table->uuid('transaction_group_id')->nullable();
            $table->string('reference', 100);
            $table->string('transaction_type', 50)->default(FinancialTransactionType::Adjustment->value);
            $table->string('direction', 20)->default(FinancialTransactionDirection::NonCash->value);
            $table->string('economic_category', 50);
            $table->string('source_type', 120);
            $table->uuid('source_id');
            $table->decimal('amount', 19, 4);
            $table->char('currency', 3);
            $table->decimal('base_amount', 19, 4)->nullable();
            $table->char('base_currency', 3)->nullable();
            $table->dateTime('occurred_at');
            $table->date('effective_on');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('transaction_status', 40)->default('pending');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'financial_account_id'], 'financial_transactions_account_fk')->references(['business_id', 'id'])->on('financial_accounts')->restrictOnDelete();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'payment_id'])->references(['business_id', 'id'])->on('payments')->restrictOnDelete();
            $table->foreign(['business_id', 'expense_id'])->references(['business_id', 'id'])->on('expenses')->restrictOnDelete();
            $table->foreign(['business_id', 'tax_category_id'])->references(['business_id', 'id'])->on('tax_categories')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->foreign(['business_id', 'reversed_transaction_id'], 'financial_tx_reversal_fk')->references(['business_id', 'id'])->on('financial_transactions')->restrictOnDelete();
            $table->unique(['business_id', 'reference']);
            $table->index(['business_id', 'effective_on', 'direction', 'transaction_status'], 'financial_transaction_cashflow_lookup');
            $table->index(['business_id', 'property_id', 'effective_on', 'economic_category'], 'financial_transaction_property_lookup');
            $table->index(['business_id', 'booking_id', 'occurred_at']);
            $table->index(['source_type', 'source_id']);
            $table->index('transaction_group_id');
        });

        Schema::create('revenue_recognition_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->string('name');
            $table->string('recognition_basis', 40)->default(RevenueRecognitionBasis::CheckOut->value);
            $table->json('rules')->nullable();
            $table->boolean('is_default')->default(false);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'property_id', 'is_default', 'effective_from'], 'revenue_policy_lookup');
        });

        Schema::create('revenue_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->uuid('revenue_recognition_policy_id');
            $table->uuid('financial_transaction_id')->nullable();
            $table->uuid('financial_document_id')->nullable();
            $table->string('revenue_category', 50);
            $table->decimal('gross_amount', 19, 4);
            $table->decimal('deduction_amount', 19, 4)->default(0);
            $table->decimal('net_amount', 19, 4);
            $table->char('currency', 3);
            $table->date('recognized_on');
            $table->string('recognition_status', 40)->default('recognized');
            $table->text('description')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'revenue_recognition_policy_id'], 'revenue_entries_policy_fk')->references(['business_id', 'id'])->on('revenue_recognition_policies')->restrictOnDelete();
            $table->foreign(['business_id', 'financial_transaction_id'])->references(['business_id', 'id'])->on('financial_transactions')->restrictOnDelete();
            $table->foreign(['business_id', 'financial_document_id'])->references(['business_id', 'id'])->on('booking_financial_documents')->restrictOnDelete();
            $table->index(['business_id', 'recognized_on', 'revenue_category']);
            $table->index(['business_id', 'property_id', 'recognized_on']);
            $table->index(['business_id', 'booking_id', 'recognized_on']);
        });

        Schema::create('refund_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->uuid('original_payment_id');
            $table->uuid('refund_payment_id')->nullable();
            $table->string('reference', 100);
            $table->string('reason_type', 50);
            $table->text('reason')->nullable();
            $table->decimal('requested_amount', 19, 4);
            $table->decimal('approved_amount', 19, 4)->nullable();
            $table->char('currency', 3);
            $table->foreignUuid('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('requested_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('refund_status', 40)->default(RefundRequestStatus::Requested->value);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->foreign(['business_id', 'original_payment_id'])->references(['business_id', 'id'])->on('payments')->restrictOnDelete();
            $table->foreign(['business_id', 'refund_payment_id'])->references(['business_id', 'id'])->on('payments')->restrictOnDelete();
            $table->unique(['business_id', 'reference']);
            $table->index(['business_id', 'refund_status', 'requested_at']);
            $table->index(['business_id', 'booking_id', 'refund_status']);
        });

        Schema::create('financial_reconciliations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('financial_account_id');
            $table->string('reference', 100);
            $table->date('period_starts_on');
            $table->date('period_ends_on');
            $table->decimal('opening_balance', 19, 4);
            $table->decimal('closing_balance', 19, 4);
            $table->decimal('calculated_balance', 19, 4)->nullable();
            $table->decimal('difference_amount', 19, 4)->nullable();
            $table->char('currency', 3);
            $table->string('reconciliation_status', 40)->default('draft');
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'financial_account_id'], 'financial_reconciliations_account_fk')->references(['business_id', 'id'])->on('financial_accounts')->restrictOnDelete();
            $table->unique(['business_id', 'reference']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'financial_account_id', 'period_ends_on'], 'financial_reconciliation_account_lookup');
        });

        Schema::create('financial_reconciliation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('financial_reconciliation_id');
            $table->uuid('financial_transaction_id')->nullable();
            $table->string('external_reference', 191)->nullable();
            $table->date('statement_date');
            $table->decimal('statement_amount', 19, 4);
            $table->decimal('transaction_amount', 19, 4)->nullable();
            $table->decimal('difference_amount', 19, 4)->nullable();
            $table->string('match_status', 40)->default('unmatched');
            $table->foreignUuid('matched_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('matched_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'financial_reconciliation_id'], 'financial_recon_items_recon_fk')->references(['business_id', 'id'])->on('financial_reconciliations')->restrictOnDelete();
            $table->foreign(['business_id', 'financial_transaction_id'], 'financial_recon_items_tx_fk')->references(['business_id', 'id'])->on('financial_transactions')->restrictOnDelete();
            $table->index(['financial_reconciliation_id', 'match_status'], 'financial_recon_items_match_idx');
            $table->index(['business_id', 'external_reference'], 'financial_recon_items_external_idx');
        });

        Schema::create('currency_conversions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('exchange_rate_id')->constrained('currency_exchange_rates')->restrictOnDelete();
            $table->uuid('financial_transaction_id')->nullable();
            $table->string('source_type', 120);
            $table->uuid('source_id');
            $table->decimal('source_amount', 19, 4);
            $table->char('source_currency', 3);
            $table->decimal('rate', 20, 10);
            $table->decimal('converted_amount', 19, 4);
            $table->char('target_currency', 3);
            $table->dateTime('converted_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'financial_transaction_id'], 'currency_conversions_tx_fk')->references(['business_id', 'id'])->on('financial_transactions')->restrictOnDelete();
            $table->index(['source_type', 'source_id']);
            $table->index(['business_id', 'source_currency', 'target_currency', 'converted_at'], 'currency_conversion_history_lookup');
        });

        Schema::create('financial_forecast_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id')->nullable();
            $table->string('forecast_type', 50);
            $table->unsignedSmallInteger('horizon_days');
            $table->date('forecast_starts_on');
            $table->date('forecast_ends_on');
            $table->char('currency', 3)->nullable();
            $table->json('forecast_values');
            $table->json('assumptions')->nullable();
            $table->json('supporting_metrics')->nullable();
            $table->string('model_provider', 80)->nullable();
            $table->string('model_name', 120)->nullable();
            $table->string('model_version', 80)->nullable();
            $table->string('calculation_version', 80);
            $table->dateTime('generated_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->index(['business_id', 'forecast_type', 'generated_at'], 'financial_forecasts_type_date_idx');
            $table->index(['business_id', 'property_id', 'forecast_starts_on', 'forecast_ends_on'], 'financial_forecast_scope_lookup');
        });

        Schema::create('financial_report_definitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->string('name');
            $table->string('report_type', 80);
            $table->json('configuration')->nullable();
            $table->boolean('is_system')->default(false);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'name']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'report_type', 'status'], 'financial_report_defs_type_idx');
        });

        Schema::create('financial_report_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('financial_report_definition_id');
            $table->string('frequency', 40);
            $table->json('schedule_rule');
            $table->json('formats');
            $table->json('delivery_channels');
            $table->json('recipients');
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'financial_report_definition_id'], 'financial_report_schedules_definition_fk')->references(['business_id', 'id'])->on('financial_report_definitions')->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'next_run_at', 'status']);
        });

        Schema::create('financial_report_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('financial_report_definition_id');
            $table->uuid('financial_report_schedule_id')->nullable();
            $table->string('run_status', 40)->default('pending');
            $table->json('parameters');
            $table->date('period_starts_on')->nullable();
            $table->date('period_ends_on')->nullable();
            $table->json('summary')->nullable();
            $table->string('storage_disk', 40)->nullable();
            $table->string('storage_path', 2048)->nullable();
            $table->string('format', 20);
            $table->string('checksum', 128)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'financial_report_definition_id'], 'financial_report_runs_definition_fk')->references(['business_id', 'id'])->on('financial_report_definitions')->restrictOnDelete();
            $table->foreign(['business_id', 'financial_report_schedule_id'], 'financial_report_runs_schedule_fk')->references(['business_id', 'id'])->on('financial_report_schedules')->restrictOnDelete();
            $table->index(['business_id', 'run_status', 'created_at']);
            $table->index(['financial_report_definition_id', 'period_starts_on', 'period_ends_on'], 'financial_report_period_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_report_runs');
        Schema::dropIfExists('financial_report_schedules');
        Schema::dropIfExists('financial_report_definitions');
        Schema::dropIfExists('financial_forecast_snapshots');
        Schema::dropIfExists('currency_conversions');
        Schema::dropIfExists('financial_reconciliation_items');
        Schema::dropIfExists('financial_reconciliations');
        Schema::dropIfExists('refund_requests');
        Schema::dropIfExists('revenue_entries');
        Schema::dropIfExists('revenue_recognition_policies');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('currency_exchange_rates');
        Schema::dropIfExists('expense_approval_events');

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['recurring_expense_schedule_id']);
            $table->dropForeign(['tax_category_id']);
            $table->dropForeign(['cost_centre_id']);
            $table->dropForeign(['submitted_by']);
            $table->dropIndex(['recurring_expense_schedule_id', 'incurred_on']);
            $table->dropIndex(['business_id', 'cost_centre_id', 'incurred_on']);
            $table->dropColumn([
                'cost_centre_id', 'tax_category_id', 'recurring_expense_schedule_id',
                'subtotal_amount', 'tax_amount', 'submitted_by', 'submitted_at',
                'approved_at', 'rejection_reason',
            ]);
        });

        Schema::dropIfExists('expense_recurring_schedules');
        Schema::dropIfExists('tax_categories');
        Schema::dropIfExists('cost_centres');
        Schema::dropIfExists('financial_accounts');
    }
};
