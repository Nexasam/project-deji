@extends('layouts.app')

@section('content')
<x-navbar />
<main class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
    <a href="{{ route('home') }}" class="text-sm text-orange-600">← Back to all stays</a>
    <div class="grid lg:grid-cols-2 gap-10 mt-6">
        <img class="w-full aspect-[4/3] object-cover rounded-3xl" src="{{ optional($property->media->firstWhere('is_primary', true) ?? $property->media->first())->external_url }}" alt="{{ $property->name }}">
        <section>
            <span class="text-xs font-bold text-emerald-700">✓ Verified serviced apartment</span>
            <h1 class="text-4xl font-extrabold mt-3">{{ $property->marketplaceListing->public_title }}</h1>
            <p class="text-gray-500 mt-2">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }}</p>
            <p class="mt-6 text-gray-700">{{ $property->marketplaceListing->public_description }}</p>
            <div class="flex gap-5 mt-6 text-sm"><span>{{ $property->bedrooms }} bedrooms</span><span>{{ $property->beds }} beds</span><span>{{ $property->capacity }} guests</span></div>
            <p class="text-2xl font-bold mt-8">₦{{ number_format((float) $property->default_nightly_price) }} <span class="text-sm font-normal text-gray-500">/ night</span></p>
        </section>
    </div>
</main>
@endsection
