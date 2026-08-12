<?php

namespace Database\Factories;

use App\Enums\BookingPaymentStatus;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $arrivalDate = fake()->dateTimeBetween('+1 day', '+3 months');
        $departureDate = (clone $arrivalDate)->modify('+'.fake()->numberBetween(1, 14).' days');
        $subtotal = fake()->randomFloat(4, 100, 10000);

        return [
            'business_id' => Business::factory(),
            'property_id' => fn (array $attributes) => Property::factory()->create([
                'business_id' => $attributes['business_id'],
            ])->id,
            'guest_user_id' => User::factory(),
            'reference' => strtoupper(fake()->unique()->bothify('BK-########')),
            'arrival_date' => $arrivalDate,
            'departure_date' => $departureDate,
            'number_of_guests' => fake()->numberBetween(1, 6),
            'source' => BookingSource::Marketplace,
            'status' => BookingStatus::Enquiry,
            'payment_status' => BookingPaymentStatus::Unpaid,
            'discount_type' => null,
            'discount_value' => 0,
            'discount_amount' => 0,
            'coupon_code' => null,
            'currency' => 'USD',
            'subtotal_amount' => $subtotal,
            'total_amount' => $subtotal,
            'created_by' => null,
        ];
    }
}
