<?php

namespace Tests\Feature\Admin;

use App\Models\Business;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlatformPermissionService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_platform_administrator_roles_can_use_the_dedicated_login(): void
    {
        $this->seed(AccessControlSeeder::class);

        foreach (['platform_super_admin', 'platform_verification_admin', 'platform_support_admin'] as $role) {
            $administrator = $this->administrator($role, "{$role}@example.test");

            $this->post(route('admin.login.store'), [
                'email' => $administrator->email,
                'password' => 'AdminPassword123!',
            ])->assertRedirect(route('admin.dashboard', absolute: false));

            $this->post(route('logout'));
        }
    }

    public function test_a_business_user_cannot_use_the_platform_login(): void
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create([
            'email' => 'business-user@example.test',
            'password' => 'AdminPassword123!',
        ]);

        $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'AdminPassword123!',
        ])->assertRedirect(route('admin.login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_platform_roles_receive_only_their_approved_permissions(): void
    {
        $this->seed(AccessControlSeeder::class);
        $permissions = app(PlatformPermissionService::class);
        $superAdmin = $this->administrator('platform_super_admin', 'super@example.test');
        $verificationAdmin = $this->administrator('platform_verification_admin', 'verification@example.test');
        $supportAdmin = $this->administrator('platform_support_admin', 'support@example.test');

        $this->assertTrue($permissions->allows($superAdmin, 'platform.configure'));
        $this->assertTrue($permissions->allows($superAdmin, 'platform.dispute.manage'));

        $this->assertTrue($permissions->allows($verificationAdmin, 'platform.property.verify'));
        $this->assertTrue($permissions->allows($verificationAdmin, 'platform.review.moderate'));
        $this->assertFalse($permissions->allows($verificationAdmin, 'platform.user.lock'));
        $this->assertFalse($permissions->allows($verificationAdmin, 'platform.configure'));

        $this->assertTrue($permissions->allows($supportAdmin, 'platform.user.lock'));
        $this->assertTrue($permissions->allows($supportAdmin, 'platform.dispute.manage'));
        $this->assertTrue($permissions->allows($supportAdmin, 'platform.property.view'));
        $this->assertFalse($permissions->allows($supportAdmin, 'platform.property.verify'));
        $this->assertFalse($permissions->allows($supportAdmin, 'platform.review.moderate'));
    }

    public function test_inactive_and_expired_platform_assignments_do_not_grant_access(): void
    {
        $this->seed(AccessControlSeeder::class);
        $permissions = app(PlatformPermissionService::class);

        $inactive = $this->administrator('platform_support_admin', 'inactive@example.test', 'inactive');
        $expired = $this->administrator('platform_support_admin', 'expired@example.test', 'active', now()->subMinute());

        $this->assertFalse($permissions->isPlatformAdministrator($inactive));
        $this->assertFalse($permissions->allows($inactive, 'platform.dispute.manage'));
        $this->assertFalse($permissions->isPlatformAdministrator($expired));
        $this->assertFalse($permissions->allows($expired, 'platform.dispute.manage'));
    }

    public function test_support_admin_can_view_properties_but_cannot_publish_them(): void
    {
        $this->seed(AccessControlSeeder::class);
        $supportAdmin = $this->administrator('platform_support_admin', 'support-routing@example.test');

        $this->actingAs($supportAdmin)
            ->get(route('admin.properties.index'))
            ->assertOk()
            ->assertSee('Property publishing');

        $propertyId = Property::factory()
            ->for(Business::factory())
            ->create()
            ->id;

        $response = $this->actingAs($supportAdmin)
            ->post(route('admin.properties.publish', $propertyId));

        $this->assertSame(403, $response->getStatusCode(), (string) $response->headers->get('Location'));
    }

    private function administrator(
        string $roleKey,
        string $email,
        string $status = 'active',
        mixed $expiresAt = null,
    ): User {
        $administrator = User::factory()->create([
            'email' => $email,
            'email_verified_at' => now(),
            'password' => 'AdminPassword123!',
        ]);

        UserRole::query()->create([
            'user_id' => $administrator->id,
            'role_id' => Role::query()->where('system_key', $roleKey)->firstOrFail()->id,
            'status' => $status,
            'assigned_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        return $administrator;
    }
}
