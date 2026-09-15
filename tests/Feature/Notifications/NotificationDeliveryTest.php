<?php

namespace Tests\Feature\Notifications;

use App\Jobs\SendNotificationEmail;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_in_app_record_is_persisted_and_email_delivery_is_queued(): void
    {
        Queue::fake();
        $user = User::factory()->create(['email' => 'guest@example.test']);

        $notification = app(ProductNotificationService::class)->user(
            $user, null, 'booking_confirmed', 'Stay confirmed', 'Your reservation is ready.'
        );

        $delivery = $notification->deliveries()->sole();
        $this->assertSame('pending', $delivery->status->value);
        Queue::assertPushed(SendNotificationEmail::class, fn ($job) => $job->deliveryId === $delivery->id);
    }

    public function test_email_job_marks_delivery_without_mutating_in_app_notification(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'guest@example.test']);
        Queue::fake();
        $notification = app(ProductNotificationService::class)->user(
            $user, null, 'booking_confirmed', 'Stay confirmed', 'Your reservation is ready.'
        );
        $delivery = $notification->deliveries()->sole();

        (new SendNotificationEmail($delivery->id))->handle();

        $this->assertSame('delivered', $delivery->fresh()->status->value);
        $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'message' => 'Your reservation is ready.']);
        Mail::assertSentCount(1);
    }
}
