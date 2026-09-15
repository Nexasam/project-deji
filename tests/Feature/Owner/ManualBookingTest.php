<?php

namespace Tests\Feature\Owner;

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_creates_idempotent_manual_booking_with_payment_calendar_and_preparation(): void
    {
        [$owner, $business] = $this->owner();
        $property = Property::factory()->for($business)->create(['capacity' => 4, 'pricing_currency' => 'NGN']);
        $payload = $this->payload($property, 'manual-one');

        $this->actingAs($owner)->post(route('owner.bookings.manual.store'), $payload)->assertRedirect();
        $this->post(route('owner.bookings.manual.store'), $payload)->assertRedirect();

        $booking = Booking::query()->sole();
        $this->assertSame('whatsapp', $booking->source->value);
        $this->assertSame('partially_paid', $booking->payment_status->value);
        $this->assertSame(2, $booking->availabilityDays()->count());
        $this->assertSame(1, $booking->payments()->count());
        $this->assertSame(2, $booking->operationalTasks()->count());
        $this->assertSame(1, $booking->bookingGuests()->count());
        $this->assertDatabaseHas('domain_events', ['booking_id' => $booking->id, 'event_name' => 'booking.confirmed']);
    }

    public function test_manual_booking_rejects_conflict_capacity_and_foreign_property(): void
    {
        [$owner, $business] = $this->owner();
        [, $foreignBusiness] = $this->owner('Foreign Stays');
        $property = Property::factory()->for($business)->create(['capacity' => 2]);
        $foreign = Property::factory()->for($foreignBusiness)->create();
        $this->actingAs($owner)->post(route('owner.bookings.manual.store'), $this->payload($property, 'first'))->assertRedirect();

        $this->post(route('owner.bookings.manual.store'), $this->payload($property, 'conflict'))->assertSessionHasErrors('arrival_date');
        $this->post(route('owner.bookings.manual.store'), [...$this->payload($property, 'capacity'), 'adult_count' => 3])->assertSessionHasErrors('adult_count');
        $this->post(route('owner.bookings.manual.store'), $this->payload($foreign, 'foreign'))->assertNotFound();
        $this->assertDatabaseCount('bookings', 1);
    }

    private function payload(Property $property, string $key): array
    {
        return ['property_id' => $property->id, 'source' => 'whatsapp', 'guest_name' => 'Ada Guest', 'guest_email' => 'ada@example.test', 'guest_phone' => '+2348012345678', 'arrival_date' => '2027-03-10', 'departure_date' => '2027-03-12', 'adult_count' => 2, 'child_count' => 0, 'nightly_rate' => 50000, 'amount_paid' => 25000, 'payment_method' => 'bank_transfer', 'notes' => 'Late arrival', 'idempotency_key' => $key];
    }

    private function owner(string $name = 'Nexa Stays'): array
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, ['name' => $name, 'country_code' => 'NG', 'business_type' => 'serviced_apartments', 'timezone' => 'Africa/Lagos', 'currency' => 'NGN']);

        return [$owner, $business];
    }
}
