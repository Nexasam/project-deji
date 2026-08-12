<?php

use App\Enums\PaymentStatus;
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
            $table->uuid('booking_id');
            $table->uuid('original_payment_id')->nullable();
            $table->string('reference', 100);
            $table->string('purpose', 40);
            $table->decimal('amount', 19, 4);
            $table->char('currency', 3);
            $table->string('method', 40);
            $table->string('provider', 40)->nullable();
            $table->string('provider_reference', 191)->nullable();
            $table->string('status', 40)->default(PaymentStatus::Pending->value);
            $table->timestamp('transaction_at');
            $table->foreignUuid('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('receipt_disk', 40)->nullable();
            $table->string('receipt_path', 2048)->nullable();
            $table->string('receipt_url', 2048)->nullable();
            $table->json('provider_metadata')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])
                ->on('bookings')
                ->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->foreign(['business_id', 'original_payment_id'])
                ->references(['business_id', 'id'])
                ->on('payments')
                ->restrictOnDelete();
            $table->unique(['business_id', 'reference']);
            $table->unique(['provider', 'provider_reference']);
            $table->index(['business_id', 'booking_id', 'status']);
            $table->index(['business_id', 'purpose', 'transaction_at']);
            $table->index(['business_id', 'status', 'transaction_at']);
            $table->index(['original_payment_id', 'status']);
            $table->index(['verified_by', 'verified_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
