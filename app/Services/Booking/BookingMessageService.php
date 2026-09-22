<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingInteraction;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use Illuminate\Support\Str;

final readonly class BookingMessageService
{
    public function __construct(private ProductNotificationService $notifications) {}

    public function guestMessage(Booking $booking, User $guest, string $content): BookingInteraction
    {
        $message = $this->create($booking, $guest, null, $content, 'inbound');

        $this->notifications->businessOwners(
            $booking->business,
            'booking_message',
            'New guest message',
            "{$guest->name} sent a message about {$booking->property->name}.",
            ['url' => route('owner.bookings.messages.show', $booking), 'booking_id' => $booking->id, 'message_id' => $message->id]
        );

        return $message;
    }

    public function ownerMessage(Booking $booking, User $sender, string $content): BookingInteraction
    {
        $message = $this->create($booking, $sender, $booking->guest, $content, 'outbound');

        $this->notifications->user(
            $booking->guest,
            $booking->business,
            'booking_message',
            'New message from your host',
            "Your host sent a message about {$booking->property->name}.",
            ['url' => route('guest.bookings.messages.show', $booking), 'booking_id' => $booking->id, 'message_id' => $message->id]
        );

        return $message;
    }

    private function create(Booking $booking, User $sender, ?User $recipient, string $content, string $direction): BookingInteraction
    {
        $content = trim($content);

        return BookingInteraction::query()->create([
            'business_id' => $booking->business_id,
            'booking_id' => $booking->id,
            'user_id' => $sender->id,
            'recipient_user_id' => $recipient?->id,
            'interaction_type' => 'message',
            'direction' => $direction,
            'channel' => 'platform',
            'recipient_name' => $recipient?->name,
            'recipient_address' => $recipient?->email,
            'summary' => Str::limit($content, 120),
            'content' => $content,
            'is_internal' => false,
            'delivery_status' => 'sent',
            'sent_at' => now(),
            'occurred_at' => now(),
            'status' => 'active',
            'created_by' => $sender->id,
            'updated_by' => $sender->id,
        ]);
    }
}
