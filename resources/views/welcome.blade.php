@extends('layouts.app')

@section('content')
    {{-- Auth Modals --}}
    <x-auth.login-modal />
    <x-auth.signup-modal />

    {{-- Toast Notification --}}
    <x-toast />

    {{-- AI Insights Modal --}}
    <x-modal id="insights-modal" title="AI Insights">
        <p>This property has been verified through our AI-powered inspection process.</p>
        <p>Key highlights include excellent maintenance standards, accurate photo representation, and responsive host communication.</p>
        <p>Recent guests rated the cleanliness at 4.8/5 and noted the property matches the listing description perfectly.</p>
    </x-modal>

    {{-- Navigation --}}
    <x-navbar />

    {{-- Hero Section --}}
    <x-hero 
        badge="Verification-first marketplace"
        title="Find a stay you don't have to second-guess."
        highlightText="second-guess."
        description="Browse shortlets across Lagos that have already passed ID checks and an on-site inspection. Every listing carries an AI-generated insight so you know what a photo alone won't tell you."
        :stats="[
            ['value' => '12,000+', 'label' => 'Verified Listings'],
            ['value' => '28', 'label' => 'Cities Covered'],
            ['value' => '< 48h', 'label' => 'Avg. Verification Time'],
        ]"
        backgroundImage="/image.png"
        :heroImages="['/hero1.jpg', '/hero2.jpg', '/hero3.jpg']"
    />

    {{-- Search Bar --}}
    <x-search-bar :filters="$filters" />

    {{-- Category Pills --}}
    <x-category-pills :filters="$filters" />

    <form action="{{ route('home').'#marketplace' }}" method="GET" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-5 grid grid-cols-2 md:grid-cols-6 gap-3" aria-label="Search serviced apartments">
        @foreach(['check_in', 'check_out', 'guests'] as $preservedFilter) @if(filled($filters[$preservedFilter] ?? null))<input type="hidden" name="{{ $preservedFilter }}" value="{{ $filters[$preservedFilter] }}">@endif @endforeach
        <input class="col-span-2 rounded-xl border-gray-300" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search apartment or location">
        <select class="rounded-xl border-gray-300" name="category">
            @foreach(['all'=>'All stays','lekki'=>'Lekki','ikoyi'=>'Ikoyi','victoria-island'=>'Victoria Island','beachfront'=>'Beachfront','family'=>'Family stays','business'=>'Business stays'] as $value => $label)
                <option value="{{ $value }}" @selected(($filters['category'] ?? 'all') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <input class="rounded-xl border-gray-300" type="number" min="0" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min price">
        <input class="rounded-xl border-gray-300" type="number" min="0" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max price">
        <select class="rounded-xl border-gray-300" name="beds">
            <option value="">Any beds</option>
            @foreach(range(1, 5) as $beds)<option value="{{ $beds }}" @selected((string)($filters['beds'] ?? '') === (string)$beds)>{{ $beds }}+ beds</option>@endforeach
        </select>
        <button class="col-span-2 md:col-span-6 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold py-3">Search stays</button>
    </form>

    {{-- Backend-powered marketplace inventory --}}
    <section id="marketplace" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-orange-500">Live marketplace</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mt-1">
                    {{ $properties->total() }} serviced {{ Str::plural('apartment', $properties->total()) }}
                </h2>
            </div>
        </div>
        @if($properties->isEmpty())
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-10 text-center text-gray-600">No serviced apartments match these filters.</div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                @foreach($properties as $property)
                    @php
                        $coverMedia = $property->media->firstWhere('is_primary', true) ?? $property->media->firstWhere('media_type', \App\Enums\PropertyMediaType::Image);
                        $coverImage = $coverMedia?->external_url ?: ($coverMedia?->storage_path ? '/storage/'.ltrim($coverMedia->storage_path, '/') : '/image.png');
                        $blockedIntervals = $property->bookings->map(fn($booking) => ['start' => $booking->arrival_date->toDateString(), 'end' => $booking->departure_date->toDateString()])
                            ->concat($property->availabilityBlocks->map(fn($block) => ['start' => $block->starts_on->toDateString(), 'end' => $block->ends_on->toDateString()]))->values();
                    @endphp
                    <div class="relative" x-data="availabilityCalendar(@js($blockedIntervals), @js($property->marketplaceListing->public_title), @js(route('marketplace.show', $property->marketplaceListing->slug)))">
                    <a href="{{ route('marketplace.show', $property->marketplaceListing->slug) }}" class="block">
                        <x-property-card
                            :image="$coverImage"
                            :name="$property->marketplaceListing->public_title"
                            :location="data_get($property->address, 'city').', '.data_get($property->address, 'state')"
                            :guests="$property->capacity"
                            :price="(float) $property->default_nightly_price"
                            :rating="$property->published_reviews_count ? number_format((float) $property->published_reviews_avg_rating, 1) : 'New'"
                        />
                    </a>
                    <button type="button" @click="open=true" class="absolute right-2 top-12 z-10 flex size-9 items-center justify-center rounded-full bg-white text-orange-600 shadow-md ring-1 ring-black/5 transition hover:scale-105 hover:bg-orange-50" aria-label="View availability calendar for {{ $property->marketplaceListing->public_title }}" title="View availability">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" stroke-width="2"/><path d="M16 3v4M8 3v4M3 10h18" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <div x-show="open" x-cloak @keydown.escape.window="open=false" class="fixed inset-0 z-[70] flex items-center justify-center p-4" role="dialog" aria-modal="true" :aria-label="'Availability for '+title">
                        <button type="button" @click="open=false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" aria-label="Close calendar"></button>
                        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
                            <div class="sticky top-0 z-10 flex items-start justify-between border-b border-slate-100 bg-white px-5 py-4"><div><p class="text-xs font-bold uppercase tracking-wider text-orange-600">Availability calendar</p><h3 class="mt-1 text-xl font-extrabold" x-text="title"></h3></div><button type="button" @click="open=false" class="flex size-9 items-center justify-center rounded-full bg-slate-100 text-xl">×</button></div>
                            <div class="p-5"><div class="mb-4 flex flex-wrap items-center justify-between gap-3"><div class="flex flex-wrap gap-4 text-xs font-semibold"><span class="flex items-center gap-2"><i class="size-3 rounded bg-white ring-1 ring-slate-200"></i>Available</span><span class="flex items-center gap-2"><i class="size-3 rounded bg-red-100 ring-1 ring-red-200"></i>Booked / unavailable</span></div><label class="flex items-center gap-2 text-xs font-bold text-slate-600">Jump to <input type="month" :min="minimumMonth" :max="maximumMonth" x-model="jumpMonth" @change="jumpToMonth" class="rounded-lg border-slate-200 py-1.5 text-xs"></label></div><div class="mb-5 flex items-center justify-between rounded-xl bg-slate-50 p-2"><button type="button" @click="previous" :disabled="offset===0" class="rounded-lg px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white disabled:cursor-not-allowed disabled:opacity-30">← Previous</button><p class="text-center text-xs font-semibold text-slate-500">Browse up to 18 months ahead</p><button type="button" @click="next" :disabled="offset>=17" class="rounded-lg px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white disabled:cursor-not-allowed disabled:opacity-30">Next →</button></div><div class="grid gap-6 md:grid-cols-2"><template x-for="month in months" :key="month.key"><section class="rounded-xl border border-slate-100 p-3"><h4 class="mb-3 text-center text-sm font-extrabold" x-text="month.label"></h4><div class="grid grid-cols-7 gap-1 text-center"><template x-for="day in ['S','M','T','W','T','F','S']"><span class="py-1 text-[10px] font-bold text-slate-400" x-text="day"></span></template><template x-for="blank in month.offset"><span></span></template><template x-for="day in month.days" :key="day.date"><span class="flex aspect-square items-center justify-center rounded-lg text-xs" :class="day.past?'text-slate-300':(day.blocked?'bg-red-100 font-bold text-red-700 line-through':'bg-emerald-50 text-emerald-800')" x-text="day.number" :title="day.blocked?'Booked or unavailable':'Available'"></span></template></div></section></template></div><a :href="detailsUrl" class="mt-6 flex w-full items-center justify-center rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white hover:bg-orange-700">View property and choose dates</a></div>
                        </div>
                    </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $properties->links() }}</div>
        @endif
    </section>

    {{-- Legacy sample listing grids removed; the live marketplace above is authoritative. --}}
    @if(false)
        <section>
            <div class="flex items-center justify-between mb-6 gap-4">
                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">
                    Recently Viewed <span class="text-orange-500">Apartments</span>
                </h2>
                <a href="#" class="shrink-0 text-[13px] text-gray-700 border border-gray-300 rounded-full px-4 py-1.5 font-medium hover:border-orange-400 hover:text-orange-500 transition-colors">
                    View All
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @php
                    $recentProperties = [
                        [
                            'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=500&h=380&fit=crop',
                            'name' => 'Sunset Loft, Lekki Phase 1',
                            'location' => 'Lekki, Lagos',
                            'guests' => 2,
                            'price' => 45000,
                            'rating' => 4.5
                        ],
                        [
                            'image' => '/hero2.jpg',
                            'name' => 'Skyline Studio, Ikoyi',
                            'location' => 'Ikoyi, Lagos',
                            'guests' => 2,
                            'price' => 52000,
                            'rating' => 4.7
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=500&h=380&fit=crop',
                            'name' => 'Garden View, VI',
                            'location' => 'Victoria Island',
                            'guests' => 4,
                            'price' => 60000,
                            'rating' => 4.6
                        ],
                        [
                            'image' => '/hero3.jpg',
                            'name' => 'Cosy Nest, GRA Ikeja',
                            'location' => 'GRA, Ikeja',
                            'guests' => 3,
                            'price' => 38000,
                            'rating' => 4.4
                        ],
                    ];
                @endphp

                @foreach($recentProperties as $property)
                    <x-property-card
                        :image="$property['image']"
                        :name="$property['name']"
                        :location="$property['location']"
                        :guests="$property['guests']"
                        :price="$property['price']"
                        :rating="$property['rating']"
                    />
                @endforeach
            </div>
        </section>

        {{-- Featured Properties Section --}}
        <section class="reveal">
            <div class="flex items-center justify-between mb-6 gap-4">
                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">
                    Featured <span class="text-orange-500">Stays</span>
                </h2>
                <a href="#" class="shrink-0 text-[13px] text-gray-700 border border-gray-300 rounded-full px-4 py-1.5 font-medium hover:border-orange-400 hover:text-orange-500 transition-colors">
                    View All
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @php
                    $featuredProperties = [
                        [
                            // 'image' => 'https://images.unsplash.com/photo-1502672260066-6bc05c107956?w=500&h=380&fit=crop',
                            'image' => 'https://images.unsplash.com/photo-1567767292278-a4f21aa2d36e?w=500&h=380&fit=crop',
                            'name' => 'Luxury Penthouse, Ikoyi',
                            'location' => 'Ikoyi, Lagos',
                            'guests' => 6,
                            'price' => 125000,
                            'rating' => 4.9
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=500&h=380&fit=crop',
                            'name' => 'Beach House, Lekki',
                            'location' => 'Lekki, Lagos',
                            'guests' => 4,
                            'price' => 85000,
                            'rating' => 4.8
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=500&h=380&fit=crop',
                            'name' => 'Modern Apartment, VI',
                            'location' => 'Victoria Island',
                            'guests' => 3,
                            'price' => 55000,
                            'rating' => 4.7
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1567767292278-a4f21aa2d36e?w=500&h=380&fit=crop',
                            'name' => 'Cozy Studio, Ikeja',
                            'location' => 'Ikeja, Lagos',
                            'guests' => 2,
                            'price' => 35000,
                            'rating' => 4.6
                        ],
                    ];
                @endphp

                @foreach($featuredProperties as $property)
                    <x-property-card
                        :image="$property['image']"
                        :name="$property['name']"
                        :location="$property['location']"
                        :guests="$property['guests']"
                        :price="$property['price']"
                        :rating="$property['rating']"
                    />
                @endforeach
            </div>
        </section>
    @endif

    {{-- AI Trip Concierge Section --}}
    <x-ai-concierge 
        title="Ask before you book, not after"
        subtitle="AI TRIP CONCIERGE"
        description="Type a question about any neighbourhood, budget or house rule and get an answer grounded in verified listing data."
    />

    {{-- Host Steps Section --}}
    <x-host-steps 
        title="four steps"
        subtitle="BECOME A HOST"
        description="The sequence every property moves through before it can appear in search results."
        :steps="[
            [
                'title' => 'Register',
                'description' => 'The host creates a profile and adds the property.'
            ],
            [
                'title' => 'Verify',
                'description' => 'ID, documents and property details are reviewed.'
            ],
            [
                'title' => 'Approve',
                'description' => 'The badge is issued and attached to the listing.'
            ],
            [
                'title' => 'Go Live',
                'description' => 'The stay appears in search, badge visible to guests.'
            ]
        ]"
    />

    {{-- Booking Difference Section --}}
    <x-booking-difference 
        title="Booking here works differently"
        subtitle="WHY GUESTS CHOOSE US"
        description="Search, compare and book stays that have already passed verification — with an AI concierge on hand for anything a listing page can't answer."
        :features="[
            [
                'icon' => '<svg class=\'w-6 h-6 text-orange-500\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z\'/></svg>',
                'title' => 'Search by neighbourhood',
                'description' => 'Filter by area, dates and guest count, and browse a grid built for comparing stays quickly.'
            ],
            [
                'icon' => '<svg class=\'w-6 h-6 text-orange-500\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>',
                'title' => 'Verified property pages',
                'description' => 'ID checks, an on-site inspection and photo verification — every badge you see means something.'
            ],
            [
                'icon' => '<svg class=\'w-6 h-6 text-orange-500\' fill=\'currentColor\' viewBox=\'0 0 24 24\'><path d=\'M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z\'/></svg>',
                'title' => 'AI insight on every listing',
                'description' => 'A short, specific read on wifi, noise, safety or host responsiveness — pulled from verified data and guest notes.'
            ]
        ]"
    />

    {{-- Host CTA Section --}}
    <x-host-cta 
        title="List a shortlet, earn from verified guests"
        description="No dashboards to learn first. Add your property, get verified, and go live."
        buttonText="Get started"
        :buttonUrl="route('register')"
        :features="[
            ['highlight' => '< 48h', 'description' => 'Average time from submitting documents to going live.'],
            ['highlight' => 'AI-suggested', 'description' => 'Nightly pricing based on comparable verified stays nearby.'],
            ['highlight' => '1 badge', 'description' => 'One verification badge shown on your listing for as long as it\'s active.']
        ]"
    />

    {{-- Final CTA Section --}}
    <x-final-cta 
        title="Ready to book a stay you don't have to second-guess?"
        subtitle="GET STARTED"
        description="Browse verified stays across Lagos, or list your own property in under 48 hours."
        primaryButton="Browse verified stays"
        primaryUrl="#"
        secondaryButton="Get started"
        :secondaryUrl="route('register')"
    />

    {{-- Footer --}}
    <x-footer />
