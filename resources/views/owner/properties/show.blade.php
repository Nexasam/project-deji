<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->name }} – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        @include('partials.sidebar-nav', ['active' => 'properties'])

        <div class="min-w-0 flex-1">
            <header class="border-b border-slate-200 bg-white px-4 py-4 lg:px-8">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Open navigation">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <a href="{{ route('owner.properties.index') }}" class="text-xs font-bold uppercase tracking-wider text-orange-600 hover:text-orange-700">← Properties</a>
                        <h1 class="mt-1 text-xl font-extrabold">Property details</h1>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-[1600px] px-4 py-6 lg:px-6">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col justify-between gap-5 border-b border-slate-100 p-5 sm:flex-row sm:items-start lg:p-7">
                        <div class="min-w-0">
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">{{ str($property->publication_status->value)->replace('_', ' ')->title() }}</span>
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">{{ str($property->verification_status->value)->replace('_', ' ')->title() }}</span>
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700">{{ str($property->readiness_status->value)->replace('_', ' ')->title() }}</span>
                            </div>
                            <h2 class="mt-4 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $property->name }}</h2>
                            <p class="mt-2 font-mono text-xs text-slate-400">{{ $property->code }}</p>
                            <p class="mt-3 text-sm text-slate-600">
                                {{ collect([data_get($property->address, 'line_1'), data_get($property->address, 'line_2')])->filter()->join(', ') }}
                                @if (data_get($property->address, 'line_1'))<br>@endif
                                {{ collect([data_get($property->address, 'city'), data_get($property->address, 'state')])->filter()->join(', ') }}
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            @if ($property->publication_status->value === 'draft')
                                <a href="{{ route('owner.properties.wizard.step', ['property' => $property, 'step' => 2]) }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Edit property</a>
                                <a href="{{ route('owner.properties.resume', $property) }}" class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-700">Continue setup</a>
                            @endif
                            @if ($property->publication_status->value === 'published' && $property->marketplaceListing?->slug)
                                <a href="{{ route('marketplace.show', $property->marketplaceListing->slug) }}" class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-700">View public listing</a>
                            @endif
                        </div>
                    </div>

                    @if ($property->media->isNotEmpty())
                        <div class="grid gap-2 p-5 sm:grid-cols-2 lg:grid-cols-4 lg:p-7">
                            @foreach ($property->media->take(4) as $photo)
                                <img src="{{ $photo->external_url ?: Storage::disk($photo->storage_disk)->url($photo->storage_path) }}" alt="{{ $photo->alt_text ?: $property->name }}" class="h-44 w-full rounded-xl object-cover">
                            @endforeach
                        </div>
                    @endif
                </section>

                <div class="mt-5 grid gap-5 lg:grid-cols-[1.5fr_1fr]">
                    <div class="space-y-5">
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:p-7">
                            <h3 class="text-lg font-extrabold">About this property</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $property->description ?: 'No property description has been added yet.' }}</p>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:p-7">
                            <h3 class="text-lg font-extrabold">Amenities</h3>
                            @if ($property->amenities->isEmpty())
                                <p class="mt-3 text-sm text-slate-500">No amenities have been added yet.</p>
                            @else
                                <div class="mt-4 flex flex-wrap gap-2">@foreach ($property->amenities as $amenity)<span class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">{{ $amenity->name }}</span>@endforeach</div>
                            @endif
                        </section>
                    </div>

                    <div class="space-y-5">
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:p-7">
                            <h3 class="text-lg font-extrabold">Property summary</h3>
                            <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                <div><dt class="text-xs text-slate-500">Type</dt><dd class="mt-1 font-bold">{{ str($property->property_type)->replace('_', ' ')->title() }}</dd></div>
                                <div><dt class="text-xs text-slate-500">Nightly price</dt><dd class="mt-1 font-bold">{{ $property->pricing_currency === 'NGN' ? '₦' : $property->pricing_currency.' ' }}{{ number_format((float) $property->default_nightly_price) }}</dd></div>
                                <div><dt class="text-xs text-slate-500">Capacity</dt><dd class="mt-1 font-bold">{{ $property->capacity }} {{ Str::plural('guest', $property->capacity) }}</dd></div>
                                <div><dt class="text-xs text-slate-500">Bedrooms</dt><dd class="mt-1 font-bold">{{ $property->bedrooms }} {{ Str::plural('bedroom', $property->bedrooms) }}</dd></div>
                                <div><dt class="text-xs text-slate-500">Beds</dt><dd class="mt-1 font-bold">{{ $property->beds }} {{ Str::plural('bed', $property->beds) }}</dd></div>
                                <div><dt class="text-xs text-slate-500">Bathrooms</dt><dd class="mt-1 font-bold">{{ rtrim(rtrim((string) $property->bathrooms, '0'), '.') }} {{ Str::plural('bathroom', (int) $property->bathrooms) }}</dd></div>
                            </dl>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:p-7">
                            <h3 class="text-lg font-extrabold">Marketplace</h3>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Listing status</dt><dd class="font-bold">{{ str($property->marketplaceListing?->publication_status ?? 'not listed')->replace('_', ' ')->title() }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Instant booking</dt><dd class="font-bold">{{ $property->marketplaceListing?->instant_booking_enabled ? 'Enabled' : 'Disabled' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Operational status</dt><dd class="font-bold">{{ str($property->operational_status?->value ?? 'not set')->replace('_', ' ')->title() }}</dd></div>
                            </dl>
                        </section>
                    </div>
                </div>

                @php $calendarExport = $property->calendarExports->first(); @endphp
                <section id="calendar-sync" class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:p-7">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start"><div><p class="text-xs font-bold uppercase tracking-wider text-orange-600">Calendar sync</p><h3 class="mt-1 text-lg font-extrabold">Prevent double bookings across platforms</h3><p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Paste each platform's calendar export URL below. Then paste your Verified Shortlet export URL into that platform's “Import calendar” area. No API key is needed.</p></div><a href="{{ route('owner.calendar',['property'=>$property->id]) }}" class="text-sm font-bold text-orange-600">View calendar →</a></div>
                    @if(session('status'))<div class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
                    @error('calendar_sync')<div class="mt-4 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-700">{{ $message }}</div>@enderror
                    @error('feed_url')<div class="mt-4 rounded-xl bg-red-50 p-3 text-sm font-semibold text-red-700">{{ $message }}</div>@enderror
                    <div class="mt-5 grid gap-4 lg:grid-cols-2">
                        @foreach(['airbnb'=>'Airbnb','bookingcom'=>'Booking.com'] as $provider=>$label)
                            @php $connection=$property->externalCalendarConnections->firstWhere('provider',$provider); @endphp
                            <div class="rounded-xl border border-slate-200 p-4"><div class="flex items-center justify-between gap-3"><h4 class="font-extrabold">{{ $label }}</h4><span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $connection?->sync_status==='connected' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ strtoupper($connection?->sync_status ?? 'NOT CONNECTED') }}</span></div>
                                <form method="POST" action="{{ route('owner.properties.calendars.store',$property) }}" class="mt-3">@csrf<input type="hidden" name="provider" value="{{ $provider }}"><label class="text-xs font-bold text-slate-600">{{ $label }} calendar export URL<input type="url" name="feed_url" required value="{{ $connection?->inboundFeedUrl() }}" placeholder="https://...calendar....ics" class="mt-1.5 block w-full rounded-lg border-slate-200 text-sm"></label><button class="mt-3 rounded-lg bg-slate-950 px-4 py-2 text-xs font-bold text-white">{{ $connection ? 'Update URL' : 'Save connection' }}</button></form>
                                @if($connection)<div class="mt-3 flex flex-wrap gap-2"><form method="POST" action="{{ route('owner.properties.calendars.sync',[$property,$connection]) }}">@csrf<button class="rounded-lg bg-orange-600 px-4 py-2 text-xs font-bold text-white">Sync now</button></form><form method="POST" action="{{ route('owner.properties.calendars.destroy',[$property,$connection]) }}">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-4 py-2 text-xs font-bold text-red-700">Disable</button></form></div><p class="mt-3 text-xs text-slate-500">Last sync: {{ $connection->last_synced_at?->format('d M Y, H:i') ?? 'Never' }}@if($connection->last_error)<br><span class="text-red-600">{{ $connection->last_error }}</span>@endif</p>@endif
                            </div>
                        @endforeach
                    </div>
                    @if($calendarExport)<div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 p-4"><label class="text-xs font-bold text-slate-700">Verified Shortlet export URL</label><div class="mt-2 flex flex-col gap-2 sm:flex-row"><input id="ical-export-url" readonly value="{{ route('property-calendar-feed',[$calendarExport,$calendarExport->plain_token]) }}" class="min-w-0 flex-1 rounded-lg border-orange-200 bg-white text-xs"><button type="button" onclick="navigator.clipboard.writeText(document.getElementById('ical-export-url').value)" class="rounded-lg bg-orange-600 px-4 py-2 text-xs font-bold text-white">Copy URL</button></div><div class="mt-3 flex items-center justify-between gap-3"><p class="text-xs text-slate-600">Paste this into each platform's <strong>Import calendar</strong> field. Treat it like a password.</p><form method="POST" action="{{ route('owner.properties.calendars.export.regenerate',$property) }}">@csrf<button class="text-xs font-bold text-red-700 underline" onclick="return confirm('The old export URL will stop working. Continue?')">Regenerate</button></form></div></div>@endif
                    <p class="mt-4 text-xs leading-5 text-slate-500">Automatic imports run every 15 minutes on Verified Shortlet. Airbnb and Booking.com choose how often they read exported calendars, so updates are not instant. iCal synchronizes unavailable dates only—not prices, messages, payments, or guest details.</p>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
