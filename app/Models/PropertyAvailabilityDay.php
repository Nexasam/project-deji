<?php

namespace App\Models;

use App\Enums\PropertyAvailabilityDayState;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_id', 'property_id', 'booking_id', 'availability_block_id', 'availability_date', 'availability_state', 'source_type', 'source_reference', 'hold_token_hash', 'held_by', 'hold_expires_at', 'active_key', 'allocated_at', 'released_at', 'release_reason', 'status', 'created_by', 'updated_by'])]
class PropertyAvailabilityDay extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'availability_date' => 'date',
            'availability_state' => PropertyAvailabilityDayState::class,
            'hold_expires_at' => 'datetime',
            'allocated_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function availabilityBlock(): BelongsTo
    {
        return $this->belongsTo(PropertyAvailabilityBlock::class);
    }

    public function holder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'held_by');
    }
}
