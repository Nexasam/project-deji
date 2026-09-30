<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestBookingController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:upcoming,completed,cancelled,pending'],
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:newest,oldest,arrival'],
        ]);

        $base = Booking::query()->where('guest_user_id', $request->user()->id);
        $counts = [
            'total' => (clone $base)->count(),
            'upcoming' => (clone $base)->whereIn('status', ['reserved', 'awaiting_payment', 'confirmed', 'checked_in'])->count(),
            'completed' => (clone $base)->whereIn('status', ['checked_out', 'completed'])->count(),
            'cancelled' => (clone $base)->whereIn('status', ['cancelled', 'refunded', 'no_show'])->count(),
            'pending' => (clone $base)->where('status', 'awaiting_payment')->count(),
        ];

        $bookings = $base->with(['property.media', 'property.marketplaceListing'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('reference', 'like', "%{$term}%")
                ->orWhereHas('property', fn ($property) => $property->where('name', 'like', "%{$term}%")
                    ->orWhereHas('marketplaceListing', fn ($listing) => $listing->where('public_title', 'like', "%{$term}%")))))
            ->when(($filters['status'] ?? null) === 'upcoming', fn ($query) => $query->whereIn('status', ['reserved', 'awaiting_payment', 'confirmed', 'checked_in']))
            ->when(($filters['status'] ?? null) === 'completed', fn ($query) => $query->whereIn('status', ['checked_out', 'completed']))
            ->when(($filters['status'] ?? null) === 'cancelled', fn ($query) => $query->whereIn('status', ['cancelled', 'refunded', 'no_show']))
            ->when(($filters['status'] ?? null) === 'pending', fn ($query) => $query->where('status', 'awaiting_payment'))
            ->when(($filters['sort'] ?? 'newest') === 'oldest', fn ($query) => $query->oldest(), fn ($query) => $query->latest())
            ->when(($filters['sort'] ?? null) === 'arrival', fn ($query) => $query->reorder('arrival_date'))
            ->paginate(12)->withQueryString();

        return view('guest.bookings.index', compact('bookings', 'counts', 'filters'));
    }

    public function show(Request $request, string $booking): View
    {
        $booking = Booking::query()->where('guest_user_id', $request->user()->id)
            ->with(['business', 'property.media', 'property.amenities', 'property.marketplaceListing', 'payments', 'cancellations', 'statusHistory', 'serviceRequests.task', 'reviews.response', 'reviewInvitations'])
            ->findOrFail($booking);

        return view('guest.bookings.show', compact('booking'));
    }

    public function confirmation(Request $request, string $booking): View
    {
        $booking = Booking::query()->where('guest_user_id', $request->user()->id)
            ->with(['business', 'property.media', 'property.marketplaceListing', 'payments'])
            ->findOrFail($booking);

        return view('guest.bookings.confirmation', compact('booking'));
    }

    public function receipt(Request $request, string $booking): View
    {
        $booking = Booking::query()->where('guest_user_id', $request->user()->id)
            ->with(['business', 'guest', 'property.marketplaceListing', 'payments'])->findOrFail($booking);

        return view('guest.bookings.receipt', compact('booking'));
    }
}
