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
        $this->createOwner(config('platform.initial_admin.email'), config('platform.initial_admin.password'), 'Verified Shortlet Platform Owner');
        $this->createOwner(config('platform.settlement_approver.email'), config('platform.settlement_approver.password'), 'Verified Shortlet Settlement Approver');
    }

    private function createOwner(?string $email, ?string $password, string $name): void
    {
        if (blank($email) || blank($password)) {
            return;
        }

        $admin = User::query()->updateOrCreate(['email' => trim($email)], [
            'name' => $name,
            'password' => $password,
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
