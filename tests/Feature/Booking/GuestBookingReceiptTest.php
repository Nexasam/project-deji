<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestBookingReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_print_authoritative_receipt_for_own_booking(): void
    {
        $guest = User::factory()->create();
        $property = Property::factory()->create(['name' => 'Waterfront Suite']);
        $booking = Booking::factory()->for($property->business)->for($property)->create([
            'guest_user_id' => $guest->id,
            'reference' => 'VS-RECEIPT-1',
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'subtotal_amount' => 100000,
            'discount_amount' => 5000,
            'total_amount' => 99750,
            'currency' => 'NGN',
        ]);
        Payment::factory()->for($booking->business)->for($booking)->create([
            'purpose' => 'deposit', 'amount' => 50000, 'currency' => 'NGN', 'status' => 'completed',
        ]);

        $this->actingAs($guest)->get(route('guest.bookings.receipt', $booking))
            ->assertOk()->assertSee('Booking receipt')->assertSee('VS-RECEIPT-1')
            ->assertSee('99,750')->assertSee('49,750');
    }

    public function test_guest_cannot_view_someone_elses_receipt(): void
    {
        $booking = Booking::factory()->create();

        $this->actingAs(User::factory()->create())->get(route('guest.bookings.receipt', $booking))->assertNotFound();
    }
}
