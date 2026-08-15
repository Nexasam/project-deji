@props([
    'image' => '',
    'status' => 'AVAILABLE',
    'statusColor' => 'green',
    'name' => '',
    'location' => '',
    'occupancy' => '0%',
    'revenue' => '₦0',
    'revenueChange' => '30d'
])

@php
    $statusBadgeColors = [
        'green' => 'bg-green-500 text-white',
        'orange' => 'bg-orange-500 text-white',
        'blue' => 'bg-blue-500 text-white',
        'purple' => 'bg-purple-500 text-white',
        'gray' => 'bg-gray-500 text-white',
    ];
    
    $badgeColor = $statusBadgeColors[$statusColor] ?? 'bg-gray-500 text-white';
@endphp

<div class="bg-white rounded-2xl overflow-hidden hover:shadow-lg transition-shadow cursor-pointer group">
    {{-- Property Image --}}
    <div class="relative h-32 sm:h-40 md:h-44 overflow-hidden">
        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

        {{-- Status Badge --}}
        <div class="absolute top-2 left-2">
            <span class="px-2 py-0.5 text-[10px] sm:text-xs font-bold rounded-md {{ $badgeColor }} uppercase">{{ $status }}</span>
        </div>

        {{-- Favorite Button --}}
        <button class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors shadow-sm">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>

    {{-- Property Details --}}
    <div class="p-3 sm:p-4">
        <h3 class="font-bold text-gray-900 text-xs sm:text-sm mb-0.5 line-clamp-1">{{ $name }}</h3>
        <p class="text-[10px] sm:text-xs text-gray-500 mb-2 sm:mb-3 line-clamp-1">{{ $location }}</p>

        {{-- Metrics --}}
        <div class="grid grid-cols-2 gap-2">
            <div>
                <div class="text-[10px] sm:text-xs text-gray-500 mb-0.5">Occupancy</div>
                <div class="font-bold text-gray-900 text-xs sm:text-sm">{{ $occupancy }}</div>
            </div>
            <div>
                <div class="text-[10px] sm:text-xs text-gray-500 mb-0.5">Rev ({{ $revenueChange }})</div>
                <div class="font-bold text-gray-900 text-xs sm:text-sm">{{ $revenue }}</div>
            </div>
        </div>
    </div>
</div>
