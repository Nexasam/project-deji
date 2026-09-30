@extends('layouts.app')

@section('title', 'Messages – Verified Shortlet')

@section('content')
<x-navbar />
<main class="min-h-screen bg-[#f3f3f2] px-4 py-10 sm:px-6 sm:py-12">
<div class="mx-auto max-w-[1380px]">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Messages</h1>
            <p class="mt-2 text-sm text-slate-500">Verified Shortlet &gt; Messages</p>
        </div>
        <a href="{{ route('guest.dashboard') }}" class="inline-flex rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700">Dashboard</a>
    </div>

    <section class="mt-8 space-y-4">
        @forelse($bookings as $booking)
            @php($latest = $booking->interactions->first())
            <a href="{{ route('guest.bookings.messages.show', $booking) }}" class="block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-600">{{ $booking->reference }}</p>
                        <h2 class="mt-1 text-lg font-black text-slate-950">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h2>
                        <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $latest?->content ?: 'Open this thread to continue the conversation.' }}</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $latest?->occurred_at?->diffForHumans() ?: 'No messages' }}</span>
                </div>
            </a>
        @empty
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center">
                <h2 class="text-xl font-black text-slate-950">No messages yet</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">Messages are tied to bookings. Start a conversation from one of your bookings below.</p>
                @if($startableBookings->isNotEmpty())
                    <div class="mx-auto mt-6 grid max-w-2xl gap-3 text-left">
                        @foreach($startableBookings as $booking)
                            <a href="{{ route('guest.bookings.messages.show', $booking) }}" class="flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-orange-200 hover:shadow-md sm:flex-row sm:items-center sm:justify-between">
                                <span>
                                    <span class="block text-xs font-bold uppercase tracking-wider text-orange-600">{{ $booking->reference }}</span>
                                    <span class="mt-1 block font-black text-slate-950">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M Y') }}</span>
                                </span>
                                <span class="shrink-0 rounded-xl bg-orange-600 px-4 py-2 text-center text-xs font-black text-white">Message property team</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <a href="{{ route('guest.bookings.index') }}" class="mt-5 inline-flex rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white">View bookings</a>
                @endif
            </div>
        @endforelse
    </section>

    <div class="mt-7">{{ $bookings->links() }}</div>
</div>
</main>
@endsection
