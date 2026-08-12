<?php

namespace App\Models;

use App\Enums\NotificationChannel;
use App\Enums\NotificationDeliveryStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id', 'notification_id', 'channel', 'destination', 'provider',
    'provider_reference', 'status', 'attempt_count', 'last_attempted_at',
    'delivered_at', 'failure_reason', 'provider_metadata',
])]
class NotificationDelivery extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'channel' => NotificationChannel::class,
            'status' => NotificationDeliveryStatus::class,
            'attempt_count' => 'integer',
            'last_attempted_at' => 'datetime',
            'delivered_at' => 'datetime',
            'provider_metadata' => 'array',
        ];
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }
}
