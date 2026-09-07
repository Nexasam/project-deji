<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Support\ActiveBusinessContext;
use Illuminate\View\View;

class OwnerBookingsController extends Controller
{
    public function __invoke(ActiveBusinessContext $context): View
    {
        $business = $context->business;
        $query = Booking::query()->where('business_id', $business->id);
        $stats = [
            ['label' => 'Total bookings', 'value' => (clone $query)->count(), 'highlight' => false],
            ['label' => 'Confirmed', 'value' => (clone $query)->where('status', 'confirmed')->count(), 'highlight' => false],
            ['label' => 'Pending', 'value' => (clone $query)->whereIn('status', ['enquiry', 'reserved', 'awaiting_payment'])->count(), 'highlight' => true],
            ['label' => 'Cancelled', 'value' => (clone $query)->where('status', 'cancelled')->count(), 'highlight' => false],
            ['label' => 'Check-ins today', 'value' => (clone $query)->whereDate('arrival_date', today())->count(), 'highlight' => true],
            ['label' => 'Check-outs today', 'value' => (clone $query)->whereDate('departure_date', today())->count(), 'highlight' => false],
        ];
        $bookings = $query->with(['guest', 'property'])->latest()->limit(20)->get()->map(fn (Booking $booking): array => [
            'id' => $booking->reference, 'guest' => $booking->guest->name, 'avatar' => null, 'property' => $booking->property->name,
            'location' => data_get($booking->property->address, 'city').', '.data_get($booking->property->address, 'state'),
            'dates' => $booking->arrival_date->format('j M').' – '.$booking->departure_date->format('j M'),
            'nights' => $booking->arrival_date->diffInDays($booking->departure_date).' nights', 'channel' => 'Verified Shortlet',
            'channel_style' => 'marketplace', 'status' => ucfirst($booking->status->value), 'amount' => '₦'.number_format((float) $booking->total_amount),
            'paid' => $booking->payment_status->value === 'paid' ? 'Paid in full' : ucwords(str_replace('_', ' ', $booking->payment_status->value)),
        ])->all();
        view()->share('activeBusiness', $business);

        return view('owner.bookings', compact('business', 'stats', 'bookings'));
    }
}
