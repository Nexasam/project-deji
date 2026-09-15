<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Figma notifications preview – {{ $activeBusiness->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F4F4F4] font-sans text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex min-h-screen">
    @include('partials.sidebar-nav', ['active' => 'notifications'])

    <div class="min-w-0 flex-1">
        <header class="border-b border-gray-200 bg-white">
            <div class="flex min-h-14 items-center justify-between gap-3 px-4 py-2 lg:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 md:hidden" aria-label="Open navigation">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h1 class="truncate text-sm font-bold">Figma notifications preview</h1>
                            <span class="rounded-md bg-amber-100 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-800">Sample data</span>
                        </div>
                        <p class="hidden text-[11px] text-gray-500 sm:block">Original dashboard activity visual language</p>
                    </div>
                </div>
                <x-owner.view-switch route-name="notifications.index" mode="demo" />
            </div>
        </header>

        <main class="px-4 py-5 lg:px-6">
            <div class="mb-4 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-900">
                <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p><strong>Sample data:</strong> this read-only tab demonstrates the original Figma styling. Use Real view to manage your actual notifications.</p>
            </div>

            <section class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                <div>
                    <h2 class="text-xl font-bold tracking-tight">Notifications</h2>
                    <p class="mt-1 text-xs text-gray-500">Booking, payment and property activity in one place</p>
                </div>
                <button type="button" disabled class="cursor-not-allowed rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-xs font-semibold text-gray-400">Mark all as read</button>
            </section>

            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-bold">Recent activity</h3>
                        <p class="mt-0.5 text-[11px] text-gray-500">The latest updates across your workspace</p>
                    </div>
                    <nav class="inline-flex self-start rounded-md bg-gray-100 p-1 text-[11px] font-semibold" aria-label="Preview notification filters">
                        <span class="rounded bg-white px-3 py-1.5 text-gray-900 shadow-sm">All</span>
                        <span class="px-3 py-1.5 text-gray-500">Unread</span>
                        <span class="px-3 py-1.5 text-gray-500">Read</span>
                    </nav>
                </div>

                @php
                    $previewNotifications = [
                        ['booking', 'New booking confirmed', 'Lekki Waterview Suites · VS-4K9M2P', '2 minutes ago', true, 'bg-orange-50 text-orange-600'],
                        ['payment', 'Payment received', '₦180,000 · Emerald Loft', '18 minutes ago', true, 'bg-emerald-50 text-emerald-600'],
                        ['maintenance', 'Maintenance flagged', 'Air-conditioner inspection · Lekki Waterview Suites', '1 hour ago', true, 'bg-red-50 text-red-600'],
                        ['calendar', 'Calendar sync completed', 'Bluewater Suite 4B · Airbnb', '3 hours ago', false, 'bg-blue-50 text-blue-600'],
                        ['review', 'Review received', '5-star guest review · Ikoyi Skyline Loft', 'Yesterday', false, 'bg-violet-50 text-violet-600'],
                    ];
                @endphp

                <div class="divide-y divide-gray-100">
                    @foreach ($previewNotifications as [$type, $title, $detail, $time, $unread, $tone])
                        <article class="flex items-center gap-3 px-4 py-3.5 transition hover:bg-gray-50 {{ $unread ? 'bg-orange-50/20' : '' }}">
                            <div class="grid size-9 shrink-0 place-items-center rounded-lg {{ $tone }}">
                                @if ($type === 'payment')
                                    <span class="text-sm font-extrabold">₦</span>
                                @elseif ($type === 'maintenance')
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none"><path d="m14.7 6.3 3-3a4.2 4.2 0 0 1-5.4 5.4l-7.6 7.6a2.1 2.1 0 1 0 3 3l7.6-7.6a4.2 4.2 0 0 0 5.4-5.4l-3 3-3-3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @elseif ($type === 'calendar')
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none"><path d="M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                @elseif ($type === 'review')
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-2.9-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                                @else
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9Zm4 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="truncate text-xs font-bold text-gray-900">{{ $title }}</h4>
                                    @if ($unread)<span class="size-1.5 shrink-0 rounded-full bg-[#FF5A00]" title="Unread"></span>@endif
                                </div>
                                <p class="mt-1 truncate text-[11px] text-gray-500">{{ $detail }}</p>
                            </div>
                            <time class="hidden shrink-0 text-[10px] text-gray-400 sm:block">{{ $time }}</time>
                            <svg class="size-4 shrink-0 text-gray-300" viewBox="0 0 24 24" fill="none"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </article>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 bg-gray-50 px-4 py-3 text-center">
                    <span class="text-[11px] font-semibold text-gray-500">Showing presentation sample activity</span>
                </div>
            </section>
        </main>
    </div>
</div>
</body>
</html>
