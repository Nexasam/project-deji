<?php

namespace Tests\Feature\Admin;

use App\Models\PlatformSetting;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlatformConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_validated_platform_settings_with_an_audit(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_super_admin', 'configuration@example.test');

        $this->actingAs($admin)->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Platform configuration')
            ->assertSee('Calendar stale threshold')
            ->assertSee('Gmail SMTP')
            ->assertSee('Brevo SMTP')
            ->assertSee('Sandbox payment cards')
            ->assertSee('4084 0840 8408 4081')
            ->assertSee('5531 8866 5214 2950');

        $this->actingAs($admin)->patch(route('admin.settings.update'), [
            'settings' => [
                'reviews' => ['auto_publish_verified' => '0', 'invitation_window_days' => '21'],
                'bookings' => ['free_cancellation_hours' => '72'],
                'notifications' => ['email_delivery_enabled' => '1'],
                'calendar' => ['stale_after_minutes' => '30'],
                'marketplace' => ['require_verified_business' => '1'],
                'support' => ['contact_email' => 'care@verifiedshortlet.test'],
            ],
            'reason' => 'Configuration adjusted for the controlled MVP release policy.',
        ])->assertRedirect(route('admin.settings.index'));

        $this->assertFalse(PlatformSetting::query()->where('key', 'reviews.auto_publish_verified')->firstOrFail()->value);
        $this->assertSame(21, PlatformSetting::query()->where('key', 'reviews.invitation_window_days')->firstOrFail()->value);
        $this->assertSame('care@verifiedshortlet.test', PlatformSetting::query()->where('key', 'support.contact_email')->firstOrFail()->value);
        $this->assertDatabaseHas('audit_events', ['event_type' => 'platform.setting.updated', 'created_by' => $admin->id]);
    }

    public function test_setting_ranges_and_types_are_enforced_without_partial_saves(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_super_admin', 'invalid-configuration@example.test');

        $this->actingAs($admin)->from(route('admin.settings.index'))->patch(route('admin.settings.update'), [
            'settings' => [
                'reviews' => ['auto_publish_verified' => '1', 'invitation_window_days' => '999'],
                'bookings' => ['free_cancellation_hours' => '-2'],
                'notifications' => ['email_delivery_enabled' => '1'],
                'calendar' => ['stale_after_minutes' => '1'],
                'marketplace' => ['require_verified_business' => '0'],
                'support' => ['contact_email' => 'not-an-email'],
            ],
            'reason' => 'Attempting an invalid configuration update for validation coverage.',
        ])->assertSessionHasErrors();

        $this->assertDatabaseCount('platform_settings', 0);
        $this->assertDatabaseCount('audit_events', 0);
    }

    public function test_non_super_admin_cannot_open_or_change_platform_configuration(): void
    {
        $this->seed(AccessControlSeeder::class);
        $support = $this->administrator('platform_support_admin', 'support-settings@example.test');

        $this->actingAs($support)->get(route('admin.settings.index'))->assertForbidden();
        $this->actingAs($support)->patch(route('admin.settings.update'), [])->assertForbidden();
    }

    public function test_super_admin_can_run_email_and_storage_connection_checks(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_super_admin', 'operations@example.test');
        Mail::shouldReceive('raw')->once()->withArgs(function (string $body, callable $callback): bool {
            return str_contains($body, 'configured successfully');
        });
        Storage::fake('platform_media');

        $this->actingAs($admin)->post(route('admin.settings.test-email'), [
            'recipient' => 'delivery-check@example.test',
        ])->assertRedirect();
        $this->post(route('admin.settings.test-storage'), ['confirm' => '1'])
            ->assertRedirect()
            ->assertSessionHas('status', 'Media storage write, read and delete checks passed.');
        $this->assertSame([], Storage::disk('platform_media')->allFiles('health-checks'));
    }

    public function test_non_super_admin_cannot_run_provider_connection_checks(): void
    {
        $this->seed(AccessControlSeeder::class);
        $support = $this->administrator('platform_support_admin', 'support-probes@example.test');

        $this->actingAs($support)->post(route('admin.settings.test-email'), [
            'recipient' => 'delivery-check@example.test',
        ])->assertForbidden();
        $this->post(route('admin.settings.test-storage'), ['confirm' => '1'])->assertForbidden();
    }

    private function administrator(string $roleKey, string $email): User
    {
        $user = User::factory()->create(['email' => $email, 'email_verified_at' => now()]);
        UserRole::query()->create(['user_id' => $user->id, 'role_id' => Role::query()->where('system_key', $roleKey)->firstOrFail()->id, 'status' => 'active', 'assigned_at' => now()]);

        return $user;
    }
}
