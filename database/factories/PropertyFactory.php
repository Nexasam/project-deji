<?php

namespace Database\Factories;

use App\Enums\PropertyMaintenanceStatus;
use App\Enums\PropertyPublicationStatus;
use App\Enums\PropertyReadinessStatus;
use App\Enums\PropertyStatus;
use App\Enums\PropertyVerificationStatus;
use App\Models\Business;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->streetName().' '.fake()->randomElement(['Apartment', 'Villa', 'Suites']),
            'code' => strtoupper(fake()->unique()->bothify('PROP-####')),
            'address' => [
                'line_1' => fake()->streetAddress(),
                'line_2' => null,
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country_code' => fake()->countryCode(),
            ],
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'property_type' => fake()->randomElement([
                'apartment',
                'villa',
                'guest_house',
                'short_let',
                'holiday_home',
                'boutique_hotel',
            ]),
            'capacity' => fake()->numberBetween(1, 20),
            'bedrooms' => fake()->numberBetween(0, 10),
            'bathrooms' => fake()->randomElement([1, 1.5, 2, 2.5, 3, 4]),
            'description' => fake()->paragraph(),
            'default_nightly_price' => fake()->randomFloat(4, 50, 1000),
            'pricing_currency' => 'USD',
            'verification_status' => PropertyVerificationStatus::Unverified,
            'maintenance_status' => PropertyMaintenanceStatus::NotRequired,
            'publication_status' => PropertyPublicationStatus::Draft,
            'readiness_status' => PropertyReadinessStatus::NotReady,
            'owner_name' => fake()->name(),
            'manager_name' => null,
            'status' => PropertyStatus::Active,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => [
            'verification_status' => PropertyVerificationStatus::Verified,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => PropertyStatus::Inactive,
        ]);
    }
}
