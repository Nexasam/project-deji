<?php

namespace App\Models;

use App\Enums\IdentityVerificationStatus;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'phone_number',
    'password',
    'nationality',
    'identity_verification_status',
    'preferred_language',
    'emergency_contact',
    'marketing_preferences',
    'guest_notes',
    'timezone',
    'status',
    'failed_login_attempts',
    'locked_until',
    'password_changed_at',
    'last_login_at',
    'last_seen_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'identity_verification_status' => IdentityVerificationStatus::class,
            'emergency_contact' => 'array',
            'marketing_preferences' => 'array',
            'status' => UserStatus::class,
            'failed_login_attempts' => 'integer',
            'locked_until' => 'datetime',
            'password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function roleAssignments(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot([
                'id', 'business_membership_id', 'scope_key', 'status', 'assigned_by',
                'assigned_at', 'expires_at', 'revoked_at',
            ])
            ->withTimestamps();
    }

    public function businessMemberships(): HasMany
    {
        return $this->hasMany(BusinessMembership::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'guest_user_id');
    }

    public function createdBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'created_by');
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    public function createdPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function employeeProfiles(): HasManyThrough
    {
        return $this->hasManyThrough(
            Employee::class,
            BusinessMembership::class,
            'user_id',
            'business_membership_id'
        );
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
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

    public function activeBusinessContext(): HasOne
    {
        return $this->hasOne(UserBusinessContext::class);
    }

    public function hasActiveGlobalRole(string $systemKey): bool
    {
        return $this->roleAssignments()->where('status', 'active')
            ->whereNull('business_membership_id')->whereNull('revoked_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereHas('role', fn ($query) => $query->where('system_key', $systemKey)->where('status', 'active'))
            ->exists();
    }

    public function hasActiveBusinessRole(string $systemKey, string $membershipId): bool
    {
        return $this->roleAssignments()->where('business_membership_id', $membershipId)
            ->where('status', 'active')->whereNull('revoked_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereHas('role', fn ($query) => $query->where('system_key', $systemKey)->where('status', 'active'))
            ->exists();
    }

    public function resolvedBusinessContext(): ?UserBusinessContext
    {
        return $this->activeBusinessContext()->where('status', 'active')
            ->with(['business', 'membership', 'activeRoleAssignment.role'])->first();
    }

    public function workspacePreferences(): HasMany
    {
        return $this->hasMany(UserWorkspacePreference::class);
    }

    public function bookingGuestEntries(): HasMany
    {
        return $this->hasMany(BookingGuest::class);
    }

    public function requestedApprovals(): HasMany
    {
        return $this->hasMany(ApprovalRequest::class, 'requested_by');
    }

    public function approvalActions(): HasMany
    {
        return $this->hasMany(ApprovalAction::class, 'actor_user_id');
    }

    public function dataSubjectRequests(): HasMany
    {
        return $this->hasMany(DataSubjectRequest::class);
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }

    public function aiRecommendationFeedback(): HasMany
    {
        return $this->hasMany(AiRecommendationFeedback::class);
    }
}
