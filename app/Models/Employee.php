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
    'business_membership_id',
    'employee_number',
    'name',
    'email',
    'phone_number',
    'department',
    'employment_status',
    'availability',
    'emergency_contact',
    'started_on',
    'ended_on',
    'created_by',
    'updated_by',
])]
class Employee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'availability' => 'array',
            'emergency_contact' => 'encrypted:array',
            'started_on' => 'date',
            'ended_on' => 'date',
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

    public function membership(): BelongsTo
    {
        return $this->belongsTo(BusinessMembership::class, 'business_membership_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class, 'assigned_employee_id');
    }

    public function completedCheckIns(): HasMany
    {
        return $this->hasMany(BookingCheckIn::class, 'completed_by_employee_id');
    }

    public function completedCheckOuts(): HasMany
    {
        return $this->hasMany(BookingCheckOut::class, 'completed_by_employee_id');
    }

    public function bookingInteractions(): HasMany
    {
        return $this->hasMany(BookingInteraction::class);
    }

    public function assignedServiceRequests(): HasMany
    {
        return $this->hasMany(GuestServiceRequest::class, 'assigned_employee_id');
    }

    public function reportedBookingIncidents(): HasMany
    {
        return $this->hasMany(BookingIncident::class, 'reporter_employee_id');
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
