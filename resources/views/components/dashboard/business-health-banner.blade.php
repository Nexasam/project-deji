@props([
    'score' => 87,
    'status' => 'EXCELLENT',
    'greeting' => 'Good morning, Samson',
    'message' => 'Revenue and occupancy are both trending up this month. 2 properties need attention — including an overdue maintenance flag at Bluewater Suite 4B.',
    'date' => 'Tuesday, 4 August.'
])

<div class="bg-[#222222] rounded-lg p-6 flex items-start gap-6">
    {{-- Score Circle --}}
    <div class="flex-shrink-0">
        <div class="relative w-24 h-24">
            <svg class="w-24 h-24 transform -rotate-90">
                <circle cx="48" cy="48" r="40" stroke="#3a3a3a" stroke-width="6" fill="none" />
                <circle 
                    cx="48" 
                    cy="48" 
                    r="40" 
                    stroke="#FF5A00" 
                    stroke-width="6" 
                    fill="none"
                    stroke-dasharray="{{ 2 * pi() * 40 }}"
                    stroke-dashoffset="{{ 2 * pi() * 40 * (1 - $score / 100) }}"
                    stroke-linecap="round"
                />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-white text-2xl font-bold">{{ $score }}</span>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1">
        <div class="mb-2">
            <span class="text-[#FF5A00] text-[10px] font-bold uppercase tracking-wider">BUSINESS HEALTH SCORE · {{ $status }}</span>
        </div>
        <h2 class="text-white text-xl font-bold mb-2">{{ $greeting }}</h2>
        <p class="text-gray-400 text-sm leading-relaxed mb-1">
            {{ $message }}
        </p>
        <p class="text-gray-500 text-xs">{{ $date }}</p>
    </div>
</div>
