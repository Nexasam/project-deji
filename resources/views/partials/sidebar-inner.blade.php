@php $active = $active ?? ''; $mobile = $mobile ?? false; @endphp

{{-- Logo --}}
<div class="p-4 border-b border-gray-200">
    <div class="flex items-center justify-between gap-2">
        <a href="/" class="flex items-center gap-2">
            <img src="/logo1.png" alt="VS Logo" class="h-10 w-auto" />
            <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
        </a>
        @if($mobile)
        <button @click="sidebarOpen = false" class="p-1 rounded text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        @endif
    </div>
</div>

{{-- User --}}
@php
    $sidebarUser     = auth()->user();
    $sidebarBusiness = $activeBusiness ?? null;
    $sidebarContext  = $activeBusinessContext ?? null;
    $sidebarCan      = fn (string $permission) => $sidebarUser && $sidebarContext
        ? app(\App\Services\Access\BusinessPermissionService::class)->allows($sidebarUser, $sidebarContext, $permission)
        : false;
    $sidebarRoleKey = $sidebarContext?->roleAssignment->role->system_key ?? $sidebarContext?->roleAssignment->role->parent?->system_key;
    $sidebarTaskOnly = in_array($sidebarRoleKey, ['cleaner', 'maintenance_technician', 'inspector'], true);
    $sidebarInitials = $sidebarUser
        ? str($sidebarUser->name)->explode(' ')->map(fn($p) => str($p)->substr(0,1))->take(2)->join('')
        : 'VS';
@endphp
<div class="px-4 py-3 border-b border-gray-200">
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-[#FF5A00] rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-white text-xs font-bold">{{ $sidebarInitials }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-gray-900 text-xs font-semibold truncate">{{ $sidebarUser?->name ?? 'Guest' }}</p>
            <p class="text-gray-500 text-[10px] truncate">{{ $sidebarBusiness?->name ?? 'My Business' }}</p>
        </div>
        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</div>
@if(($availableBusinessMemberships ?? collect())->count() > 1)
<form method="POST" action="{{ route('owner.business-context.update') }}" class="border-b border-gray-200 px-3 py-3">@csrf @method('PATCH')
    <label class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Active business</label>
    <select name="membership_id" onchange="this.form.submit()" class="mt-1 w-full rounded-lg border-gray-200 py-1.5 text-xs font-semibold">
        @foreach($availableBusinessMemberships as $membership)<option value="{{ $membership->id }}" @selected($sidebarContext?->membership->id === $membership->id)>{{ $membership->business->name }}</option>@endforeach
    </select>
</form>
@endif
{{-- Nav --}}
<nav class="flex-1 p-3 overflow-y-auto">

    {{-- Overview --}}
    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider px-2 mb-1.5 mt-1">Overview</p>

    @if($sidebarCan('business.view'))<a href="{{ route('owner.dashboard') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors
              {{ $active === 'dashboard' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
        </svg>
        Dashboard
    </a>@endif

    @php($sidebarUnread = $sidebarUser?->notifications()->whereNull('read_at')->count() ?? 0)
    <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors {{ $active === 'notifications' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 01-6 0"/></svg><span>Notifications</span>@if($sidebarUnread)<span class="ml-auto rounded-full bg-orange-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $sidebarUnread }}</span>@endif</a>

    @if($sidebarCan('property.view'))<a href="{{ route('owner.properties.index') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors
              {{ $active === 'properties' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
        Properties
    </a>@endif

    @if($sidebarCan('booking.view_history'))<a href="{{ route('owner.bookings') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors relative
              {{ $active === 'bookings' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Bookings
    </a>@endif

    @if($sidebarCan('booking.communicate'))<a href="{{ route('owner.messages.index') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors relative
              {{ $active === 'messages' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m8-2a9 9 0 11-4.2-7.6L21 3l-1.4 4.2A8.96 8.96 0 0121 12z"/>
        </svg>
        Messages
    </a>@endif

    @if($sidebarCan('calendar.view'))<a href="{{ route('owner.calendar') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors
              {{ $active === 'calendar' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
        </svg>
        Calendar
    </a>@endif

    {{-- Manage --}}
    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider px-2 mb-1.5">Manage</p>

    @if($sidebarCan('finance.view_transactions'))<a href="{{ route('owner.finance') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors {{ $active === 'finance' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
        </svg>
        Finance
    </a>@endif

    @if($sidebarCan('task.view_assigned'))<a href="{{ route($sidebarTaskOnly ? 'staff.tasks.index' : 'owner.operations') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors {{ $active === 'operations' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} relative">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Operations
    </a>@endif

    @if($sidebarCan('employee.invite'))
    <a href="{{ route('owner.team.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg mb-0.5 text-sm font-medium transition-colors {{ $active === 'team' ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
        <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h3m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6"/></svg>
        Team & access
    </a>
    @endif

    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg mb-3 text-sm font-medium transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-50">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Marketplace
    </a>

</nav>
