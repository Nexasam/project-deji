<?php

namespace Tests\Feature\Guest;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_calendar_requires_authentication(): void
    {
        $this->get(route('guest.calendar'))->assertRedirect('/login');
    }

    public function test_guest_calendar_only_shows_the_authenticated_guests_trips(): void
    {
        $guest = User::factory()->create();
        $otherGuest = User::factory()->create();
        $month = now()->addMonth()->startOfMonth();

        $booking = Booking::factory()->create([
            'guest_user_id' => $guest->id,
            'reference' => 'VS-MY-CALENDAR',
            'arrival_date' => $month->copy()->addDays(3),
            'departure_date' => $month->copy()->addDays(6),
            'status' => BookingStatus::Confirmed,
        ]);

        Booking::factory()->create([
            'guest_user_id' => $otherGuest->id,
            'reference' => 'VS-OTHER-CALENDAR',
            'arrival_date' => $month->copy()->addDays(4),
            'departure_date' => $month->copy()->addDays(7),
            'status' => BookingStatus::Confirmed,
        ]);

        $this->actingAs($guest)
            ->get(route('guest.calendar', ['month' => $month->format('Y-m')]))
            ->assertOk()
            ->assertSee('Calendar')
            ->assertSee($booking->property->name)
            ->assertDontSee('VS-OTHER-CALENDAR');
    }

    public function test_guest_calendar_rejects_an_invalid_month(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('guest.calendar', ['month' => 'not-a-month']))
            ->assertSessionHasErrors('month');
    }
}
