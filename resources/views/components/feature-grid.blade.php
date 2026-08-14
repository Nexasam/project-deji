@props([
    'title' => 'Why Choose Us',
    'subtitle' => 'What makes us different',
    'features' => []
])

<section class="reveal bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <p class="text-sm font-bold text-orange-500 uppercase tracking-wider mb-2">{{ $subtitle }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $title }}</h2>
        </div>

        {{-- Features Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($features as $feature)
                <div class="text-center group">
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-orange-500 transition-colors duration-300">
                        {!! $feature['icon'] !!}
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
