<?php

namespace Tests\Feature\Guest;

use App\Models\Booking;
use App\Models\BookingInteraction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_inbox_lists_only_their_booking_threads(): void
    {
        $guest = User::factory()->create();
        $otherGuest = User::factory()->create();
        $ownBooking = Booking::factory()->create(['guest_user_id' => $guest->id, 'reference' => 'VS-GUEST-MSG']);
        $otherBooking = Booking::factory()->create(['guest_user_id' => $otherGuest->id, 'reference' => 'VS-OTHER-MSG']);

        BookingInteraction::query()->create($this->messageAttributes($ownBooking, $guest, 'Hello from my booking'));
        BookingInteraction::query()->create($this->messageAttributes($otherBooking, $otherGuest, 'Hidden guest message'));

        $this->actingAs($guest)
            ->get(route('guest.messages.index'))
            ->assertOk()
            ->assertSee('VS-GUEST-MSG')
            ->assertSee('Hello from my booking')
            ->assertDontSee('VS-OTHER-MSG')
            ->assertDontSee('Hidden guest message');
    }

    public function test_guest_can_post_message_to_their_booking(): void
    {
        $guest = User::factory()->create();
        $booking = Booking::factory()->create(['guest_user_id' => $guest->id]);

        $this->actingAs($guest)
            ->post(route('guest.bookings.messages.store', $booking), [
                'content' => 'Please confirm the check-in instructions.',
            ])
            ->assertRedirect(route('guest.bookings.messages.show', $booking));

        $this->assertDatabaseHas('booking_interactions', [
            'booking_id' => $booking->id,
            'user_id' => $guest->id,
            'interaction_type' => 'message',
            'direction' => 'inbound',
            'channel' => 'platform',
            'delivery_status' => 'sent',
            'content' => 'Please confirm the check-in instructions.',
        ]);
    }

    public function test_messages_empty_state_allows_guest_to_start_from_existing_booking(): void
    {
        $guest = User::factory()->create();
        $booking = Booking::factory()->create([
            'guest_user_id' => $guest->id,
            'reference' => 'VS-START-MSG',
        ]);

        $this->actingAs($guest)
            ->get(route('guest.messages.index'))
            ->assertOk()
            ->assertSee('Start a conversation from one of your bookings below')
            ->assertSee('VS-START-MSG')
            ->assertSee('Message property team')
            ->assertSee(route('guest.bookings.messages.show', $booking), false);
    }

    public function test_guest_booking_list_has_message_action_for_each_booking(): void
    {
        $guest = User::factory()->create();
        $booking = Booking::factory()->create([
            'guest_user_id' => $guest->id,
            'reference' => 'VS-BOOKING-MSG-ACTION',
        ]);

        $this->actingAs($guest)
            ->get(route('guest.bookings.index'))
            ->assertOk()
            ->assertSee('VS-BOOKING-MSG-ACTION')
            ->assertSee('Message')
            ->assertSee(route('guest.bookings.messages.show', $booking), false);
    }

    public function test_guest_cannot_open_or_post_to_another_guest_booking_thread(): void
    {
        $guest = User::factory()->create();
        $otherBooking = Booking::factory()->create(['guest_user_id' => User::factory()->create()->id]);

        $this->actingAs($guest)
            ->get(route('guest.bookings.messages.show', $otherBooking))
            ->assertNotFound();

        $this->post(route('guest.bookings.messages.store', $otherBooking), [
            'content' => 'This should not be allowed.',
        ])->assertNotFound();
    }

    private function messageAttributes(Booking $booking, User $sender, string $content): array
    {
        return [
            'business_id' => $booking->business_id,
            'booking_id' => $booking->id,
            'user_id' => $sender->id,
            'interaction_type' => 'message',
            'direction' => 'inbound',
            'channel' => 'platform',
            'summary' => $content,
            'content' => $content,
            'is_internal' => false,
            'delivery_status' => 'sent',
            'sent_at' => now(),
            'occurred_at' => now(),
            'status' => 'active',
        ];
    }
}
