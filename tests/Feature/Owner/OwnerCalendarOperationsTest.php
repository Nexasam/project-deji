<?php

namespace Tests\Feature\Owner;

use App\Models\OperationalTask;
use App\Models\Property;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerCalendarOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_displays_due_operational_work_with_bookings_and_blocks(): void
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, [
            'name' => 'Nexa Stays', 'country_code' => 'NG', 'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos', 'currency' => 'NGN',
        ]);
        $property = Property::factory()->for($business)->create(['name' => 'Calendar Suite']);
        OperationalTask::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id,
            'reference' => 'TASK-CALENDAR', 'title' => 'Turnover clean', 'task_type' => 'cleaning',
            'priority' => 'high', 'status' => 'pending', 'generation_source' => 'system',
            'due_at' => '2026-09-18 12:00:00',
        ]);

        $this->actingAs($owner)->get(route('owner.calendar', ['month' => '2026-09']))
            ->assertOk()
            ->assertSee('Turnover clean')
            ->assertSee('Operations due')
            ->assertSee('Calendar overview')
            ->assertSee('Month at a glance')
            ->assertSee('Mobile agenda')
            ->assertSee('Block property dates')
            ->assertSee('Quick reasons')
            ->assertSee('Dates are checked against every booking and synchronized calendar')
            ->assertSee('Demo view');

        $this->actingAs($owner)->get(route('owner.calendar', ['view' => 'demo']))
            ->assertOk()
            ->assertSee('Sample data')
            ->assertSee('Figma calendar preview')
            ->assertSee('LOCK STATE')
            ->assertSee('Bluewater Suite 4B')
            ->assertDontSee('Turnover clean');
    }
}
