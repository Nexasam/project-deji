<?php

namespace Tests\Feature\Notifications;

use App\Models\Notification;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_a_professional_filterable_notification_workspace(): void
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, [
            'name' => 'Nexa Stays',
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        $this->notification($owner, 'booking_confirmed', 'New reservation confirmed', null, $business->id);
        $this->notification($owner, 'calendar_conflict', 'Calendar sync needs attention', now(), $business->id);

        $this->actingAs($owner)->get(route('notifications.index', ['status' => 'unread']))
            ->assertOk()
            ->assertSee('Notification centre')
            ->assertSee('Nexa Stays')
            ->assertSee('New reservation confirmed')
            ->assertDontSee('Calendar sync needs attention')
            ->assertSee('Mark as read')
            ->assertSee('Demo view');

        $this->actingAs($owner)->get(route('notifications.index', ['view' => 'demo']))
            ->assertOk()
            ->assertSee('Sample data')
            ->assertSee('Figma notifications preview')
            ->assertSee('Recent activity')
            ->assertSee('Payment received')
            ->assertDontSee('New reservation confirmed');
    }

    public function test_user_can_mark_only_their_own_notification_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = $this->notification($user, 'booking_confirmed', 'Stay confirmed');
        $foreignNotification = $this->notification($otherUser, 'booking_confirmed', 'Another stay');

        $this->actingAs($user)->post(route('notifications.read', $notification))
            ->assertRedirect()
            ->assertSessionHas('status', 'Notification marked as read.');

        $this->assertNotNull($notification->fresh()->read_at);
        $this->actingAs($user)->post(route('notifications.read', $foreignNotification))->assertNotFound();
        $this->assertNull($foreignNotification->fresh()->read_at);
    }

    private function notification(User $user, string $type, string $title, mixed $readAt = null, ?string $businessId = null): Notification
    {
        return Notification::query()->create([
            'business_id' => $businessId,
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => 'Open this alert to review the latest activity.',
            'data' => ['url' => '/guest/bookings'],
            'read_at' => $readAt,
            'status' => 'active',
        ]);
    }
}
