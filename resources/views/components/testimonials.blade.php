@props([
    'title' => 'What Our Guests Say',
    'subtitle' => 'Real experiences from real people',
    'testimonials' => []
])

<section class="reveal bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <p class="text-sm font-bold text-orange-500 uppercase tracking-wider mb-2">{{ $subtitle }}</p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $title }}</h2>
        </div>

        {{-- Testimonials Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
                <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-xl transition-shadow duration-300">
                    {{-- Rating Stars --}}
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 {{ $i < $testimonial['rating'] ? 'text-orange-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>

                    {{-- Testimonial Text --}}
                    <p class="text-gray-700 leading-relaxed mb-6 italic">"{{ $testimonial['text'] }}"</p>

                    {{-- Author Info --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($testimonial['name'], 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $testimonial['name'] }}</p>
                            <p class="text-sm text-gray-500">{{ $testimonial['location'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
