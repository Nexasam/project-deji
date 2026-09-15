<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class PlatformAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('platform.initial_admin.email');
        $admin = User::query()->updateOrCreate(['email' => $email], [
            'name' => 'Verified Shortlet Admin',
            'password' => config('platform.initial_admin.password'),
            'email_verified_at' => now(),
            'timezone' => 'Africa/Lagos',
            'status' => 'active',
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
        $role = Role::query()->where('system_key', 'platform_super_admin')->firstOrFail();

        UserRole::query()->updateOrCreate([
            'user_id' => $admin->id,
            'role_id' => $role->id,
            'business_membership_id' => null,
        ], [
            'status' => 'active',
            'assigned_at' => now(),
            'expires_at' => null,
            'revoked_at' => null,
        ]);
    }
}
