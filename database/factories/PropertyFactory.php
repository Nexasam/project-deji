<?php

namespace Database\Factories;

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
            'verification_status' => 'unverified',
            'cleaning_schedule' => null,
            'maintenance_status' => 'not_required',
            'owner_user_id' => null,
            'manager_user_id' => null,
            'status' => 'active',
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => [
            'verification_status' => 'verified',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => 'inactive',
        ]);
    }
}
