<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_payment_confirms_booking_and_reserves_each_night(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create();

        $response = $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', [
            'arrival_date' => '2026-11-10', 'departure_date' => '2026-11-13',
            'adult_count' => 2, 'child_count' => 1, 'idempotency_key' => 'checkout-one',
            'total_amount' => 1,
        ]);

        $booking = Booking::query()->sole();
        $response->assertRedirect(route('guest.bookings.show', $booking));
        $this->assertSame('confirmed', $booking->status->value);
        $this->assertSame('paid', $booking->payment_status->value);
        $this->assertSame('299250.0000', $booking->total_amount);
        $this->assertSame(3, $booking->availabilityDays()->count());
        $this->assertSame('completed', Payment::query()->sole()->status->value);
    }

    public function test_checkout_rejects_dates_already_booked(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create();
        $payload = ['arrival_date'=>'2026-11-10','departure_date'=>'2026-11-12','adult_count'=>1,'child_count'=>0,'idempotency_key'=>'first'];
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', $payload)->assertRedirect();
        $this->post('/stays/lekki-admiralty-waterfront/checkout', [...$payload, 'idempotency_key'=>'second'])->assertSessionHasErrors('arrival_date');
        $this->assertDatabaseCount('bookings', 1);
    }
}
