<?php

namespace Database\Factories;

use App\Enums\BusinessOnboardingStatus;
use App\Enums\BusinessStatus;
use App\Enums\BusinessVerificationStatus;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countryCode = fake()->countryCode();

        return [
            'name' => fake()->company(),
            'description' => fake()->sentence(),
            'registration_number' => strtoupper(fake()->bothify('REG-####-????')),
            'country_code' => $countryCode,
            'address' => [
                'line_1' => fake()->streetAddress(),
                'line_2' => null,
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country_code' => $countryCode,
            ],
            'primary_contact_name' => fake()->name(),
            'email' => fake()->unique()->companyEmail(),
            'phone_number' => fake()->e164PhoneNumber(),
            'tax_information' => [
                'tax_identifier' => fake()->bothify('TAX-########'),
                'tax_registered' => fake()->boolean(),
            ],
            'business_type' => fake()->randomElement([
                'individual',
                'partnership',
                'limited_company',
                'hospitality_group',
            ]),
            'timezone' => fake()->timezone(),
            'currency' => fake()->currencyCode(),
            'subscription_plan' => null,
            'onboarding_status' => BusinessOnboardingStatus::Registered,
            'verification_status' => BusinessVerificationStatus::Unverified,
            'status' => BusinessStatus::Active,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => [
            'verification_status' => BusinessVerificationStatus::Verified,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => BusinessStatus::Inactive,
        ]);
    }
}
