@extends('layouts.app')
@section('title', 'Bookings – Verified Shortlet')
@section('content')
<x-navbar />
@php
    $upcoming = $bookings->getCollection()->filter(fn ($booking) => in_array($booking->status->value, ['reserved','awaiting_payment','confirmed','checked_in'], true));
    $past = $bookings->getCollection()->reject(fn ($booking) => in_array($booking->status->value, ['reserved','awaiting_payment','confirmed','checked_in'], true));
    $tab = $filters['status'] ?? null;
@endphp
<main class="min-h-screen bg-[#f3f3f2] py-6 sm:py-12">
    <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10">
        <header class="flex flex-wrap items-end justify-between gap-4 sm:gap-5">
            <div><h1 class="text-2xl font-black text-slate-950 sm:text-3xl">Bookings</h1><p class="mt-1 text-xs text-slate-500 sm:mt-2 sm:text-sm">Verified Shortlet &gt; Bookings</p></div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-extrabold text-white sm:px-6 sm:py-3.5"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg> Find a stay</a>
        </header>

        <section class="mt-5 grid grid-cols-2 gap-2 sm:mt-7 sm:gap-3 lg:grid-cols-5">
            @foreach([['total','Total bookings','▣','text-slate-950'],['upcoming','Upcoming','◷','text-orange-600'],['completed','Completed','✓','text-emerald-600'],['cancelled','Cancelled','×','text-slate-950'],['pending','Pending payment','▤','text-orange-600']] as [$key,$label,$icon,$tone])
                <div class="flex min-h-20 items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-3 sm:min-h-24 sm:rounded-2xl sm:px-5 sm:py-4"><div><strong class="text-xl font-black sm:text-2xl {{ $tone }}">{{ $counts[$key] }}</strong><p class="mt-0.5 text-xs text-slate-500 sm:mt-1 sm:text-sm">{{ $label }}</p></div><span class="grid size-8 place-items-center rounded-lg bg-orange-100 text-base text-orange-600 sm:size-10 sm:rounded-xl sm:text-xl">{{ $icon }}</span></div>
            @endforeach
        </section>

        <form method="GET" class="mt-5 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex gap-2 overflow-x-auto pb-1 xl:pb-0">
                @foreach([[null,'All',$counts['total']],['upcoming','Upcoming',$counts['upcoming']],['completed','Completed',$counts['completed']],['cancelled','Cancelled',$counts['cancelled']]] as [$value,$label,$count])
                    <a href="{{ route('guest.bookings.index', array_filter(['status' => $value])) }}" class="whitespace-nowrap rounded-full px-5 py-2.5 text-sm font-bold {{ $tab === $value ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $label }} ({{ $count }})</a>
                @endforeach
            </div>
            <div class="grid gap-2 sm:grid-cols-[minmax(220px,1fr)_160px_auto]">
                <input type="hidden" name="status" value="{{ $tab }}"><input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search property or ref" class="rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500">
                <select name="sort" class="rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500"><option value="newest">Sort: Newest</option><option value="oldest" @selected(($filters['sort'] ?? '')==='oldest')>Sort: Oldest</option><option value="arrival" @selected(($filters['sort'] ?? '')==='arrival')>Arrival date</option></select>
                <button class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white">Apply</button>
            </div>
        </form>

        @if($upcoming->isNotEmpty())
            <div class="mt-8 flex items-center justify-between"><h2 class="text-xl font-black">Upcoming stays <span class="ml-2 rounded-full bg-orange-600 px-2.5 py-1 text-xs text-white">{{ $upcoming->count() }}</span></h2><a href="{{ route('guest.calendar') }}" class="text-sm font-extrabold text-orange-600">▣ View calendar</a></div>
            <div class="mt-4 grid gap-4 lg:grid-cols-3">
                @foreach($upcoming as $booking)
                    @php
                        $media=$booking->property->media->firstWhere('is_primary',true)??$booking->property->media->first(); $image=$media?->external_url?:($media?->storage_path?'/storage/'.ltrim($media->storage_path,'/'):'/image.png');
                        $status=$booking->status->value; $confirmed=in_array($status,['confirmed','checked_in'],true); $nights=$booking->arrival_date->diffInDays($booking->departure_date);
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
                        <div class="relative h-28 overflow-hidden rounded-xl sm:h-40"><img src="{{ $image }}" onerror="this.src='/image.png'" class="h-full w-full object-cover" alt=""><span class="absolute left-2 top-2 rounded-full px-3 py-1 text-[10px] font-extrabold sm:left-3 sm:top-3 sm:px-4 sm:py-1.5 sm:text-xs {{ $confirmed?'bg-emerald-700 text-white':'bg-amber-400 text-slate-950' }}">{{ str($status)->replace('_',' ')->title() }}</span>@if($status === 'checked_in')<span class="absolute right-2 top-2 rounded-full bg-slate-950 px-3 py-1 text-[10px] font-bold text-white sm:right-3 sm:top-3 sm:px-4 sm:py-1.5 sm:text-xs">Checked in</span>@elseif($booking->arrival_date->isToday())<span class="absolute right-2 top-2 rounded-full bg-slate-950 px-3 py-1 text-[10px] font-bold text-white sm:right-3 sm:top-3 sm:px-4 sm:py-1.5 sm:text-xs">Check-in today</span>@elseif($booking->arrival_date->isFuture())<span class="absolute right-2 top-2 rounded-full bg-slate-950 px-3 py-1 text-[10px] font-bold text-white sm:right-3 sm:top-3 sm:px-4 sm:py-1.5 sm:text-xs">In {{ now()->startOfDay()->diffInDays($booking->arrival_date) }} days</span>@endif</div>
                        <div class="p-2 sm:p-2.5"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="truncate text-sm font-black sm:text-base">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}</h3><p class="mt-1 truncate text-[11px] text-slate-500 sm:text-xs">⌖ {{ data_get($booking->property->address,'city') }}, {{ data_get($booking->property->address,'state') }} · {{ $booking->reference }}</p></div><div class="shrink-0 text-right"><strong class="text-sm sm:text-base">₦{{ number_format((float)$booking->total_amount) }}</strong><p class="text-[10px] text-slate-500 sm:text-xs">{{ $booking->payment_status->value==='paid'?'Paid in full':str($booking->payment_status->value)->replace('_',' ')->title() }}</p></div></div><p class="mt-2 text-xs font-semibold text-slate-600 sm:mt-3 sm:text-sm">▣ {{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M') }} · {{ $nights }} {{ Str::plural('night',$nights) }}</p><div class="mt-3 grid grid-cols-2 gap-2 sm:mt-4"><a href="{{ route('guest.bookings.show',$booking) }}" class="rounded-lg bg-slate-950 px-2 py-2.5 text-center text-xs font-bold text-white sm:px-3 sm:py-3 sm:text-sm">View details</a><a href="{{ route('guest.bookings.messages.show',$booking) }}" class="rounded-lg border border-slate-300 px-2 py-2.5 text-center text-xs font-bold sm:px-3 sm:py-3 sm:text-sm">Message host</a></div></div>
                    </article>
                @endforeach
            </div>
        @endif

        @if($past->isNotEmpty())
            <h2 class="mt-10 text-xl font-black">Past &amp; cancelled <span class="ml-2 rounded-full bg-orange-600 px-2.5 py-1 text-xs text-white">{{ $past->count() }}</span></h2>
            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white"><table class="min-w-[920px] w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-6 py-4">Stay</th><th>Booking ref</th><th>Dates</th><th>Status</th><th>Amount</th><th></th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($past as $booking)<tr><td class="px-6 py-4 font-bold">{{ $booking->property->marketplaceListing?->public_title ?: $booking->property->name }}<small class="mt-1 block font-normal text-slate-500">{{ data_get($booking->property->address,'city') }}, {{ data_get($booking->property->address,'state') }}</small></td><td>{{ $booking->reference }}</td><td><strong>{{ $booking->arrival_date->format('d M') }} – {{ $booking->departure_date->format('d M') }}</strong></td><td><span class="rounded-full px-4 py-1.5 text-xs font-bold {{ in_array($booking->status->value,['completed','checked_out'])?'bg-emerald-700 text-white':'bg-rose-600 text-white' }}">{{ str($booking->status->value)->replace('_',' ')->title() }}</span></td><td><strong>₦{{ number_format((float)$booking->total_amount) }}</strong></td><td><div class="flex items-center justify-end gap-2"><a href="{{ route('guest.bookings.messages.show',$booking) }}" class="rounded-lg border border-slate-300 px-3 py-2.5 font-bold">Message</a><a href="{{ route('guest.bookings.show',$booking) }}" class="rounded-lg bg-slate-950 px-4 py-2.5 font-bold text-white">View details</a></div></td></tr>@endforeach</tbody></table></div>
        @endif

        @if($bookings->isEmpty())<div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white p-14 text-center"><h2 class="text-xl font-black">No bookings found</h2><p class="mt-2 text-sm text-slate-500">Your stays will appear here.</p></div>@endif
        <div class="mt-6">{{ $bookings->links() }}</div>
    </div>
</main>
@endsection
