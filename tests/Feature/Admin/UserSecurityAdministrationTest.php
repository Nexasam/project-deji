<?php

namespace Tests\Feature\Admin;

use App\Models\AuditEvent;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserSecurityAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_admin_can_find_lock_and_unlock_an_ordinary_user(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_support_admin', 'support@example.test');
        $target = User::factory()->create([
            'name' => 'Guest Security Case',
            'email' => 'security-case@example.test',
            'password' => 'GuestPassword123!',
            'failed_login_attempts' => 3,
        ]);
        DB::table('sessions')->insert([
            'id' => 'target-session', 'user_id' => $target->id, 'payload' => 'payload',
            'last_activity' => now()->timestamp,
        ]);

        $this->actingAs($admin)->get(route('admin.users.index', ['q' => 'security-case']))
            ->assertOk()->assertSee('User directory')->assertSee('Guest Security Case');

        $this->actingAs($admin)->post(route('admin.users.lock', $target), [
            'reason' => 'Account access paused while a security report is investigated.',
        ])->assertRedirect(route('admin.users.show', $target));

        $target->refresh();
        $this->assertSame('suspended', $target->status->value);
        $this->assertNotNull($target->locked_until);
        $this->assertDatabaseMissing('sessions', ['user_id' => $target->id]);
        $this->assertDatabaseHas('audit_events', ['event_type' => 'platform.user.locked', 'auditable_id' => $target->id]);

        $this->post(route('logout'));
        $this->post(route('login'), ['email' => $target->email, 'password' => 'GuestPassword123!'])
            ->assertSessionHasErrors('email');

        $this->actingAs($admin)->post(route('admin.users.unlock', $target), [
            'reason' => 'Security checks completed and the account owner was verified.',
        ])->assertRedirect(route('admin.users.show', $target));

        $target->refresh();
        $this->assertSame('active', $target->status->value);
        $this->assertNull($target->locked_until);
        $this->assertSame(0, $target->failed_login_attempts);
        $this->assertSame(2, AuditEvent::query()->where('auditable_id', $target->id)->count());
    }

    public function test_administrator_cannot_lock_their_own_account(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_super_admin', 'self@example.test');

        $this->actingAs($admin)->from(route('admin.users.show', $admin))
            ->post(route('admin.users.lock', $admin), ['reason' => 'Attempting to lock the current administrator account.'])
            ->assertSessionHasErrors('user');

        $this->assertSame('active', $admin->fresh()->status->value);
    }

    public function test_only_super_admin_can_lock_another_platform_administrator(): void
    {
        $this->seed(AccessControlSeeder::class);
        $support = $this->administrator('platform_support_admin', 'support-guard@example.test');
        $verification = $this->administrator('platform_verification_admin', 'verification-target@example.test');

        $this->actingAs($support)->from(route('admin.users.show', $verification))
            ->post(route('admin.users.lock', $verification), ['reason' => 'Escalated security concern needs immediate access restriction.'])
            ->assertSessionHasErrors('user');
        $this->assertSame('active', $verification->fresh()->status->value);

        $super = $this->administrator('platform_super_admin', 'super@example.test');
        $this->actingAs($super)->post(route('admin.users.lock', $verification), [
            'reason' => 'Confirmed platform-account compromise requires immediate containment.',
        ])->assertRedirect(route('admin.users.show', $verification));
        $this->assertSame('suspended', $verification->fresh()->status->value);
    }

    public function test_verification_admin_has_user_visibility_without_security_mutation(): void
    {
        $this->seed(AccessControlSeeder::class);
        $verification = $this->administrator('platform_verification_admin', 'verification-view@example.test');
        $target = User::factory()->create();

        $this->actingAs($verification)->get(route('admin.users.show', $target))->assertOk();
        $response = $this->actingAs($verification)->post(route('admin.users.lock', $target), [
            'reason' => 'This role must not be able to restrict account access.',
        ]);
        $this->assertSame(403, $response->getStatusCode());
    }

    private function administrator(string $roleKey, string $email): User
    {
        $admin = User::factory()->create(['email' => $email, 'email_verified_at' => now()]);
        UserRole::query()->create([
            'user_id' => $admin->id,
            'role_id' => Role::query()->where('system_key', $roleKey)->firstOrFail()->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        return $admin;
    }
}
