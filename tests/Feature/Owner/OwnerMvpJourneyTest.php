<?php

namespace Tests\Feature\Owner;

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerMvpJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_payment_calendar_task_and_dashboard_share_authoritative_records(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $guest=User::factory()->create();
        $this->actingAs($guest)->post('/stays/lekki-admiralty-waterfront/checkout',['arrival_date'=>'2027-04-10','departure_date'=>'2027-04-12','adult_count'=>1,'child_count'=>0,'idempotency_key'=>'owner-mvp-journey'])->assertRedirect();
        $booking=Booking::query()->sole();
        $owner=User::query()->where('email','owner@lagoonstays.test')->firstOrFail();
        $property=Property::query()->where('name','Admiralty Waterfront Residence')->firstOrFail();

        $this->actingAs($owner)->get(route('owner.calendar',['month'=>'2027-04']))->assertOk()->assertSee($booking->reference);
        $this->post(route('owner.operations.tasks.store'),['property_id'=>$property->id,'title'=>'Prepare for '.$booking->reference,'task_type'=>'guest_welcome','priority'=>'urgent','due_at'=>'2027-04-10T10:00'])->assertRedirect(route('owner.operations'));
        $this->get(route('owner.operations'))->assertOk()->assertSee('Prepare for '.$booking->reference);
        $this->get(route('owner.dashboard'))->assertOk()->assertSee('Open tasks')->assertSee('Urgent tasks')->assertSee('₦199,500.00');
    }
}
