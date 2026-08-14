@props(['id' => 'insights-modal', 'title' => 'AI Insights'])

<div 
    id="{{ $id }}" 
    role="dialog" 
    aria-modal="true" 
    aria-labelledby="modal-title"
    x-data="{ open: false }"
    x-show="open"
    @open-modal.window="if ($event.detail.id === '{{ $id }}') open = true"
    @close-modal.window="if ($event.detail.id === '{{ $id }}') open = false"
    @keydown.escape.window="open = false"
    style="display: none; position: fixed; inset: 0; z-index: 200; align-items: center; justify-content: center;"
    x-bind:style="open ? 'display: flex;' : 'display: none;'"
>
    <div class="overlay" @click="open = false"></div>
    <div class="box">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-orange-500 rounded-full flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span id="modal-title" class="font-bold text-gray-900 text-base">{{ $title }}</span>
            </div>
            <button 
                @click="open = false" 
                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors" 
                aria-label="Close"
            >
                <svg width="14" height="14" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="modal-body" class="space-y-3 text-sm text-gray-600 leading-relaxed">
            {{ $slot }}
        </div>
    </div>
</div>
