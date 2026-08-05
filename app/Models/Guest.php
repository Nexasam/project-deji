<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id',
    'user_id',
    'full_name',
    'email',
    'phone_number',
    'nationality',
    'identity_verification',
    'preferred_language',
    'emergency_contact',
    'marketing_preferences',
    'notes',
    'status',
    'created_by',
    'updated_by',
])]
class Guest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'identity_verification' => 'encrypted:array',
            'emergency_contact' => 'encrypted:array',
            'marketing_preferences' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingInteractions(): HasMany
    {
        return $this->hasMany(BookingInteraction::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(GuestServiceRequest::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'owner');
    }

    public function receivedNotifications(): MorphMany
    {
        return $this->morphMany(PlatformNotification::class, 'recipient');
    }
}
