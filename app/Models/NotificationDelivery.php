<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id', 'notification_id', 'channel', 'destination', 'provider',
    'provider_reference', 'status', 'attempt_count', 'attempted_at', 'sent_at',
    'delivered_at', 'failed_at', 'failure_reason', 'created_by', 'updated_by',
])]
class NotificationDelivery extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'attempt_count' => 'integer',
            'attempted_at' => 'datetime',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(PlatformNotification::class, 'notification_id');
    }
}
