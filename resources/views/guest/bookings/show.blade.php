@extends('layouts.app')
@section('content')
<x-navbar />
<main class="max-w-3xl mx-auto px-4 py-12">
    <a href="{{ route('guest.bookings.index') }}" class="text-orange-600">← My bookings</a>
    <section class="mt-6 rounded-3xl border border-gray-200 bg-white p-8">
        <p class="text-xs font-bold uppercase text-orange-500">{{ $booking->reference }}</p>
        <h1 class="text-3xl font-extrabold mt-2">{{ $booking->property->name }}</h1>
        <p class="mt-4">{{ $booking->arrival_date->format('d M Y') }} – {{ $booking->departure_date->format('d M Y') }}</p>
        <p class="mt-2">{{ $booking->number_of_guests }} guests · {{ ucfirst($booking->status->value) }}</p>
        <p class="text-2xl font-bold mt-6">₦{{ number_format((float)$booking->total_amount) }}</p>
    </section>
</main>
@endsection
