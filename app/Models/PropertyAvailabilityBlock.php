<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['business_id', 'property_id', 'booking_id', 'external_calendar_connection_id', 'source_type', 'source_reference', 'blocks_booking', 'starts_on', 'ends_on', 'validation_status', 'validated_by', 'validated_at', 'block_state', 'reason', 'released_at', 'status'])]
class PropertyAvailabilityBlock extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['blocks_booking' => 'boolean', 'starts_on' => 'date', 'ends_on' => 'date', 'validated_at' => 'datetime', 'released_at' => 'datetime'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(ExternalCalendarConnection::class, 'external_calendar_connection_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function availabilityDays(): HasMany
    {
        return $this->hasMany(PropertyAvailabilityDay::class, 'availability_block_id');
    }
}
