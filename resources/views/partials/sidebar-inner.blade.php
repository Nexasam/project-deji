@php
    $active = $active ?? '';
    $mobile = $mobile ?? false;
    $sidebarUser = auth()->user();
    $sidebarBusiness = $activeBusiness ?? null;
    $sidebarContext = $activeBusinessContext ?? null;
    $sidebarCan = fn (string $permission) => $sidebarUser && $sidebarContext
        ? app(\App\Services\Access\BusinessPermissionService::class)->allows($sidebarUser, $sidebarContext, $permission)
        : false;
    $sidebarRoleKey = $sidebarContext?->roleAssignment->role->system_key ?? $sidebarContext?->roleAssignment->role->parent?->system_key;
    $sidebarTaskOnly = in_array($sidebarRoleKey, ['cleaner', 'maintenance_technician', 'inspector'], true);
    $sidebarInitials = $sidebarUser
        ? str($sidebarUser->name)->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->join('')
        : 'VS';
    $overviewItems = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'route' => route('owner.dashboard'), 'show' => $sidebarCan('business.view')],
        ['key' => 'notifications', 'label' => 'Notifications', 'icon' => 'bell', 'route' => route('notifications.index'), 'show' => true],
        ['key' => 'properties', 'label' => 'Properties', 'icon' => 'building', 'route' => route('owner.properties.index'), 'show' => $sidebarCan('property.view')],
        ['key' => 'bookings', 'label' => 'Bookings', 'icon' => 'booking', 'route' => route('owner.bookings'), 'show' => $sidebarCan('booking.view_history')],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'message', 'route' => route('owner.messages.index'), 'show' => $sidebarCan('booking.communicate')],
        ['key' => 'calendar', 'label' => 'Calendar', 'icon' => 'calendar', 'route' => route('owner.calendar'), 'show' => $sidebarCan('calendar.view')],
    ];
    $manageItems = [
        ['key' => 'finance', 'label' => 'Finance', 'icon' => 'finance', 'route' => route('owner.finance'), 'show' => $sidebarCan('finance.view_transactions')],
        ['key' => 'operations', 'label' => 'Operations', 'icon' => 'operations', 'route' => route($sidebarTaskOnly ? 'staff.tasks.index' : 'owner.operations'), 'show' => $sidebarCan('task.view_assigned')],
        ['key' => 'team', 'label' => 'Team & access', 'icon' => 'team', 'route' => route('owner.team.index'), 'show' => $sidebarCan('employee.invite')],
    ];
@endphp

<div class="border-b border-slate-200 p-4">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('owner.dashboard') }}" class="flex min-w-0 items-center gap-2" aria-label="Owner dashboard">
            <img src="/logo1.png" alt="" class="h-9 w-auto">
            <span class="truncate text-sm font-semibold text-slate-900">Verified Shortlet</span>
        </a>
        @if($mobile)<button type="button" @click="sidebarOpen=false" class="grid size-9 place-items-center rounded-lg text-slate-500 hover:bg-slate-100 md:hidden" aria-label="Close navigation"><x-ui.icon name="close" class="size-5" /></button>@endif
    </div>
</div>

<div class="border-b border-slate-200 px-4 py-3">
    <div class="flex items-center gap-3">
        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-orange-600 text-xs font-semibold text-white">{{ $sidebarInitials }}</span>
        <div class="min-w-0"><p class="truncate text-sm font-semibold text-slate-900">{{ $sidebarUser?->name ?? 'User' }}</p><p class="truncate text-xs text-slate-500">{{ $sidebarBusiness?->name ?? 'Business workspace' }}</p></div>
    </div>
</div>

@if(($availableBusinessMemberships ?? collect())->count() > 1)
<form method="POST" action="{{ route('owner.business-context.update') }}" class="border-b border-slate-200 px-4 py-3">@csrf @method('PATCH')
    <label class="text-[11px] uppercase tracking-wider text-slate-500">Active business</label>
    <select name="membership_id" onchange="this.form.submit()" class="mt-1.5 w-full text-sm">
        @foreach($availableBusinessMemberships as $membership)<option value="{{ $membership->id }}" @selected($sidebarContext?->membership->id === $membership->id)>{{ $membership->business->name }}</option>@endforeach
    </select>
</form>
@endif

<nav class="flex-1 overflow-y-auto p-3" aria-label="Business workspace">
    @foreach([['label' => 'Overview', 'items' => $overviewItems], ['label' => 'Manage', 'items' => $manageItems]] as $section)
        @if(collect($section['items'])->contains('show', true))
            <p class="mb-1.5 mt-3 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 first:mt-0">{{ $section['label'] }}</p>
            <div class="space-y-1">
                @foreach($section['items'] as $item)
                    @if($item['show'])
                        <a href="{{ $item['route'] }}" @click="sidebarOpen=false" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ $active === $item['key'] ? 'bg-orange-50 text-orange-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' }}" @if($active === $item['key']) aria-current="page" @endif>
                            <x-ui.icon :name="$item['icon']" />
                            <span>{{ $item['label'] }}</span>
                            @if($item['key'] === 'notifications' && ($sidebarUnread = $sidebarUser?->notifications()->whereNull('read_at')->count() ?? 0))<span class="ml-auto rounded-full bg-orange-600 px-2 py-0.5 text-[10px] font-semibold text-white">{{ $sidebarUnread }}</span>@endif
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    @endforeach
</nav>
