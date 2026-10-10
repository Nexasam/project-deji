<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;

final class GoLivePlatformOwnerSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) config('platform.initial_admin.email'));
        $password = (string) config('platform.initial_admin.password');
        $approverEmail = trim((string) config('platform.settlement_approver.email'));
        $approverPassword = (string) config('platform.settlement_approver.password');

        if ($email === '' || $password === '' || $approverEmail === '' || $approverPassword === '') {
            throw new RuntimeException('Configure both PLATFORM_ADMIN_* and PLATFORM_APPROVER_* credentials before seeding platform owners.');
        }
        if (strcasecmp($email, $approverEmail) === 0) {
            throw new RuntimeException('The platform owner and settlement approver must use different email addresses.');
        }

        if (app()->environment('production') && in_array($password, ['AdminPassword123!', 'Password123!', 'DemoPassword123!'], true)) {
            throw new RuntimeException('Refusing to create the production platform owner with a documented development password. Set a unique PLATFORM_ADMIN_PASSWORD first.');
        }
        if (app()->environment('production') && in_array($approverPassword, ['AdminPassword123!', 'Password123!', 'DemoPassword123!'], true)) {
            throw new RuntimeException('Refusing to create the settlement approver with a documented development password.');
        }

        $this->call([
            AccessControlSeeder::class,
            PlatformAdminSeeder::class,
        ]);

        $this->command?->info("Platform owners ready: {$email} and {$approverEmail}");
        $this->command?->warn('No demonstration business, property, guest, booking or staff records were seeded.');
    }
}
