@props([
    'title' => 'List a shortlet, earn from verified guests',
    'description' => 'No dashboards to learn first. Add your property, get verified, and go live.',
    'buttonText' => 'Get started',
    'buttonUrl' => '/register',
    'features' => []
])

<section class="bg-white py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3">
                List a shortlet, earn from <span class="text-orange-500">verified guests</span>
            </h2>
            <p class="text-gray-600 text-sm">{{ $description }}</p>
        </div>
        

        {{-- Features Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10 max-w-4xl mx-auto">
            @foreach($features as $feature)
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-orange-200 transition-all duration-300">
                    {{-- Feature Title --}}
                    <h3 class="text-base font-bold mb-2">
                        <span class="text-orange-500">{{ $feature['highlight'] }}</span>
                    </h3>

                    {{-- Feature Description --}}
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- CTA Button --}}
        <div class="text-center">
            <a href="{{ $buttonUrl }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold px-16 py-4 rounded-full text-base transition-colors duration-300">
                {{ $buttonText }}
            </a>
        </div>
    </div>
</section>
