@php
    $platformPermissions = app(\App\Services\PlatformPermissionService::class);
    $administrator = auth()->user();
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'permission' => 'platform.dashboard.view', 'icon' => 'M4 13h6V4H4v9Zm10 7h6v-9h-6v9ZM4 20h6v-3H4v3Zm10-13h6V4h-6v3Z'],
        ['label' => 'Businesses', 'route' => 'admin.businesses.index', 'pattern' => 'admin.businesses.*', 'permission' => 'platform.business.view', 'icon' => 'M4 21V10l8-6 8 6v11M8 21v-7h8v7M3 21h18'],
        ['label' => 'Users', 'route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'permission' => 'platform.user.view', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
        ['label' => 'Properties', 'route' => 'admin.properties.index', 'pattern' => 'admin.properties.*', 'permission' => 'platform.property.view', 'icon' => 'm3 11 9-8 9 8v9a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-9Z'],
        ['label' => 'Reviews', 'route' => 'admin.reviews.index', 'pattern' => 'admin.reviews.*', 'permission' => 'platform.review.view', 'icon' => 'm12 3 2.8 5.67 6.2.9-4.5 4.38 1.06 6.18L12 17.77l-5.56 2.92 1.06-6.18L3 9.57l6.2-.9L12 3Z'],
        ['label' => 'Disputes', 'route' => 'admin.disputes.index', 'pattern' => 'admin.disputes.*', 'permission' => 'platform.dispute.view', 'icon' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Zm0-13v4m0 4h.01'],
        ['label' => 'Configuration', 'route' => 'admin.settings.index', 'pattern' => 'admin.settings.*', 'permission' => 'platform.configure', 'icon' => 'M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7.4-3.5a7.7 7.7 0 0 0-.1-1l2-1.55-2-3.46-2.45 1a7.8 7.8 0 0 0-1.75-1L14.75 3h-4l-.35 2.99a7.8 7.8 0 0 0-1.75 1l-2.45-1-2 3.46 2 1.55a7.7 7.7 0 0 0 0 2l-2 1.55 2 3.46 2.45-1a7.8 7.8 0 0 0 1.75 1l.35 2.99h4l.35-2.99a7.8 7.8 0 0 0 1.75-1l2.45 1 2-3.46-2-1.55a7.7 7.7 0 0 0 .1-1Z'],
        ['label' => 'Audit history', 'route' => 'admin.audit.index', 'pattern' => 'admin.audit.*', 'permission' => 'platform.audit.view', 'icon' => 'M9 11h6M9 15h4M7 3h10a2 2 0 0 1 2 2v16l-7-3-7 3V5a2 2 0 0 1 2-2Z'],
    ];
@endphp

<div class="border-b border-slate-800 px-5 py-5">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
        <span class="grid size-10 place-items-center rounded-xl bg-orange-500 text-sm font-black text-white shadow-lg shadow-orange-950/30">VS</span>
        <span><span class="block text-sm font-black">Verified Shortlet</span><span class="block text-[10px] font-bold uppercase tracking-[.2em] text-slate-400">Control centre</span></span>
    </a>
</div>
<nav class="flex-1 space-y-1 overflow-y-auto p-3">
    @foreach($navigation as $item)
        @if(Route::has($item['route']) && $platformPermissions->allows($administrator, $item['permission']))
            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition {{ request()->routeIs($item['pattern']) ? 'bg-orange-500 text-white shadow-lg shadow-orange-950/20' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $item['icon'] }}"/></svg>
                {{ $item['label'] }}
            </a>
        @endif
    @endforeach
</nav>
<div class="border-t border-slate-800 p-4">
    <div class="rounded-xl bg-slate-900 p-3">
        <p class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400">System operational</p>
        <p class="mt-1 text-xs leading-5 text-slate-400">Core marketplace and booking services are available.</p>
    </div>
</div>
