@props([
    'title' => 'From listing to badge, in four steps',
    'subtitle' => 'BECOME A HOST',
    'description' => 'The sequence every property moves through before it can appear in search results.',
    'steps' => []
])

<section class="bg-orange-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <p class="text-sm font-bold text-orange-500 uppercase tracking-wider mb-3">{{ $subtitle }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                From listing to badge, in <span class="text-orange-500">{{ $title }}</span>
            </h2>
            <p class="text-gray-600 mt-4 text-base max-w-2xl mx-auto">{{ $description }}</p>
        </div>

        {{-- Steps Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($steps as $index => $step)
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
                    {{-- Step Number --}}
                    <div class="inline-block bg-orange-100 text-orange-500 font-bold text-sm px-3 py-1 rounded-full mb-4">
                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                    </div>

                    {{-- Step Title --}}
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $step['title'] }}</h3>

                    {{-- Step Description --}}
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $step['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
