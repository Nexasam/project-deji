@extends('layouts.admin', ['title' => 'Property publishing', 'pageTitle' => 'Property publishing'])

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div><p class="text-sm font-semibold text-slate-500">Review every listing before it reaches guests.</p><p class="mt-1 text-xs text-slate-400">{{ number_format($properties->total()) }} properties in this queue</p></div>
    </div>

    <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_14rem_auto]">
        <label class="sr-only" for="property-search">Search properties</label>
        <input id="property-search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search property, code or business" class="rounded-xl border-slate-200 text-sm focus:border-orange-400 focus:ring-orange-200">
        <label class="sr-only" for="property-status">Publication status</label>
        <select id="property-status" name="status" class="rounded-xl border-slate-200 text-sm focus:border-orange-400 focus:ring-orange-200"><option value="">All statuses</option>@foreach(['draft','pending','published','unpublished'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str($status)->title() }}</option>@endforeach</select>
        <button class="rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-black text-white hover:bg-orange-600">Apply filters</button>
    </form>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden grid-cols-[1.35fr_.8fr_.7fr_.55fr_auto] gap-4 border-b border-slate-100 bg-slate-50 px-5 py-3 text-[11px] font-black uppercase tracking-wider text-slate-500 md:grid">
            <span>Property</span><span>Business</span><span>Details</span><span>Status</span><span></span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($properties as $property)
                @php
                    $status=$property->publication_status->value;
                    $statusColor=['published'=>'bg-emerald-100 text-emerald-800','pending'=>'bg-amber-100 text-amber-800','unpublished'=>'bg-red-100 text-red-800','draft'=>'bg-slate-100 text-slate-700'][$status] ?? 'bg-slate-100 text-slate-700';
                @endphp
                <article class="grid gap-4 px-5 py-4 transition hover:bg-slate-50 md:grid-cols-[1.35fr_.8fr_.7fr_.55fr_auto] md:items-center">
                    <div><h2 class="font-black text-slate-950">{{ $property->name }}</h2><p class="mt-1 text-xs font-semibold text-slate-400">{{ $property->code }}</p></div>
                    <p class="text-sm font-semibold text-slate-700">{{ $property->business->name }}</p>
                    <p class="text-sm text-slate-600">{{ str($property->property_type)->replace('_',' ')->title() }}<span class="block text-xs text-slate-400">{{ $property->capacity }} guests</span></p>
                    <div><span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-black {{ $statusColor }}">{{ str($status)->title() }}</span></div>
                    <a href="{{ route('admin.properties.show',$property) }}" class="inline-flex justify-center rounded-xl bg-orange-600 px-4 py-2 text-sm font-black text-white hover:bg-orange-700">Review</a>
                </article>
            @empty
                <div class="p-12 text-center"><p class="font-bold text-slate-700">No properties match these filters.</p><p class="mt-1 text-sm text-slate-500">Try clearing one of the search filters.</p></div>
            @endforelse
        </div>
    </section>
    {{ $properties->links() }}
</div>
@endsection
