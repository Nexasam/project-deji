<?php

namespace Tests\Feature\Owner;

use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_render_the_operations_page_with_the_shared_dashboard_layout(): void
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create();
        app(BusinessOnboardingService::class)->onboard($user, [
            'name' => 'Nexa Stays',
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        $this->actingAs($user)->get(route('owner.operations'))
            ->assertOk()
            ->assertSee('Operations')
            ->assertSee('Nexa Stays');
    }
}
