@extends('layouts.app')

@section('title', 'Dashboard – Verified Shortlet')

@section('content')
<x-navbar />

@php
    $firstName = Str::of(auth()->user()->name ?? 'Guest')->squish()->explode(' ')->first() ?: 'Guest';
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $nextBooking = $bookings->first(fn ($booking) => $booking->departure_date->gte(today()) && in_array($booking->status->value, ['reserved','awaiting_payment','confirmed','checked_in'], true));
    $nextArrival = $nextBooking ? today()->diffInDays($nextBooking->arrival_date, false) : null;
    $statusTone = fn ($status) => match($status) {
        'confirmed', 'checked_in' => 'bg-orange-600 text-white',
        'awaiting_payment', 'reserved' => 'bg-orange-100 text-orange-700',
        'cancelled', 'refunded' => 'bg-rose-100 text-rose-700',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<main class="min-h-screen bg-[#f3f3f2]">
    <section class="mx-auto max-w-[1440px] px-4 py-7 sm:px-6 lg:px-9 lg:py-8">
        <section class="flex min-h-32 flex-col justify-between gap-5 rounded-md bg-[#1e1e1e] px-6 py-7 text-white sm:flex-row sm:items-center sm:px-12">
            <div>
                <h1 class="text-lg font-extrabold text-orange-500 sm:text-xl">{{ $greeting }}, {{ $firstName }}</h1>
                @if($nextBooking)
                    <p class="mt-3 max-w-3xl text-sm leading-5 text-slate-200 sm:text-base">Your next stay at {{ $nextBooking->property->marketplaceListing?->public_title ?: $nextBooking->property->name }} {{ $nextArrival > 0 ? 'starts in '.$nextArrival.' '.Str::plural('day', $nextArrival) : ($nextArrival === 0 ? 'starts today' : 'is currently active') }}. Manage the booking, payment and property-team messages from here.</p>
                @else
                    <p class="mt-3 max-w-3xl text-sm leading-5 text-slate-200 sm:text-base">You have no upcoming stay yet. Explore verified homes, compare live dates and save your favourites.</p>
                @endif
            </div>
            <a href="{{ $nextBooking ? route('guest.bookings.show', $nextBooking) : route('home') }}" class="inline-flex shrink-0 items-center justify-center gap-3 rounded-xl bg-orange-600 px-8 py-3.5 text-sm font-bold text-white hover:bg-orange-700">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                {{ $nextBooking ? 'View booking' : 'Explore stays' }}
            </a>
        </section>

        <section class="mt-4 grid gap-4 sm:grid-cols-3">
            <a href="{{ route('guest.bookings.index') }}" class="flex min-h-28 items-center justify-between rounded-2xl border border-slate-200 bg-white px-7 py-5 shadow-sm">
                <div><p class="text-sm text-slate-500">Upcoming stays</p><p class="mt-2 text-3xl font-black text-slate-950">{{ $dashboardStats['upcoming'] }}</p><p class="mt-2 text-xs text-slate-500">{{ $nextBooking ? 'Next: '.$nextBooking->arrival_date->format('d M') : 'No upcoming stay' }}</p></div>
                <span class="grid size-12 place-items-center rounded-xl bg-orange-100 text-orange-600"><svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg></span>
            </a>
            <a href="{{ route('guest.bookings.index', ['status' => 'completed']) }}" class="flex min-h-28 items-center justify-between rounded-2xl border border-slate-200 bg-white px-7 py-5 shadow-sm">
                <div><p class="text-sm text-slate-500">Completed stays</p><p class="mt-2 text-3xl font-black text-slate-950">{{ $dashboardStats['completed'] }}</p><p class="mt-2 text-xs text-slate-500">{{ $reviewBooking ? 'Reviews waiting' : 'All caught up' }}</p></div>
                <span class="grid size-12 place-items-center rounded-xl bg-orange-100 text-orange-600"><svg class="size-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg></span>
            </a>
            <a href="{{ route('guest.favourites.index') }}" class="flex min-h-28 items-center justify-between rounded-2xl border border-slate-200 bg-white px-7 py-5 shadow-sm">
                <div><p class="text-sm text-slate-500">Saved stays</p><p class="mt-2 text-3xl font-black text-slate-950">{{ $dashboardStats['saved'] }}</p><p class="mt-2 text-xs font-semibold text-emerald-600">Ready when you are</p></div>
                <span class="grid size-12 place-items-center rounded-xl bg-orange-100 text-orange-600"><svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg></span>
            </a>
        </section>

        <section class="mt-4 grid gap-5 lg:grid-cols-[1.44fr_1fr]">
            <div class="rounded-2xl border border-slate-200 bg-white px-6 py-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 pb-4"><h2 class="text-xl font-black text-slate-950">Upcoming stays</h2><a href="{{ route('guest.bookings.index') }}" class="text-sm font-bold text-orange-600">View all bookings <span class="ml-2">→</span></a></div>
                <div class="divide-y divide-slate-200">
                    @forelse($bookings->filter(fn ($booking) => $booking->departure_date->gte(today()))->take(3) as $booking)
                        @php
                            $media = $booking->property->media->firstWhere('is_primary', true) ?? $booking->property->media->first();
                            $image = $media?->external_url ?: ($media?->storage_path ? '/storage/'.ltrim($media->storage_path, '/') : '/image.png');
                            $nights = $booking->arrival_date->diffInDays($booking->departure_date);
                        @endphp
                        <a href="{{ route('guest.bookings.show', $booking) }}" class="grid grid-cols-[74px_minmax(0,1fr)_auto] items-center gap-4 py-3.5">
                            <img src="{{ $image }}" onerror="this.src='/image.png'" alt="" class="size-[74px] rounded-xl object-cover">
                            <div class="min-w-0"><h3 class="truncate font-extrabold text-slate-950">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h3><p class="mt-1 truncate text-sm text-slate-500">{{ data_get($booking->property->address, 'city') }}, {{ data_get($booking->property->address, 'state') }}</p><p class="mt-1 text-sm text-slate-700">{{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M') }} · {{ $nights }} {{ Str::plural('night', $nights) }}</p></div>
                            <div class="text-right"><span class="rounded-full px-3 py-1.5 text-[10px] font-bold {{ $statusTone($booking->status->value) }}">{{ str($booking->status->value)->replace('_', ' ')->title() }}</span><p class="mt-3 font-black text-slate-950">₦{{ number_format((float)$booking->total_amount) }}</p></div>
                        </a>
                    @empty
                        <div class="py-12 text-center"><p class="font-bold text-slate-900">No upcoming stays</p><a href="{{ route('home') }}" class="mt-2 inline-flex text-sm font-bold text-orange-600">Explore verified stays →</a></div>
                    @endforelse
                </div>
            </div>

            <aside class="flex min-h-[350px] flex-col rounded-2xl bg-[#1e1e1e] px-7 py-7 text-white">
                <p class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wide text-orange-500"><span class="text-xl">✦</span> AI Stay Concierge</p>
                <h2 class="mt-3 text-xl font-black">Ask before you book, not after</h2>
                <div class="mt-5 rounded-xl bg-[#2b2b2b] px-5 py-4 text-sm leading-6 text-slate-300">Hi {{ $firstName }}! Ask me about neighbourhoods, pricing, wifi or house rules for any verified stay.</div>
                <div class="mt-4 flex flex-wrap gap-3"><button type="button" class="rounded-full bg-[#2b2b2b] px-5 py-2.5 text-xs text-slate-300">Beachfront under ₦100k?</button><button type="button" class="rounded-full bg-[#2b2b2b] px-5 py-2.5 text-xs text-slate-300">Ikoyi for business?</button></div>
                <div class="mt-auto flex gap-2"><input disabled placeholder="Ask about any verified stay..." class="min-w-0 flex-1 rounded-xl border-0 bg-[#2b2b2b] px-5 text-sm text-slate-300 placeholder:text-slate-500"><button disabled class="grid size-12 shrink-0 place-items-center rounded-xl bg-orange-600" aria-label="AI Concierge coming soon"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4 20-7Z"/></svg></button></div>
            </aside>
        </section>

        @if($recommendations->isNotEmpty())
            <section class="mt-10">
                <div class="flex items-center justify-between"><h2 class="text-xl font-black text-slate-950">Recommended for You</h2><a href="{{ route('home') }}" class="text-sm font-bold text-orange-600">See all stays &gt;</a></div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($recommendations as $property)
                        @php($media = $property->media->firstWhere('is_primary', true) ?? $property->media->first())
                        @php($image = $media?->external_url ?: ($media?->storage_path ? '/storage/'.ltrim($media->storage_path, '/') : '/image.png'))
                        <a href="{{ route('marketplace.show', $property->marketplaceListing->slug) }}" class="group min-w-0"><div class="relative aspect-[1.38] overflow-hidden rounded-3xl bg-slate-200"><img src="{{ $image }}" onerror="this.src='/image.png'" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105"><span class="absolute left-4 top-4 rounded-full bg-white px-4 py-2 text-xs font-bold shadow"><span class="mr-1">✓</span> Verified</span><span class="absolute right-4 top-4 grid size-8 place-items-center rounded-full bg-white text-slate-400">♡</span></div><div class="px-4 py-3"><div class="flex items-start justify-between gap-3"><h3 class="truncate font-extrabold text-slate-950">{{ $property->marketplaceListing->public_title }}</h3><span class="shrink-0 text-sm">⭐ New</span></div><p class="mt-2 text-xs text-slate-600">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }} · {{ $property->capacity }} guests</p><div class="mt-3 flex items-center justify-between"><p class="text-sm font-black">₦{{ number_format((float)$property->default_nightly_price) }} / night</p><span class="rounded-full bg-black px-4 py-2 text-[11px] font-bold text-white">💡 AI Insights</span></div></div></a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($nextSteps->isNotEmpty())
            <section class="mt-10 rounded-2xl border border-orange-100 bg-white p-6"><div class="flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-orange-600">Complete your account</p><h2 class="mt-1 text-xl font-black">Your next steps</h2></div></div><div class="mt-5 grid gap-3 md:grid-cols-2">@foreach($nextSteps as $step)<a href="{{ $step['url'] }}" class="flex items-center justify-between rounded-xl border border-slate-200 p-4"><div><h3 class="font-bold">{{ $step['title'] }}</h3><p class="mt-1 text-xs text-slate-500">{{ $step['description'] }}</p></div><span class="ml-4 shrink-0 text-xs font-bold text-orange-600">{{ $step['action'] }} →</span></a>@endforeach</div></section>
        @endif

        <div class="sr-only">Bookings Messages Reviews Open bookings Open messages Message property team Your reservations
            @foreach($tabs as $key => $tab) {{ $tab['label'] }} ({{ $counts[$key] }}) @endforeach
            @foreach($bookings as $booking) {{ $booking->reference }} @endforeach
        </div>
    </section>
</main>
@endsection
