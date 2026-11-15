@extends('layouts.app')
@section('title', 'Calendar – Verified Shortlet')
@section('content')
<x-navbar />
@php
    $calendarStart=$month->startOfWeek(Carbon\CarbonInterface::SUNDAY); $calendarEnd=$month->endOfMonth()->endOfWeek(Carbon\CarbonInterface::SATURDAY); $calendarDays=collect();
    for($day=$calendarStart;$day->lte($calendarEnd);$day=$day->addDay()){$calendarDays->push($day);}
@endphp
<main class="min-h-screen bg-[#f3f3f2] py-9 sm:py-12">
    <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-10">
        <header class="flex items-end justify-between gap-4"><div><h1 class="text-3xl font-black">Calendar</h1><p class="mt-2 text-sm text-slate-500">Verified Shortlet &gt; Calendar</p></div><a href="{{ route('home') }}" class="rounded-lg bg-orange-600 px-6 py-3.5 text-sm font-bold text-white">⌕ &nbsp; Find a stay</a></header>
        <div class="mt-7 grid gap-5 lg:grid-cols-[340px_1fr]">
            <aside class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6"><h2 class="text-xl font-black">My bookings</h2><label class="mt-4 block sm:hidden"><span class="sr-only">Filter calendar</span><select onchange="window.location=this.value" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-extrabold text-slate-800">@foreach(['today'=>'Today','upcoming'=>'Upcoming','completed'=>'Completed','cancelled'=>'Cancelled'] as $key=>$label)<option value="{{ route('guest.calendar',['month'=>$month->format('Y-m'),'view'=>$key]) }}" @selected($view===$key)>{{ $label }}</option>@endforeach</select></label><nav class="mt-4 hidden grid-cols-2 gap-2 sm:grid" aria-label="Booking calendar views">@foreach(['today'=>'Today','upcoming'=>'Upcoming','completed'=>'Completed','cancelled'=>'Cancelled'] as $key=>$label)<a href="{{ route('guest.calendar',['month'=>$month->format('Y-m'),'view'=>$key]) }}" class="rounded-xl px-3 py-2.5 text-center text-xs font-extrabold {{ $view===$key?'bg-slate-950 text-white':'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">{{ $label }}</a>@endforeach</nav><div class="mt-5 space-y-4">
                @forelse($trips as $trip)
                    @php $media=$trip->property->media->firstWhere('is_primary',true)??$trip->property->media->first(); $image=$media?->external_url?:($media?->storage_path?'/storage/'.ltrim($media->storage_path,'/'):'/image.png'); @endphp
                    <a href="{{ route('guest.bookings.show',$trip) }}" class="block rounded-xl bg-slate-100 p-3"><div class="flex gap-3"><img src="{{ $image }}" onerror="this.src='/image.png'" class="size-14 rounded-lg object-cover" alt=""><div class="min-w-0"><h3 class="truncate font-black">{{ $trip->property->marketplaceListing?->public_title ?: $trip->property->name }}</h3><p class="mt-1 text-sm text-slate-500">{{ $trip->arrival_date->format('d M') }} – {{ $trip->departure_date->format('d M') }}</p></div></div><span class="mt-3 block rounded-full bg-slate-200 py-2 text-center text-xs font-extrabold text-slate-700">{{ str($trip->status->value)->replace('_',' ')->title() }}</span></a>
                @empty <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No {{ $view }} bookings.</div> @endforelse
            </div><a href="{{ route('guest.bookings.index') }}" class="mt-7 block text-center text-sm font-extrabold text-orange-600">View all bookings</a></aside>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 sm:p-7">
                <header class="flex flex-wrap items-center justify-between gap-4"><div><h2 class="text-2xl font-black">{{ $month->format('F Y') }}</h2><div class="mt-4 flex flex-wrap gap-5 text-sm text-slate-500"><span><i class="mr-2 inline-block size-3 rounded-sm {{ $view === 'today' ? 'border border-orange-300 bg-orange-100' : ($view === 'completed' ? 'border border-emerald-300 bg-emerald-100' : ($view === 'cancelled' ? 'border border-red-300 bg-red-100' : 'border border-sky-300 bg-sky-100')) }}"></i>{{ ucfirst($view) }} bookings</span><span><i class="mr-2 inline-block size-3 rounded-sm border-2 border-orange-600"></i>Today</span></div></div><div class="flex gap-2"><a href="{{ route('guest.calendar',['month'=>$month->subMonth()->format('Y-m'),'view'=>$view]) }}" class="grid size-10 place-items-center rounded-lg border border-slate-200 text-xl">‹</a><a href="{{ route('guest.calendar',['month'=>$month->addMonth()->format('Y-m'),'view'=>$view]) }}" class="grid size-10 place-items-center rounded-lg border border-slate-200 text-xl">›</a></div></header>
                <div class="mt-5 overflow-x-auto">
                    <div class="min-w-0 sm:min-w-[760px]">
                        <div class="grid grid-cols-7 text-center text-sm font-bold text-slate-500">@foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $weekday)<div class="py-3">{{ $weekday }}</div>@endforeach</div>
                        <div class="space-y-1.5">
                            @foreach($calendarDays->chunk(7) as $week)
                                @php
                                    $weekStart = $week->first();
                                    $weekEnd = $week->last();
                                    $weekEndExclusive = $weekEnd->addDay();
                                    $weekTrips = $bookings
                                        ->filter(fn($booking) => $booking->arrival_date->lt($weekEndExclusive) && $booking->departure_date->gt($weekStart))
                                        ->values()
                                        ->take(3);
                                @endphp
                                <div class="relative grid min-h-[108px] grid-cols-7 gap-0.5 sm:min-h-[128px] sm:gap-1.5">
                                    @foreach($week as $day)
                                        @php $current=$day->month===$month->month; @endphp
                                        <div class="min-h-[108px] rounded-lg border p-2 sm:min-h-[128px] sm:rounded-xl sm:p-3 {{ $day->isToday()?'border-2 border-orange-600':($current?'border-slate-200':'border-slate-100 bg-slate-50') }}">
                                            <strong class="text-sm {{ $current?'text-slate-900':'text-slate-300' }}">{{ $day->day }}</strong>
                                        </div>
                                    @endforeach

                                    @foreach($weekTrips as $barIndex => $trip)
                                        @php
                                            $segmentStart = $trip->arrival_date->greaterThan($weekStart) ? $trip->arrival_date : $weekStart;
                                            $segmentEnd = $trip->departure_date->lessThan($weekEndExclusive) ? $trip->departure_date : $weekEndExclusive;
                                            $columnStart = $segmentStart->dayOfWeek + 1;
                                            $columnEnd = $segmentEnd->dayOfWeek + 1;
                                            if ($segmentEnd->isSameDay($weekEndExclusive)) { $columnEnd = 8; }
                                            $top = 42 + ($barIndex * 28);
                                            $isStart = $segmentStart->isSameDay($trip->arrival_date);
                                            $isEnd = $segmentEnd->isSameDay($trip->departure_date);
                                            $nights = $trip->arrival_date->diffInDays($trip->departure_date);
                                        @endphp
                                        <a href="{{ route('guest.bookings.show',$trip) }}"
                                           class="absolute z-10 flex h-6 items-center overflow-hidden rounded-md px-1 text-[9px] font-black leading-none shadow-sm sm:h-7 sm:rounded-lg sm:px-3 sm:text-[11px] {{ $view === 'today' ? 'bg-orange-100 text-orange-900 ring-1 ring-orange-300' : ($view === 'completed' ? 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-300' : ($view === 'cancelled' ? 'bg-red-100 text-red-900 ring-1 ring-red-300' : 'bg-sky-100 text-sky-900 ring-1 ring-sky-300')) }}"
                                           style="grid-column: {{ $columnStart }} / {{ $columnEnd }}; top: {{ $top }}px; left: 3px; right: 3px;">
                                            <span class="truncate">
                                                @if($isStart)
                                                    Check-in {{ substr($trip->property->marketplaceListing?->check_in_time ?: '14:00',0,5) }} ·
                                                @endif
                                                {{ $trip->property->marketplaceListing?->public_title ?: $trip->property->name }}
                                                @if($isStart)
                                                    · {{ $nights }} {{ Str::plural('night', $nights) }}
                                                @endif
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
@endsection
