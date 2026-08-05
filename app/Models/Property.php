<?php

namespace App\Models;

use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id',
    'name',
    'code',
    'address',
    'latitude',
    'longitude',
    'property_type',
    'capacity',
    'bedrooms',
    'bathrooms',
    'description',
    'default_nightly_price',
    'pricing_currency',
    'verification_status',
    'publication_status',
    'readiness_status',
    'information_completed_at',
    'media_completed_at',
    'verification_submitted_at',
    'verified_at',
    'published_at',
    'archived_at',
    'verified_by',
    'published_by',
    'cleaning_schedule',
    'maintenance_status',
    'owner_user_id',
    'manager_user_id',
    'status',
    'created_by',
    'updated_by',
])]
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'capacity' => 'integer',
            'bedrooms' => 'integer',
            'bathrooms' => 'decimal:1',
            'default_nightly_price' => 'decimal:4',
            'cleaning_schedule' => 'array',
            'information_completed_at' => 'datetime',
            'media_completed_at' => 'datetime',
            'verification_submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class)
            ->withPivot(['business_id', 'details'])
            ->withTimestamps();
    }

    public function houseRules(): HasMany
    {
        return $this->hasMany(PropertyHouseRule::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function lifecycleEvents(): HasMany
    {
        return $this->hasMany(PropertyLifecycleEvent::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(PropertyAvailabilityBlock::class);
    }

    public function operationalTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class);
    }

    public function bookingCheckIns(): HasMany
    {
        return $this->hasMany(BookingCheckIn::class);
    }

    public function bookingCheckOuts(): HasMany
    {
        return $this->hasMany(BookingCheckOut::class);
    }

    public function guestServiceRequests(): HasMany
    {
        return $this->hasMany(GuestServiceRequest::class);
    }

    public function bookingIncidents(): HasMany
    {
        return $this->hasMany(BookingIncident::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'owner');
    }

    public function auditEvents(): MorphMany
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }

    public function roleAssignments(): HasMany
    {
        return $this->hasMany(MembershipRoleAssignment::class);
    }

    public function permissionOverrides(): HasMany
    {
        return $this->hasMany(MembershipPermissionOverride::class);
    }
}
