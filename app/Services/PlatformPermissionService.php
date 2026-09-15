<?php

namespace App\Services;

use App\Models\User;

final class PlatformPermissionService
{
    /** @var array<string, bool> */
    private array $administratorCache = [];

    /** @var array<string, bool> */
    private array $permissionCache = [];

    public function isPlatformAdministrator(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->administratorCache[$user->id] ??= $this->activeAssignments($user)
            ->whereHas('role', fn ($query) => $query
                ->where('scope', 'platform')
                ->where('status', 'active'))
            ->exists();
    }

    public function allows(?User $user, string $permission): bool
    {
        if (! $user) {
            return false;
        }

        $cacheKey = $user->id.':'.$permission;

        return $this->permissionCache[$cacheKey] ??= $this->activeAssignments($user)
            ->whereHas('role', fn ($query) => $query
                ->where('scope', 'platform')
                ->where('status', 'active')
                ->whereHas('permissions', fn ($permissions) => $permissions
                    ->where('permissions.key', $permission)
                    ->where('permissions.status', 'active')
                    ->where('role_permissions.status', 'active')))
            ->exists();
    }

    private function activeAssignments(User $user)
    {
        return $user->roleAssignments()
            ->whereNull('business_membership_id')
            ->where('user_roles.status', 'active')
            ->whereNull('revoked_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now()));
    }
}
