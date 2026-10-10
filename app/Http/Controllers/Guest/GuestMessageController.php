<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Booking\BookingMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestMessageController extends Controller
{
    public function index(Request $request): View
    {
        $startableBookings = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->with(['property.marketplaceListing', 'property.media'])
            ->latest()
            ->limit(6)
            ->get();

        $bookings = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->whereHas('interactions', fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false))
            ->with(['property.marketplaceListing', 'property.media', 'guest', 'interactions' => fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false)->latest('occurred_at')])
            ->withMax(['interactions as latest_message_at' => fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false)], 'occurred_at')
            ->orderByDesc('latest_message_at')
            ->paginate(12);

        return view('guest.messages.index', compact('bookings', 'startableBookings'));
    }

    public function show(Request $request, string $booking): View
    {
        $booking = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->with(['business', 'property.marketplaceListing', 'property.media', 'guest', 'interactions' => fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false)->orderBy('occurred_at')])
            ->findOrFail($booking);

        return view('guest.messages.show', compact('booking'));
    }

    public function store(Request $request, string $booking, BookingMessageService $messages): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $booking = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->with(['business', 'property', 'guest'])
            ->findOrFail($booking);

        $message = $messages->guestMessage($booking, $request->user(), $data['content']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'time' => $message->occurred_at->format('H:i'),
                    'sender' => 'You',
                ],
            ], 201);
        }

        return redirect()->route('guest.bookings.messages.show', $booking)->with('status', 'Message sent to the property team.');
    }
}
