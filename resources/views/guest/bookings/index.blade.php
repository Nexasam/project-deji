@extends('layouts.app')
@section('content')
<x-navbar />
<main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-orange-600">Your trips</p><h1 class="mt-2 text-3xl font-extrabold text-slate-950 sm:text-4xl">My bookings</h1><p class="mt-2 text-sm text-slate-500">Everything you need for your upcoming and previous stays.</p></div>
        <a href="{{ route('home') }}#marketplace" class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-orange-700">Explore more stays</a>
    </div>

    <div class="mt-8 grid gap-5">
        @forelse($bookings as $booking)
            @php
                $media = $booking->property->media->firstWhere('is_primary', true) ?? $booking->property->media->firstWhere('media_type', \App\Enums\PropertyMediaType::Image);
                $image = $media?->external_url ?: ($media?->storage_path ? '/storage/'.ltrim($media->storage_path, '/') : '/image.png');
                $status = $booking->status->value;
                $statusClass = match($status) {'confirmed', 'checked_in' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-red-100 text-red-700', 'awaiting_payment' => 'bg-amber-100 text-amber-800', default => 'bg-slate-100 text-slate-700'};
                $nights = $booking->arrival_date->diffInDays($booking->departure_date);
            @endphp
            <article class="group grid overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg sm:grid-cols-[220px_1fr]">
                <a class="h-48 overflow-hidden bg-slate-100 sm:h-full" href="{{ route('guest.bookings.show', $booking) }}">
                    <img src="{{ $image }}" onerror="this.src='/image.png'" alt="{{ $booking->property->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                </a>
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-wider text-orange-600">{{ $booking->reference }}</p><h2 class="mt-1 text-xl font-extrabold text-slate-950"><a href="{{ route('guest.bookings.show', $booking) }}" class="hover:text-orange-600">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</a></h2><p class="mt-1 text-sm text-slate-500">{{ data_get($booking->property->address, 'city') }}, {{ data_get($booking->property->address, 'state') }}</p></div><span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $statusClass }}">{{ str($status)->replace('_', ' ')->title() }}</span></div>
                        <div class="mt-5 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4"><div><p class="text-xs text-slate-400">Check in</p><strong>{{ $booking->arrival_date->format('d M Y') }}</strong></div><div><p class="text-xs text-slate-400">Check out</p><strong>{{ $booking->departure_date->format('d M Y') }}</strong></div><div><p class="text-xs text-slate-400">Stay</p><strong>{{ $nights }} {{ Str::plural('night', $nights) }} · {{ $booking->number_of_guests }} {{ Str::plural('guest', $booking->number_of_guests) }}</strong></div><div><p class="text-xs text-slate-400">Total</p><strong class="text-orange-600">₦{{ number_format((float)$booking->total_amount) }}</strong></div></div>
                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 text-xs"><span class="font-semibold text-slate-500">Payment: {{ str($booking->payment_status->value)->replace('_', ' ')->title() }}</span><span class="flex flex-wrap gap-2"><a href="{{ route('guest.bookings.messages.show', $booking) }}" class="rounded-xl border border-slate-200 px-4 py-2 font-extrabold text-slate-700 hover:border-orange-200 hover:text-orange-600">Message</a><a href="{{ route('guest.bookings.show', $booking) }}" class="rounded-xl bg-orange-600 px-4 py-2 font-extrabold text-white">View booking</a></span></div>
                    </div>
            </article>
        @empty
            <section class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center"><div class="mx-auto flex size-14 items-center justify-center rounded-full bg-orange-100 text-2xl">⌂</div><h2 class="mt-4 text-xl font-extrabold">No bookings yet</h2><p class="mt-2 text-sm text-slate-500">When you reserve a stay, all its details will appear here.</p><a href="{{ route('home') }}#marketplace" class="mt-5 inline-flex rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white">Find a stay</a></section>
        @endforelse
    </div>
    <div class="mt-7">{{ $bookings->links() }}</div>
</main>
@endsection
