@props(['property', 'filters' => []])

@php
    $coverMedia = $property->media->firstWhere('is_primary', true) ?? $property->media->firstWhere('media_type', \App\Enums\PropertyMediaType::Image);
    $coverImage = $coverMedia?->external_url ?: ($coverMedia?->storage_path ? '/storage/'.ltrim($coverMedia->storage_path, '/') : '/image.png');
    $blockedIntervals = $property->bookings->map(fn($booking) => ['start' => $booking->arrival_date->toDateString(), 'end' => $booking->departure_date->toDateString()])
        ->concat($property->availabilityBlocks->map(fn($block) => ['start' => $block->starts_on->toDateString(), 'end' => $block->ends_on->toDateString()]))->values();
    $hasDateRange = filled($filters['check_in'] ?? null) && filled($filters['check_out'] ?? null);
    $nights = $hasDateRange ? \Carbon\CarbonImmutable::parse($filters['check_in'])->diffInDays(\Carbon\CarbonImmutable::parse($filters['check_out'])) : 0;
    $subtotal = $nights > 0 ? (float) $property->default_nightly_price * $nights : 0;
    $promotion = $nights > 0 ? $property->promotions
        ->filter(fn($promotion) => $promotion->discount_type->value === 'percentage' && ($promotion->minimum_stay_nights ?? 1) <= $nights)
        ->sortByDesc(fn($promotion) => (float) $promotion->discount_value)
        ->first() : null;
    $discount = $promotion ? round($subtotal * ((float) $promotion->discount_value / 100)) : 0;
    $rangeTotal = max(0, $subtotal - $discount);
    $discountPercent = $promotion ? rtrim(rtrim((string)$promotion->discount_value, '0'), '.') : null;
    $dateRangeLabel = $hasDateRange && $nights > 0
        ? \Carbon\CarbonImmutable::parse($filters['check_in'])->format('M j').' - '.\Carbon\CarbonImmutable::parse($filters['check_out'])->format(\Carbon\CarbonImmutable::parse($filters['check_in'])->month === \Carbon\CarbonImmutable::parse($filters['check_out'])->month ? 'j' : 'M j')
        : null;
    $pricingNote = $hasDateRange && $nights > 0 ? $dateRangeLabel.' · '.$nights.' '.Str::plural('night', $nights) : null;
    $savingsLabel = $discount > 0 ? 'You save ₦'.number_format($discount).' with '.$discountPercent.'% longer-stay pricing' : null;
    $basePriceLabel = $hasDateRange && $nights > 0 ? 'Base: ₦'.number_format((float) $property->default_nightly_price).' / night' : null;
    $valueBadge = $discount > 0 ? 'Best value for '.$nights.' nights' : ($hasDateRange && $nights > 0 ? 'Selected dates total' : null);
    $hostSource = trim((string) ($property->business?->primary_contact_name ?: $property->owner_name ?: ''));
    $hostFirstName = filled($hostSource) ? Str::of($hostSource)->squish()->explode(' ')->first() : null;
    $hostDisplayName = $hostFirstName ?: 'Verified host';
    $hostingSince = $property->business?->onboarding_completed_at ?: $property->business?->created_at;
    $hostingYears = $hostingSince ? max(1, (int) floor($hostingSince->diffInYears(now()))) : null;
    $topAmenities = $property->amenities->take(3)->pluck('name')->values()->all();
    $ratingLabel = $property->published_reviews_count ? number_format((float) $property->published_reviews_avg_rating, 1).' from '.$property->published_reviews_count.' verified '.Str::plural('review', $property->published_reviews_count) : 'New verified listing';
    $insights = [
        'title' => $property->marketplaceListing->public_title,
        'host' => 'Hosted by '.$hostDisplayName,
        'hostMeta' => 'Verified Shortlet host'.($hostingYears ? ' · '.$hostingYears.' '.Str::plural('year', $hostingYears).' hosting' : ''),
        'locationTitle' => 'Stay in '.trim(data_get($property->address, 'city').', '.data_get($property->address, 'state'), ', '),
        'locationBody' => 'Verified serviced apartment with live availability and marketplace booking protection.',
        'accessTitle' => 'Easy whole-apartment stay',
        'accessBody' => 'Book the entire place, choose dates instantly and avoid double-booked calendar dates.',
        'qualityTitle' => $ratingLabel,
        'qualityBody' => $topAmenities ? 'Guest-ready highlights include '.implode(', ', $topAmenities).'.' : 'Verified listing details, calendar and host information are checked before publication.',
        'pricingTitle' => $discount > 0 ? 'Longer-stay value available' : 'Transparent pricing',
        'pricingBody' => $discount > 0 ? 'Selected dates save ₦'.number_format($discount).' with host-set longer-stay pricing.' : 'Adjust dates to compare totals and unlock any eligible host-set longer-stay offers.',
    ];
    $isFavourite = auth()->check()
        ? \App\Models\GuestFavourite::query()->where('user_id', auth()->id())->where('property_id', $property->id)->exists()
        : false;
