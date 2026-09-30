@extends('layouts.app')

@section('content')
    {{-- Auth Modals --}}
    <x-auth.login-modal />
    <x-auth.signup-modal />

    {{-- Toast Notification --}}
    <x-toast />

    {{-- AI Insights Preview --}}
    <div x-data="{ open:false, insight:{} }" @open-property-insights.window="insight=$event.detail; open=true" x-show="open" x-cloak @keydown.escape.window="open=false" class="fixed inset-0 z-[90] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="property-insights-title">
        <button type="button" @click="open=false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" aria-label="Close AI insights"></button>
        <article class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 p-5">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[.16em] text-orange-600">AI Insights preview</p>
                    <h2 id="property-insights-title" class="mt-1 text-2xl font-extrabold text-slate-950" x-text="insight.title || 'Property insights'"></h2>
                    <p class="mt-1 text-xs text-slate-500">Generated from verified marketplace data. Full AI assistant coming later.</p>
                </div>
                <button type="button" @click="open=false" class="flex size-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-600">×</button>
            </div>
            <div class="p-5">
                <div class="flex items-center gap-4 border-b border-slate-100 pb-5">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-orange-50 text-lg font-black text-orange-700" x-text="(insight.host || 'Verified host').replace('Hosted by ','').slice(0,2).toUpperCase()"></div>
                    <div>
                        <p class="font-extrabold text-slate-950" x-text="insight.host || 'Hosted by Verified host'"></p>
                        <p class="mt-1 text-sm text-slate-500" x-text="insight.hostMeta || 'Verified Shortlet host'"></p>
                    </div>
                </div>
                <div class="mt-5 space-y-5">
                    <template x-for="item in [
                        {icon:'⌁', title:insight.locationTitle, body:insight.locationBody},
                        {icon:'↳', title:insight.accessTitle, body:insight.accessBody},
                        {icon:'★', title:insight.qualityTitle, body:insight.qualityBody},
                        {icon:'₦', title:insight.pricingTitle, body:insight.pricingBody}
                    ]" :key="item.title">
                        <div class="flex gap-4">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-base font-black text-slate-700" x-text="item.icon"></div>
                            <div>
                                <p class="font-extrabold text-slate-950" x-text="item.title"></p>
                                <p class="mt-1 text-sm leading-6 text-slate-500" x-text="item.body"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </article>
    </div>

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

    <section class="mx-auto mt-4 max-w-7xl px-4 sm:mt-6 sm:px-6 lg:px-8" aria-label="Refine marketplace search">
        <details class="marketplace-advanced-filters">
            <summary class="marketplace-advanced-summary">
                <span>
                    <strong>More filters</strong>
                    <small>Search, stay type, price and beds</small>
                </span>
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </summary>
        <form action="{{ route('home').'#marketplace' }}" method="GET" class="marketplace-advanced-form rounded-2xl border border-gray-200 bg-white p-3 shadow-sm sm:p-4">
            @foreach(['check_in', 'check_out', 'guests'] as $preservedFilter) @if(filled($filters[$preservedFilter] ?? null))<input type="hidden" name="{{ $preservedFilter }}" value="{{ $filters[$preservedFilter] }}">@endif @endforeach
            <div class="grid gap-3 md:grid-cols-[minmax(220px,1.5fr)_minmax(150px,.85fr)_repeat(3,minmax(120px,.7fr))_auto] md:items-end">
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400">Search</span>
                    <input class="h-11 w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Apartment, city or area">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400">Stay type</span>
                    <select class="h-11 w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500" name="category">
                        @foreach(['all'=>'All stays','lekki'=>'Lekki','ikoyi'=>'Ikoyi','victoria-island'=>'Victoria Island','beachfront'=>'Beachfront','family'=>'Family','business'=>'Business'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['category'] ?? 'all') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400">Min price</span>
                    <input class="h-11 w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500" type="number" min="0" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Any">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400">Max price</span>
                    <input class="h-11 w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500" type="number" min="0" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Any">
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400">Beds</span>
                    <select class="h-11 w-full rounded-xl border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500" name="beds">
                        <option value="">Any</option>
                        @foreach(range(1, 5) as $beds)<option value="{{ $beds }}" @selected((string)($filters['beds'] ?? '') === (string)$beds)>{{ $beds }}+</option>@endforeach
                    </select>
                </label>
                <button class="h-11 rounded-xl bg-orange-500 px-5 text-sm font-bold text-white hover:bg-orange-600">Search</button>
            </div>
        </form>
        </details>
    </section>

    {{-- Backend-powered marketplace inventory --}}
    <section id="marketplace" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-orange-500">Find the right place</p>
                <h2 class="mt-1 text-2xl font-extrabold text-gray-900">
                    {{ $properties->total() }} serviced {{ Str::plural('apartment', $properties->total()) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">Simple verified stays, clear pricing and live availability.</p>
            </div>
        </div>
        @if($properties->isEmpty())
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-10 text-center text-gray-600">No serviced apartments match these filters.</div>
        @else
            @php
                $propertyCollection = $properties->getCollection();
                $marketplaceRows = collect([
                    ['title' => 'Popular verified stays in Lagos', 'subtitle' => 'Guest-ready homes with live availability.', 'items' => $propertyCollection->take(10)],
                    ['title' => 'Great stays for your next trip', 'subtitle' => 'Comfortable apartments across Lekki, Ikoyi and Victoria Island.', 'items' => $propertyCollection->skip(3)->take(10)],
                    ['title' => 'Best value longer stays', 'subtitle' => 'Homes where selected dates can unlock host discounts.', 'items' => $propertyCollection->filter(fn($property) => $property->promotions->isNotEmpty())->take(10)],
                    ['title' => 'Family and group stays', 'subtitle' => 'More space for guests travelling together.', 'items' => $propertyCollection->filter(fn($property) => $property->capacity >= 4)->take(10)],
                ])->filter(fn($row) => $row['items']->isNotEmpty())->values();
            @endphp

            <div class="space-y-12">
                @foreach($marketplaceRows as $index => $row)
                    <section x-data="{ scrollBy(direction) { this.$refs.rail.scrollBy({ left: direction * 860, behavior: 'smooth' }) } }" aria-labelledby="marketplace-row-{{ $index }}">
                        <div class="marketplace-row-header mb-4 flex items-start justify-between gap-4">
                            <div class="marketplace-row-heading min-w-0 flex-1">
                                <a id="marketplace-row-{{ $index }}" href="#marketplace-row-{{ $index }}" class="marketplace-row-title group inline-flex items-center gap-2 text-xl font-extrabold text-slate-950 sm:text-2xl">
                                    {{ $row['title'] }}
                                    <span class="flex size-7 items-center justify-center rounded-full bg-slate-100 text-sm text-slate-700 transition group-hover:bg-slate-950 group-hover:text-white">→</span>
                                </a>
                                <p class="mt-1 text-sm text-slate-500">{{ $row['subtitle'] }}</p>
                            </div>
                            <div class="marketplace-row-controls flex items-center gap-2">
                                <button type="button" @click="scrollBy(-1)" class="marketplace-row-nav" aria-label="Scroll {{ $row['title'] }} left">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                                </button>
                                <button type="button" @click="scrollBy(1)" class="marketplace-row-nav" aria-label="Scroll {{ $row['title'] }} right">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                                </button>
                            </div>
                        </div>
                        <div x-ref="rail" class="marketplace-row-rail -mx-4 flex snap-x gap-4 overflow-x-auto scroll-smooth px-4 pb-3 sm:-mx-6 sm:px-6 lg:mx-0 lg:px-0">
                            @foreach($row['items'] as $property)
                                <x-marketplace-property-tile :property="$property" :filters="$filters" class="snap-start" />
                            @endforeach
                        </div>
                    </section>
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
