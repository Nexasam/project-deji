<?php

namespace Tests\Feature\Admin;

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Platform\PlatformAudit;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformAuditWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorised_admin_can_search_and_filter_immutable_platform_history(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_verification_admin', 'audit-viewer@example.test');
        $business = Business::factory()->create(['name' => 'Audit Trail Hospitality']);
        app(PlatformAudit::class)->record($admin, 'platform.business.verified', $business, 'Verified Audit Trail Hospitality.', ['verification_status' => 'pending'], ['verification_status' => 'verified'], ['reason' => 'Registration evidence was complete.']);
        app(PlatformAudit::class)->record($admin, 'platform.business.suspended', $business, 'Suspended Audit Trail Hospitality.', ['status' => 'active'], ['status' => 'suspended'], ['reason' => 'A safety review was opened.']);

        $this->actingAs($admin)->get(route('admin.audit.index', [
            'event' => 'platform.business.verified',
            'administrator' => $admin->id,
            'business' => $business->id,
        ]))->assertOk()
            ->assertSee('Audit history')
            ->assertSee('Verified Audit Trail Hospitality.')
            ->assertDontSee('Suspended Audit Trail Hospitality.');
    }

    public function test_audit_workspace_is_read_only(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_super_admin', 'audit-immutable@example.test');
        $business = Business::factory()->create();
        $event = app(PlatformAudit::class)->record($admin, 'platform.business.verified', $business, 'Immutable event.');

        $this->expectException(\LogicException::class);
        $event->delete();
    }

    private function administrator(string $roleKey, string $email): User
    {
        $user = User::factory()->create(['email' => $email, 'email_verified_at' => now()]);
        UserRole::query()->create(['user_id' => $user->id, 'role_id' => Role::query()->where('system_key', $roleKey)->firstOrFail()->id, 'status' => 'active', 'assigned_at' => now()]);

        return $user;
    }
}
