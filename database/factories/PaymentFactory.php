<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentPurpose;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'business_id' => fn (array $attributes) => Booking::query()
                ->findOrFail($attributes['booking_id'])
                ->business_id,
            'original_payment_id' => null,
            'reference' => strtoupper(fake()->unique()->bothify('PAY-########')),
            'purpose' => PaymentPurpose::Deposit,
            'amount' => fake()->randomFloat(4, 10, 5000),
            'currency' => 'USD',
            'method' => PaymentMethod::Card,
            'provider' => null,
            'provider_reference' => null,
            'status' => PaymentStatus::Pending,
            'transaction_at' => now(),
            'verified_by' => null,
            'verified_at' => null,
            'created_by' => null,
        ];
    }
}
