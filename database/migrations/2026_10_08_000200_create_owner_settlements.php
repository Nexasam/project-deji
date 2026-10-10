<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('platform_settlement_accounts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->unique()->constrained('businesses')->restrictOnDelete();
            $table->string('bank_name');
            $table->string('account_name');
            $table->text('account_number');
            $table->string('account_number_last4', 4);
            $table->string('status', 30)->default('pending');
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_settlement_batches', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('settlement_account_id')->constrained('platform_settlement_accounts')->restrictOnDelete();
            $table->string('reference')->unique();
            $table->decimal('amount', 19, 4);
            $table->char('currency', 3);
            $table->string('settlement_status', 30)->default('draft');
            $table->foreignUuid('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignUuid('reversed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('transfer_reference')->nullable()->unique();
            $table->text('reversal_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'settlement_status']);
        });

        Schema::create('platform_settlement_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('settlement_batch_id')->constrained('platform_settlement_batches')->restrictOnDelete();
            $table->foreignUuid('financial_allocation_id')->constrained('booking_financial_allocations')->restrictOnDelete();
            $table->decimal('amount', 19, 4);
            $table->timestamps();
            $table->unique('financial_allocation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settlement_items');
        Schema::dropIfExists('platform_settlement_batches');
        Schema::dropIfExists('platform_settlement_accounts');
    }
};
