<?php

namespace App\Services\Dashboard;

use App\Models\Business;
use App\Models\User;

final class OwnerDashboardSummary
{
    /** @param array<int, string>|null $propertyIds */
    public function build(Business $business, ?User $viewer = null, ?array $propertyIds = null): array
    {
        $properties = $business->properties()
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('id', $propertyIds));
        $bookings = $business->bookings()
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds));
        $tasks = $business->operationalTasks()
            ->when($propertyIds !== null, fn ($query) => $query->whereIn('property_id', $propertyIds));
        $payments = $business->payments()
            ->when($propertyIds !== null, fn ($query) => $query->whereHas(
                'booking',
                fn ($booking) => $booking->whereIn('property_id', $propertyIds)
            ));
        $completed = (clone $payments)->where('status', 'completed');
        $revenue = (float) (clone $completed)->where('purpose', '!=', 'refund')->sum('amount')
            - (float) (clone $completed)->where('purpose', 'refund')->sum('amount');
        $todayTasks = (clone $tasks)->with('property')->whereNotIn('status', ['completed', 'cancelled'])
            ->whereDate('due_at', today())->orderBy('due_at')->get();
        $todayPayments = (clone $payments)->where('status', 'completed')->whereDate('transaction_at', today())->get();

        return [
            'propertyCount' => (clone $properties)->count(),
            'publishedProperties' => (clone $properties)->where('publication_status', 'published')->count(),
            'upcomingArrivals' => (clone $bookings)->whereIn('status', ['reserved', 'awaiting_payment', 'confirmed'])->whereBetween('arrival_date', [today(), today()->addDays(30)])->count(),
            'upcomingDepartures' => (clone $bookings)->whereIn('status', ['confirmed', 'checked_in'])->whereBetween('departure_date', [today(), today()->addDays(30)])->count(),
            'receivedRevenue' => $revenue,
            'unpaidBookings' => (clone $bookings)->whereIn('payment_status', ['unpaid', 'partially_paid'])->whereNotIn('status', ['cancelled', 'refunded'])->count(),
            'openTasks' => (clone $tasks)->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'urgentTasks' => (clone $tasks)->where('priority', 'urgent')->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'today' => [
                'arrivals' => (clone $bookings)->with(['guest', 'property'])->whereIn('status', ['reserved', 'awaiting_payment', 'confirmed'])->whereDate('arrival_date', today())->get(),
                'departures' => (clone $bookings)->with(['guest', 'property'])->whereIn('status', ['confirmed', 'checked_in'])->whereDate('departure_date', today())->get(),
                'tasks' => $todayTasks,
                'payments_count' => $todayPayments->count(),
                'payments_total' => (float) $todayPayments->reject(fn ($payment) => $payment->purpose->value === 'refund')->sum('amount')
                    - (float) $todayPayments->filter(fn ($payment) => $payment->purpose->value === 'refund')->sum('amount'),
                'unread_alerts' => $viewer?->notifications()->whereNull('read_at')->count() ?? 0,
            ],
        ];
    }
}
