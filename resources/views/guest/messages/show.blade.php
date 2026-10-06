@extends('layouts.app')

@section('title', 'Booking messages – Verified Shortlet')

@section('content')
<x-navbar />
@php
    $media = $booking->property->media->firstWhere('is_primary', true) ?? $booking->property->media->first();
    $image = $media?->external_url ?: ($media?->storage_path ? '/storage/'.ltrim($media->storage_path, '/') : '/image.png');
    $nights = $booking->arrival_date->diffInDays($booking->departure_date);
    $hostName = Str::of($booking->business?->primary_contact_name ?: 'Property team')->squish();
    $hostInitials = $hostName->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->join('');
@endphp
<main class="min-h-screen bg-[#f3f3f2] px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-[1380px]">
        <div><h1 class="text-3xl font-black text-slate-950">Messages</h1><p class="mt-1 text-sm text-slate-500">Verified Shortlet &gt; Messages &gt; {{ $booking->reference }}</p></div>
        @if(session('status'))<div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif

        <div class="mt-6 grid gap-5 lg:grid-cols-[280px_minmax(0,1fr)_300px]">
            <aside class="h-fit overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-200 p-4"><a href="{{ route('guest.messages.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-slate-700">← All conversations</a></div>
                <div class="border-l-4 border-orange-600 bg-slate-100 p-5"><p class="text-xs font-black uppercase tracking-wide text-orange-600">{{ $booking->reference }}</p><h2 class="mt-2 font-black text-slate-950">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h2><p class="mt-2 text-xs text-slate-500">{{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M Y') }}</p></div>
            </aside>

            <section class="flex min-h-[650px] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <header class="flex items-center justify-between gap-4 border-b border-slate-200 p-5"><div class="flex items-center gap-3"><span class="grid size-11 place-items-center rounded-full bg-orange-100 font-black text-orange-800">{{ $hostInitials }}</span><div><h2 class="font-black text-slate-950">{{ $hostName }}</h2><p class="text-xs text-slate-500">Host · {{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</p></div></div><a href="{{ route('guest.bookings.show', $booking) }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-black text-slate-800">View booking</a></header>
                <div class="flex-1 space-y-4 bg-white p-5 sm:p-7">
                    <p class="mx-auto w-fit rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600">Booking {{ $booking->reference }} · {{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M') }}</p>
                    @forelse($booking->interactions as $message)
                        @php($mine = $message->user_id === auth()->id())
                        <article class="flex {{ $mine ? 'justify-end' : 'justify-start' }}"><div class="max-w-[85%] rounded-2xl px-5 py-3 text-sm {{ $mine ? 'rounded-tr-sm bg-orange-600 text-white' : 'rounded-tl-sm bg-slate-100 text-slate-700' }}"><p class="leading-6">{{ $message->content }}</p><p class="mt-1 text-right text-[10px] {{ $mine ? 'text-orange-100' : 'text-slate-400' }}">{{ $message->occurred_at->format('H:i') }}</p></div></article>
                    @empty
                        <div class="grid min-h-72 place-items-center text-center"><div><h3 class="font-black text-slate-950">Start the conversation</h3><p class="mt-2 text-sm text-slate-500">Ask the property team about check-in, directions or your stay.</p></div></div>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('guest.bookings.messages.store', $booking) }}" class="border-t border-slate-200 p-4">@csrf<div class="flex items-end gap-3"><textarea name="content" required minlength="2" maxlength="2000" rows="2" class="min-h-[52px] flex-1 resize-none rounded-2xl px-4 py-3 text-sm" placeholder="Write a message…">{{ old('content') }}</textarea><button aria-label="Send message" class="grid size-12 shrink-0 place-items-center rounded-full bg-orange-600 font-black text-white">➤</button></div></form>
            </section>

            <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5"><h2 class="text-lg font-black">Booking</h2><img src="{{ $image }}" onerror="this.src='/image.png'" alt="" class="mt-4 h-36 w-full rounded-xl object-cover"><h3 class="mt-4 font-black text-slate-950">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h3><span class="mt-3 inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800">{{ str($booking->status->value)->replace('_', ' ')->title() }}</span><dl class="mt-4 space-y-3 text-sm text-slate-600"><div>📅 {{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M') }} · {{ $nights }} {{ Str::plural('night', $nights) }}</div><div>👥 {{ $booking->number_of_guests }} {{ Str::plural('guest', $booking->number_of_guests) }}</div><div>₦{{ number_format((float) $booking->total_amount) }}</div></dl><a href="{{ route('guest.bookings.show', $booking) }}" class="mt-5 block rounded-xl bg-slate-950 px-5 py-3 text-center text-sm font-black text-white">View booking</a><div class="mt-5 border-t border-slate-200 pt-5"><p class="text-sm font-black">Host</p><div class="mt-3 flex items-center gap-3"><span class="grid size-10 place-items-center rounded-full bg-orange-100 font-black text-orange-800">{{ $hostInitials }}</span><div><p class="text-sm font-black">{{ $hostName }}</p><p class="text-xs text-slate-500">{{ $booking->property->superhost_badge_enabled ? '★ Super host' : 'Property team' }}</p></div></div></div></aside>
        </div>
    </div>
</main>
@endsection
