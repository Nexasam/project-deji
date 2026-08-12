<?php

use App\Enums\BookingPaymentStatus;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->uuid('property_id');
            $table->foreignUuid('guest_user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('reference', 100);
            $table->date('arrival_date');
            $table->date('departure_date');
            $table->unsignedSmallInteger('number_of_guests')->default(1);
            $table->unsignedSmallInteger('adult_count')->default(1);
            $table->unsignedSmallInteger('child_count')->default(0);
            $table->unsignedSmallInteger('pet_count')->default(0);
            $table->string('source', 80)->default(BookingSource::Marketplace->value);
            $table->string('status', 40)->default(BookingStatus::Enquiry->value);
            $table->string('payment_status', 40)
                ->default(BookingPaymentStatus::Unpaid->value);
            $table->text('special_requests')->nullable();
            $table->string('discount_type', 20)->nullable();
            $table->decimal('discount_value', 19, 4)->default(0);
            $table->decimal('discount_amount', 19, 4)->default(0);
            $table->string('coupon_code', 100)->nullable();
            $table->char('currency', 3);
            $table->decimal('subtotal_amount', 19, 4);
            $table->decimal('total_amount', 19, 4);
            $table->string('external_reference', 191)->nullable();
            $table->json('source_metadata')->nullable();
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])
                ->on('properties')
                ->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->unique(['business_id', 'reference']);
            $table->index(
                ['business_id', 'property_id', 'status', 'arrival_date', 'departure_date'],
                'bookings_property_availability_lookup'
            );
            $table->index(['business_id', 'guest_user_id', 'created_at']);
            $table->index(['business_id', 'status', 'arrival_date']);
            $table->index(['business_id', 'payment_status']);
            $table->index(['business_id', 'source', 'created_at']);
            $table->index(['source', 'external_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
