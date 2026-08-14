@props([
    'title' => 'Ready to find your perfect stay?',
    'description' => 'Join thousands of verified guests and hosts',
    'primaryButton' => 'Browse Properties',
    'primaryUrl' => '#',
    'secondaryButton' => 'List Your Property',
    'secondaryUrl' => '#'
])

<section class="reveal bg-gradient-to-r from-orange-500 to-orange-600 py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">{{ $title }}</h2>
        <p class="text-lg text-orange-100 mb-8">{{ $description }}</p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ $primaryUrl }}" class="inline-flex items-center justify-center bg-white text-orange-600 font-bold px-8 py-4 rounded-full hover:bg-gray-100 transition-colors duration-300 shadow-lg">
                {{ $primaryButton }}
            </a>
            <a href="{{ $secondaryUrl }}" class="inline-flex items-center justify-center bg-transparent border-2 border-white text-white font-bold px-8 py-4 rounded-full hover:bg-white hover:text-orange-600 transition-colors duration-300">
                {{ $secondaryButton }}
            </a>
        </div>
    </div>
</section>
