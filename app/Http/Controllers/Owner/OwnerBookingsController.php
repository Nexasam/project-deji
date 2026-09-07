<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\ActiveBusinessContext;
use Illuminate\View\View;

class OwnerBookingsController extends Controller
{
    public function __invoke(ActiveBusinessContext $context): View
    {
        $business = $context->business;

        // ── KPI stats ─────────────────────────────────────────────────────────
        $stats = [
            ['label' => 'Total bookings',  'value' => 6,  'highlight' => false],
            ['label' => 'Confirmed',        'value' => 4,  'highlight' => false],
            ['label' => 'Pending',          'value' => 2,  'highlight' => true],   // orange
            ['label' => 'Cancelled',        'value' => 1,  'highlight' => false],
            ['label' => 'Check-ins today',  'value' => 1,  'highlight' => true],
            ['label' => 'Check-outs today', 'value' => 0,  'highlight' => false],
        ];

        // ── Bookings table ────────────────────────────────────────────────────
        $bookings = [
            [
                'id'       => 'VS-28610',
                'guest'    => 'Tariye Fubara',
                'avatar'   => null,
                'property' => 'Lekki Waterview Suites',
                'location' => 'Egbeda, Lagos',
                'dates'    => '22 Aug – 24 Aug',
                'nights'   => '2 nights',
                'channel'  => 'Direct - Walk-in',
                'channel_style' => 'direct-walkin',
                'status'   => 'Pending',
                'amount'   => '₦82,000',
                'paid'     => 'Unpaid',
            ],
            [
                'id'       => 'VS-28610',
                'guest'    => 'Sammy T',
                'avatar'   => null,
                'property' => 'Lekki Waterview Suites',
                'location' => 'Egbeda, Lagos',
                'dates'    => '2 Sept – 8 Sept',
                'nights'   => '4 nights',
                'channel'  => 'Bookings.com',
                'channel_style' => 'bookingcom',
                'status'   => 'Pending',
                'amount'   => '₦182,000',
                'paid'     => 'Unpaid',
            ],
            [
                'id'       => 'VS-28610',
                'guest'    => 'Tariye Fubara',
                'avatar'   => null,
                'property' => 'Lekki Waterview Suites',
                'location' => 'Egbeda, Lagos',
                'dates'    => '19 Aug – 24 Aug',
                'nights'   => '5 nights',
                'channel'  => 'Offline/manual',
                'channel_style' => 'manual',
                'status'   => 'Confirmed',
                'amount'   => '₦282,000',
                'paid'     => 'Paid in full',
            ],
            [
                'id'       => 'VS-28610',
                'guest'    => 'Sammy T',
                'avatar'   => null,
                'property' => 'Lekki Waterview Suites',
                'location' => 'Egbeda, Lagos',
                'dates'    => '22 Aug – 24 Aug',
                'nights'   => '2 nights',
                'channel'  => 'Direct - Phone-in',
                'channel_style' => 'direct-phonein',
                'status'   => 'Completed',
                'amount'   => '₦182,000',
                'paid'     => 'Partially paid',
            ],
            [
                'id'       => 'VS-28610',
                'guest'    => 'Airbnb Guest',
                'avatar'   => null,
                'property' => 'Lekki Waterview Suites',
                'location' => 'Egbeda, Lagos',
                'dates'    => '22 Aug – 24 Aug',
                'nights'   => '2 nights',
                'channel'  => 'Airbnb',
                'channel_style' => 'airbnb',
                'status'   => 'Pending',
                'amount'   => '₦82,000',
                'paid'     => 'Unpaid',
            ],
            [
                'id'       => 'VS-28610',
                'guest'    => 'Booking.com Guest',
                'avatar'   => null,
                'property' => 'Lekki Waterview Suites',
                'location' => 'Egbeda, Lagos',
                'dates'    => '22 Aug – 24 Aug',
                'nights'   => '2 nights',
                'channel'  => 'Bookings.com',
                'channel_style' => 'bookingcom',
                'status'   => 'Cancelled',
                'amount'   => '₦82,000',
                'paid'     => 'Unpaid',
            ],
        ];

        view()->share('activeBusiness', $business);

        return view('owner.bookings', compact('business', 'stats', 'bookings'));
    }
}
