<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $canManageBlocks = app(\App\Services\Access\BusinessPermissionService::class)->allows(auth()->user(), $activeBusinessContext, 'calendar.manage_blocks');
    $selectedPropertyName = $selectedProperty ? $properties->firstWhere('id', $selectedProperty)?->name : 'All properties';
    $agendaDays = $days->filter(fn ($day) => $day['in_month'] && ($day['events']->isNotEmpty() || $day['date']->isToday()));
@endphp
<body
    class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased"
    x-data="{
        sidebarOpen: false,
        blockModal: @js($errors->any()),
        blockStart: @js(old('starts_on', '')),
        blockEnd: @js(old('ends_on', '')),
        blockReason: @js(old('reason', '')),
        today: @js(today()->toDateString())
    }"
>
<div class="flex min-h-screen">
    @include('partials.sidebar-nav', ['active' => 'calendar'])

    <div class="min-w-0 flex-1">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-[1600px] items-center justify-between gap-4 px-4 py-4 lg:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Open navigation">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-600">{{ $business->name }}</p>
                        <h1 class="mt-1 truncate text-xl font-extrabold tracking-tight">Calendar</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="hidden items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-bold sm:inline-flex {{ $calendarHealth['attention'] ? 'border-red-200 bg-red-50 text-red-700' : ($calendarHealth['connections'] ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-600') }}">
                        <span class="size-1.5 rounded-full {{ $calendarHealth['attention'] ? 'bg-red-500' : ($calendarHealth['connections'] ? 'bg-emerald-500' : 'bg-slate-400') }}"></span>
                        {{ $calendarHealth['connections'] ? ($calendarHealth['attention'] ? 'Sync needs attention' : 'Calendars healthy') : 'No external calendars' }}
                    </span>
                    <x-owner.view-switch route-name="owner.calendar" mode="real" />
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-[1600px] px-4 py-6 lg:px-6 lg:py-8">
            @if (session('status'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <p class="font-semibold">{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-bold">The dates could not be blocked.</p>
                    <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <section class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-semibold text-orange-600">{{ $monthStart->format('F Y') }} · {{ $selectedPropertyName }}</p>
                    <h2 class="mt-1 text-xl font-extrabold tracking-tight">Calendar overview</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Bookings, owner blocks, operational work and synchronized reservations in one clear monthly view.</p>
                </div>
                @if ($canManageBlocks)
                    <button type="button" @click="blockModal = true" class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-orange-700">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Block dates
                    </button>
                @endif
            </section>

            <section class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ([
                    ['Bookings', $bookings->count(), 'Current and historical stays', 'orange'],
                    ['Blocked periods', $blocks->count(), 'Owner and imported blocks', 'red'],
                    ['Operations due', $tasks->count(), 'Open tasks this month', 'violet'],
                    ['Calendar feeds', $calendarHealth['connections'], $calendarHealth['attention'] ? $calendarHealth['attention'].' need attention' : 'External sync connections', 'emerald'],
                ] as [$label, $value, $detail, $tone])
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $label }}</p>
                            <span class="size-2 rounded-full {{ match ($tone) { 'orange' => 'bg-orange-500', 'red' => 'bg-red-500', 'violet' => 'bg-violet-500', default => 'bg-emerald-500' } }}"></span>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold">{{ $value }}</p>
                        <p class="mt-1 hidden text-xs text-slate-500 sm:block">{{ $detail }}</p>
                    </article>
                @endforeach
            </section>

            <section class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-slate-100 p-4 sm:p-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center justify-between gap-2 sm:justify-start">
                        <a href="{{ route('owner.calendar', ['month' => $previousMonth, 'property' => $selectedProperty]) }}" class="grid size-10 place-items-center rounded-xl border border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50" aria-label="Previous month">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <div class="min-w-44 text-center">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Month at a glance</p>
                            <h3 class="mt-0.5 text-lg font-extrabold">{{ $monthStart->format('F Y') }}</h3>
                        </div>
                        <a href="{{ route('owner.calendar', ['month' => $nextMonth, 'property' => $selectedProperty]) }}" class="grid size-10 place-items-center rounded-xl border border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50" aria-label="Next month">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <a href="{{ route('owner.calendar', ['month' => now()->format('Y-m'), 'property' => $selectedProperty]) }}" class="ml-1 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Today</a>
                    </div>

                    <form method="GET" class="grid gap-2 sm:grid-cols-[9rem_minmax(12rem,1fr)_auto]">
                        <input type="month" name="month" value="{{ $monthStart->format('Y-m') }}" aria-label="Calendar month" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-semibold focus:border-orange-500 focus:ring-orange-500">
                        <select name="property" aria-label="Filter by property" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-semibold focus:border-orange-500 focus:ring-orange-500">
                            <option value="">All properties</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->id }}" @selected($selectedProperty === $property->id)>{{ $property->name }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-800">View</button>
                    </form>
                </div>

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 border-b border-slate-100 bg-slate-50/70 px-4 py-3 text-xs font-semibold text-slate-600 sm:px-5">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Legend</span>
                    <span class="inline-flex items-center gap-2"><span class="size-2 rounded-full bg-orange-500"></span>Booking</span>
                    <span class="inline-flex items-center gap-2"><span class="size-2 rounded-full bg-emerald-500"></span>Completed stay</span>
                    <span class="inline-flex items-center gap-2"><span class="size-2 rounded-full bg-red-500"></span>Unavailable</span>
                    <span class="inline-flex items-center gap-2"><span class="size-2 rounded-full bg-violet-500"></span>Operational task</span>
                    <span class="ml-auto hidden text-slate-400 sm:inline">Checkout day becomes available for the next guest.</span>
                </div>

                <div class="hidden md:block">
                    <div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50 text-center text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">
                        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                            <div class="px-2 py-3">{{ $weekday }}</div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-7">
                        @foreach ($days as $day)
                            <div class="group min-h-32 border-b border-r border-slate-100 p-2 transition-colors hover:bg-slate-50/80 xl:min-h-36 {{ $day['in_month'] ? 'bg-white' : 'bg-slate-50/60' }}">
                                <div class="flex items-center justify-between">
                                    <span class="grid size-7 place-items-center rounded-full text-xs font-extrabold {{ $day['date']->isToday() ? 'bg-orange-600 text-white' : ($day['in_month'] ? 'text-slate-700' : 'text-slate-300') }}">{{ $day['date']->day }}</span>
                                    @if ($day['events']->isNotEmpty())
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-300">{{ $day['events']->count() }} {{ Str::plural('item', $day['events']->count()) }}</span>
                                    @endif
                                </div>
                                <div class="mt-2 space-y-1.5">
                                    @foreach ($day['events']->take(3) as $event)
                                        @if ($event['type'] === 'booking')
                                            @php($historicalStay = ! $event['blocks_availability'])
                                            <a
                                                href="{{ route('owner.bookings.show', $event['booking_id']) }}"
                                                title="{{ $event['property'] }} · {{ $event['label'] }} · {{ str($event['status'])->replace('_', ' ')->title() }} · {{ $event['range_label'] }}"
                                                aria-label="{{ $event['label'] }}, {{ $event['property'] }}, {{ $event['range_label'] }}, {{ str($event['status'])->replace('_', ' ')->title() }}"
                                                data-booking-range-start="{{ $event['starts_on'] }}"
                                                data-booking-range-end="{{ $event['ends_on'] }}"
                                                class="relative z-10 flex min-h-6 items-center truncate border-y px-2 py-1 text-[10px] font-bold {{ $historicalStay ? 'border-emerald-200 bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'border-orange-200 bg-orange-100 text-orange-800 hover:bg-orange-200' }} {{ $event['segment_start'] ? 'rounded-l-lg border-l' : '-ml-2 rounded-l-none border-l-0 pl-3' }} {{ $event['segment_end'] ? 'rounded-r-lg border-r' : '-mr-2 rounded-r-none border-r-0 pr-3' }}"
                                            >
                                                <span class="mr-1 shrink-0 {{ $historicalStay ? 'text-emerald-500' : 'text-orange-500' }}">■</span><span class="truncate">{{ $event['segment_start'] ? $event['label'] : 'Continues' }}</span>
                                            </a>
                                        @elseif ($event['type'] === 'task')
                                            <a href="{{ route('owner.operations', ['q' => $event['label']]) }}" title="{{ $event['property'] }} · {{ $event['label'] }}" class="block truncate rounded-lg border border-violet-100 bg-violet-50 px-2 py-1.5 text-[10px] font-bold text-violet-800 hover:bg-violet-100">
                                                <span class="mr-1 text-violet-500">●</span>{{ $event['label'] }}
                                            </a>
                                        @else
                                            <span
                                                title="{{ $event['property'] }} · {{ $event['label'] }} · {{ $event['range_label'] }}"
                                                aria-label="{{ $event['property'] }} is unavailable from {{ $event['range_label'] }}"
                                                data-range-start="{{ $event['starts_on'] }}"
                                                data-range-end="{{ $event['ends_on'] }}"
                                                class="relative z-10 flex min-h-6 items-center truncate border-y border-red-200 bg-red-100 px-2 py-1 text-[10px] font-bold text-red-800 {{ $event['segment_start'] ? 'rounded-l-lg border-l' : '-ml-2 rounded-l-none border-l-0 pl-3' }} {{ $event['segment_end'] ? 'rounded-r-lg border-r' : '-mr-2 rounded-r-none border-r-0 pr-3' }}"
                                            >
                                                <span class="mr-1 shrink-0 text-red-500">■</span><span class="truncate">{{ $event['segment_start'] ? $event['label'] : 'Continues' }}</span>
                                            </span>
                                        @endif
                                    @endforeach
                                    @if ($day['events']->count() > 3)
                                        <p class="px-1 text-[10px] font-bold text-slate-400">+{{ $day['events']->count() - 3 }} more</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 md:hidden">
                    <div class="mb-3 flex items-center justify-between">
                        <h4 class="text-sm font-extrabold">Mobile agenda</h4>
                        <span class="text-xs text-slate-400">{{ $agendaDays->count() }} active {{ Str::plural('day', $agendaDays->count()) }}</span>
                    </div>
                    <div class="space-y-2">
                        @forelse ($agendaDays as $day)
                            <article class="rounded-2xl border border-slate-200 p-3 {{ $day['date']->isToday() ? 'ring-2 ring-orange-100' : '' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <span class="grid size-10 place-items-center rounded-xl {{ $day['date']->isToday() ? 'bg-orange-600 text-white' : 'bg-slate-100 text-slate-700' }}"><strong class="text-sm">{{ $day['date']->day }}</strong></span>
                                        <div><p class="text-sm font-bold">{{ $day['date']->format('l') }}</p><p class="text-xs text-slate-400">{{ $day['date']->format('j F Y') }}</p></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-400">{{ $day['events']->count() }}</span>
                                </div>
                                <div class="mt-3 space-y-2">
                                    @forelse ($day['events'] as $event)
                                        <div class="flex items-start gap-2 rounded-xl bg-slate-50 px-3 py-2">
                                            <span class="mt-1 size-2 shrink-0 rounded-full {{ $event['type'] === 'booking' ? ($event['blocks_availability'] ? 'bg-orange-500' : 'bg-emerald-500') : ($event['type'] === 'task' ? 'bg-violet-500' : 'bg-red-500') }}"></span>
                                            <div class="min-w-0"><p class="truncate text-xs font-bold text-slate-800">{{ $event['label'] }}</p><p class="truncate text-[11px] text-slate-500">{{ $event['property'] }}@if ($event['type'] === 'booking') · {{ str($event['status'])->replace('_', ' ')->title() }}@endif</p></div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400">No activity scheduled today.</p>
                                    @endforelse
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 px-5 py-10 text-center">
                                <p class="text-sm font-bold text-slate-700">A clear month</p>
                                <p class="mt-1 text-xs text-slate-500">No bookings, blocks or tasks are scheduled in this view.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1fr)_360px]">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4">
                        <div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Availability controls</p><h3 class="mt-1 font-extrabold">Active blocks in this view</h3></div>
                        @if ($canManageBlocks)<button type="button" @click="blockModal = true" class="text-sm font-bold text-orange-600 hover:text-orange-700">Add block →</button>@endif
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($blocks->sortBy('starts_on') as $block)
                            <article class="flex flex-col justify-between gap-3 px-5 py-4 sm:flex-row sm:items-center">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-xl bg-red-50 text-red-600"><svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 2v3M18 2v3M3.5 9h17M5 4h14a2 2 0 012 2v13a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                                    <div class="min-w-0"><p class="truncate text-sm font-bold">{{ $block->property->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $block->starts_on->format('j M Y') }} – {{ $block->ends_on->format('j M Y') }} · {{ $block->reason ?: 'Unavailable' }}</p><span class="mt-1 inline-block text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ str($block->source_type)->replace('_', ' ') }}</span></div>
                                </div>
                                @if ($canManageBlocks && $block->source_type === 'owner')
                                    <form method="POST" action="{{ route('owner.calendar.blocks.destroy', $block) }}" onsubmit="return confirm('Release these dates and make them available again?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="month" value="{{ $monthStart->format('Y-m') }}">
                                        <input type="hidden" name="property" value="{{ $selectedProperty }}">
                                        <button class="rounded-xl border border-red-200 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-50">Release dates</button>
                                    </form>
                                @endif
                            </article>
                        @empty
                            <div class="px-5 py-10 text-center"><p class="text-sm font-bold text-slate-700">No blocked periods</p><p class="mt-1 text-xs text-slate-500">This view has no owner or imported availability blocks.</p></div>
                        @endforelse
                    </div>
                </section>

                <aside class="rounded-2xl border p-5 shadow-sm {{ $calendarHealth['attention'] ? 'border-red-200 bg-red-50' : ($calendarHealth['connections'] ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white') }}">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-xl {{ $calendarHealth['attention'] ? 'bg-red-100 text-red-700' : ($calendarHealth['connections'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600') }}"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 12a4 4 0 016.83-2.83L17 11.34M16 12a4 4 0 01-6.83 2.83L7 12.66M17 8v3.34h-3.34M7 16v-3.34h3.34" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <div><p class="text-xs font-bold uppercase tracking-wider opacity-60">iCal synchronization</p><h3 class="mt-0.5 font-extrabold">{{ $calendarHealth['connections'] ? ($calendarHealth['attention'] ? 'Action required' : 'Feeds are healthy') : 'Connect when ready' }}</h3></div>
                    </div>
                    <p class="mt-4 text-sm leading-6 opacity-75">{{ $calendarHealth['connections'] ? $calendarHealth['connections'].' active connection(s). Imported reservations protect the same dates as direct bookings.' : 'iCal is optional. Connect Airbnb or Booking.com from each property’s Calendar sync section when you need it.' }}</p>
                    <a href="{{ route('owner.properties.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-bold underline decoration-current/30 underline-offset-4">Manage property calendars <span>→</span></a>
                </aside>
            </div>
        </main>
    </div>
</div>

@if ($canManageBlocks)
    <div x-show="blockModal" x-cloak style="display: none" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="block-dates-title">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" @click="blockModal = false"></div>
        <div class="relative flex min-h-full items-end justify-center p-0 sm:items-center sm:p-6">
            <section @click.stop x-transition class="flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
                    <div class="flex items-start gap-3">
                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-orange-50 text-orange-600"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M9 14h6M12 11v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                        <div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-orange-600">Availability control</p><h2 id="block-dates-title" class="mt-1 text-lg font-extrabold">Block property dates</h2><p class="mt-1 text-xs leading-5 text-slate-500">Reserve a period for maintenance, owner stays or operational work.</p></div>
                    </div>
                    <button type="button" @click="blockModal = false" class="grid size-8 shrink-0 place-items-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-900" aria-label="Close block dates dialog"><svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
                </div>
                <form method="POST" action="{{ route('owner.calendar.blocks.store') }}" class="overflow-y-auto">
                    @csrf
                    <input type="hidden" name="month" value="{{ $monthStart->format('Y-m') }}">
                    <div class="space-y-5 p-5 sm:p-6">
                        <label class="block"><span class="text-xs font-bold text-slate-700">Property</span><span class="mt-1 block text-[11px] text-slate-400">Choose the property whose availability should be protected.</span><select name="property_id" required class="mt-2 block w-full rounded-lg border-slate-200 bg-white text-sm font-semibold focus:border-orange-500 focus:ring-orange-500"><option value="">Select a property</option>@foreach ($properties as $property)<option value="{{ $property->id }}" @selected(old('property_id', $selectedProperty) === $property->id)>{{ $property->name }}</option>@endforeach</select></label>

                        <fieldset>
                            <legend class="text-xs font-bold text-slate-700">Blocked period</legend>
                            <p class="mt-1 text-[11px] text-slate-400">The property becomes available again on the end date.</p>
                            <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                <label class="rounded-lg border border-slate-200 bg-slate-50 p-3"><span class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-500"><span class="grid size-6 place-items-center rounded-md bg-white text-orange-600"><svg class="size-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3v3m10-3v3M4 9h16M5 5h14v15H5V5Z" stroke="currentColor" stroke-width="1.8"/></svg></span>Starts on</span><input type="date" name="starts_on" x-model="blockStart" :min="today" required class="mt-2 block w-full rounded-lg border-slate-200 bg-white text-sm focus:border-orange-500 focus:ring-orange-500"></label>
                                <label class="rounded-lg border border-slate-200 bg-slate-50 p-3"><span class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-500"><span class="grid size-6 place-items-center rounded-md bg-white text-emerald-600"><svg class="size-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3v3m10-3v3M4 9h16M5 5h14v15H5V5Z" stroke="currentColor" stroke-width="1.8"/></svg></span>Available again</span><input type="date" name="ends_on" x-model="blockEnd" :min="blockStart || today" required class="mt-2 block w-full rounded-lg border-slate-200 bg-white text-sm focus:border-orange-500 focus:ring-orange-500"></label>
                            </div>
                        </fieldset>

                        <div>
                            <div class="flex items-center justify-between gap-3"><label for="block-reason" class="text-xs font-bold text-slate-700">Reason</label><span class="text-[10px] text-slate-400" x-text="blockReason.length + '/1000'"></span></div>
                            <p class="mt-1 text-[11px] text-slate-400">This note is visible to your operations team.</p>
                            <div class="mt-2"><p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Quick reasons</p><div class="flex flex-wrap gap-2">@foreach (['Planned maintenance', 'Owner stay', 'Deep cleaning', 'Inspection'] as $reason)<button type="button" @click="blockReason = $el.textContent.trim()" class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-600 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-700">{{ $reason }}</button>@endforeach</div></div>
                            <textarea id="block-reason" name="reason" x-model="blockReason" required maxlength="1000" rows="3" placeholder="Add a short operational note…" class="mt-3 block w-full resize-none rounded-lg border-slate-200 bg-white text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                        </div>

                        <div class="flex items-start gap-2 rounded-lg border border-blue-100 bg-blue-50 px-3 py-2.5 text-[11px] leading-5 text-blue-800"><svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg><p>Dates are checked against every booking and synchronized calendar before the block is saved.</p></div>
                    </div>
                    <div class="sticky bottom-0 flex flex-col-reverse gap-2 border-t border-slate-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <button type="button" @click="blockModal = false" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-orange-700"><svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v18M3 12h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Confirm blocked dates</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endif
</body>
</html>
