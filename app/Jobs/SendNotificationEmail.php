<?php

namespace App\Jobs;

use App\Mail\ProductNotificationMail;
use App\Models\NotificationDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNotificationEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public readonly string $deliveryId) {}

    public function handle(): void
    {
        $delivery = NotificationDelivery::query()->with('notification')->findOrFail($this->deliveryId);
        if ($delivery->status->value === 'delivered') {
            return;
        }

        $delivery->update([
            'status' => 'processing',
            'attempt_count' => $delivery->attempt_count + 1,
            'last_attempted_at' => now(),
            'failure_reason' => null,
        ]);

        try {
            Mail::to($delivery->destination)->send(new ProductNotificationMail(
                $delivery->notification->title,
                $delivery->notification->message,
                data_get($delivery->notification->data, 'url'),
            ));
            $delivery->update(['status' => 'delivered', 'delivered_at' => now()]);
        } catch (Throwable $exception) {
            $delivery->update([
                'status' => 'failed',
                'failure_reason' => str($exception->getMessage())->limit(4000),
            ]);
            throw $exception;
        }
    }
}
