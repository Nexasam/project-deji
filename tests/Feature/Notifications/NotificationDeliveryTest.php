<?php

namespace Tests\Feature\Notifications;

use App\Jobs\SendNotificationEmail;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use App\Services\Notifications\PlatformMailConfigurator;
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

        (new SendNotificationEmail($delivery->id))->handle(app(PlatformMailConfigurator::class));

        $this->assertSame('delivered', $delivery->fresh()->status->value);
        $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'message' => 'Your reservation is ready.']);
        Mail::assertSentCount(1);
    }

    public function test_brevo_provider_applies_its_smtp_relay_preset(): void
    {
        $this->setting('notifications.mail_provider', 'brevo');
        $this->setting('notifications.mail_host', 'old-provider.example');
        $this->setting('notifications.mail_port', 465, 'integer');
        $this->setting('notifications.mail_scheme', 'smtps');

        app(PlatformMailConfigurator::class)->apply();

        $this->assertSame('smtp-relay.brevo.com', config('mail.mailers.smtp.host'));
        $this->assertSame(587, config('mail.mailers.smtp.port'));
        $this->assertSame('smtp', config('mail.mailers.smtp.scheme'));
    }

    private function setting(string $key, mixed $value, string $type = 'string'): void
    {
        [$group] = explode('.', $key, 2);
        PlatformSetting::query()->create([
            'group_key' => $group,
            'key' => $key,
            'value' => $value,
            'value_type' => $type,
            'label' => $key,
        ]);
    }
}
