@extends('layouts.app')

@section('title', 'Booking messages – Verified Shortlet')

@section('content')
<x-navbar />
<main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-14">
    <a href="{{ route('guest.messages.index') }}" class="text-sm font-bold text-slate-600 hover:text-orange-600">← Messages</a>
    @if(session('status'))<div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif

    <section class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-100 p-5 sm:p-6">
            <p class="text-xs font-bold uppercase tracking-wider text-orange-600">{{ $booking->reference }}</p>
            <h1 class="mt-1 text-2xl font-black text-slate-950">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $booking->arrival_date->format('d M Y') }} – {{ $booking->departure_date->format('d M Y') }}</p>
        </header>

        <div class="space-y-4 bg-slate-50 p-5 sm:p-6">
            @forelse($booking->interactions as $message)
                @php($mine = $message->user_id === auth()->id())
                <article class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm shadow-sm {{ $mine ? 'rounded-tr-sm bg-orange-600 text-white' : 'rounded-tl-sm bg-white text-slate-700' }}">
                        <p class="font-bold {{ $mine ? 'text-orange-100' : 'text-slate-500' }}">{{ $mine ? 'You' : 'Property team' }}</p>
                        <p class="mt-1 leading-6">{{ $message->content }}</p>
                        <p class="mt-2 text-right text-[11px] {{ $mine ? 'text-orange-100' : 'text-slate-400' }}">{{ $message->occurred_at->format('d M, H:i') }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl bg-white p-6 text-center text-sm text-slate-500">Start the conversation with the property team.</div>
            @endforelse
        </div>

        <form method="POST" action="{{ route('guest.bookings.messages.store', $booking) }}" class="border-t border-slate-100 p-5 sm:p-6">
            @csrf
            <label class="block text-sm font-bold text-slate-700">Message property team
                <textarea name="content" required minlength="2" maxlength="2000" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Type your message…">{{ old('content') }}</textarea>
            </label>
            <div class="mt-4 flex justify-end">
                <button class="rounded-xl bg-orange-600 px-5 py-3 text-sm font-black text-white hover:bg-orange-700">Send message</button>
            </div>
        </form>
    </section>
</main>
@endsection
