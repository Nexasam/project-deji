<?php

namespace App\Models;

use Database\Factories\BusinessFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'description',
    'logo_disk',
    'logo_path',
    'website_url',
    'social_links',
    'registration_number',
    'country_code',
    'address',
    'primary_contact_user_id',
    'primary_contact_name',
    'email',
    'phone_number',
    'tax_information',
    'business_type',
    'timezone',
    'currency',
    'subscription_plan',
    'verification_status',
    'onboarding_status',
    'onboarding_started_at',
    'onboarding_completed_at',
    'status',
    'created_by',
    'updated_by',
])]
class Business extends Model
{
    /** @use HasFactory<BusinessFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'social_links' => 'array',
            'tax_information' => 'encrypted:array',
            'onboarding_started_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function primaryContact(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_contact_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function operationalTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
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

    public function memberships(): HasMany
    {
        return $this->hasMany(BusinessMembership::class);
    }

    public function customRoles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function membershipRoleAssignments(): HasMany
    {
        return $this->hasMany(MembershipRoleAssignment::class);
    }

    public function passwordPolicies(): HasMany
    {
        return $this->hasMany(PasswordPolicy::class);
    }
}
