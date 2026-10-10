<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Enums\IdentityVerificationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

final class RegisterUserService
{
    public function register(array $attributes): User
    {
        return DB::transaction(function () use ($attributes): User {
            $role = Role::query()->where('system_key', 'guest')->where('status', 'active')->first();
            if (! $role) {
                throw new RuntimeException('The guest role has not been configured.');
            }

            $user = User::query()->create([
                'name' => $attributes['name'], 'email' => $attributes['email'],
                'phone_number' => $attributes['phone_number'] ?? null,
                'account_type' => $attributes['account_type'] ?? 'individual',
                'identity_verification_status' => filled($attributes['nin'] ?? null) ? IdentityVerificationStatus::Pending : IdentityVerificationStatus::Unverified,
                'identity_verification' => [
                    'nin_last4' => filled($attributes['nin'] ?? null) ? substr((string) $attributes['nin'], -4) : null,
                    'bvn_last4' => filled($attributes['bvn'] ?? null) ? substr((string) $attributes['bvn'], -4) : null,
                    'business_name' => $attributes['business_name'] ?? null,
                    'submitted_at' => filled($attributes['nin'] ?? null) ? now()->toIso8601String() : null,
                ],
                'password' => Hash::make($attributes['password']),
            ]);
            UserRole::query()->create([
                'user_id' => $user->id, 'role_id' => $role->id, 'status' => 'active',
                'assigned_by' => $user->id, 'assigned_at' => now(),
            ]);

            return $user;
        });
    }
}
