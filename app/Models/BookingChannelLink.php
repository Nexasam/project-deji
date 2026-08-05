<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id', 'booking_id', 'channel', 'external_booking_reference',
    'external_property_reference', 'external_account_reference', 'metadata',
    'synchronized_at', 'synchronization_state', 'status', 'created_by', 'updated_by',
])]
class BookingChannelLink extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'synchronized_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
