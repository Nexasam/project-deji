<?php

namespace App\Services\Platform;

use App\Models\User;
use App\Services\PlatformPermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ManageUserSecurity
{
    public function __construct(
        private readonly PlatformAudit $audit,
        private readonly PlatformPermissionService $permissions,
    ) {}

    public function lock(User $target, User $actor, string $reason): User
    {
        $this->guard($target, $actor, $reason);

        return DB::transaction(function () use ($target, $actor, $reason): User {
            $target = User::query()->lockForUpdate()->findOrFail($target->id);
            $before = $this->snapshot($target);
            $target->forceFill([
                'status' => 'suspended',
                'locked_until' => now()->addYears(10),
                'remember_token' => null,
            ])->save();
            $terminatedSessions = DB::table('sessions')->where('user_id', $target->id)->delete();
            $target->refresh();
            $this->audit->record($actor, 'platform.user.locked', $target, "Locked account access for {$target->name}.", $before, $this->snapshot($target), [
                'reason' => trim($reason),
                'terminated_sessions' => $terminatedSessions,
            ]);

            return $target;
        });
    }

    public function unlock(User $target, User $actor, string $reason): User
    {
        $this->guard($target, $actor, $reason);

        return DB::transaction(function () use ($target, $actor, $reason): User {
            $target = User::query()->lockForUpdate()->findOrFail($target->id);
            $before = $this->snapshot($target);
            $target->forceFill([
                'status' => 'active',
                'locked_until' => null,
                'failed_login_attempts' => 0,
                'remember_token' => null,
            ])->save();
            $target->refresh();
            $this->audit->record($actor, 'platform.user.unlocked', $target, "Restored account access for {$target->name}.", $before, $this->snapshot($target), ['reason' => trim($reason)]);

            return $target;
        });
    }

    private function guard(User $target, User $actor, string $reason): void
    {
        if (mb_strlen(trim($reason)) < 10) {
            throw ValidationException::withMessages(['reason' => 'Give a clear reason of at least 10 characters.']);
        }
        if ($target->is($actor)) {
            throw ValidationException::withMessages(['user' => 'You cannot lock or unlock your own active administrator account.']);
        }
        if ($this->permissions->isPlatformAdministrator($target) && ! $actor->hasActiveGlobalRole('platform_super_admin')) {
            throw ValidationException::withMessages(['user' => 'Only a Platform Super Administrator can change another platform administrator’s access.']);
        }
    }

    /** @return array<string, mixed> */
    private function snapshot(User $user): array
    {
        return [
            'status' => $user->status->value,
            'locked_until' => $user->locked_until?->toIso8601String(),
            'failed_login_attempts' => $user->failed_login_attempts,
        ];
    }
}
