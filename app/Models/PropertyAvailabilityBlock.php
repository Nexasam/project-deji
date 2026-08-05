<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'business_id', 'property_id', 'booking_id', 'block_type', 'starts_on',
    'ends_on', 'state', 'source_provider', 'source_reference', 'reason',
    'released_at', 'released_by', 'release_reason', 'status', 'created_by', 'updated_by',
])]
class PropertyAvailabilityBlock extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Property availability blocks are historical records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
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

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
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
