<?php

namespace Tests\Feature\Marketplace;

use App\Models\Property;
use App\Models\PropertyAvailabilityDay;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
    }

    public function test_home_displays_only_eligible_database_listings(): void
    {
        $hidden = Property::query()->where('code', 'LAG-001')->firstOrFail();
        $hidden->update(['publication_status' => 'draft']);

        $this->get('/')->assertOk()
            ->assertDontSee($hidden->name)
            ->assertSee('Ikoyi Executive Maisonette')
            ->assertSee('9 serviced apartments');
    }

    public function test_home_shows_up_to_one_hundred_results_without_ai_insights_controls(): void
    {
        $response = $this->get('/')->assertOk()
            ->assertSee('Admiralty Waterfront Residence')
            ->assertSee('Oniru Beachside Studio')
            ->assertDontSee('class="insights-btn"', false);

        $this->assertSame(10, $response->viewData('properties')->count());
        $this->assertSame(100, $response->viewData('properties')->perPage());
    }

    public function test_search_and_filters_are_combined_on_the_backend(): void
    {
        $this->get('/?q=Coastal&category=beachfront&min_price=80000&max_price=100000&beds=2')
            ->assertOk()
            ->assertSee('Elegushi Coastal Apartment')
            ->assertDontSee('Oniru Beachside Studio')
            ->assertSee('1 serviced apartment');
    }

    public function test_category_and_location_filters_return_database_matches(): void
    {
        $this->get('/?category=ikoyi')->assertOk()
            ->assertSee('Ikoyi Executive Maisonette')
            ->assertSee('Banana Island Harbour Flat')
            ->assertDontSee('Chevron Family Residence');

        $this->get('/?category=business')->assertOk()
            ->assertSee('Eko Atlantic Business Stay')
            ->assertDontSee('Elegushi Coastal Apartment');
    }

    public function test_public_detail_requires_an_eligible_listing(): void
    {
        $property = Property::query()->where('code', 'LAG-001')->firstOrFail();
        $this->get('/stays/lekki-admiralty-waterfront')->assertOk()->assertSee($property->name);

        $property->update(['verification_status' => 'unverified']);
        $this->get('/stays/lekki-admiralty-waterfront')->assertNotFound();
    }

    public function test_logged_out_guest_is_prompted_to_create_an_account_or_log_in_to_book(): void
    {
        $this->get('/stays/banana-island-harbour-flat')->assertOk()
            ->assertSee('Create account to book')
            ->assertSee('Already have an account? Log in')
            ->assertSee('/register?redirect=%2Fstays%2Fbanana-island-harbour-flat', false)
            ->assertSee('/login?redirect=%2Fstays%2Fbanana-island-harbour-flat', false);
    }

    public function test_invalid_filter_ranges_return_validation_errors(): void
    {
        $this->get('/?min_price=100000&max_price=50000')->assertSessionHasErrors('max_price');
    }

    public function test_primary_search_form_submits_real_marketplace_filters(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('action="http://localhost"', false)
            ->assertSee('name="q"', false)
            ->assertSee('name="check_in"', false)
            ->assertSee('name="check_out"', false)
            ->assertSee('name="guests"', false)
            ->assertDontSee('Search coming soon!');
    }

    public function test_date_filter_excludes_a_property_with_an_allocated_night(): void
    {
        $property = Property::query()->where('code', 'LAG-001')->firstOrFail();
        PropertyAvailabilityDay::query()->create([
            'business_id' => $property->business_id,
            'property_id' => $property->id,
            'availability_date' => '2026-12-20',
            'availability_state' => 'blocked',
            'source_type' => 'owner',
            'active_key' => 'active',
            'allocated_at' => now(),
            'status' => 'active',
        ]);

        $this->get('/?check_in=2026-12-20&check_out=2026-12-21&guests=2')
            ->assertOk()
            ->assertDontSee('Admiralty Waterfront Residence')
            ->assertSee('9 serviced apartments');
    }
}