@endsection

@push('scripts')
<script>
function availabilityCalendar(intervals, title, detailsUrl) {
    return {
        open: false, title, detailsUrl, offset: 0, jumpMonth: '', months: [], minimumMonth: '', maximumMonth: '',
        init() {
            const today = new Date(); today.setHours(0,0,0,0);
            this.minimumMonth = this.monthValue(today);
            this.maximumMonth = this.monthValue(new Date(today.getFullYear(), today.getMonth()+17, 1));
            this.jumpMonth = this.minimumMonth;
            this.buildMonths();
        },
        monthValue(date) { return date.getFullYear()+'-'+String(date.getMonth()+1).padStart(2,'0'); },
        buildMonths() {
            const today = new Date(); today.setHours(0,0,0,0);
            this.months = [0,1].map(add => {
                const first = new Date(today.getFullYear(), today.getMonth()+this.offset+add, 1);
                const count = new Date(first.getFullYear(), first.getMonth()+1, 0).getDate();
                return {key:first.toISOString(),label:first.toLocaleDateString('en-GB',{month:'long',year:'numeric'}),offset:first.getDay(),days:Array.from({length:count},(_,i)=>{
                    const date = new Date(first.getFullYear(),first.getMonth(),i+1); const iso = [date.getFullYear(),String(date.getMonth()+1).padStart(2,'0'),String(date.getDate()).padStart(2,'0')].join('-');
                    return {number:i+1,date:iso,past:date<today,blocked:intervals.some(range=>iso>=range.start&&iso<range.end)};
                })};
            });
            this.jumpMonth = this.monthValue(new Date(today.getFullYear(),today.getMonth()+this.offset,1));
        },
        previous() { this.offset=Math.max(0,this.offset-1); this.buildMonths(); },
        next() { this.offset=Math.min(16,this.offset+1); this.buildMonths(); },
        jumpToMonth() { const [year,month]=this.jumpMonth.split('-').map(Number); const today=new Date(); this.offset=Math.max(0,Math.min(16,(year-today.getFullYear())*12+(month-1-today.getMonth()))); this.buildMonths(); }
    }
}
</script>
@endpush

@push('scripts')
<script>
// Scroll reveal + header scroll effect
document.addEventListener('DOMContentLoaded', function() {
    // Header scroll effect
    const header = document.getElementById('site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 10);
        });
    }
});
</script>
@endpush
