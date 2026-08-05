<?php

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
            $table->foreignUuid('guest_id')
                ->constrained('guests')
                ->restrictOnDelete();
            $table->string('reference', 100);
            $table->date('arrival_date');
            $table->date('departure_date');
            $table->unsignedSmallInteger('number_of_guests')->default(1);
            $table->string('source', 80)->default('manual');
            $table->string('status', 40)->default('enquiry');
            $table->string('payment_status', 40)->default('unpaid');
            $table->text('special_requests')->nullable();
            $table->decimal('discount_amount', 19, 4)->default(0);
            $table->string('discount_currency', 3)->nullable();
            $table->string('coupon_code', 100)->nullable();
            $table->string('external_reference', 191)->nullable();
            $table->json('source_metadata')->nullable();
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])
                ->on('properties')
                ->restrictOnDelete();
            $table->unique(['business_id', 'id']);
            $table->unique(['business_id', 'property_id', 'id']);
            $table->unique(['business_id', 'reference']);
            $table->index([
                'business_id',
                'property_id',
                'status',
                'arrival_date',
                'departure_date',
            ], 'bookings_property_overlap_lookup');
            $table->index(['business_id', 'guest_id', 'created_at']);
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
