<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GuestCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cancels_outside_48_hours_and_receives_simulated_refund(): void
    {
        Carbon::setTestNow('2026-11-01 10:00');
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create();
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', ['arrival_date' => '2026-11-10', 'departure_date' => '2026-11-12', 'adult_count' => 1, 'child_count' => 0, 'idempotency_key' => 'cancel-me']);
        $booking = Booking::sole();
        $this->post("/guest/bookings/{$booking->id}/cancel", ['reason' => 'Plans changed'])->assertRedirect(route('guest.bookings.show', $booking));
        $booking->refresh();
        $this->assertSame('cancelled', $booking->status->value);
        $this->assertSame('refunded', $booking->payment_status->value);
        $this->assertSame(0, $booking->availabilityDays()->where('active_key', 'active')->count());
        $this->assertSame(1, $booking->cancellations()->count());
        $this->assertSame(2, $booking->payments()->count());
    }

    public function test_guest_cannot_cancel_someone_elses_booking(): void
    {
        $booking = Booking::factory()->create(['status' => 'confirmed']);
        $this->actingAs(User::factory()->create())->post("/guest/bookings/{$booking->id}/cancel")->assertNotFound();
    }
}
