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

        $trips = Booking::query()
            ->where('guest_user_id', $request->user()->id)
            ->whereDate('departure_date', '>=', today()->toDateString())
            ->with(['property.media', 'property.marketplaceListing'])
            ->orderBy('arrival_date')
            ->limit(4)
            ->get();

        return view('guest.calendar', [
            'month' => $month,
            'bookings' => $bookings,
            'trips' => $trips,
        ]);
    }
}
