@extends('layouts.app')

@section('title', 'Dashboard – Verified Shortlet')

@section('content')
<x-navbar />

@php
    $firstName = Str::of(auth()->user()->name ?? 'Guest')->squish()->explode(' ')->first() ?: 'Guest';
@endphp

<main class="bg-white">
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.15fr_.85fr] lg:items-start">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.18em] text-orange-600">Guest workspace</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Welcome {{ $firstName }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">Pick up from where you left off, manage your reservations and complete the final steps for a smoother stay.</p>

                <div class="mt-8 rounded-[2rem] border border-slate-200 bg-slate-950 p-5 text-white shadow-xl shadow-slate-200/70 sm:p-6">
                    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[.18em] text-orange-300">Resume quickly</p>
                            @if($resumeBooking)
                                <h2 class="mt-2 text-2xl font-black">{{ $resumeBooking->property->marketplaceListing?->public_title ?: $resumeBooking->property->name }}</h2>
                                <p class="mt-2 text-sm text-slate-300">{{ $resumeBooking->arrival_date->format('d M') }} – {{ $resumeBooking->departure_date->format('d M Y') }} · {{ str($resumeBooking->status->value)->replace('_', ' ')->title() }}</p>
                            @else
                                <h2 class="mt-2 text-2xl font-black">Find your next verified stay</h2>
                                <p class="mt-2 text-sm text-slate-300">Search available apartments, compare totals and book with protected availability.</p>
                            @endif
                        </div>
                        <a href="{{ $resumeBooking ? route('guest.bookings.show', $resumeBooking) : route('home').'#marketplace' }}" class="inline-flex items-center justify-center rounded-2xl bg-orange-500 px-5 py-3 text-sm font-black text-white transition hover:bg-orange-600">
                            {{ $resumeBooking ? 'Continue' : 'Explore stays' }}
                        </a>
                    </div>
                </div>
            </div>

            <aside class="rounded-[2rem] border border-orange-100 bg-orange-50/70 p-5 sm:p-6">
                <p class="text-xs font-black uppercase tracking-[.18em] text-orange-600">Your next steps</p>
                <div class="mt-4 space-y-3">
                    @forelse($nextSteps as $step)
                        <article class="flex items-center justify-between gap-4 rounded-2xl border border-white bg-white p-4 shadow-sm">
                            <div class="min-w-0">
                                <h2 class="font-extrabold text-slate-950">{{ $step['title'] }}</h2>
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $step['description'] }}</p>
                            </div>
                            <a href="{{ $step['url'] }}" class="shrink-0 rounded-xl px-4 py-2.5 text-xs font-black {{ $step['tone'] === 'dark' ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-900 hover:bg-slate-200' }}">{{ $step['action'] }}</a>
                        </article>
                    @empty
                        <article class="rounded-2xl border border-white bg-white p-5 shadow-sm">
                            <h2 class="font-extrabold text-slate-950">You are all set</h2>
                            <p class="mt-1 text-sm text-slate-500">Your profile and current stays do not need any action right now.</p>
                        </article>
                    @endforelse
                </div>
            </aside>
        </div>

        <section class="mt-10 grid gap-4 md:grid-cols-3">
            <a href="{{ route('guest.bookings.index') }}" class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg">
                <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-extrabold uppercase tracking-[.16em] text-orange-600">Bookings</p><h2 class="mt-2 text-2xl font-black text-slate-950">{{ $counts['all'] }}</h2><p class="mt-1 text-sm text-slate-500">View upcoming, active and past stays.</p></div><span class="grid size-10 place-items-center rounded-2xl bg-orange-50 text-orange-600">⌂</span></div>
                <p class="mt-4 text-sm font-black text-orange-600">Open bookings →</p>
            </a>
            <a href="{{ route('guest.messages.index') }}" class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg">
                <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-extrabold uppercase tracking-[.16em] text-orange-600">Messages</p><h2 class="mt-2 text-2xl font-black text-slate-950">{{ $messageThreads->count() }}</h2><p class="mt-1 text-sm text-slate-500">Continue booking conversations.</p></div><span class="grid size-10 place-items-center rounded-2xl bg-slate-100 text-slate-700">✉</span></div>
                <p class="mt-4 text-sm font-black text-orange-600">Open messages →</p>
            </a>
            <a href="{{ $reviewBooking ? route('guest.bookings.show', $reviewBooking) : route('guest.bookings.index') }}" class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg">
                <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-extrabold uppercase tracking-[.16em] text-orange-600">Reviews</p><h2 class="mt-2 text-2xl font-black text-slate-950">{{ $reviewBooking ? '1' : '0' }}</h2><p class="mt-1 text-sm text-slate-500">{{ $reviewBooking ? 'A completed stay is ready for review.' : 'Completed stays ready for review appear here.' }}</p></div><span class="grid size-10 place-items-center rounded-2xl bg-emerald-50 text-emerald-700">★</span></div>
                <p class="mt-4 text-sm font-black text-orange-600">{{ $reviewBooking ? 'Review stay' : 'View stays' }} →</p>
            </a>
        </section>

        <section class="mt-10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-950">Your reservations</h2>
                    <p class="mt-1 text-sm text-slate-500">A simple snapshot of upcoming, active and recent stays.</p>
                </div>
                <a href="{{ route('guest.bookings.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">View all bookings</a>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                @foreach($tabs as $key => $tab)
                    <a
                        href="{{ route('guest.dashboard', $key === 'all' ? [] : ['tab' => $key]) }}"
                        aria-current="{{ $activeTab === $key ? 'page' : 'false' }}"
                        class="rounded-full border px-4 py-2 text-sm font-bold transition {{ $activeTab === $key ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-orange-200 hover:text-orange-600' }}"
                    >{{ $tab['label'] }} ({{ $counts[$key] }})</a>
                @endforeach
            </div>

            <div class="mt-6 grid gap-4">
                @forelse($bookings as $booking)
                    @php
                        $media = $booking->property->media->firstWhere('is_primary', true) ?? $booking->property->media->firstWhere('media_type', \App\Enums\PropertyMediaType::Image);
                        $image = $media?->external_url ?: ($media?->storage_path ? '/storage/'.ltrim($media->storage_path, '/') : '/image.png');
                        $status = $booking->status->value;
                        $statusClass = match($status) {'confirmed', 'checked_in' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-red-100 text-red-700', 'awaiting_payment', 'reserved' => 'bg-amber-100 text-amber-800', 'completed', 'checked_out' => 'bg-slate-100 text-slate-700', default => 'bg-slate-100 text-slate-700'};
                    @endphp
                    <a href="{{ route('guest.bookings.show', $booking) }}" class="group grid overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg sm:grid-cols-[170px_1fr]">
                        <div class="h-44 overflow-hidden bg-slate-100 sm:h-full">
                            <img src="{{ $image }}" onerror="this.src='/image.png'" alt="{{ $booking->property->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-orange-600">{{ $booking->reference }}</p>
                                    <h3 class="mt-1 text-lg font-black text-slate-950">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ data_get($booking->property->address, 'city') }}, {{ data_get($booking->property->address, 'state') }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusClass }}">{{ str($status)->replace('_', ' ')->title() }}</span>
                            </div>
                            <div class="mt-5 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                                <div><p class="text-xs text-slate-400">Check in</p><strong>{{ $booking->arrival_date->format('d M Y') }}</strong></div>
                                <div><p class="text-xs text-slate-400">Check out</p><strong>{{ $booking->departure_date->format('d M Y') }}</strong></div>
                                <div><p class="text-xs text-slate-400">Guests</p><strong>{{ $booking->number_of_guests }} {{ Str::plural('guest', $booking->number_of_guests) }}</strong></div>
                                <div><p class="text-xs text-slate-400">Total</p><strong class="text-orange-600">₦{{ number_format((float)$booking->total_amount) }}</strong></div>
                            </div>
                            <div class="mt-5 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                                <span class="inline-flex rounded-xl bg-orange-600 px-4 py-2 text-xs font-black text-white">View booking</span>
                                <span onclick="event.preventDefault(); event.stopPropagation(); window.location='{{ route('guest.bookings.messages.show', $booking) }}'" class="inline-flex rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-700 hover:border-orange-200 hover:text-orange-600">Message property team</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                        <div class="mx-auto grid size-14 place-items-center rounded-full bg-orange-100 text-2xl">⌂</div>
                        <h3 class="mt-4 text-xl font-black text-slate-950">No reservations in this view</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ $activeTab === 'all' ? 'Start with verified stays and your bookings will appear here.' : 'Try another reservation tab or explore a new verified stay.' }}</p>
                        <div class="mt-5 flex flex-wrap justify-center gap-3">
                            @if($activeTab !== 'all')
                                <a href="{{ route('guest.dashboard') }}" class="inline-flex rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700">View all</a>
                            @endif
                            <a href="{{ route('home') }}#marketplace" class="inline-flex rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white">Start exploring</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </section>
</main>
@endsection
