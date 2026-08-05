<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LogicException;

#[Fillable([
    'business_id',
    'property_id',
    'guest_id',
    'reference',
    'arrival_date',
    'departure_date',
    'number_of_guests',
    'source',
    'status',
    'payment_status',
    'special_requests',
    'discount_amount',
    'discount_currency',
    'coupon_code',
    'external_reference',
    'source_metadata',
    'created_by',
    'updated_by',
])]
class Booking extends Model
{
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::deleting(function (): never {
            throw new LogicException('Bookings are historical records and cannot be deleted.');
        });
    }

    protected function casts(): array
    {
        return [
            'arrival_date' => 'date',
            'departure_date' => 'date',
            'number_of_guests' => 'integer',
            'discount_amount' => 'decimal:4',
            'source_metadata' => 'array',
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

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function operationalTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class);
    }

    public function checkIn(): HasOne
    {
        return $this->hasOne(BookingCheckIn::class);
    }

    public function checkOut(): HasOne
    {
        return $this->hasOne(BookingCheckOut::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(BookingInteraction::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(GuestServiceRequest::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(BookingIncident::class);
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(PropertyAvailabilityBlock::class);
    }

    public function channelLinks(): HasMany
    {
        return $this->hasMany(BookingChannelLink::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function dateChanges(): HasMany
    {
        return $this->hasMany(BookingDateChange::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'owner');
    }

    public function auditEvents(): MorphMany
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }
}
