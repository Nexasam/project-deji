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
    <x-search-bar />

    {{-- Category Pills --}}
    <x-category-pills 
        :categories="[
            ['label' => 'All stays', 'active' => true],
            ['label' => 'Lekki', 'active' => false],
            ['label' => 'Ikoyi', 'active' => false],
            ['label' => 'Victoria Island', 'active' => false],
            ['label' => 'Beachfront', 'active' => false],
            ['label' => 'Family stays', 'active' => false],
            ['label' => 'Business stays', 'active' => false],
        ]"
    />

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-20">
        {{-- Recently Viewed Section --}}
        <section class="reveal">
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
    </main>

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
        buttonText="Register your property"
        buttonUrl="#"
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
        secondaryButton="List your property"
        secondaryUrl="#"
    />

    {{-- Footer --}}
    <x-footer />
@endsection

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
