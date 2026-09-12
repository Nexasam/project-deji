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
            @auth
                <form method="POST" action="{{ route('marketplace.checkout.store', $property->marketplaceListing->slug) }}" class="mt-8 grid grid-cols-2 gap-3 rounded-2xl bg-gray-50 p-5">
                    @csrf
                    <label class="text-xs font-bold">Check in<input required type="date" name="arrival_date" min="{{ now()->toDateString() }}" class="mt-1 w-full rounded-xl border-gray-300"></label>
                    <label class="text-xs font-bold">Check out<input required type="date" name="departure_date" min="{{ now()->addDay()->toDateString() }}" class="mt-1 w-full rounded-xl border-gray-300"></label>
                    <label class="text-xs font-bold">Adults<input required type="number" name="adult_count" value="1" min="1" max="{{ $property->capacity }}" class="mt-1 w-full rounded-xl border-gray-300"></label>
                    <label class="text-xs font-bold">Children<input type="number" name="child_count" value="0" min="0" class="mt-1 w-full rounded-xl border-gray-300"></label>
                    <input type="hidden" name="idempotency_key" value="{{ (string) Str::uuid() }}">
                    <p class="col-span-2 text-xs text-gray-500">Payment is simulated in this development version. Your final amount is calculated securely before confirmation.</p>
                    <button class="col-span-2 rounded-xl bg-orange-500 py-3 font-bold text-white">Book and simulate payment</button>
                </form>
            @else
                <div class="mt-8 rounded-2xl bg-gray-50 p-5 text-center">
                    <p class="text-sm text-gray-600">Create an account to book securely and manage this stay whenever you return.</p>
                    <a href="{{ route('register', ['redirect' => request()->getRequestUri()]) }}" class="mt-4 block rounded-xl bg-orange-500 py-3 font-bold text-white hover:bg-orange-600">Create account to book</a>
                    <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="mt-3 inline-block text-sm font-semibold text-gray-700 hover:text-orange-600">Already have an account? Log in</a>
                </div>
            @endauth
        </section>
    </div>
</main>
@endsection
