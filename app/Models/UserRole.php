<?php

namespace App\Models;

use App\Enums\RoleScope;
use App\Enums\UserRoleStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable([
    'user_id', 'role_id', 'business_membership_id', 'status', 'assigned_by',
    'assigned_at', 'expires_at', 'revoked_at',
])]
class UserRole extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::saving(function (UserRole $assignment): void {
            $role = Role::query()->findOrFail($assignment->role_id);

            if ($role->scope !== RoleScope::Business) {
                if ($assignment->business_membership_id !== null) {
                    throw new LogicException('Public and platform roles must be global.');
                }

                $assignment->scope_key = 'global';

                return;
            }

            if ($assignment->business_membership_id === null) {
                throw new LogicException('Business roles require a business membership.');
            }

            $membership = BusinessMembership::query()
                ->whereKey($assignment->business_membership_id)
                ->where('user_id', $assignment->user_id)
                ->first();

            if ($membership === null) {
                throw new LogicException('The business membership does not belong to this user.');
            }

            if ($role->business_id !== null && $role->business_id !== $membership->business_id) {
                throw new LogicException('The custom role belongs to a different business.');
            }

            $assignment->scope_key = $membership->id;
        });
    }

    protected function casts(): array
    {
        return [
            'status' => UserRoleStatus::class,
            'assigned_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function businessMembership(): BelongsTo
    {
        return $this->belongsTo(BusinessMembership::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
