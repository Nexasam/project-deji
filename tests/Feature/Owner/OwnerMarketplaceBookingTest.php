<?php

namespace Tests\Feature\Owner;

use App\Models\User;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerMarketplaceBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_appears_only_for_its_property_business_owner(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create();
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', ['arrival_date' => '2026-11-10', 'departure_date' => '2026-11-12', 'adult_count' => 1, 'child_count' => 0, 'idempotency_key' => 'owner-view']);
        $lagoon = User::where('email', 'owner@lagoonstays.test')->firstOrFail();
        $coastline = User::where('email', 'owner@coastlineresidences.test')->firstOrFail();
        $this->actingAs($lagoon)->get('/owner/bookings')->assertOk()->assertSee('Admiralty Waterfront Residence')->assertSee($guest->name);
        $this->actingAs($coastline)->get('/owner/bookings')->assertOk()->assertDontSee('Admiralty Waterfront Residence')->assertDontSee($guest->name);
    }
}
