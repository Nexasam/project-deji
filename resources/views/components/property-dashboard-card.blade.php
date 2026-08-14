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
    <div class="relative h-44 overflow-hidden">
        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        
        {{-- Status Badge --}}
        <div class="absolute top-3 left-3">
            <span class="px-3 py-1 text-xs font-bold rounded-md {{ $badgeColor }} uppercase">{{ $status }}</span>
        </div>
        
        {{-- Favorite Button --}}
        <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>
    
    {{-- Property Details --}}
    <div class="p-4">
        <h3 class="font-bold text-gray-900 text-sm mb-0.5 line-clamp-1">{{ $name }}</h3>
        <p class="text-xs text-gray-500 mb-3">{{ $location }}</p>
        
        {{-- Metrics --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <div class="text-xs text-gray-500 mb-0.5">Occupancy</div>
                <div class="font-bold text-gray-900 text-sm">{{ $occupancy }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-0.5">Revenue ({{ $revenueChange }})</div>
                <div class="font-bold text-gray-900 text-sm">{{ $revenue }}</div>
            </div>
        </div>
    </div>
</div>
