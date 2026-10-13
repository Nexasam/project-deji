<?php

namespace App\Support;

use App\Models\User;
use App\Services\PlatformPermissionService;

final class UserWorkspaceDestination
{
    public function __construct(private readonly PlatformPermissionService $platformPermissions)
    {
    }

    public function routeName(User $user): string
    {
        if ($this->platformPermissions->isPlatformAdministrator($user)) {
            return 'admin.dashboard';
        }

        if ($user->businessMemberships()->where('status', 'active')->exists()) {
            return 'owner.entry';
        }

        if ($user->account_type === 'business') {
            return 'owner.onboarding.business.create';
        }

        return 'guest.dashboard';
    }

    public function url(User $user, bool $verified = false): string
    {
        $url = route($this->routeName($user), absolute: false);

        return $verified ? $url.'?verified=1' : $url;
    }
}
