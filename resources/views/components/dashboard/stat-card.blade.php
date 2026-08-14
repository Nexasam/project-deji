@props([
    'label',
    'value',
    'subtext',
    'icon',
    'trend' => null
])

<div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
    <div class="flex items-start justify-between mb-3">
        <div class="text-xs text-gray-600">{{ $label }}</div>
        <div class="w-8 h-8 rounded-lg bg-[#FFF5ED] flex items-center justify-center flex-shrink-0">
            {!! $icon !!}
        </div>
    </div>
    <div class="mb-1">
        <div class="text-2xl font-bold text-gray-900">{{ $value }}</div>
    </div>
    <div class="text-xs text-gray-500 flex items-center gap-1">
        @if($trend)
            @if(str_contains($trend, '↑'))
                <span class="text-gray-600">{{ $trend }}</span>
            @else
                <span class="text-gray-600">{{ $trend }}</span>
            @endif
        @else
            <span>{{ $subtext }}</span>
        @endif
    </div>
</div>
