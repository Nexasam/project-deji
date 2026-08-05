<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'phone_number', 'password', 'preferred_locale', 'timezone',
    'status', 'failed_login_attempts', 'locked_until', 'password_changed_at',
    'last_login_at', 'last_seen_at', 'created_by', 'updated_by',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'failed_login_attempts' => 'integer',
            'locked_until' => 'datetime',
            'password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function primaryContactForBusinesses(): HasMany
    {
        return $this->hasMany(Business::class, 'primary_contact_user_id');
    }

    public function ownedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_user_id');
    }

    public function managedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'manager_user_id');
    }

    public function completedOnboardingSteps(): HasMany
    {
        return $this->hasMany(BusinessOnboardingStep::class, 'completed_by');
    }

    public function propertyLifecycleEvents(): HasMany
    {
        return $this->hasMany(PropertyLifecycleEvent::class, 'actor_user_id');
    }

    public function guestProfiles(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function employeeProfiles(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    public function platformNotifications(): MorphMany
    {
        return $this->morphMany(PlatformNotification::class, 'recipient');
    }

    public function identities(): HasMany
    {
        return $this->hasMany(UserIdentity::class);
    }

    public function mfaMethods(): HasMany
    {
        return $this->hasMany(UserMfaMethod::class);
    }

    public function refreshTokens(): HasMany
    {
        return $this->hasMany(UserRefreshToken::class);
    }

    public function passwordHistories(): HasMany
    {
        return $this->hasMany(UserPasswordHistory::class);
    }

    public function businessMemberships(): HasMany
    {
        return $this->hasMany(BusinessMembership::class);
    }

    public function directRoleAssignments(): HasMany
    {
        return $this->hasMany(UserRoleAssignment::class);
    }

    public function directRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role_assignments')
            ->withPivot(['id', 'assigned_by', 'expires_at', 'revoked_at', 'status'])
            ->withTimestamps();
    }

    public function activeBusinessContext(): HasOne
    {
        return $this->hasOne(UserBusinessContext::class);
    }

    public function impersonationSessionsStarted(): HasMany
    {
        return $this->hasMany(ImpersonationSession::class, 'platform_user_id');
    }

    public function impersonationSessionsReceived(): HasMany
    {
        return $this->hasMany(ImpersonationSession::class, 'target_user_id');
    }
}
