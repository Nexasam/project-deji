<?php

namespace Tests\Feature\Owner;

use App\Enums\IdentityVerificationStatus;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerMarketplaceBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_appears_only_for_its_property_business_owner(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create([
            'name' => 'Marketplace Isolation Guest',
            'identity_verification_status' => IdentityVerificationStatus::Verified,
        ]);
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', ['arrival_date' => '2026-11-10', 'departure_date' => '2026-11-12', 'adult_count' => 1, 'child_count' => 0, 'guest_phone' => '+2348012345678', 'quoted_total' => 190000, 'idempotency_key' => 'owner-view']);
        $coastline = User::where('email', 'owner@coastlineresidences.test')->firstOrFail();
        $unrelatedOwner = User::factory()->create();
        app(BusinessOnboardingService::class)->onboard($unrelatedOwner, [
            'name' => 'Unrelated Live Host',
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        $this->actingAs($coastline)->get('/owner/bookings')->assertOk()->assertSee('Admiralty Waterfront Residence')->assertSee($guest->name);
        $this->actingAs($unrelatedOwner)->get('/owner/bookings')->assertOk()->assertDontSee('Admiralty Waterfront Residence')->assertDontSee($guest->name);
    }
}
