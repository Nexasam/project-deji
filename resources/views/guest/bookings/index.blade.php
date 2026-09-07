@extends('layouts.app')
@section('content')
<x-navbar />
<main class="max-w-5xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-extrabold">My bookings</h1>
    <div class="mt-8 space-y-4">
        @forelse($bookings as $booking)
            <a class="block rounded-2xl border border-gray-200 bg-white p-5 hover:border-orange-300" href="{{ route('guest.bookings.show', $booking) }}">
                <div class="flex justify-between gap-4"><strong>{{ $booking->property->name }}</strong><span>{{ strtoupper($booking->status->value) }}</span></div>
                <p class="text-sm text-gray-500 mt-2">{{ $booking->reference }} · {{ $booking->arrival_date->format('d M Y') }} – {{ $booking->departure_date->format('d M Y') }}</p>
                <p class="font-bold mt-3">₦{{ number_format((float)$booking->total_amount) }}</p>
            </a>
        @empty
            <p class="rounded-2xl bg-gray-50 p-8 text-gray-500">You have no bookings yet.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $bookings->links() }}</div>
</main>
@endsection