@endphp

<div {{ $attributes->merge(['class' => 'relative w-[246px] shrink-0 sm:w-[260px]']) }} x-data="availabilityCalendar(@js($blockedIntervals), @js($property->marketplaceListing->public_title), @js(route('marketplace.show', $property->marketplaceListing->slug)))">
    <x-property-card
        :image="$coverImage"
        :name="$property->marketplaceListing->public_title"
        :location="data_get($property->address, 'city').', '.data_get($property->address, 'state')"
        :guests="$property->capacity"
        :price="(float) $property->default_nightly_price"
        :price-label="$hasDateRange && $nights > 0 ? '₦'.number_format($rangeTotal) : null"
        :price-unit="$hasDateRange && $nights > 0 ? 'total' : '/ night'"
        :pricing-note="$pricingNote"
        :base-price-label="$basePriceLabel"
        :savings-label="$savingsLabel"
        :value-badge="$valueBadge"
        :insights="$insights"
        :property-id="$property->id"
        :favourite-url="auth()->check() ? route('guest.favourites.store', $property) : null"
        :unfavourite-url="auth()->check() ? route('guest.favourites.destroy', $property) : null"
        :is-favourite="$isFavourite"
        :href="route('marketplace.show', $property->marketplaceListing->slug)"
        :rating="$property->published_reviews_count ? number_format((float) $property->published_reviews_avg_rating, 1) : 'New'"
    />
    <button type="button" @click="open=true" class="absolute right-2 top-12 z-10 flex size-9 items-center justify-center rounded-full bg-white text-orange-600 shadow-md ring-1 ring-black/5 transition hover:scale-105 hover:bg-orange-50" aria-label="View availability calendar for {{ $property->marketplaceListing->public_title }}" title="View availability">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" stroke-width="2"/><path d="M16 3v4M8 3v4M3 10h18" stroke-width="2" stroke-linecap="round"/></svg>
    </button>
    <div x-show="open" x-cloak @keydown.escape.window="open=false" class="fixed inset-0 z-[70] flex items-center justify-center p-4" role="dialog" aria-modal="true" :aria-label="'Availability for '+title">
        <button type="button" @click="open=false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" aria-label="Close calendar"></button>
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="sticky top-0 z-10 flex items-start justify-between border-b border-slate-100 bg-white px-5 py-4"><div><p class="text-xs font-bold uppercase tracking-wider text-orange-600">Availability calendar</p><h3 class="mt-1 text-xl font-extrabold" x-text="title"></h3></div><button type="button" @click="open=false" class="flex size-9 items-center justify-center rounded-full bg-slate-100 text-xl">×</button></div>
            <div class="p-5"><div class="mb-4 flex flex-wrap items-center justify-between gap-3"><div class="flex flex-wrap gap-4 text-xs font-semibold"><span class="flex items-center gap-2"><i class="size-3 rounded bg-emerald-200 ring-1 ring-emerald-300"></i>Available</span><span class="flex items-center gap-2"><i class="size-3 rounded bg-red-100 ring-1 ring-red-200"></i>Booked / unavailable</span></div><label class="flex items-center gap-2 text-xs font-bold text-slate-600">Jump to <input type="month" :min="minimumMonth" :max="maximumMonth" x-model="jumpMonth" @change="jumpToMonth" class="rounded-lg border-slate-200 py-1.5 text-xs"></label></div><div class="mb-5 flex items-center justify-between rounded-xl bg-slate-50 p-2"><button type="button" @click="previous" :disabled="offset===0" class="rounded-lg px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white disabled:cursor-not-allowed disabled:opacity-30">← Previous</button><p class="text-center text-xs font-semibold text-slate-500">Browse up to 18 months ahead</p><button type="button" @click="next" :disabled="offset>=17" class="rounded-lg px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white disabled:cursor-not-allowed disabled:opacity-30">Next →</button></div><div class="grid gap-6 md:grid-cols-2"><template x-for="month in months" :key="month.key"><section class="rounded-xl border border-slate-100 p-3"><h4 class="mb-3 text-center text-sm font-extrabold" x-text="month.label"></h4><div class="grid grid-cols-7 gap-1 text-center"><template x-for="day in ['S','M','T','W','T','F','S']"><span class="py-1 text-[10px] font-bold text-slate-400" x-text="day"></span></template><template x-for="blank in month.offset"><span></span></template><template x-for="day in month.days" :key="day.date"><span class="flex aspect-square items-center justify-center rounded-lg text-xs" :class="day.past?'text-slate-300':(day.blocked?'bg-red-100 font-bold text-red-700 line-through':'bg-emerald-200 font-bold text-emerald-900 ring-1 ring-emerald-300')" x-text="day.number" :title="day.blocked?'Booked or unavailable':'Available'"></span></template></div></section></template></div><a :href="detailsUrl" class="mt-6 flex w-full items-center justify-center rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white hover:bg-orange-700">View property and choose dates</a></div>
        </div>
    </div>
</div>
