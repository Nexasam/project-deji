{{-- Shared sidebar nav partial --}}
{{-- Usage: @include('partials.sidebar-nav', ['active' => 'dashboard']) --}}
@php $active = $active ?? ''; @endphp

{{-- Mobile overlay backdrop --}}
<div x-show="sidebarOpen"
     x-cloak
     @click="sidebarOpen = false"
     style="display:none"
     class="fixed inset-0 bg-black/50 z-30 md:hidden"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

{{-- Desktop sidebar (always visible md+) --}}
<aside class="w-[200px] bg-white border-r border-gray-200 flex-shrink-0 hidden md:flex flex-col">
    @include('partials.sidebar-inner', ['active' => $active, 'mobile' => false])
</aside>

{{-- Mobile sidebar drawer --}}
<div x-show="sidebarOpen"
     x-cloak
     style="display:none"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 -translate-x-full"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 -translate-x-full"
     class="fixed inset-y-0 left-0 z-40 w-[200px] md:hidden">
    <aside class="w-full h-full bg-white border-r border-gray-200 flex flex-col">
        @include('partials.sidebar-inner', ['active' => $active, 'mobile' => true])
    </aside>
</div>
