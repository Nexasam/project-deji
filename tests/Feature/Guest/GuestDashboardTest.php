<?php

namespace Tests\Feature\Guest;

use App\Enums\BookingPaymentStatus;
use App\Enums\BookingStatus;
use App\Enums\IdentityVerificationStatus;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_dashboard_requires_authentication(): void
    {
        $this->get(route('guest.dashboard'))->assertRedirect('/login');
    }

    public function test_guest_dashboard_shows_reservations_and_next_steps(): void
    {
        $guest = User::factory()->create([
            'name' => 'Ayodeji Guest',
            'phone_number' => null,
            'identity_verification_status' => IdentityVerificationStatus::Unverified,
        ]);
        $otherGuest = User::factory()->create();

        $booking = Booking::factory()->create([
            'guest_user_id' => $guest->id,
            'reference' => 'VS-GUEST-HOME',
            'arrival_date' => now()->addDays(3)->toDateString(),
            'departure_date' => now()->addDays(6)->toDateString(),
            'status' => BookingStatus::Confirmed,
            'payment_status' => BookingPaymentStatus::Unpaid,
            'total_amount' => 250000,
        ]);
        Booking::factory()->create([
            'guest_user_id' => $otherGuest->id,
            'reference' => 'VS-OTHER-GUEST',
            'arrival_date' => now()->addDays(4)->toDateString(),
            'departure_date' => now()->addDays(7)->toDateString(),
            'status' => BookingStatus::Confirmed,
        ]);

        $this->actingAs($guest)
            ->get(route('guest.dashboard'))
            ->assertOk()
            ->assertSee('Welcome Ayodeji')
            ->assertSee('Bookings')
            ->assertSee('Messages')
            ->assertSee('Reviews')
            ->assertSee('Open bookings')
            ->assertSee('Open messages')
            ->assertSee('Message property team')
            ->assertDontSee('Welcome home Ayodeji')
            ->assertSee('Your reservations')
            ->assertSee('All (1)')
            ->assertSee('Arriving soon (1)')
            ->assertSee('Currently staying (0)')
            ->assertSee('ID verification required')
            ->assertSee('Add your phone number')
            ->assertSee('Review your booking payment')
            ->assertSee($booking->reference)
            ->assertDontSee('VS-OTHER-GUEST');
    }

    public function test_guest_dashboard_tabs_filter_reservations(): void
    {
        $guest = User::factory()->create([
            'identity_verification_status' => IdentityVerificationStatus::Verified,
            'phone_number' => '+2348012345678',
        ]);

        $arriving = Booking::factory()->create([
            'guest_user_id' => $guest->id,
            'reference' => 'VS-ARRIVING-SOON',
            'arrival_date' => now()->addDays(2)->toDateString(),
            'departure_date' => now()->addDays(5)->toDateString(),
            'status' => BookingStatus::Confirmed,
        ]);
        $completed = Booking::factory()->create([
            'guest_user_id' => $guest->id,
            'reference' => 'VS-COMPLETED-OLD',
            'arrival_date' => now()->subDays(12)->toDateString(),
            'departure_date' => now()->subDays(9)->toDateString(),
            'status' => BookingStatus::Completed,
        ]);

        $this->actingAs($guest)
            ->get(route('guest.dashboard', ['tab' => 'arriving']))
            ->assertOk()
            ->assertSee('aria-current="page"', false)
            ->assertSee('All (2)')
            ->assertSee('Arriving soon (1)')
            ->assertSee($arriving->reference)
            ->assertDontSee($completed->reference);
    }

    public function test_guest_dashboard_hides_add_phone_step_after_phone_is_saved(): void
    {
        $guest = User::factory()->create([
            'identity_verification_status' => IdentityVerificationStatus::Verified,
            'phone_number' => '+2348012345678',
        ]);

        $this->actingAs($guest)
            ->get(route('guest.dashboard'))
            ->assertOk()
            ->assertDontSee('Add your phone number');
    }
}
