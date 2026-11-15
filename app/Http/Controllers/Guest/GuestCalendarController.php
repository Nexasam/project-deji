<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestCalendarController extends Controller
{
    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'view' => ['nullable', 'in:today,upcoming,completed,cancelled'],
        ]);

        $month = isset($validated['month'])
            ? CarbonImmutable::createFromFormat('Y-m', $validated['month'])->startOfMonth()
            : CarbonImmutable::today()->startOfMonth();

        $bookings = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->whereDate('arrival_date', '<=', $month->endOfMonth()->toDateString())
            ->whereDate('departure_date', '>=', $month->startOfMonth()->toDateString())
            ->with(['property.media', 'property.marketplaceListing'])
            ->orderBy('arrival_date')
            ->get();

        $allTrips = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->with(['property.media', 'property.marketplaceListing'])
            ->orderBy('arrival_date')
            ->get();
        $view = $validated['view'] ?? 'upcoming';
        $trips = $allTrips->filter(function (Booking $booking) use ($view): bool {
            return match ($view) {
                'today' => $booking->arrival_date->isToday() || ($booking->arrival_date->isPast() && $booking->departure_date->isFuture()),
                'completed' => in_array($booking->status->value, ['checked_out', 'completed'], true),
                'cancelled' => in_array($booking->status->value, ['cancelled', 'refunded', 'no_show'], true),
                default => $booking->departure_date->gte(today()) && ! in_array($booking->status->value, ['cancelled', 'refunded', 'no_show'], true),
            };
        })->take(6)->values();

        return view('guest.calendar', [
            'month' => $month,
            'bookings' => $bookings,
            'trips' => $trips,
            'view' => $view,
        ]);
    }
}
