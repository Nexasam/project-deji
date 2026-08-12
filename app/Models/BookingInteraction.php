<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'booking_id', 'user_id', 'recipient_user_id', 'interaction_type', 'direction', 'channel', 'recipient_name', 'recipient_address', 'summary', 'content', 'is_internal', 'external_thread_id', 'external_message_id', 'delivery_status', 'sent_at', 'delivered_at', 'read_at', 'failed_at', 'failure_reason', 'metadata', 'occurred_at', 'status'])]
class BookingInteraction extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
