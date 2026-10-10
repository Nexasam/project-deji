<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\GoLivePlatformOwnerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GoLivePlatformOwnerSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_only_the_configured_platform_owner_account(): void
    {
        config([
            'platform.initial_admin.email' => 'launch.owner@verifiedshortlet.test',
            'platform.initial_admin.password' => 'LaunchOwner123!',
            'platform.settlement_approver.email' => 'settlement.approver@verifiedshortlet.test',
            'platform.settlement_approver.password' => 'SettlementApprover123!',
        ]);

        $this->seed(GoLivePlatformOwnerSeeder::class);

        $this->assertDatabaseCount('users', 2);
        $owner = User::query()->where('email', 'launch.owner@verifiedshortlet.test')->sole();
        $this->assertSame('launch.owner@verifiedshortlet.test', $owner->email);
        $this->assertTrue($owner->hasActiveGlobalRole('platform_super_admin'));
        $approver = User::query()->where('email', 'settlement.approver@verifiedshortlet.test')->sole();
        $this->assertTrue($approver->hasActiveGlobalRole('platform_super_admin'));
        $this->assertDatabaseCount('businesses', 0);
        $this->assertDatabaseCount('properties', 0);
        $this->assertDatabaseCount('bookings', 0);
    }
}
