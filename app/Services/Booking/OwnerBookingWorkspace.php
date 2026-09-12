<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class OwnerBookingWorkspace
{
    /** @param array<string, mixed> $filters */
    public function paginate(Business $business, array $filters): LengthAwarePaginator
    {
        return $business->bookings()->with(['guest', 'property'])
            ->when($filters['property'] ?? null, fn ($query, $id) => $query->where('property_id', $id))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['channel'] ?? null, fn ($query, $source) => $query->where('source', $source))
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('departure_date', '>', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('arrival_date', '<=', $date))
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(function ($query) use ($term): void {
                    $query->where('reference', 'like', "%{$term}%")
                        ->orWhereHas('guest', fn ($guest) => $guest->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('property', fn ($property) => $property->where('name', 'like', "%{$term}%"));
                });
            })
            ->when(($filters['sort'] ?? 'newest') === 'arrival', fn ($query) => $query->orderBy('arrival_date'), fn ($query) => $query->latest())
            ->paginate(20)->withQueryString();
    }

    /** @return array<string, int> */
    public function stats(Business $business): array
    {
        $query = $business->bookings();

        return [
            'total' => (clone $query)->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'pending' => (clone $query)->whereIn('status', ['enquiry', 'reserved', 'awaiting_payment'])->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'check_ins' => (clone $query)->whereDate('arrival_date', today())->count(),
            'check_outs' => (clone $query)->whereDate('departure_date', today())->count(),
        ];
    }

    public function find(Business $business, string $booking): Booking
    {
        return $business->bookings()->with(['guest', 'property', 'payments', 'cancellations', 'dateChanges' => fn ($query) => $query->latest('occurred_at')])->findOrFail($booking);
    }
}
