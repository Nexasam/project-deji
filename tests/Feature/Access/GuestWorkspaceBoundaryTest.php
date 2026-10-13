<?php

namespace Tests\Feature\Access;

use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GuestWorkspaceBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_invited_staff_cannot_enter_guest_or_marketplace_surfaces(): void
    {
        $this->seed(AccessControlSeeder::class);
        $staff = User::factory()->create(['email_verified_at' => now()]);
        $guestRole = Role::query()->where('system_key', 'guest')->firstOrFail();
        UserRole::query()->create([
            'user_id' => $staff->id,
            'role_id' => $guestRole->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);
        BusinessMembership::query()->create([
            'business_id' => Business::factory()->create()->id,
            'user_id' => $staff->id,
            'job_title' => 'Cleaner',
            'status' => 'active',
            'invited_at' => now(),
            'joined_at' => now(),
        ]);

        $this->actingAs($staff)->get(route('guest.dashboard'))->assertForbidden();
        $this->get(route('home'))->assertForbidden();
    }

    public function test_anonymous_visitors_and_guest_only_users_keep_marketplace_access(): void
    {
        $this->seed(AccessControlSeeder::class);
        $this->get(route('home'))->assertOk();

        $guest = User::factory()->create(['email_verified_at' => now()]);
        UserRole::query()->create([
            'user_id' => $guest->id,
            'role_id' => Role::query()->where('system_key', 'guest')->firstOrFail()->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        $this->actingAs($guest)->get(route('home'))->assertOk();
        $this->get(route('guest.dashboard'))->assertOk();
    }
}
