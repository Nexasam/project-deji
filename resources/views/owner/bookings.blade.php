<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $canCreateBooking = app(\App\Services\Access\BusinessPermissionService::class)->allows(auth()->user(), $activeBusinessContext, 'booking.create');
    $summaryCards = [
        ['Total bookings', $stats['total'], 'All reservations', 'bg-orange-50 text-orange-600'],
        ['Confirmed', $stats['confirmed'], 'Dates protected', 'bg-emerald-50 text-emerald-600'],
        ['Pending', $stats['pending'], 'Needs attention', 'bg-amber-50 text-amber-600'],
        ['Cancelled', $stats['cancelled'], 'Released dates', 'bg-red-50 text-red-600'],
        ['Check-ins today', $stats['check_ins'], 'Arrivals', 'bg-blue-50 text-blue-600'],
        ['Check-outs today', $stats['check_outs'], 'Departures', 'bg-violet-50 text-violet-600'],
    ];
@endphp
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex min-h-screen">
    @include('partials.sidebar-nav', ['active' => 'bookings'])

    <div class="min-w-0 flex-1">
        <header class="flex h-[60px] items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 md:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Open navigation">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
                <p class="truncate text-sm font-semibold text-slate-700">{{ $business->name }}</p>
            </div>
            @if ($canCreateBooking)
                <a href="{{ route('owner.bookings.manual.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-orange-700">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Create booking
                </a>
            @endif
        </header>

        <main class="mx-auto max-w-[1600px] px-4 py-5 lg:px-6">
            <section>
                <h1 class="text-xl font-extrabold tracking-tight">Bookings</h1>
                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-slate-500"><span>{{ $business->name }}</span><span aria-hidden="true">›</span><strong class="text-slate-800">All reservations</strong></div>
            </section>

            <section class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6" aria-label="Booking summary">
                @foreach ($summaryCards as [$label, $value, $detail, $tone])
                    <article data-testid="booking-summary-card" class="rounded-lg border border-slate-200 bg-white p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div><p class="text-xs font-semibold text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold">{{ $value }}</p><p class="mt-1 hidden text-[10px] text-slate-400 sm:block">{{ $detail }}</p></div>
                            <span class="grid size-7 shrink-0 place-items-center rounded-md {{ $tone }}"><svg class="size-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-4 py-3">
                    <div class="mb-3"><h2 class="text-sm font-bold">Reservation directory</h2><p class="mt-0.5 text-[11px] text-slate-500">Search, filter and open every booking belonging to this business</p></div>
                    <form method="GET" class="grid gap-2 sm:grid-cols-2 lg:grid-cols-12">
                        <label class="relative sm:col-span-2 lg:col-span-3">
                            <span class="sr-only">Search bookings</span>
                            <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Guest, property or reference" class="w-full rounded-lg border-slate-200 py-2 pl-9 pr-3 text-xs focus:border-orange-500 focus:ring-orange-500">
                        </label>
                        <select name="property" aria-label="Filter by property" class="rounded-lg border-slate-200 text-xs lg:col-span-2"><option value="">All properties</option>@foreach ($properties as $property)<option value="{{ $property->id }}" @selected(($filters['property'] ?? '') === $property->id)>{{ $property->name }}</option>@endforeach</select>
                        <select name="status" aria-label="Filter by status" class="rounded-lg border-slate-200 text-xs lg:col-span-2"><option value="">All statuses</option>@foreach (['confirmed' => 'Confirmed', 'awaiting_payment' => 'Awaiting payment', 'reserved' => 'Reserved', 'checked_in' => 'Checked in', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>@endforeach</select>
                        <select name="channel" aria-label="Filter by channel" class="rounded-lg border-slate-200 text-xs lg:col-span-2"><option value="">All channels</option>@foreach (['marketplace' => 'Verified Shortlet', 'walk_in' => 'Walk-in', 'whatsapp' => 'WhatsApp', 'manual' => 'Manual', 'airbnb' => 'Airbnb', 'booking_dot_com' => 'Booking.com'] as $value => $label)<option value="{{ $value }}" @selected(($filters['channel'] ?? '') === $value)>{{ $label }}</option>@endforeach</select>
                        <select name="sort" aria-label="Sort bookings" class="rounded-lg border-slate-200 text-xs lg:col-span-1"><option value="newest">Newest</option><option value="arrival" @selected(($filters['sort'] ?? '') === 'arrival')>Arrival</option></select>
                        <button class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 lg:col-span-2">Apply filters</button>
                        <label class="lg:col-span-2"><span class="sr-only">Bookings from</span><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" aria-label="Bookings from" class="w-full rounded-lg border-slate-200 text-xs"></label>
                        <label class="lg:col-span-2"><span class="sr-only">Bookings to</span><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" aria-label="Bookings to" class="w-full rounded-lg border-slate-200 text-xs"></label>
                        <a href="{{ route('owner.bookings') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 lg:col-span-1">Reset</a>
                    </form>
                </div>

                @if ($bookings->isEmpty())
                    <div class="px-6 py-14 text-center"><span class="mx-auto grid size-12 place-items-center rounded-lg bg-slate-100 text-slate-400"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.8"/></svg></span><h2 class="mt-4 text-sm font-bold">No bookings found</h2><p class="mt-1 text-xs text-slate-500">Try changing your filters or wait for a new marketplace booking.</p></div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[900px] text-left text-xs">
                            <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Guest</th><th class="px-3 py-3">Property</th><th class="px-3 py-3">Stay dates</th><th class="px-3 py-3">Channel</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Amount</th><th class="px-4 py-3"><span class="sr-only">Action</span></th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($bookings as $booking)
                                    @php
                                        $statusValue = $booking->status->value;
                                        $statusTone = match ($statusValue) {
                                            'confirmed', 'checked_in', 'completed', 'checked_out' => 'bg-emerald-50 text-emerald-700',
                                            'awaiting_payment', 'reserved', 'enquiry' => 'bg-amber-50 text-amber-700',
                                            'cancelled', 'refunded', 'no_show' => 'bg-red-50 text-red-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <tr class="transition hover:bg-slate-50/80">
                                        <td class="px-4 py-3.5"><div class="flex items-center gap-2.5"><span class="grid size-8 shrink-0 place-items-center rounded-md bg-orange-50 text-[10px] font-bold text-orange-600">{{ str($booking->guest->name)->substr(0, 2)->upper() }}</span><div><p class="max-w-36 truncate font-bold">{{ $booking->guest->name }}</p><p class="mt-0.5 text-[10px] text-slate-400">{{ $booking->reference }}</p></div></div></td>
                                        <td class="px-3 py-3.5"><p class="max-w-44 truncate font-semibold">{{ $booking->property->name }}</p><p class="mt-0.5 text-[10px] text-slate-400">{{ data_get($booking->property->address, 'city') }}, {{ data_get($booking->property->address, 'state') }}</p></td>
                                        <td class="whitespace-nowrap px-3 py-3.5"><p class="font-medium">{{ $booking->arrival_date->format('j M') }} – {{ $booking->departure_date->format('j M Y') }}</p><p class="mt-0.5 text-[10px] text-slate-400">{{ $booking->arrival_date->diffInDays($booking->departure_date) }} nights</p></td>
                                        <td class="px-3 py-3.5"><span class="font-medium text-slate-600">{{ str($booking->source->value)->replace('_', ' ')->title() }}</span></td>
                                        <td class="px-3 py-3.5"><span class="rounded-md px-2 py-1 text-[10px] font-bold {{ $statusTone }}">{{ str($statusValue)->replace('_', ' ')->title() }}</span></td>
                                        <td class="whitespace-nowrap px-3 py-3.5 font-bold">{{ $booking->currency === 'NGN' ? '₦' : $booking->currency.' ' }}{{ number_format((float) $booking->total_amount) }}</td>
                                        <td class="px-4 py-3.5 text-right"><a href="{{ route('owner.bookings.show', $booking) }}" class="inline-flex rounded-md border border-slate-200 px-3 py-2 text-[11px] font-bold hover:border-orange-200 hover:text-orange-600">View details</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-200 px-4 py-3">{{ $bookings->links() }}</div>
                @endif
            </section>

            <p class="mt-4 text-center text-[11px] text-slate-400">Marketplace and owner-recorded bookings share the same availability protection.</p>
        </main>
    </div>
</div>
</body>
</html>
