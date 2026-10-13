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

{{-- One responsive sidebar. Rendering navigation once prevents duplicate links in
     screen readers, crawlers and keyboard navigation. --}}
<aside
    x-cloak
    class="owner-sidebar fixed inset-y-0 left-0 z-40 flex w-60 shrink-0 flex-col border-r border-slate-200 bg-white transition-transform duration-200 md:static md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    @include('partials.sidebar-inner', ['active' => $active, 'mobile' => true])
</aside>
