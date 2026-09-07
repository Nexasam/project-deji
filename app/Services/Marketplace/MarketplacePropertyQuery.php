<?php

namespace App\Services\Marketplace;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class MarketplacePropertyQuery
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = $this->eligible()->with(['marketplaceListing', 'media', 'amenities']);
        $category = $filters['category'] ?? null;
        $query->when($filters['q'] ?? null, function ($query, string $term): void {
            $query->where(function ($query) use ($term): void {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('address', 'like', "%{$term}%")
                    ->orWhereHas('marketplaceListing', fn ($listing) => $listing->where('public_title', 'like', "%{$term}%"));
            });
        })->when(in_array($category, ['lekki', 'ikoyi', 'victoria-island'], true), function ($query) use ($category): void {
            $area = ['lekki' => 'Lekki', 'ikoyi' => 'Ikoyi', 'victoria-island' => 'Victoria Island'][$category];
            $query->where('address', 'like', "%{$area}%");
        })->when(in_array($category, ['beachfront', 'family', 'business'], true),
            fn ($query) => $query->whereHas('marketplaceListing', fn ($listing) => $listing->whereJsonContains('stay_categories', $category))
        )->when($filters['min_price'] ?? null, fn ($query, $value) => $query->where('default_nightly_price', '>=', $value))
            ->when($filters['max_price'] ?? null, fn ($query, $value) => $query->where('default_nightly_price', '<=', $value))
            ->when($filters['beds'] ?? null, fn ($query, $value) => $query->where('beds', '>=', $value))
            ->when($filters['guests'] ?? null, fn ($query, $value) => $query->where('capacity', '>=', $value));

        return $query->orderBy('name')->paginate(100)->withQueryString();
    }

    public function eligibleBySlug(string $slug): Property
    {
        return $this->eligible()->with(['marketplaceListing', 'media', 'amenities'])
            ->whereHas('marketplaceListing', fn ($query) => $query->where('slug', $slug))->firstOrFail();
    }

    private function eligible()
    {
        return Property::query()->where([
            'property_type' => 'serviced_apartment', 'booking_mode' => 'entire',
            'verification_status' => 'verified', 'publication_status' => 'published',
            'readiness_status' => 'ready', 'operational_status' => 'available', 'status' => 'active',
        ])->whereHas('marketplaceListing', fn ($query) => $query->where([
            'publication_status' => 'published', 'is_publication_eligible' => true, 'status' => 'active',
        ]));
    }
}
