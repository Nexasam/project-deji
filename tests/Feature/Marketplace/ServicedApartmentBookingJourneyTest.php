<?php

namespace Tests\Feature\Marketplace;

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicedApartmentBookingJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_to_correct_owner_marketplace_booking_journey(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);

        $this->get('/?q=Admiralty&category=lekki&guests=4&beds=3&check_in=2026-12-24&check_out=2026-12-26')
            ->assertOk()->assertSee('Admiralty Waterfront Residence')->assertSee('1 serviced apartment');
        $this->get('/stays/lekki-admiralty-waterfront')->assertOk()->assertSee('₦95,000');

        $this->get('/register?redirect=/stays/lekki-admiralty-waterfront');
        $this->post('/register', [
            'name' => 'Tuesday Acceptance Guest', 'email' => 'tuesday.guest@example.com',
            'password' => 'SecurePass123', 'password_confirmation' => 'SecurePass123', 'terms' => '1',
        ])->assertRedirect('/stays/lekki-admiralty-waterfront');

        $this->post('/stays/lekki-admiralty-waterfront/checkout', [
            'arrival_date' => '2026-12-24', 'departure_date' => '2026-12-26',
            'adult_count' => 2, 'child_count' => 2, 'idempotency_key' => 'tuesday-acceptance',
        ])->assertRedirect();

        $booking = Booking::query()->sole();
        $this->get(route('guest.bookings.show', $booking))->assertOk()
            ->assertSee($booking->reference)->assertSee('Admiralty Waterfront Residence')->assertSee('Confirmed');

        $lagoonOwner = User::query()->where('email', 'owner@lagoonstays.test')->firstOrFail();
        $coastlineOwner = User::query()->where('email', 'owner@coastlineresidences.test')->firstOrFail();
        $this->actingAs($lagoonOwner)->get(route('owner.bookings'))->assertOk()
            ->assertSee($booking->reference)->assertSee('Tuesday Acceptance Guest');
        $this->get(route('owner.bookings.show', $booking))->assertOk()->assertSee($booking->reference);

        $this->actingAs($coastlineOwner)->get(route('owner.bookings'))->assertOk()->assertDontSee($booking->reference);
        $this->get(route('owner.bookings.show', $booking))->assertNotFound();
    }
}
