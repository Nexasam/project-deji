<?php

namespace App\Events;

use App\Models\BookingInteraction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class BookingMessageCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly BookingInteraction $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('booking.'.$this->message->booking_id)];
    }

    public function broadcastAs(): string
    {
        return 'booking.message.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'booking_id' => $this->message->booking_id,
            'sender_id' => $this->message->user_id,
            'sender' => $this->message->user?->name ?: 'User',
            'content' => $this->message->content,
            'time' => $this->message->occurred_at?->format('d M, H:i'),
        ];
    }
}
