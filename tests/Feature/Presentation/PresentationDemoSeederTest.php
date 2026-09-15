<?php

namespace Tests\Feature\Presentation;

use App\Models\BookingDispute;
use App\Models\Business;
use App\Models\Property;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Database\Seeders\PlatformAdminSeeder;
use Database\Seeders\PresentationDemoSeeder;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PresentationDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_seeder_uses_cache_safe_configuration(): void
    {
        $this->seed(AccessControlSeeder::class);
        config([
            'platform.initial_admin.email' => 'staging-admin@example.test',
            'platform.initial_admin.password' => 'UniqueStagingPassword123!',
        ]);

        $this->seed(PlatformAdminSeeder::class);

        $admin = User::query()->where('email', 'staging-admin@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('UniqueStagingPassword123!', $admin->password));
        $this->assertTrue($admin->hasActiveGlobalRole('platform_super_admin'));
    }

    public function test_presentation_accounts_are_repeatable_and_receive_expected_scopes(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->seed(PresentationDemoSeeder::class);
        $this->seed(PresentationDemoSeeder::class);

        $emails = [
            'guest.demo@verifiedshortlet.test',
            'manager.demo@verifiedshortlet.test',
            'reception.demo@verifiedshortlet.test',
            'accountant.demo@verifiedshortlet.test',
            'operations.demo@verifiedshortlet.test',
            'cleaner.demo@verifiedshortlet.test',
            'maintenance.demo@verifiedshortlet.test',
            'inspector.demo@verifiedshortlet.test',
            'support.demo@verifiedshortlet.test',
            'verification.admin@verifiedshortlet.test',
            'disputes.admin@verifiedshortlet.test',
        ];
        $users = User::query()->whereIn('email', $emails)->get();

        $this->assertCount(count($emails), $users);
        $users->each(fn (User $user) => $this->assertTrue(Hash::check(PresentationDemoSeeder::PASSWORD, $user->password)));

        $manager = $users->firstWhere('email', 'manager.demo@verifiedshortlet.test');
        $this->assertCount(2, $manager->businessMemberships()->where('status', 'active')->get());
        $this->assertSame('Coastline Residences', $manager->resolvedBusinessContext()->business->name);

        $cleaner = $users->firstWhere('email', 'cleaner.demo@verifiedshortlet.test');
        $chevron = Property::query()->where('code', 'CSR-003')->firstOrFail();
        $this->assertDatabaseHas('property_staff_assignments', [
            'employee_id' => $cleaner->resolvedBusinessContext()->membership->employee->id,
            'property_id' => $chevron->id,
            'assignment_status' => 'active',
        ]);
        $this->actingAs($cleaner)->get(route('owner.entry'))->assertRedirect(route('staff.tasks.index'));

        $this->assertSame(2, Business::query()->whereIn('email', [
            'owner@lagoonstays.test',
            'owner@coastlineresidences.test',
        ])->count());

        $reviewProperty = Property::query()->where('code', 'DEMO-REVIEW-001')->firstOrFail();
        $this->assertSame('Eko Pearl Executive Residence', $reviewProperty->name);
        $this->assertSame('pending', $reviewProperty->publication_status->value);
        $this->assertSame('pending', $reviewProperty->verification_status->value);
        $this->assertSame('ready', $reviewProperty->readiness_status->value);
        $this->assertSame('eko-pearl-executive-residence', $reviewProperty->marketplaceListing->slug);
        $this->assertSame('draft', $reviewProperty->marketplaceListing->publication_status);
        $this->assertCount(3, $reviewProperty->media);
        $this->assertGreaterThanOrEqual(6, $reviewProperty->amenities()->count());
        $this->assertSame(1, Property::query()->where('code', 'DEMO-REVIEW-001')->count());

        $verificationAdmin = $users->firstWhere('email', 'verification.admin@verifiedshortlet.test');
        $supportAdmin = $users->firstWhere('email', 'disputes.admin@verifiedshortlet.test');
        $this->assertTrue($verificationAdmin->hasActiveGlobalRole('platform_verification_admin'));
        $this->assertTrue($supportAdmin->hasActiveGlobalRole('platform_support_admin'));
        $this->assertSame(1, Review::query()->where('title', 'Needs a moderation decision')->where('moderation_status', 'hidden')->count());
        $this->assertSame(1, BookingDispute::query()->where('reference', 'DSP-DEMO-OPEN')->where('dispute_status', 'open')->count());
    }

    public function test_admin_review_renders_seeded_external_property_media(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->seed(PlatformAdminSeeder::class);
        $this->seed(PresentationDemoSeeder::class);

        $admin = User::query()->where('email', 'admin@verifiedshortlet.test')->firstOrFail();
        $reviewProperty = Property::query()->where('code', 'DEMO-REVIEW-001')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.properties.show', $reviewProperty))
            ->assertOk()
            ->assertSee('Eko Pearl Executive Residence')
            ->assertSee('src="/apt1.jpg"', false);
    }

    public function test_reseeding_repairs_documented_demo_credentials_and_account_locks(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->seed(PlatformAdminSeeder::class);
        $this->seed(PresentationDemoSeeder::class);

        $credentials = [
            'guest.demo@verifiedshortlet.test' => PresentationDemoSeeder::PASSWORD,
            'owner@coastlineresidences.test' => 'Password123!',
            'admin@verifiedshortlet.test' => 'AdminPassword123!',
        ];

        User::query()->whereIn('email', array_keys($credentials))->update([
            'password' => 'stale-password',
            'status' => 'suspended',
            'failed_login_attempts' => 5,
            'locked_until' => now()->addDay(),
        ]);

        $admin = User::query()->where('email', 'admin@verifiedshortlet.test')->firstOrFail();
        $admin->roleAssignments()->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->seed(PlatformAdminSeeder::class);
        $this->seed(PresentationDemoSeeder::class);

        foreach ($credentials as $email => $password) {
            $user = User::query()->where('email', $email)->firstOrFail();

            $this->assertTrue(Hash::check($password, $user->password), $email.' password was not restored.');
            $this->assertSame('active', $user->status->value);
            $this->assertSame(0, $user->failed_login_attempts);
            $this->assertNull($user->locked_until);
        }

        $this->assertTrue($admin->fresh()->hasActiveGlobalRole('platform_super_admin'));

        $this->post(route('login'), [
            'email' => 'guest.demo@verifiedshortlet.test',
            'password' => PresentationDemoSeeder::PASSWORD,
        ])->assertRedirect(route('guest.bookings.index', absolute: false));
    }
}
