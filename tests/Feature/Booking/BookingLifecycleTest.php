<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\OperationalTask;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyAvailabilityDay;
use App\Models\PropertyCleaningSchedule;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use App\Services\Operations\BookingOperationsService;
use App\Services\Operations\ManageOperationalTask;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_moves_stay_through_check_in_checkout_cleaning_inspection_and_completion(): void
    {
        Carbon::setTestNow('2026-09-13 14:00:00');
        [$owner, $business] = $this->ownerWithBusiness();
        $guest = User::factory()->create();
        $property = Property::factory()->for($business)->create(['operational_status' => 'reserved']);
        $booking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $guest->id,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'arrival_date' => '2026-09-13',
            'departure_date' => '2026-09-15',
        ]);

        $this->actingAs($owner)->post(route('owner.bookings.check-in', $booking), [
            'identity_status' => 'verified',
            'balance_status' => 'paid',
            'access_method' => 'keys',
        ])->assertRedirect();
        $this->assertSame('checked_in', $booking->fresh()->status->value);
        $this->assertSame('occupied', $property->fresh()->operational_status->value);

        $this->post(route('owner.bookings.check-out', $booking), [
            'access_return_status' => 'returned',
            'room_condition' => 'requires_cleaning',
            'damage_status' => 'none',
            'handover_notes' => 'Keys returned.',
        ])->assertRedirect();
        $this->assertSame('checked_out', $booking->fresh()->status->value);
        $turnover = OperationalTask::query()->where('booking_id', $booking->id)
            ->where('title', 'Turnover cleaning after checkout')->sole();
        $this->assertSame('cleaning', $property->fresh()->operational_status->value);

        $manager = app(ManageOperationalTask::class);
        $manager->transition($turnover, $owner, 'start');
        $manager->transition($turnover->fresh(), $owner, 'complete', 'Cleaning complete.', $turnover->checklistItems()->pluck('id')->all());
        $inspection = OperationalTask::query()->where('booking_id', $booking->id)
            ->where('title', 'Post-cleaning property inspection')->sole();
        $this->post(route('owner.operations.tasks.inspection', $inspection), [
            'result' => 'passed',
        ])->assertRedirect();

        $this->assertSame('completed', $booking->fresh()->status->value);
        $this->assertSame('available', $property->fresh()->operational_status->value);
        $this->assertDatabaseHas('notifications', ['user_id' => $guest->id, 'type' => 'booking_checked_in']);
        $this->assertDatabaseHas('notifications', ['user_id' => $guest->id, 'type' => 'booking_completed']);
    }

    public function test_repeated_transition_is_rejected_without_duplicate_effects(): void
    {
        [$owner, $business] = $this->ownerWithBusiness();
        $booking = Booking::factory()->for($business)->for(Property::factory()->for($business))->create(['status' => 'confirmed']);
        $payload = ['identity_status' => 'verified', 'balance_status' => 'paid'];

        $this->actingAs($owner)->post(route('owner.bookings.check-in', $booking), $payload)->assertRedirect();
        $this->post(route('owner.bookings.check-in', $booking), $payload)->assertSessionHasErrors('status');

        $this->assertSame(1, $booking->checkIn()->count());
        $this->assertSame(1, $booking->statusHistory()->where('new_status', 'checked_in')->count());
    }

    public function test_future_arrival_cannot_be_marked_as_no_show(): void
    {
        Carbon::setTestNow('2026-09-13 10:00:00');
        [$owner, $business] = $this->ownerWithBusiness();
        $booking = Booking::factory()->for($business)->for(Property::factory()->for($business))->create([
            'status' => 'confirmed',
            'arrival_date' => '2026-09-18',
            'departure_date' => '2026-09-20',
        ]);

        $this->actingAs($owner)->post(route('owner.bookings.no-show', $booking), ['reason' => 'Too early'])->assertSessionHasErrors('status');
        $this->assertSame('confirmed', $booking->fresh()->status->value);
    }

    public function test_failed_inspection_creates_corrective_maintenance_and_alerts_owner(): void
    {
        [$owner, $business] = $this->ownerWithBusiness();
        $property = Property::factory()->for($business)->create(['operational_status' => 'inspection']);
        $booking = Booking::factory()->for($business)->for($property)->create(['status' => 'checked_out']);
        $inspection = OperationalTask::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id, 'booking_id' => $booking->id,
            'reference' => 'TASK-INSPECT-FAIL', 'title' => 'Post-cleaning property inspection',
            'task_type' => 'inspection', 'priority' => 'high', 'status' => 'in_progress',
            'generation_source' => 'system', 'generation_metadata' => ['workflow_stage' => 'post_cleaning_inspection'],
        ]);

        $this->actingAs($owner)->post(route('owner.operations.tasks.inspection', $inspection), [
            'result' => 'failed',
            'findings' => 'Air conditioner is leaking.',
            'corrective_action' => 'maintenance',
        ])->assertRedirect();

        $this->assertDatabaseHas('maintenance_issues', ['booking_id' => $booking->id, 'description' => 'Air conditioner is leaking.']);
        $this->assertDatabaseHas('operational_tasks', ['booking_id' => $booking->id, 'task_type' => 'maintenance', 'priority' => 'urgent']);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'type' => 'inspection_failed']);
    }

    public function test_owner_cancellation_releases_dates_cancels_obsolete_work_and_notifies_guest(): void
    {
        Carbon::setTestNow('2026-09-13 10:00:00');
        [$owner, $business] = $this->ownerWithBusiness();
        $guest = User::factory()->create();
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create(['guest_user_id' => $guest->id, 'status' => 'confirmed', 'payment_status' => 'paid', 'arrival_date' => '2026-09-20', 'departure_date' => '2026-09-22', 'total_amount' => 100000]);
        $payment = Payment::factory()->for($business)->for($booking)->create(['purpose' => 'balance', 'status' => 'completed', 'amount' => 100000, 'currency' => $booking->currency]);
        PropertyAvailabilityDay::query()->create(['business_id' => $business->id, 'property_id' => $property->id, 'booking_id' => $booking->id, 'availability_date' => '2026-09-20', 'availability_state' => 'booked', 'source_type' => 'marketplace', 'active_key' => 'active', 'allocated_at' => now(), 'status' => 'active']);
        $task = OperationalTask::query()->create(['business_id' => $business->id, 'property_id' => $property->id, 'booking_id' => $booking->id, 'reference' => 'TASK-CANCEL', 'title' => 'Prepare stay', 'task_type' => 'cleaning', 'priority' => 'high', 'status' => 'pending', 'generation_source' => 'system']);

        $this->actingAs($owner)->post(route('owner.bookings.cancel', $booking), ['reason' => 'Property unavailable'])->assertRedirect();

        $this->assertSame('cancelled', $booking->fresh()->status->value);
        $this->assertSame('cancelled', $task->fresh()->status->value);
        $this->assertDatabaseHas('property_availability_days', ['booking_id' => $booking->id, 'active_key' => null, 'status' => 'released']);
        $this->assertDatabaseHas('payments', ['original_payment_id' => $payment->id, 'purpose' => 'refund', 'status' => 'completed']);
        $this->assertDatabaseHas('notifications', ['user_id' => $guest->id, 'type' => 'booking_cancelled']);
        $this->assertDatabaseHas('domain_events', ['booking_id' => $booking->id, 'event_name' => 'booking.cancelled']);
    }

    public function test_confirmation_uses_property_cleaning_schedule_for_pre_arrival_work(): void
    {
        Carbon::setTestNow('2026-09-13 10:00:00');
        [$owner, $business] = $this->ownerWithBusiness();
        $property = Property::factory()->for($business)->create();
        PropertyCleaningSchedule::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id,
            'name' => 'Default cleaning schedule', 'frequency' => 'between_stays',
            'preferred_start_time' => '09:30', 'instructions' => 'Use fragrance-free products.',
            'status' => 'active',
        ]);
        $booking = Booking::factory()->for($business)->for($property)->create([
            'status' => 'confirmed', 'arrival_date' => '2026-09-20', 'departure_date' => '2026-09-22',
        ]);

        app(BookingOperationsService::class)->onBookingConfirmed($booking, $owner);

        $task = $booking->operationalTasks()->where('reference', 'TASK-'.$booking->reference.'-PREP')->sole();
        $this->assertSame('2026-09-20 09:30', $task->due_at->format('Y-m-d H:i'));
        $this->assertSame('Use fragrance-free products.', $task->notes);
    }

    public function test_upcoming_arrival_command_notifies_guest_and_owner_once(): void
    {
        Carbon::setTestNow('2026-09-13 09:00:00');
        [$owner, $business] = $this->ownerWithBusiness();
        $guest = User::factory()->create();
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $guest->id, 'status' => 'confirmed',
            'arrival_date' => '2026-09-14', 'departure_date' => '2026-09-16',
        ]);

        $this->artisan('bookings:notify-upcoming-arrivals')->assertSuccessful();
        $this->artisan('bookings:notify-upcoming-arrivals')->assertSuccessful();

        $this->assertSame(1, $guest->notifications()->where('type', 'arrival_reminder')->count());
        $this->assertSame(1, $owner->notifications()->where('type', 'arrival_reminder')->count());
        $this->assertDatabaseHas('notifications', ['user_id' => $guest->id, 'data->booking_id' => $booking->id]);
    }

    private function ownerWithBusiness(): array
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, [
            'name' => 'Nexa Stays', 'country_code' => 'NG', 'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos', 'currency' => 'NGN',
        ]);

        return [$owner, $business];
    }
}
