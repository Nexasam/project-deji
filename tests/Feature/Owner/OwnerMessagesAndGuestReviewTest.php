<?php

namespace Tests\Feature\Owner;

use App\Models\Booking;
use App\Models\BookingInteraction;
use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\Employee;
use App\Models\Property;
use App\Models\PropertyStaffAssignment;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBusinessContext;
use App\Models\UserRole;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerMessagesAndGuestReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AccessControlSeeder::class);
    }

    public function test_owner_inbox_lists_business_message_threads_only(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Owner Messages Stays');
        $booking = Booking::factory()->for($business)->create(['reference' => 'VS-OWNER-MSG']);
        $foreignBooking = Booking::factory()->create(['reference' => 'VS-FOREIGN-MSG']);

        BookingInteraction::query()->create($this->messageAttributes($booking, $booking->guest, 'Visible owner thread'));
        BookingInteraction::query()->create($this->messageAttributes($foreignBooking, $foreignBooking->guest, 'Hidden owner thread'));

        $this->actingAs($owner)
            ->get(route('owner.messages.index'))
            ->assertOk()
            ->assertSee('VS-OWNER-MSG')
            ->assertSee('Visible owner thread')
            ->assertDontSee('VS-FOREIGN-MSG')
            ->assertDontSee('Hidden owner thread');
    }

    public function test_owner_can_reply_to_booking_thread_and_guest_is_notified(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Reply Stays');
        $booking = Booking::factory()->for($business)->create();

        $this->actingAs($owner)
            ->post(route('owner.bookings.messages.store', $booking), [
                'content' => 'Thanks, the access code will be shared before arrival.',
            ])
            ->assertRedirect(route('owner.bookings.messages.show', $booking));

        $this->assertDatabaseHas('booking_interactions', [
            'booking_id' => $booking->id,
            'user_id' => $owner->id,
            'recipient_user_id' => $booking->guest_user_id,
            'interaction_type' => 'message',
            'direction' => 'outbound',
            'channel' => 'platform',
            'delivery_status' => 'sent',
        ]);
        $this->assertDatabaseHas('notifications', [
            'business_id' => $business->id,
            'user_id' => $booking->guest_user_id,
            'type' => 'booking_message',
            'title' => 'New message from your host',
        ]);
    }

    public function test_property_scoped_staff_cannot_access_out_of_scope_booking_thread(): void
    {
        [, $business] = $this->ownerWithBusiness('Scoped Messages Stays');
        $visible = Property::factory()->for($business)->create();
        $hidden = Property::factory()->for($business)->create();
        [$manager] = $this->staffWithRole($business, 'property_manager', [$visible]);
        $hiddenBooking = Booking::factory()->for($business)->for($hidden)->create();

        $this->actingAs($manager)
            ->get(route('owner.bookings.messages.show', $hiddenBooking))
            ->assertForbidden();
    }

    public function test_owner_can_review_guest_stay_good_or_bad(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Guest Review Stays');
        $booking = Booking::factory()->for($business)->create();

        $this->actingAs($owner)
            ->post(route('owner.bookings.guest-review.store', $booking), [
                'rating' => 2,
                'sentiment' => 'bad',
                'content' => 'Guest left the apartment untidy and ignored checkout instructions.',
            ])
            ->assertRedirect(route('owner.bookings.show', $booking));

        $this->assertDatabaseHas('booking_interactions', [
            'booking_id' => $booking->id,
            'user_id' => $owner->id,
            'recipient_user_id' => $booking->guest_user_id,
            'interaction_type' => 'owner_guest_review',
            'direction' => 'internal',
            'channel' => 'platform',
            'delivery_status' => 'recorded',
            'content' => 'Guest left the apartment untidy and ignored checkout instructions.',
        ]);
    }

    /** @return array{User, Business} */
    private function ownerWithBusiness(string $name): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($user, [
            'name' => $name,
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        return [$user, $business];
    }

    /** @return array{User, BusinessMembership, Employee} */
    private function staffWithRole(Business $business, string $roleKey, array $properties = []): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $membership = BusinessMembership::query()->create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'job_title' => str($roleKey)->replace('_', ' ')->title(),
            'status' => 'active',
            'joined_at' => now(),
        ]);
        $role = Role::query()->where('system_key', $roleKey)->firstOrFail();
        $assignment = UserRole::query()->create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'business_membership_id' => $membership->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);
        UserBusinessContext::query()->create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'business_membership_id' => $membership->id,
            'active_user_role_id' => $assignment->id,
            'switched_at' => now(),
            'status' => 'active',
        ]);
        $employee = Employee::query()->create([
            'business_id' => $business->id,
            'business_membership_id' => $membership->id,
            'employee_code' => 'EMP-'.str()->upper(str()->random(8)),
            'employment_status' => 'active',
            'started_on' => today(),
            'status' => 'active',
        ]);

        foreach ($properties as $property) {
            PropertyStaffAssignment::query()->create([
                'business_id' => $business->id,
                'property_id' => $property->id,
                'employee_id' => $employee->id,
                'assignment_role' => 'property_manager',
                'assignment_status' => 'active',
                'status' => 'active',
            ]);
        }

        return [$user, $membership, $employee];
    }

    private function messageAttributes(Booking $booking, User $sender, string $content): array
    {
        return [
            'business_id' => $booking->business_id,
            'booking_id' => $booking->id,
            'user_id' => $sender->id,
            'interaction_type' => 'message',
            'direction' => 'inbound',
            'channel' => 'platform',
            'summary' => $content,
            'content' => $content,
            'is_internal' => false,
            'delivery_status' => 'sent',
            'sent_at' => now(),
            'occurred_at' => now(),
            'status' => 'active',
        ];
    }
}
