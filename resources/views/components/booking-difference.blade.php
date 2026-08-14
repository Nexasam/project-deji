@props([
    'title' => 'Booking here works differently',
    'subtitle' => 'WHY GUESTS CHOOSE US',
    'description' => 'Search, compare and book stays that have already passed verification — with an AI concierge on hand for anything a listing page can\'t answer.',
    'features' => []
])

<section class="bg-black py-20 relative overflow-hidden">
    {{-- Decorative Circle --}}
    <div class="absolute right-0 top-1/2 transform -translate-y-1/2 w-96 h-96 bg-orange-900 rounded-full opacity-30 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Section Header --}}
        <div class="mb-12 max-w-2xl">
            <p class="text-sm font-bold text-orange-500 uppercase tracking-wider mb-3">{{ $subtitle }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">{{ $title }}</h2>
            <p class="text-gray-400 text-base">{{ $description }}</p>
        </div>

        {{-- Features Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($features as $feature)
                <div class="bg-white rounded-2xl p-8 hover:shadow-2xl transition-shadow duration-300">
                    {{-- Icon --}}
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-6">
                        {!! $feature['icon'] !!}
                    </div>

                    {{-- Title --}}
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>

                    {{-- Description --}}
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
