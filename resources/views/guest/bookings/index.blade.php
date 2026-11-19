@extends('layouts.app')
@section('title', 'Bookings – Verified Shortlet')
@section('content')
<x-navbar />
@php
    $activeStatuses = ['reserved', 'awaiting_payment', 'confirmed', 'checked_in'];
    $upcoming = $bookings->getCollection()->filter(fn ($booking) => in_array($booking->status->value, $activeStatuses, true) && $booking->departure_date->isAfter(today()));
    $past = $bookings->getCollection()->reject(fn ($booking) => in_array($booking->status->value, $activeStatuses, true) && $booking->departure_date->isAfter(today()));
    $tab = $filters['status'] ?? null;
@endphp
<main class="min-h-screen bg-[#f3f3f2] py-6 sm:py-12">
    <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10">
        <header class="flex flex-wrap items-end justify-between gap-4 sm:gap-5">
            <div><h1 class="text-2xl font-black text-slate-950 sm:text-3xl">Bookings</h1><p class="mt-1 text-xs text-slate-500 sm:mt-2 sm:text-sm">Verified Shortlet &gt; Bookings</p></div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-extrabold text-white sm:px-6 sm:py-3.5"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg> Find a stay</a>
        </header>

        <section class="mt-5 grid grid-cols-2 gap-2 sm:mt-7 sm:gap-3 lg:grid-cols-5">
            @foreach([['total','Total bookings','▣','text-slate-950'],['upcoming','Upcoming','◷','text-orange-600'],['completed','Completed','✓','text-emerald-600'],['cancelled','Cancelled','×','text-slate-950'],['pending','Pending payment','▤','text-orange-600']] as [$key,$label,$icon,$tone])
                <div class="flex min-h-20 items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-3 sm:min-h-24 sm:rounded-2xl sm:px-5 sm:py-4"><div><strong class="text-xl font-black sm:text-2xl {{ $tone }}">{{ $counts[$key] }}</strong><p class="mt-0.5 text-xs text-slate-500 sm:mt-1 sm:text-sm">{{ $label }}</p></div><span class="grid size-8 place-items-center rounded-lg bg-orange-100 text-base text-orange-600 sm:size-10 sm:rounded-xl sm:text-xl">{{ $icon }}</span></div>
            @endforeach
        </section>

        <section class="mt-8" x-data="bookingBrowser({ q: @js($filters['q'] ?? ''), from: @js($filters['from'] ?? ''), to: @js($filters['to'] ?? ''), status: @js($tab), sort: @js($filters['sort'] ?? 'newest') })">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <h2 class="text-xl font-black">Stays <span class="ml-2 rounded-full bg-orange-600 px-2.5 py-1 text-xs text-white">{{ $upcoming->count() }}</span></h2>
                <form @submit.prevent="refresh()" class="grid gap-2 sm:grid-cols-2 xl:flex xl:items-center">
                    <label class="sr-only" for="booking-search">Search stays</label><input id="booking-search" x-model="q" @input.debounce.350ms="refresh()" placeholder="Search property" class="min-w-0 rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 xl:w-44">
                    <div class="relative sm:col-span-2 xl:col-span-1" @click.outside="rangeOpen=false"><button type="button" @click="rangeOpen=!rangeOpen" class="flex w-full min-w-0 items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-left text-sm text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 xl:w-56"><span class="truncate" x-text="rangeLabel()"></span><svg class="size-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg></button><div x-show="rangeOpen" x-cloak x-transition class="absolute right-0 z-20 mt-2 w-[min(20rem,calc(100vw-2rem))] rounded-2xl border border-slate-200 bg-white p-4 shadow-xl"><p class="text-sm font-extrabold text-slate-900">Filter by stay dates</p><div class="mt-3 grid grid-cols-2 gap-3"><label class="text-xs font-bold text-slate-600">From<input type="date" x-model="from" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500"></label><label class="text-xs font-bold text-slate-600">To<input type="date" x-model="to" :min="from || null" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500"></label></div><div class="mt-4 flex items-center justify-between gap-3"><button type="button" @click="clearDateRange()" class="text-sm font-bold text-slate-600 hover:text-slate-950">Clear</button><button type="button" @click="rangeOpen=false; refresh()" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white">Apply dates</button></div></div></div>
                    <select x-model="status" @change="refresh()" aria-label="Filter bookings by status" class="rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500"><option value="">All stays</option><option value="upcoming">Upcoming</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select>
                    <select x-model="sort" @change="refresh()" aria-label="Sort bookings" class="rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500"><option value="newest">Newest</option><option value="oldest">Oldest</option><option value="arrival">Arrival date</option></select>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white disabled:opacity-60" :disabled="loading"><span x-show="!loading">Search</span><span x-show="loading" x-cloak>Loading…</span></button>
                </form>
            </div>
            <p x-show="error" x-text="error" x-cloak class="mt-3 text-sm font-semibold text-rose-700"></p>

            <div x-ref="results" data-booking-results @click="handleResultsClick($event)">
        @if($upcoming->isNotEmpty())
            <div class="mt-4"></div>
            <div class="mt-4 grid gap-4 lg:grid-cols-3">
                @foreach($upcoming as $booking)
                    @php
                        $media=$booking->property->media->firstWhere('is_primary',true)??$booking->property->media->first(); $image=$media?->external_url?:($media?->storage_path?'/storage/'.ltrim($media->storage_path,'/'):'/image.png');
                        $status=$booking->status->value; $isBooked=in_array($status,['confirmed','checked_in'],true); $nights=$booking->arrival_date->diffInDays($booking->departure_date);
                        $isWithinStay = $booking->arrival_date->lte(today()) && $booking->departure_date->gt(today());
                        $isCompleted = $booking->departure_date->lte(today()) || in_array($status, ['checked_out', 'completed'], true);
                        $statusLabel = $status === 'confirmed' ? 'Booked' : str($status)->replace('_',' ')->title();
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
                        <div class="relative h-28 overflow-hidden rounded-xl sm:h-40"><img src="{{ $image }}" onerror="this.src='/image.png'" class="h-full w-full object-cover" alt=""><span class="absolute left-2 top-2 rounded-full px-3 py-1 text-[10px] font-extrabold sm:left-3 sm:top-3 sm:px-4 sm:py-1.5 sm:text-xs {{ $isBooked?'bg-emerald-700 text-white':'bg-amber-400 text-slate-950' }}">{{ $statusLabel }}</span><span class="absolute right-2 top-2 rounded-full bg-slate-950 px-3 py-1 text-[10px] font-bold text-white sm:right-3 sm:top-3 sm:px-4 sm:py-1.5 sm:text-xs">{{ $isWithinStay ? 'Checked in' : ($isCompleted ? 'Completed' : ($booking->arrival_date->isToday() ? 'Check-in today' : 'In '.now()->startOfDay()->diffInDays($booking->arrival_date).' days')) }}</span></div>
                        <div class="p-2 sm:p-2.5"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="truncate text-sm font-black sm:text-base">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h3><p class="mt-1 truncate text-[11px] text-slate-500 sm:text-xs">⌖ {{ data_get($booking->property->address,'city') }}, {{ data_get($booking->property->address,'state') }} · {{ $booking->reference }}</p></div><div class="shrink-0 text-right"><strong class="text-sm sm:text-base">₦{{ number_format((float)$booking->total_amount) }}</strong><p class="text-[10px] text-slate-500 sm:text-xs">{{ $booking->payment_status->value==='paid'?'Paid in full':str($booking->payment_status->value)->replace('_',' ')->title() }}</p></div></div><p class="mt-2 text-xs font-semibold text-slate-600 sm:mt-3 sm:text-sm">▣ {{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M') }} · {{ $nights }} {{ Str::plural('night',$nights) }}</p><div class="mt-3 grid grid-cols-2 gap-2 sm:mt-4"><a href="{{ route('guest.bookings.show',$booking) }}" class="rounded-lg bg-slate-950 px-2 py-2.5 text-center text-xs font-bold text-white sm:px-3 sm:py-3 sm:text-sm">View details</a><a href="{{ route('guest.bookings.messages.show',$booking) }}" class="rounded-lg border border-slate-300 px-2 py-2.5 text-center text-xs font-bold sm:px-3 sm:py-3 sm:text-sm">Message host</a></div></div>
                    </article>
                @endforeach
            </div>
        @endif

        @if($past->isNotEmpty())
            <h2 class="mt-10 text-xl font-black">History</h2>
            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white"><table class="min-w-[920px] w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-6 py-4">Stay</th><th>Booking ref</th><th>Dates</th><th>Status</th><th>Amount</th><th></th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($past as $booking)@php($isCancelled = in_array($booking->status->value, ['cancelled', 'refunded', 'no_show'], true))<tr><td class="px-6 py-4 font-bold">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}<small class="mt-1 block font-normal text-slate-500">{{ data_get($booking->property->address,'city') }}, {{ data_get($booking->property->address,'state') }}</small></td><td>{{ $booking->reference }}</td><td><strong>{{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M') }}</strong></td><td><span class="rounded-full px-4 py-1.5 text-xs font-bold {{ $isCancelled ? 'bg-rose-600 text-white' : 'bg-emerald-700 text-white' }}">{{ $isCancelled ? 'Cancelled' : 'Completed' }}</span></td><td><strong>₦{{ number_format((float)$booking->total_amount) }}</strong></td><td><div class="flex items-center justify-end gap-2"><a href="{{ route('guest.bookings.messages.show',$booking) }}" class="rounded-lg border border-slate-300 px-3 py-2.5 font-bold">Message</a><a href="{{ route('guest.bookings.show',$booking) }}" class="rounded-lg bg-slate-950 px-4 py-2.5 font-bold text-white">View details</a></div></td></tr>@endforeach</tbody></table></div>
        @endif

        @if($bookings->isEmpty())<div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white p-14 text-center"><h2 class="text-xl font-black">No bookings found</h2><p class="mt-2 text-sm text-slate-500">Your stays will appear here.</p></div>@endif
        <div class="mt-6">{{ $bookings->links() }}</div>
            </div>
        </section>
    </div>
</main>
@endsection
