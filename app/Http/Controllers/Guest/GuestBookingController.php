<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class GuestBookingController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:upcoming,completed,cancelled,pending'],
            'q' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sort' => ['nullable', 'in:newest,oldest,arrival'],
        ]);

        $base = Booking::query()->where('guest_user_id', $request->user()->id);
        $activeStatuses = ['reserved', 'awaiting_payment', 'confirmed', 'checked_in'];
        $cancelledStatuses = ['cancelled', 'refunded', 'no_show'];
        $today = today()->toDateString();
        $counts = [
            'total' => (clone $base)->count(),
            'upcoming' => (clone $base)->whereIn('status', $activeStatuses)->whereDate('arrival_date', '>', $today)->count(),
            'completed' => (clone $base)->whereNotIn('status', $cancelledStatuses)->where(function ($query) use ($today): void {
                $query->whereDate('departure_date', '<=', $today)->orWhereIn('status', ['checked_out', 'completed']);
            })->count(),
            'cancelled' => (clone $base)->whereIn('status', $cancelledStatuses)->count(),
            'pending' => (clone $base)->where('status', 'awaiting_payment')->count(),
        ];

        $bookings = $base->with(['property.media', 'property.marketplaceListing'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('reference', 'like', "%{$term}%")
                ->orWhereHas('property', fn ($property) => $property->where('name', 'like', "%{$term}%")
                    ->orWhereHas('marketplaceListing', fn ($listing) => $listing->where('public_title', 'like', "%{$term}%")))))
            ->when(($filters['status'] ?? null) === 'upcoming', fn ($query) => $query->whereIn('status', $activeStatuses)->whereDate('arrival_date', '>', $today))
            ->when(($filters['status'] ?? null) === 'completed', fn ($query) => $query->whereNotIn('status', $cancelledStatuses)->where(function ($query) use ($today): void {
                $query->whereDate('departure_date', '<=', $today)->orWhereIn('status', ['checked_out', 'completed']);
            }))
            ->when(($filters['status'] ?? null) === 'cancelled', fn ($query) => $query->whereIn('status', $cancelledStatuses))
            ->when(($filters['status'] ?? null) === 'pending', fn ($query) => $query->where('status', 'awaiting_payment'))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('departure_date', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('arrival_date', '<=', $to))
            ->when(($filters['sort'] ?? 'newest') === 'oldest', fn ($query) => $query->oldest(), fn ($query) => $query->latest())
            ->when(($filters['sort'] ?? null) === 'arrival', fn ($query) => $query->reorder('arrival_date'))
            ->paginate(12)->withQueryString();

        $data = compact('bookings', 'counts', 'filters');

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('guest.bookings.index', $data)->render(),
            ]);
        }

        return view('guest.bookings.index', $data);
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
