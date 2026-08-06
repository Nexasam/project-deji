<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('booking_id')
                ->constrained('bookings')
                ->restrictOnDelete();
            $table->foreignUuid('original_payment_id')
                ->nullable()
                ->constrained('payments')
                ->restrictOnDelete();
            $table->string('payment_type', 40);
            $table->decimal('amount', 19, 4);
            $table->char('currency', 3);
            $table->string('payment_method', 40);
            $table->string('provider', 80)->nullable();
            $table->string('reference', 191);
            $table->string('provider_reference', 191)->nullable();
            $table->string('status', 40)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->foreignUuid('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('receipt_disk', 40)->nullable();
            $table->string('receipt_path', 2048)->nullable();
            $table->json('provider_metadata')->nullable();
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['business_id', 'reference']);
            $table->unique(
                ['business_id', 'provider', 'provider_reference'],
                'payments_provider_reference_unique'
            );
            $table->index(['business_id', 'booking_id', 'status']);
            $table->index(['business_id', 'status', 'paid_at']);
            $table->index(['business_id', 'payment_method', 'paid_at']);
            $table->index(['original_payment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
