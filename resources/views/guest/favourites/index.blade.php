@extends('layouts.app')

@section('title', 'Favourites – Verified Shortlet')

@section('content')
<x-navbar />
<main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-orange-600">Saved stays</p>
            <h1 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Favourites</h1>
            <p class="mt-2 text-sm text-slate-500">Return to properties you saved while browsing the marketplace.</p>
        </div>
        <a href="{{ route('home') }}#marketplace" class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-orange-700">Explore more stays</a>
    </div>

    @if(session('status'))
        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
    @endif

    <section class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($favourites as $favourite)
            @php
                $property = $favourite->property;
                $media = $property->media->firstWhere('is_primary', true) ?? $property->media->firstWhere('media_type', \App\Enums\PropertyMediaType::Image);
                $image = $media?->external_url ?: ($media?->storage_path ? '/storage/'.ltrim($media->storage_path, '/') : '/image.png');
                $listing = $property->marketplaceListing;
            @endphp
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:shadow-lg">
                <a href="{{ route('marketplace.show', $listing->slug) }}" class="block">
                    <div class="h-52 overflow-hidden bg-slate-100">
                        <img src="{{ $image }}" onerror="this.src='/image.png'" alt="{{ $listing->public_title ?: $property->name }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-600">Saved {{ $favourite->favourited_at->diffForHumans() }}</p>
                        <h2 class="mt-2 text-lg font-black text-slate-950">{{ $listing->public_title ?: $property->name }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }} · {{ $property->capacity }} guests</p>
                        <p class="mt-4 text-xl font-black text-orange-600">₦{{ number_format((float) $property->default_nightly_price) }} <span class="text-sm font-bold text-slate-400">/ night</span></p>
                    </div>
                </a>
                <div class="flex items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
                    <a href="{{ route('marketplace.show', $listing->slug) }}" class="text-sm font-black text-orange-600">View property →</a>
                    <form method="POST" action="{{ route('guest.favourites.destroy', $property) }}">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-black text-slate-600 hover:border-red-200 hover:text-red-600">Remove</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center sm:col-span-2 lg:col-span-3">
                <div class="mx-auto grid size-14 place-items-center rounded-full bg-orange-100 text-2xl">♡</div>
                <h2 class="mt-4 text-xl font-black text-slate-950">No favourites yet</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">Tap the heart on any marketplace property to save it here for later.</p>
                <a href="{{ route('home') }}#marketplace" class="mt-5 inline-flex rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white">Browse marketplace</a>
            </div>
        @endforelse
    </section>

    <div class="mt-7">{{ $favourites->links() }}</div>
</main>
@endsection
