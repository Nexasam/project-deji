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
        $email = env('PLATFORM_ADMIN_EMAIL', 'admin@verifiedshortlet.test');
        $admin = User::query()->firstOrCreate(['email' => $email], [
            'name' => 'Verified Shortlet Admin',
            'password' => env('PLATFORM_ADMIN_PASSWORD', 'AdminPassword123!'),
            'email_verified_at' => now(),
            'timezone' => 'Africa/Lagos',
            'status' => 'active',
        ]);
        $role = Role::query()->where('system_key', 'platform_super_admin')->firstOrFail();

        UserRole::query()->firstOrCreate([
            'user_id' => $admin->id,
            'role_id' => $role->id,
            'business_membership_id' => null,
        ], [
            'status' => 'active',
            'assigned_at' => now(),
        ]);
    }
}
