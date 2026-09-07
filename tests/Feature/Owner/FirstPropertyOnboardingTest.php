<?php

namespace Tests\Feature\Owner;

use App\Models\Business;
use App\Models\Property;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirstPropertyOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_business_owner_sees_an_honest_zero_property_dashboard(): void
    {
        [$user, $business] = $this->ownerWithBusiness();

        $response = $this->actingAs($user)->get(route('owner.dashboard'));

        $response->assertOk()
            ->assertSee($business->name)
            ->assertSee('Add your first property')
            ->assertDontSee('Lekki Waterview Suites')
            ->assertDontSee('₦2.84M');
    }

    public function test_first_property_is_created_as_a_draft_for_the_active_business(): void
    {
        [$user, $business] = $this->ownerWithBusiness();
        $response = $this->actingAs($user)->post(route('owner.properties.wizard.start'), [
            'property_kind' => 'brand_new',
        ]);

        $property = Property::query()->sole();
        $response->assertRedirect(route('owner.properties.wizard.step', ['property' => $property, 'step' => 2]));

        $this->assertSame($business->id, $property->business_id);
        $this->assertSame('draft', $property->publication_status->value);
        $this->assertSame('unverified', $property->verification_status->value);
        $this->assertSame('NGN', $property->pricing_currency);
    }

    public function test_owner_can_explicitly_switch_dashboard_and_properties_to_demo_view(): void
    {
        [$user] = $this->ownerWithBusiness();

        $this->actingAs($user)->get(route('owner.dashboard', ['view' => 'demo']))
            ->assertOk()
            ->assertSee('Sample data')
            ->assertSee('BUSINESS HEALTH SCORE');

        $this->actingAs($user)->get(route('owner.properties.index', ['view' => 'demo']))
            ->assertOk()
            ->assertSee('Sample data')
            ->assertSee('Sunset Loft, Lekki Phase 1');
    }

    public function test_unrecognized_view_value_falls_back_to_real_data(): void
    {
        [$user] = $this->ownerWithBusiness();

        $this->actingAs($user)->get(route('owner.dashboard', ['view' => 'anything']))
            ->assertOk()
            ->assertSee('Add your first property')
            ->assertDontSee('BUSINESS HEALTH SCORE');
    }

    /** @return array{User, Business} */
    private function ownerWithBusiness(): array
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($user, [
            'name' => 'Nexa Stays',
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        return [$user, $business];
    }
}
