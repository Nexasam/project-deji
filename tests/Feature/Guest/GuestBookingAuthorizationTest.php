<?php

namespace Tests\Feature\Guest;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestBookingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_only_see_their_own_bookings(): void
    {
        $guest = User::factory()->create();
        $other = User::factory()->create();
        $own = Booking::factory()->create(['guest_user_id' => $guest->id]);
        $foreign = Booking::factory()->create(['guest_user_id' => $other->id]);

        $this->actingAs($guest)->get('/guest/bookings')->assertOk()->assertSee($own->reference)->assertDontSee($foreign->reference);
        $this->get("/guest/bookings/{$own->id}")->assertOk()->assertSee($own->reference);
        $this->get("/guest/bookings/{$foreign->id}")->assertNotFound();
    }

    public function test_guest_bookings_require_authentication(): void
    {
        $this->get('/guest/bookings')->assertRedirect('/login');
    }
}
