<?php

namespace Tests\Feature\Owner;

use App\Models\Booking;
use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\Employee;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\PropertyStaffAssignment;
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
            ->assertSee('A waterfront serviced apartment for business and family stays.')
            ->assertSee('Publishing')->assertSee('Verification')->assertSee('Setup')
            ->assertSee('Marketplace listing')->assertSee('Items inside the property')
            ->assertSee('Documents')->assertSee('Booking channels');
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

    public function test_property_portfolio_cards_display_the_uploaded_primary_image(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create(['name' => 'Waterfront Suite']);
        PropertyMedia::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'media_type' => 'image',
            'storage_disk' => 'public',
            'storage_path' => 'properties/waterfront/cover.jpg',
            'title' => 'Waterfront cover',
            'alt_text' => 'Waterfront Suite living room',
            'sort_order' => 1,
            'is_primary' => true,
            'status' => 'active',
        ]);

        $this->actingAs($owner)->get(route('owner.properties.index'))
            ->assertOk()
            ->assertSee('/storage/properties/waterfront/cover.jpg', false)
            ->assertSee('Waterfront Suite living room');

        $this->actingAs($owner)->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('/storage/properties/waterfront/cover.jpg', false)
            ->assertSee('Waterfront Suite living room');
    }

    public function test_property_portfolio_search_status_filter_and_list_layout_are_database_backed(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        Property::factory()->for($business)->create(['name' => 'Lekki Waterfront', 'publication_status' => 'published']);
        Property::factory()->for($business)->create(['name' => 'Ikoyi Garden', 'publication_status' => 'draft']);

        $this->actingAs($owner)->get(route('owner.properties.index', [
            'q' => 'Waterfront',
            'status' => 'published',
            'layout' => 'list',
        ]))->assertOk()
            ->assertSee('Lekki Waterfront')
            ->assertDontSee('Ikoyi Garden')
            ->assertSee('data-testid="property-list"', false)
            ->assertSee('value="Waterfront"', false);
    }

    public function test_property_portfolio_location_cards_use_real_counts_managers_and_current_month_occupancy(): void
    {
        $this->travelTo('2026-09-15 10:00:00');
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $lekki = Property::factory()->for($business)->create(['name' => 'Lekki Suite', 'address' => ['city' => 'Lekki', 'state' => 'Lagos']]);
        Property::factory()->for($business)->create(['name' => 'Ikoyi Suite', 'address' => ['city' => 'Ikoyi', 'state' => 'Lagos']]);
        $manager = User::factory()->create(['name' => 'Amina Property Manager']);
        $membership = BusinessMembership::query()->create([
            'business_id' => $business->id,
            'user_id' => $manager->id,
            'job_title' => 'Property Manager',
            'status' => 'active',
            'joined_at' => now(),
        ]);
        $employee = Employee::query()->create([
            'business_id' => $business->id,
            'business_membership_id' => $membership->id,
            'employee_code' => 'EMP-LOCATION-MANAGER',
            'employment_status' => 'active',
            'started_on' => today(),
            'status' => 'active',
        ]);
        PropertyStaffAssignment::query()->create([
            'business_id' => $business->id,
            'property_id' => $lekki->id,
            'employee_id' => $employee->id,
            'assignment_role' => 'property_manager',
            'is_primary' => true,
            'assignment_status' => 'active',
            'status' => 'active',
        ]);
        Booking::factory()->for($business)->for($lekki)->create([
            'status' => 'confirmed',
            'arrival_date' => '2026-09-01',
            'departure_date' => '2026-09-16',
        ]);

        $this->actingAs($owner)->get(route('owner.properties.index'))
            ->assertOk()
            ->assertSee('All Locations')
            ->assertSee('Across Nexa Stays')
            ->assertSee('Lekki')
            ->assertSee('Ikoyi')
            ->assertSee('Amina Property Manager')
            ->assertSee('50%')
            ->assertSee('25%');
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
