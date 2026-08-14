@props([
    'title' => 'How It Works',
    'subtitle' => 'Simple steps to your perfect stay',
    'steps' => []
])

<section class="reveal bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <p class="text-sm font-bold text-orange-500 uppercase tracking-wider mb-2">{{ $subtitle }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $title }}</h2>
        </div>

        {{-- Steps --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
            @foreach($steps as $index => $step)
                <div class="relative">
                    {{-- Connector Line (except for last item) --}}
                    @if($index < count($steps) - 1)
                        <div class="hidden md:block absolute top-12 left-1/2 w-full h-0.5 bg-orange-200 -z-10"></div>
                    @endif

                    <div class="text-center">
                        {{-- Step Number --}}
                        <div class="w-24 h-24 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-6 relative z-10">
                            <span class="text-3xl font-bold text-white">{{ $index + 1 }}</span>
                        </div>

                        {{-- Step Title --}}
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $step['title'] }}</h3>

                        {{-- Step Description --}}
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $step['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
