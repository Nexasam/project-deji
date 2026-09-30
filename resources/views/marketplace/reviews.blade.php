@extends('layouts.app')

@section('title', 'Reviews – '.$property->marketplaceListing->public_title)

@section('content')
<x-navbar />
<main class="min-h-screen bg-[#f4f4f3] px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-[1380px]">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs text-slate-500">Verified Shortlet &gt; Explore stays &gt; {{ $property->marketplaceListing->public_title }} &gt; Reviews</p>
                <h1 class="mt-2 text-3xl font-black text-slate-950">Reviews</h1>
            </div>
            <a href="{{ route('marketplace.show', $property->marketplaceListing->slug) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white">View listing</a>
        </div>

        <section class="mt-6 grid gap-7 rounded-2xl bg-[#1c1c1c] p-6 text-white lg:grid-cols-[220px_320px_1fr] lg:p-8">
            <div class="border-white/10 lg:border-r">
                <p class="text-[11px] font-black uppercase tracking-wider text-orange-500">Consolidated score</p>
                <p class="mt-1 text-6xl font-black">{{ number_format($average, 1) }}</p>
                <p class="mt-2 text-xl tracking-wider text-orange-500">{{ str_repeat('★', (int) round($average)) }}<span class="text-white/20">{{ str_repeat('★', 5 - (int) round($average)) }}</span></p>
                <p class="mt-3 text-sm text-white/60">{{ $reviews->count() }} verified {{ Str::plural('review', $reviews->count()) }}</p>
            </div>
            <div class="space-y-2 border-white/10 lg:border-r lg:pr-7">
                @foreach($distribution as $score => $percentage)
                    <div class="grid grid-cols-[22px_1fr_38px] items-center gap-2 text-xs text-white/60"><span>{{ $score }}</span><span class="h-2 overflow-hidden rounded-full bg-white/15"><i class="block h-full rounded-full bg-orange-500" style="width: {{ $percentage }}%"></i></span><span>{{ $percentage }}%</span></div>
                @endforeach
            </div>
            <div class="grid gap-x-10 gap-y-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($categories as $label => $score)
                    <div><p class="text-xs text-white/50">{{ $label }}</p><p class="mt-1 text-2xl font-black">{{ $score ? number_format($score, 1) : '—' }}</p><div class="mt-2 h-1 rounded-full bg-white/15"><i class="block h-full rounded-full bg-orange-500" style="width: {{ $score ? $score / 5 * 100 : 0 }}%"></i></div></div>
                @endforeach
            </div>
        </section>

        <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="font-black text-slate-950">What guests mention most</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach(['Clean & tidy', 'Great location', 'Responsive host', 'Good value', 'Easy check-in', 'Comfortable stay'] as $mention)
                    <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700">{{ $mention }}</span>
                @endforeach
            </div>
        </section>

        <section class="mt-9">
            <h2 class="text-2xl font-black text-slate-950">All reviews</h2>
            <p class="mt-1 text-sm text-slate-500">Verified reviews from guests who completed a stay</p>
            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                @forelse($reviews as $review)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6">
                        <div class="flex items-start justify-between gap-4"><div class="flex items-center gap-3"><span class="grid size-10 place-items-center rounded-full bg-slate-100 text-sm font-black">{{ Str::of($review->guest?->name ?: 'Guest')->substr(0, 1)->upper() }}</span><div><p class="font-black text-slate-950">{{ $review->guest?->name ?: 'Verified guest' }}</p><p class="text-xs text-slate-400">{{ $review->published_at?->format('F Y') }}</p></div></div><p class="text-sm tracking-wider text-orange-500">{{ str_repeat('★', $review->rating) }}</p></div>
                        @if($review->title)<h3 class="mt-4 font-black text-slate-950">{{ $review->title }}</h3>@endif
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $review->content }}</p>
                        <p class="mt-4 text-xs font-bold text-emerald-600">✓ Verified stay</p>
                        @if($review->response)<div class="mt-4 rounded-xl bg-slate-50 p-4"><p class="text-xs font-black uppercase tracking-wide text-slate-500">Host response</p><p class="mt-2 text-sm text-slate-600">{{ $review->response->content }}</p></div>@endif
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center lg:col-span-2"><h3 class="font-black text-slate-950">No reviews yet</h3><p class="mt-2 text-sm text-slate-500">Verified reviews will appear after completed stays.</p></div>
                @endforelse
            </div>
        </section>
    </div>
</main>
@endsection
