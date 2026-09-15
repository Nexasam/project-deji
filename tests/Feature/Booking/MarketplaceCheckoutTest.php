<?php

namespace Tests\Feature\Booking;

use App\Contracts\Payments\PaymentGateway;
use App\Data\PaymentResult;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use App\Services\Booking\PropertyAvailabilityService;
use Carbon\CarbonImmutable;
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
            'adult_count' => 2, 'child_count' => 1, 'guest_phone' => '+2348012345678', 'quoted_total' => 299250, 'idempotency_key' => 'checkout-one',
            'total_amount' => 1,
        ]);

        $booking = Booking::query()->sole();
        $response->assertRedirect(route('guest.bookings.show', $booking));
        $this->assertSame('confirmed', $booking->status->value);
        $this->assertSame('paid', $booking->payment_status->value);
        $this->assertSame('299250.0000', $booking->total_amount);
        $this->assertSame(3, $booking->availabilityDays()->count());
        $this->assertSame('completed', Payment::query()->sole()->status->value);
        $this->assertSame(['awaiting_payment', 'confirmed'], BookingStatusHistory::query()->orderBy('occurred_at')->pluck('new_status')->all());
        $this->assertDatabaseHas('notifications', ['user_id' => $guest->id, 'type' => 'booking_confirmed']);
        $this->assertDatabaseHas('notifications', ['user_id' => $booking->business->memberships()->first()->user_id, 'type' => 'new_booking']);
        $this->assertDatabaseHas('operational_tasks', ['booking_id' => $booking->id, 'generation_source' => 'system', 'title' => 'Pre-arrival cleaning and readiness']);
        $this->assertDatabaseHas('booking_guests', ['booking_id' => $booking->id, 'user_id' => $guest->id, 'phone_number' => '+2348012345678', 'is_primary' => true]);
        $this->assertDatabaseHas('domain_events', [
            'booking_id' => $booking->id,
            'event_name' => 'booking.confirmed',
            'idempotency_key' => 'booking:'.$booking->id.':confirmed',
        ]);
    }

    public function test_checkout_rejects_dates_already_booked(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create();
        $payload = ['arrival_date' => '2026-11-10', 'departure_date' => '2026-11-12', 'adult_count' => 1, 'child_count' => 0, 'guest_phone' => '+2348012345678', 'quoted_total' => 199500, 'idempotency_key' => 'first'];
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', $payload)->assertRedirect();
        $this->post('/stays/lekki-admiralty-waterfront/checkout', [...$payload, 'idempotency_key' => 'second'])->assertSessionHasErrors('arrival_date');
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_failed_payment_is_recorded_without_reserving_calendar_nights(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest = User::factory()->create();
        $this->app->instance(PaymentGateway::class, new class implements PaymentGateway
        {
            public function charge(string $reference, int $amountMinor, string $currency): PaymentResult
            {
                return new PaymentResult(false, 'SIM-FAILED', ['simulated' => true]);
            }
        });

        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout', [
            'arrival_date' => '2026-12-10', 'departure_date' => '2026-12-12',
            'adult_count' => 1, 'child_count' => 0, 'guest_phone' => '+2348012345678', 'quoted_total' => 199500, 'idempotency_key' => 'failed-payment',
        ])->assertSessionHasErrors('payment');

        $booking = Booking::query()->sole();
        $this->assertSame('failed', $booking->payment_status->value);
        $this->assertSame('failed', Payment::query()->sole()->status->value);
        $this->assertSame(0, $booking->availabilityDays()->count());
        $this->assertDatabaseHas('booking_status_history', ['booking_id' => $booking->id, 'new_status' => 'awaiting_payment']);
        $property = Property::query()->where('code', 'LAG-001')->firstOrFail();
        $this->assertTrue(app(PropertyAvailabilityService::class)->isAvailable(
            $property,
            CarbonImmutable::parse('2026-12-10'),
            CarbonImmutable::parse('2026-12-12'),
        ));
    }
}
