<?php

namespace Tests\Feature\Owner;

use App\Models\Business;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BusinessIdentityOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_identity_is_provider_verified_without_storing_raw_numbers(): void
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($owner)->post(route('owner.onboarding.business.store'), $this->payload())
            ->assertSessionHasNoErrors()->assertRedirect(route('owner.dashboard'));

        $business = Business::query()->sole();
        $this->assertSame('verified', data_get($business->verification_payload, 'nin_status'));
        $this->assertSame('8901', data_get($business->verification_payload, 'nin_last4'));
        $this->assertSame('platform-test', data_get($business->verification_payload, 'nin_provider'));
        $rawPayload = $business->getRawOriginal('verification_payload');
        $this->assertStringNotContainsString('12345678901', $rawPayload);
    }

    public function test_business_onboarding_requires_identity_consent(): void
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $payload = $this->payload();
        unset($payload['identity_consent']);

        $this->actingAs($owner)->post(route('owner.onboarding.business.store'), $payload)
            ->assertSessionHasErrors('identity_consent');
        $this->assertDatabaseCount('businesses', 0);
    }

    public function test_owner_can_skip_identity_and_complete_it_later(): void
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $payload = $this->payload();
        unset($payload['nin'], $payload['identity_consent']);
        $payload['skip_identity'] = '1';

        $this->actingAs($owner)->post(route('owner.onboarding.business.store'), $payload)
            ->assertSessionHasNoErrors()->assertRedirect(route('owner.dashboard'));

        $business = Business::query()->sole();
        $this->assertSame('not_submitted', data_get($business->verification_payload, 'nin_status'));

        $this->actingAs($owner)->get(route('owner.onboarding.identity.edit'))
            ->assertOk()->assertSee('Verify the administrator');

        $this->actingAs($owner)->post(route('owner.onboarding.identity.update'), [
            'nin' => '12345678901',
            'identity_consent' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('owner.dashboard'));

        $business->refresh();
        $this->assertSame('verified', data_get($business->verification_payload, 'nin_status'));
        $this->assertSame('8901', data_get($business->verification_payload, 'nin_last4'));
        $this->assertStringNotContainsString('12345678901', $business->getRawOriginal('verification_payload'));
    }

    private function payload(): array
    {
        return [
            'name' => 'Identity Verified Stays', 'business_type' => 'Serviced apartments',
            'registration_number' => 'RC-10001', 'country_code' => 'NG', 'currency' => 'NGN',
            'timezone' => 'Africa/Lagos', 'phone_number' => '+2348012345678',
            'address_line' => 'Lekki, Lagos', 'nin' => '12345678901',
            'identity_consent' => '1',
        ];
    }
}
