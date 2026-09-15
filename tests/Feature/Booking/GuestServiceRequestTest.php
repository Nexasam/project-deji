<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\GuestServiceRequest;
use App\Models\Property;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_checked_in_guest_creates_booking_linked_maintenance_request_and_task(): void
    {
        [$owner, $business] = $this->ownerWithBusiness();
        $guest = User::factory()->create();
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $guest->id,
            'status' => 'checked_in',
        ]);

        $response = $this->actingAs($guest)->post(route('guest.bookings.service-requests.store', $booking), [
            'request_type' => 'maintenance',
            'priority' => 'urgent',
            'description' => 'The bedroom air conditioner has stopped working.',
        ]);

        $response->assertRedirect(route('guest.bookings.show', $booking));
        $request = GuestServiceRequest::query()->sole();
        $this->assertSame($booking->id, $request->booking_id);
        $this->assertSame($guest->id, $request->guest_user_id);
        $this->assertNotNull($request->operational_task_id);
        $this->assertDatabaseHas('operational_tasks', [
            'id' => $request->operational_task_id,
            'business_id' => $business->id,
            'booking_id' => $booking->id,
            'task_type' => 'maintenance',
            'priority' => 'urgent',
        ]);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'type' => 'guest_service_request']);
        $this->assertDatabaseHas('notifications', ['user_id' => $guest->id, 'type' => 'guest_service_request_received']);
    }

    public function test_guest_cannot_create_request_for_another_or_inactive_booking(): void
    {
        [, $business] = $this->ownerWithBusiness();
        $guest = User::factory()->create();
        $otherGuest = User::factory()->create();
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $guest->id,
            'status' => 'confirmed',
        ]);

        $payload = ['request_type' => 'other', 'priority' => 'normal', 'description' => 'Please call me.'];
        $this->actingAs($otherGuest)->post(route('guest.bookings.service-requests.store', $booking), $payload)->assertNotFound();
        $this->actingAs($guest)->post(route('guest.bookings.service-requests.store', $booking), $payload)->assertSessionHasErrors('booking');
        $this->assertDatabaseCount('guest_service_requests', 0);
    }

    public function test_owner_acknowledges_and_resolves_only_own_business_request(): void
    {
        [$owner, $business] = $this->ownerWithBusiness();
        [$foreignOwner, $foreignBusiness] = $this->ownerWithBusiness('Foreign Stays');
        $guest = User::factory()->create();
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create(['guest_user_id' => $guest->id, 'status' => 'checked_in']);
        $request = GuestServiceRequest::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'booking_id' => $booking->id,
            'guest_user_id' => $guest->id,
            'request_type' => 'other',
            'priority' => 'normal',
            'description' => 'Please bring an extra blanket.',
            'requested_at' => now(),
            'status' => 'open',
        ]);

        $this->actingAs($foreignOwner)->patch(route('owner.service-requests.update', $request), ['action' => 'acknowledge'])->assertNotFound();
        $this->actingAs($owner)->patch(route('owner.service-requests.update', $request), ['action' => 'acknowledge'])->assertRedirect();
        $this->assertSame('acknowledged', $request->fresh()->status);
        $this->actingAs($owner)->patch(route('owner.service-requests.update', $request), [
            'action' => 'resolve',
            'resolution' => 'An extra blanket was delivered to the guest.',
        ])->assertRedirect();
        $this->assertSame('resolved', $request->fresh()->status);
        $this->assertNotNull($request->fresh()->resolved_at);
        $this->assertDatabaseHas('notifications', ['user_id' => $guest->id, 'type' => 'guest_service_request_resolved']);
        $this->assertDatabaseMissing('guest_service_requests', ['business_id' => $foreignBusiness->id, 'id' => $request->id]);
    }

    private function ownerWithBusiness(string $name = 'Nexa Stays'): array
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, [
            'name' => $name,
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        return [$owner, $business];
    }
}
