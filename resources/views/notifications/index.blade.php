<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications – Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{ sidebarOpen: false }">
@if ($activeBusinessContext)
    <div class="flex min-h-screen">
        @include('partials.sidebar-nav', ['active' => 'notifications'])
        <div class="min-w-0 flex-1">
            <header class="flex h-[60px] items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 md:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Open navigation"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
                    <p class="truncate text-sm font-semibold text-slate-700">{{ $activeBusiness->name }}</p>
                </div>
                <x-owner.view-switch route-name="notifications.index" mode="real" />
            </header>
@else
    <x-navbar />
@endif

<main class="mx-auto w-full max-w-[1600px] px-4 py-5 lg:px-6">
    @if (session('status'))
        <div class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800"><svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><p class="font-semibold">{{ session('status') }}</p></div>
    @endif

    <section class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight">Notifications</h1>
            <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                @if ($activeBusinessContext)<span>{{ $activeBusiness->name }}</span><span aria-hidden="true">›</span>@endif
                <strong class="text-slate-800">{{ $stats['unread'] }} unread {{ Str::plural('notification', $stats['unread']) }}</strong>
            </div>
        </div>
        @if ($stats['unread'] > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-orange-700 sm:w-auto"><svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 12 4 4L18 6M12 16l2 2 6-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>Mark all as read</button></form>
        @endif
    </section>

    <section class="mt-5 grid grid-cols-3 gap-3" aria-label="Notification summary">
        @foreach ([
            ['All activity', $stats['total'], 'Complete history', 'bg-orange-50 text-orange-600'],
            ['Unread', $stats['unread'], 'Needs attention', 'bg-amber-50 text-amber-600'],
            ['Received today', $stats['today'], 'Latest updates', 'bg-blue-50 text-blue-600'],
        ] as [$label, $value, $detail, $tone])
            <article data-testid="notification-summary-card" class="rounded-lg border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-2">
                    <div><p class="text-xs font-semibold text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold">{{ $value }}</p><p class="mt-1 hidden text-[10px] text-slate-400 sm:block">{{ $detail }}</p></div>
                    <span class="grid size-7 shrink-0 place-items-center rounded-md {{ $tone }}"><svg class="size-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-4 py-3">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div><h2 class="text-sm font-bold">Recent activity</h2><p class="mt-0.5 text-[11px] text-slate-500">Notification centre for booking, payment, calendar and operational updates</p></div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <nav class="flex rounded-lg bg-slate-100 p-1" aria-label="Notification filters">
                        @foreach (['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $value => $label)
                            <a href="{{ route('notifications.index', array_filter(['status' => $value, 'q' => $filters['q']])) }}" class="flex-1 rounded-md px-3 py-1.5 text-center text-[11px] font-bold transition sm:flex-none {{ $filters['status'] === $value ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">{{ $label }}</a>
                        @endforeach
                    </nav>
                    <form method="GET" class="flex gap-2">
                        <input type="hidden" name="status" value="{{ $filters['status'] }}">
                        <label class="relative min-w-0 flex-1 sm:w-64"><span class="sr-only">Search notifications</span><svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><input name="q" value="{{ $filters['q'] }}" placeholder="Search activity" class="w-full rounded-lg border-slate-200 py-2 pl-9 pr-3 text-xs focus:border-orange-500 focus:ring-orange-500"></label>
                        <button class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white hover:bg-slate-800">Search</button>
                        @if ($filters['q'] !== '')<a href="{{ route('notifications.index', ['status' => $filters['status']]) }}" class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-500" aria-label="Clear search">×</a>@endif
                    </form>
                </div>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($notifications as $notification)
                @php
                    $isUnread = $notification->read_at === null;
                    $type = str($notification->type)->replace('_', ' ')->title();
                    $actionUrl = data_get($notification->data, 'url');
                    $actionUrl = is_string($actionUrl) && str_starts_with($actionUrl, '/') ? $actionUrl : null;
                    $tone = match (true) {
                        str_contains($notification->type, 'booking') => ['bg-orange-50 text-orange-600', 'bg-orange-500'],
                        str_contains($notification->type, 'calendar') => ['bg-red-50 text-red-600', 'bg-red-500'],
                        str_contains($notification->type, 'payment') => ['bg-emerald-50 text-emerald-600', 'bg-emerald-500'],
                        str_contains($notification->type, 'task'), str_contains($notification->type, 'service') => ['bg-violet-50 text-violet-600', 'bg-violet-500'],
                        default => ['bg-slate-100 text-slate-600', 'bg-slate-400'],
                    };
                @endphp
                <article class="relative flex items-start gap-3 px-4 py-3.5 transition hover:bg-slate-50 {{ $isUnread ? 'bg-orange-50/20' : '' }}">
                    <span class="grid size-9 shrink-0 place-items-center rounded-lg {{ $tone[0] }}">
                        @if (str_contains($notification->type, 'payment'))
                            <span class="text-sm font-extrabold">₦</span>
                        @elseif (str_contains($notification->type, 'calendar'))
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v5m0 3h.01M10.3 3.8 2.6 17.2A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.8L13.7 3.8a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @else
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0"><div class="flex items-center gap-2"><h3 class="truncate text-xs font-bold">{{ $notification->title }}</h3>@if ($isUnread)<span class="size-1.5 shrink-0 rounded-full {{ $tone[1] }}" title="Unread"></span>@endif</div><p class="mt-1 text-[11px] leading-5 text-slate-500">{{ $notification->message }}</p></div>
                            <time datetime="{{ $notification->created_at->toIso8601String() }}" class="hidden shrink-0 text-[10px] text-slate-400 sm:block" title="{{ $notification->created_at->format('j M Y, g:i A') }}">{{ $notification->created_at->diffForHumans() }}</time>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-3"><span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">{{ $type }}</span>@if ($actionUrl)<a href="{{ $actionUrl }}" class="text-[11px] font-bold text-orange-600">View details →</a>@endif @if ($isUnread)<form method="POST" action="{{ route('notifications.read', $notification) }}">@csrf<button class="text-[11px] font-bold text-slate-500 hover:text-slate-900">Mark as read</button></form>@else<span class="text-[10px] font-semibold text-slate-400">✓ Read</span>@endif</div>
                    </div>
                </article>
            @empty
                <div class="px-5 py-14 text-center"><span class="mx-auto grid size-12 place-items-center rounded-lg bg-slate-100 text-slate-400"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span><h3 class="mt-4 text-sm font-bold">{{ $filters['status'] === 'unread' ? 'You are all caught up' : 'No notifications found' }}</h3><p class="mx-auto mt-1 max-w-md text-xs text-slate-500">{{ $filters['q'] !== '' ? 'Try a different search term or clear the current filter.' : 'New booking, payment and operational updates will appear here.' }}</p>@if ($filters['status'] !== 'all' || $filters['q'] !== '')<a href="{{ route('notifications.index') }}" class="mt-4 inline-flex rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700">View all activity</a>@endif</div>
            @endforelse
        </div>

        @if ($notifications->hasPages())<div class="border-t border-slate-200 px-4 py-3">{{ $notifications->links() }}</div>@endif
    </section>

    <p class="mt-4 text-center text-[11px] text-slate-400">Notifications are private to your account. Read actions never affect another team member’s inbox.</p>
</main>

@if ($activeBusinessContext)
        </div>
    </div>
@endif
</body>
</html>
