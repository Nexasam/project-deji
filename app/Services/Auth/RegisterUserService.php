<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
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
