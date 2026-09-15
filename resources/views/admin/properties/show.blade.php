@extends('layouts.admin', ['title' => $property->name, 'pageTitle' => 'Property review'])

@section('content')
@php
    $publication=$property->publication_status->value;
    $verification=$property->verification_status->value;
    $publicationColor=['published'=>'bg-emerald-100 text-emerald-800','pending'=>'bg-amber-100 text-amber-800','unpublished'=>'bg-red-100 text-red-800','draft'=>'bg-slate-100 text-slate-700'][$publication] ?? 'bg-slate-100 text-slate-700';
    $canDecide=app(\App\Services\PlatformPermissionService::class)->allows(auth()->user(), 'platform.property.verify');
@endphp
<div class="mx-auto max-w-6xl space-y-5" x-data="{ action: null }">
    <a href="{{ route('admin.properties.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-orange-600">← Property publishing</a>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-start">
            <div>
                <p class="text-xs font-black uppercase tracking-[.18em] text-orange-600">{{ $property->business->name }}</p>
                <h2 class="mt-2 text-2xl font-black sm:text-3xl">{{ $property->name }}</h2>
                <div class="mt-3 flex flex-wrap items-center gap-2"><span class="text-xs font-semibold text-slate-400">{{ $property->code }}</span><span class="rounded-full px-2.5 py-1 text-[11px] font-black {{ $publicationColor }}">{{ str($publication)->title() }}</span><span class="rounded-full px-2.5 py-1 text-[11px] font-black {{ $verification==='verified'?'bg-emerald-50 text-emerald-700':($verification==='rejected'?'bg-red-50 text-red-700':'bg-amber-50 text-amber-700') }}">{{ str($verification)->title() }}</span></div>
            </div>
            @if($canDecide)
                <div class="flex flex-wrap gap-2">
                    @if($publication === 'published')
                        <button type="button" @click="action='unpublish'" class="rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-black text-red-700">Unpublish</button>
                    @else
                        <button type="button" @click="action='publish'" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-black text-white">Publish property</button>
                        @if($verification !== 'rejected')<button type="button" @click="action='reject'" class="rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-black text-red-700">Reject property</button>@endif
                    @endif
                </div>
            @else
                <div class="rounded-xl bg-slate-100 px-4 py-3 text-xs font-bold text-slate-600">View-only access</div>
            @endif
        </div>

        @if($verification === 'rejected' && data_get($property->marketplaceListing?->publication_eligibility_details,'rejection_reason'))
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4"><p class="text-xs font-black uppercase tracking-wide text-red-700">Rejection reason</p><p class="mt-1 text-sm text-red-900">{{ data_get($property->marketplaceListing->publication_eligibility_details,'rejection_reason') }}</p></div>
        @endif

        <dl class="mt-8 grid gap-4 rounded-2xl bg-slate-50 p-5 text-sm sm:grid-cols-2 lg:grid-cols-4">
            <div><dt class="text-slate-500">Marketplace title</dt><dd class="mt-1 font-black">{{ $property->marketplaceListing?->public_title ?? 'Missing' }}</dd></div>
            <div><dt class="text-slate-500">Type</dt><dd class="mt-1 font-black">{{ str($property->property_type)->replace('_',' ')->title() }}</dd></div>
            <div><dt class="text-slate-500">Capacity</dt><dd class="mt-1 font-black">{{ $property->capacity }} guests</dd></div>
            <div><dt class="text-slate-500">Nightly price</dt><dd class="mt-1 font-black">{{ $property->pricing_currency }} {{ number_format((float)$property->default_nightly_price) }}</dd></div>
            <div><dt class="text-slate-500">Booking mode</dt><dd class="mt-1 font-black">{{ str($property->booking_mode ?? 'not set')->replace('_',' ')->title() }}</dd></div>
            <div><dt class="text-slate-500">Bedrooms / beds</dt><dd class="mt-1 font-black">{{ $property->bedrooms }} / {{ $property->beds }}</dd></div>
            <div><dt class="text-slate-500">Bathrooms</dt><dd class="mt-1 font-black">{{ $property->bathrooms }}</dd></div>
            <div><dt class="text-slate-500">Eligibility</dt><dd class="mt-1 font-black">{{ $property->marketplaceListing?->is_publication_eligible ? 'Eligible' : 'Not yet eligible' }}</dd></div>
        </dl>

        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 p-5"><h3 class="font-black">Location & description</h3><p class="mt-2 text-sm text-slate-600">{{ collect([data_get($property->address,'line_1'),data_get($property->address,'city'),data_get($property->address,'state'),data_get($property->address,'country_code')])->filter()->join(', ') ?: 'No address provided' }}</p><p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $property->description ?: 'No description provided' }}</p></section>
            <section class="rounded-2xl border border-slate-200 p-5"><h3 class="font-black">Amenities & inventory</h3><div class="mt-3 flex flex-wrap gap-2">@forelse($property->amenities as $amenity)<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $amenity->name }}</span>@empty<span class="text-sm text-slate-500">No amenities selected</span>@endforelse</div><div class="mt-4 flex flex-wrap gap-2">@foreach($property->assets->where('status','active') as $asset)<span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-bold text-orange-700">{{ $asset->name }}</span>@endforeach</div></section>
        </div>

        <section class="mt-6"><div class="flex items-center justify-between"><h3 class="font-black">Photos & videos</h3><span class="text-xs font-bold text-slate-400">{{ $property->media->count() }} files</span></div><div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@forelse($property->media as $media)@php $type=$media->media_type->value; $url=$media->external_url ?: ($media->storage_path ? '/storage/'.ltrim($media->storage_path,'/') : '/image.png'); @endphp<div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50"><div class="aspect-video bg-slate-900">@if($type==='video')<video controls preload="metadata" class="h-full w-full object-contain" src="{{ $url }}"></video>@else<img class="h-full w-full object-cover" src="{{ $url }}" onerror="this.src='/image.png'" alt="{{ $media->title }}">@endif</div><p class="truncate p-3 text-xs font-bold">{{ $media->title }}</p></div>@empty<p class="text-sm text-slate-500">No media uploaded</p>@endforelse</div></section>

        <div class="mt-6 grid gap-5 lg:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 p-5"><h3 class="font-black">Documents</h3><div class="mt-3 space-y-2">@forelse($property->documents as $document)@php $version=$document->versions->sortByDesc('version_number')->first(); @endphp<div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2"><span class="truncate text-sm font-semibold">{{ $document->title }}</span><span class="ml-3 shrink-0 text-xs font-bold text-slate-400">{{ strtoupper(pathinfo($version?->original_name ?? '',PATHINFO_EXTENSION) ?: 'FILE') }}</span></div>@empty<p class="text-sm text-slate-500">No documents uploaded</p>@endforelse</div></section>
            <section class="rounded-2xl border border-slate-200 p-5"><h3 class="font-black">Booking channels</h3><div class="mt-3 flex flex-wrap gap-2"><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">Verified Shortlet</span>@foreach($property->channelConnections->where('status','active') as $channel)<span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700">{{ $channel->provider==='bookingcom'?'Booking.com':str($channel->provider)->title() }}</span>@endforeach</div></section>
        </div>
    </section>

    @if($canDecide)
        <div x-cloak x-show="action" x-transition.opacity class="fixed inset-0 z-[70] grid place-items-center bg-slate-950/55 p-4 backdrop-blur-sm" @keydown.escape.window="action=null">
            <div x-show="action" x-transition class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl" @click.outside="action=null">
                <div class="flex items-start justify-between gap-4"><div><p class="text-xs font-black uppercase tracking-widest text-orange-600">Confirm decision</p><h3 class="mt-2 text-xl font-black" x-text="action === 'publish' ? 'Publish this property?' : (action === 'reject' ? 'Reject this property?' : 'Remove it from the marketplace?')"></h3></div><button type="button" @click="action=null" class="grid size-9 place-items-center rounded-full bg-slate-100 text-xl text-slate-500">×</button></div>
                <p class="mt-3 text-sm leading-6 text-slate-600">Your reason becomes part of the permanent platform audit history.</p>
                <form method="POST" :action="action === 'publish' ? '{{ route('admin.properties.publish',$property) }}' : (action === 'reject' ? '{{ route('admin.properties.reject',$property) }}' : '{{ route('admin.properties.unpublish',$property) }}')" class="mt-5">@csrf
                    <label for="decision-reason" class="text-sm font-black text-slate-800">Decision reason</label>
                    <textarea id="decision-reason" name="reason" required minlength="10" maxlength="1000" rows="4" placeholder="Summarise what you reviewed and why this decision is appropriate…" class="mt-2 block w-full rounded-xl border-slate-200 text-sm focus:border-orange-400 focus:ring-orange-200">{{ old('reason') }}</textarea>
                    <div class="mt-5 flex justify-end gap-2"><button type="button" @click="action=null" class="rounded-xl px-4 py-2.5 text-sm font-black text-slate-600">Cancel</button><button class="rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-black text-white hover:bg-orange-600">Confirm decision</button></div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
