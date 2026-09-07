@props([
    'title' => 'Ready to book a stay you don\'t have to second-guess?',
    'subtitle' => 'GET STARTED',
    'description' => 'Browse verified stays across Lagos, or list your own property in under 48 hours.',
    'primaryButton' => 'Browse verified stays',
    'primaryUrl' => '#',
    'secondaryButton' => 'Get started',
    'secondaryUrl' => '/register'
])

<section class="bg-[#FFF5ED] py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        {{-- Subtitle --}}
        <p class="text-xs font-bold text-orange-500 uppercase tracking-wider mb-4">{{ $subtitle }}</p>

        {{-- Title --}}
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">{{ $title }}</h2>

        {{-- Description --}}
        <p class="text-gray-600 text-sm mb-10 max-w-2xl mx-auto">{{ $description }}</p>

        {{-- CTA Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ $primaryUrl }}" class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white font-semibold px-10 py-3.5 rounded-full transition-colors duration-300 text-sm">
                {{ $primaryButton }}
            </a>
            <a href="{{ $secondaryUrl }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-50 text-gray-900 font-semibold px-10 py-3.5 rounded-full border border-gray-300 hover:border-gray-400 transition-all duration-300 text-sm">
                {{ $secondaryButton }}
            </a>
        </div>
    </div>
</section>
