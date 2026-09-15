<?php

namespace Tests\Feature\Owner;

use App\Models\Booking;
use App\Models\OperationalTask;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use App\Services\Notifications\ProductNotificationService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerDashboardTodayTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_summarises_todays_stays_work_payments_and_alerts(): void
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, ['name' => 'Nexa Stays', 'country_code' => 'NG', 'business_type' => 'serviced_apartments', 'timezone' => 'Africa/Lagos', 'currency' => 'NGN']);
        $property = Property::factory()->for($business)->create();
        $arrival = Booking::factory()->for($business)->for($property)->create(['status' => 'confirmed', 'arrival_date' => today(), 'departure_date' => today()->addDays(2)]);
        Booking::factory()->for($business)->for($property)->create(['status' => 'checked_in', 'arrival_date' => today()->subDays(2), 'departure_date' => today()]);
        foreach ([['Clean today', 'cleaning'], ['Inspect today', 'inspection'], ['Repair today', 'maintenance']] as [$title, $type]) {
            OperationalTask::query()->create(['business_id' => $business->id, 'property_id' => $property->id, 'reference' => 'TASK-'.str($type)->upper(), 'title' => $title, 'task_type' => $type, 'priority' => 'high', 'status' => 'pending', 'generation_source' => 'manual', 'due_at' => today()->setTime(12, 0)]);
        }
        Payment::factory()->for($business)->for($arrival)->create(['purpose' => 'balance', 'amount' => 25000, 'status' => 'completed', 'transaction_at' => now()]);
        app(ProductNotificationService::class)->user($owner, $business, 'calendar_conflict', 'Calendar conflict', 'Review imported dates.');

        $this->actingAs($owner)->get(route('owner.dashboard'))->assertOk()
            ->assertSee('Today’s operations')->assertSee('Clean today')->assertSee('Inspect today')->assertSee('Repair today')
            ->assertSee('Unread alerts')->assertSee('Payments today');
    }
}
