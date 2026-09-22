<?php

namespace App\Http\Controllers\Guest;

use App\Enums\BookingPaymentStatus;
use App\Enums\BookingStatus;
use App\Enums\IdentityVerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $today = today();

        $baseQuery = Booking::query()
            ->where('guest_user_id', $user->id);

        $activeStatuses = [
            BookingStatus::Reserved,
            BookingStatus::AwaitingPayment,
            BookingStatus::Confirmed,
            BookingStatus::CheckedIn,
        ];

        $tabs = collect([
            'all' => ['label' => 'All'],
            'arriving' => ['label' => 'Arriving soon'],
            'checking_out' => ['label' => 'Checking out'],
            'current' => ['label' => 'Currently staying'],
        ]);
        $activeTab = $request->string('tab')->toString();
        $activeTab = $tabs->has($activeTab) ? $activeTab : 'all';

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'arriving' => (clone $baseQuery)
                ->whereIn('status', $activeStatuses)
                ->whereBetween('arrival_date', [$today->toDateString(), $today->copy()->addDays(14)->toDateString()])
                ->count(),
            'checking_out' => (clone $baseQuery)
                ->where('status', BookingStatus::CheckedIn)
                ->whereBetween('departure_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])
                ->count(),
            'current' => (clone $baseQuery)
                ->where('status', BookingStatus::CheckedIn)
                ->count(),
        ];

        $bookingsQuery = (clone $baseQuery)
            ->with(['property.media', 'property.marketplaceListing', 'serviceRequests', 'reviews']);

        match ($activeTab) {
            'arriving' => $bookingsQuery
                ->whereIn('status', $activeStatuses)
                ->whereBetween('arrival_date', [$today->toDateString(), $today->copy()->addDays(14)->toDateString()]),
            'checking_out' => $bookingsQuery
                ->where('status', BookingStatus::CheckedIn)
                ->whereBetween('departure_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()]),
            'current' => $bookingsQuery->where('status', BookingStatus::CheckedIn),
            default => null,
        };

        $bookings = $bookingsQuery
            ->orderByRaw('arrival_date >= ? desc', [$today->toDateString()])
            ->orderBy('arrival_date')
            ->limit(6)
            ->get();

        $messageThreads = (clone $baseQuery)
            ->whereHas('interactions', fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false))
            ->with(['property.marketplaceListing', 'interactions' => fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false)->latest('occurred_at')])
            ->withMax(['interactions as latest_message_at' => fn ($query) => $query->where('interaction_type', 'message')->where('is_internal', false)], 'occurred_at')
            ->orderByDesc('latest_message_at')
            ->limit(3)
            ->get();

        $resumeBooking = (clone $baseQuery)
            ->with(['property.marketplaceListing'])
            ->whereIn('status', [BookingStatus::AwaitingPayment, BookingStatus::Reserved, BookingStatus::Confirmed, BookingStatus::CheckedIn])
            ->orderByRaw('arrival_date >= ? desc', [$today->toDateString()])
            ->orderBy('arrival_date')
            ->first();

        $nextSteps = collect();

        if ($user->identity_verification_status !== IdentityVerificationStatus::Verified) {
            $nextSteps->push([
                'title' => 'ID verification required',
                'description' => 'Complete your guest profile before your next verified stay.',
                'action' => 'Verify now',
                'url' => route('profile.edit'),
                'tone' => 'dark',
            ]);
        }

        if (blank($user->phone_number)) {
            $nextSteps->push([
                'title' => 'Add your phone number',
                'description' => 'Hosts use this for arrival coordination and urgent stay updates.',
                'action' => 'Add phone',
                'url' => route('profile.edit'),
                'tone' => 'light',
            ]);
        }

        if ($resumeBooking && $resumeBooking->payment_status !== BookingPaymentStatus::Paid) {
            $nextSteps->push([
                'title' => 'Review your booking payment',
                'description' => $resumeBooking->reference.' may still need payment confirmation.',
                'action' => 'Open booking',
                'url' => route('guest.bookings.show', $resumeBooking),
                'tone' => 'light',
            ]);
        }

        $reviewBooking = (clone $baseQuery)
            ->where('status', BookingStatus::Completed)
            ->whereDoesntHave('reviews')
            ->latest('departure_date')
            ->first();

        if ($reviewBooking) {
            $nextSteps->push([
                'title' => 'Share a verified review',
                'description' => 'Help future guests decide with confidence after your completed stay.',
                'action' => 'Review stay',
                'url' => route('guest.bookings.show', $reviewBooking),
                'tone' => 'light',
            ]);
        }

        return view('guest.dashboard', [
            'bookings' => $bookings,
            'messageThreads' => $messageThreads,
            'activeTab' => $activeTab,
            'counts' => $counts,
            'resumeBooking' => $resumeBooking,
            'reviewBooking' => $reviewBooking,
            'nextSteps' => $nextSteps->take(4),
            'tabs' => $tabs,
        ]);
    }
}
