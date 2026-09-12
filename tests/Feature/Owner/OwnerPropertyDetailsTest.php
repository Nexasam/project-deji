<?php

namespace Tests\Feature\Owner;

use App\Models\Business;
use App\Models\Property;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerPropertyDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_the_details_of_a_property_owned_by_their_business(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create([
            'name' => 'Admiralty Waterfront Residence',
            'code' => 'ADM-WATER-01',
            'address' => [
                'line_1' => '12 Admiralty Way',
                'city' => 'Lekki',
                'state' => 'Lagos',
                'country_code' => 'NG',
            ],
            'property_type' => 'serviced_apartment',
            'capacity' => 4,
            'bedrooms' => 2,
            'beds' => 2,
            'bathrooms' => 2,
            'default_nightly_price' => 95000,
            'pricing_currency' => 'NGN',
            'description' => 'A waterfront serviced apartment for business and family stays.',
        ]);

        $this->actingAs($owner)->get(route('owner.properties.show', $property))
            ->assertOk()
            ->assertSee('Admiralty Waterfront Residence')
            ->assertSee('ADM-WATER-01')
            ->assertSee('12 Admiralty Way')
            ->assertSee('Lekki, Lagos')
            ->assertSee('₦95,000')
            ->assertSee('4 guests')
            ->assertSee('2 bedrooms')
            ->assertSee('2 beds')
            ->assertSee('A waterfront serviced apartment for business and family stays.');
    }

    public function test_owner_cannot_view_a_property_owned_by_another_business(): void
    {
        [$owner] = $this->ownerWithBusiness('Nexa Stays');
        [, $otherBusiness] = $this->ownerWithBusiness('Other Stays');
        $property = Property::factory()->for($otherBusiness)->create();

        $this->actingAs($owner)->get(route('owner.properties.show', $property))
            ->assertNotFound();
    }

    public function test_property_cards_link_to_the_owner_property_details_page(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create();
        $detailsUrl = route('owner.properties.show', $property);

        $this->actingAs($owner)->get(route('owner.properties.index'))
            ->assertOk()
            ->assertSee($detailsUrl, false);

        $this->actingAs($owner)->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee($detailsUrl, false);
    }

    /** @return array{User, Business} */
    private function ownerWithBusiness(string $name): array
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($user, [
            'name' => $name,
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        return [$user, $business];
    }
}
