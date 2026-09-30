<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Reviews\VerifiedStayReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuestReviewController extends Controller
{
    public function store(Request $request, string $booking, VerifiedStayReviewService $reviews): RedirectResponse
    {
        $record = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->findOrFail($booking);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'cleanliness_rating' => ['nullable', 'integer', 'between:1,5'],
            'communication_rating' => ['nullable', 'integer', 'between:1,5'],
            'location_rating' => ['nullable', 'integer', 'between:1,5'],
            'value_rating' => ['nullable', 'integer', 'between:1,5'],
            'accuracy_rating' => ['nullable', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'content' => ['required', 'string', 'min:20', 'max:3000'],
        ]);

        $reviews->submit($record, $request->user(), $data);

        return redirect()->route('guest.bookings.show', $record)
            ->with('status', 'Thank you. Your verified-stay review is now live.');
    }
}
