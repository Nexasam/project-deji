@extends('layouts.app')
@section('title', 'Calendar – Verified Shortlet')
@section('content')
<x-navbar />
@php
    $calendarStart=$month->startOfWeek(Carbon\CarbonInterface::SUNDAY); $calendarEnd=$month->endOfMonth()->endOfWeek(Carbon\CarbonInterface::SATURDAY); $calendarDays=collect();
    for($day=$calendarStart;$day->lte($calendarEnd);$day=$day->addDay()){$calendarDays->push($day);}
    $isPending=fn($booking)=>in_array($booking->status->value,['awaiting_payment','reserved'],true);
@endphp
<main class="min-h-screen bg-[#f3f3f2] py-9 sm:py-12">
    <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10">
        <header class="flex items-end justify-between gap-4"><div><h1 class="text-3xl font-black">Calendar</h1><p class="mt-2 text-sm text-slate-500">Verified Shortlet &gt; Calendar</p></div><a href="{{ route('home') }}" class="rounded-lg bg-orange-600 px-6 py-3.5 text-sm font-bold text-white">⌕ &nbsp; Find a stay</a></header>
        <div class="mt-7 grid gap-5 lg:grid-cols-[340px_1fr]">
            <aside class="rounded-2xl border border-slate-200 bg-white p-6"><h2 class="text-xl font-black">My stays</h2><div class="mt-5 space-y-4">
                @forelse($trips as $trip)
                    @php $media=$trip->property->media->firstWhere('is_primary',true)??$trip->property->media->first(); $image=$media?->external_url?:($media?->storage_path?'/storage/'.ltrim($media->storage_path,'/'):'/image.png'); @endphp
                    <a href="{{ route('guest.bookings.show',$trip) }}" class="block rounded-xl bg-slate-100 p-3"><div class="flex gap-3"><img src="{{ $image }}" onerror="this.src='/image.png'" class="size-14 rounded-lg object-cover" alt=""><div class="min-w-0"><h3 class="truncate font-black">{{ $trip->property->marketplaceListing?->public_title ?: $trip->property->name }}</h3><p class="mt-1 text-sm text-slate-500">{{ $trip->arrival_date->format('d M') }} – {{ $trip->departure_date->format('d M') }}</p></div></div><span class="mt-3 block rounded-full py-2 text-center text-xs font-extrabold {{ $isPending($trip)?'bg-amber-400 text-slate-950':'bg-emerald-700 text-white' }}">{{ $isPending($trip)?'Pending payment':'Confirmed' }}</span></a>
                @empty <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No upcoming stays.</div> @endforelse
            </div><a href="{{ route('guest.bookings.index') }}" class="mt-7 block text-center text-sm font-extrabold text-orange-600">View all bookings</a></aside>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
                <header class="flex flex-wrap items-center justify-between gap-4"><div><h2 class="text-2xl font-black">{{ $month->format('F Y') }}</h2><div class="mt-4 flex flex-wrap gap-5 text-sm text-slate-500"><span><i class="mr-2 inline-block size-3 rounded-sm border border-sky-400 bg-sky-100"></i>Confirmed stay</span><span><i class="mr-2 inline-block size-3 rounded-sm border border-amber-400 bg-amber-100"></i>Pending payment</span><span><i class="mr-2 inline-block size-3 rounded-sm border-2 border-orange-600"></i>Today</span></div></div><div class="flex gap-2"><a href="{{ route('guest.calendar',['month'=>$month->subMonth()->format('Y-m')]) }}" class="grid size-10 place-items-center rounded-lg border border-slate-200 text-xl">‹</a><a href="{{ route('guest.calendar',['month'=>$month->addMonth()->format('Y-m')]) }}" class="grid size-10 place-items-center rounded-lg border border-slate-200 text-xl">›</a></div></header>
                <div class="mt-5 overflow-x-auto"><div class="min-w-[700px]"><div class="grid grid-cols-7 text-center text-sm font-bold text-slate-500">@foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $weekday)<div class="py-3">{{ $weekday }}</div>@endforeach</div><div class="grid grid-cols-7 gap-1.5">
                    @foreach($calendarDays as $day)
                        @php $dayTrips=$bookings->filter(fn($booking)=>$day->gte($booking->arrival_date)&&$day->lt($booking->departure_date)); $current=$day->month===$month->month; @endphp
                        <div class="min-h-28 rounded-xl border p-3 {{ $day->isToday()?'border-2 border-orange-600':($current?'border-slate-200':'border-slate-100 bg-slate-50') }}">
                            <strong class="text-sm {{ $current?'text-slate-900':'text-slate-300' }}">{{ $day->day }}</strong>
                            <div class="mt-2 space-y-1.5">@foreach($dayTrips->take(1) as $trip)<a href="{{ route('guest.bookings.show',$trip) }}" class="block rounded-lg border p-2 text-[11px] leading-4 {{ $isPending($trip)?'border-amber-400 bg-amber-50':'border-sky-400 bg-sky-50' }}">@if($day->isSameDay($trip->arrival_date))<strong class="block">Check-in {{ substr($trip->property->marketplaceListing?->check_in_time ?: '14:00',0,5) }}</strong>@endif<span class="block truncate">{{ $trip->property->marketplaceListing?->public_title ?: $trip->property->name }}</span>@if($day->isSameDay($trip->arrival_date))<span>{{ $trip->arrival_date->diffInDays($trip->departure_date) }} nights</span>@endif</a>@endforeach</div>
                        </div>
                    @endforeach
                </div></div></div>
            </section>
        </div>
    </div>
</main>
@endsection
