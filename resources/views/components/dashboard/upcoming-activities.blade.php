@props([
    'activities' => []
])

<div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm">
    <div class="mb-4">
        <h3 class="text-base font-bold text-gray-900 mb-0.5">Upcoming activities</h3>
        <p class="text-xs text-gray-500">Next 3 check-ins</p>
    </div>

    <div class="space-y-0">
        @foreach($activities as $index => $activity)
            <div class="flex items-center gap-3 py-3 {{ $index > 0 ? 'border-t border-gray-200' : '' }}">
                {{-- Date Block --}}
                <div class="flex-shrink-0 text-center">
                    <div class="text-xs font-bold text-gray-900">{{ $activity['day'] }}</div>
                    <div class="text-[10px] text-gray-500 uppercase">{{ $activity['month'] }}</div>
                </div>

                {{-- Property Info --}}
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $activity['property'] }}</div>
                    <div class="text-xs text-gray-500">{{ $activity['guests'] }} · {{ $activity['nights'] }}</div>
                </div>

                {{-- Menu --}}
                <button class="flex-shrink-0 w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="5" r="1.5"/>
                        <circle cx="12" cy="12" r="1.5"/>
                        <circle cx="12" cy="19" r="1.5"/>
                    </svg>
                </button>
            </div>
        @endforeach
    </div>
</div>
