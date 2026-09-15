<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $propertyPermissions = app(\App\Services\Access\BusinessPermissionService::class);
    $canCreateProperty = $propertyPermissions->allows(auth()->user(), $activeBusinessContext, 'property.create');
    $canEditProperty = $propertyPermissions->allows(auth()->user(), $activeBusinessContext, 'property.edit');
    $canManageListing = $propertyPermissions->allows(auth()->user(), $activeBusinessContext, 'property.manage_listing');
    $publishedCount = $properties->filter(fn ($property) => $property->publication_status->value === 'published')->count();
    $reviewCount = $properties->filter(fn ($property) => $property->verification_status->value === 'pending')->count();
    $draftCount = $properties->filter(fn ($property) => $property->publication_status->value === 'draft')->count();
@endphp
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex min-h-screen">
    @include('partials.sidebar-nav', ['active' => 'properties'])

    <div class="min-w-0 flex-1">
        <header class="flex h-[60px] items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 md:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Open navigation">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
                <p class="truncate text-sm font-semibold text-slate-700">{{ $business->name }}</p>
            </div>
            <x-owner.view-switch route-name="owner.properties.index" mode="real" />
        </header>

        <main class="mx-auto max-w-[1600px] px-4 py-5 lg:px-6">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">{{ $errors->first() }}</div>
            @endif

            <section class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight">Property portfolio</h1>
                    <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                        <span>{{ $business->name }}</span><span aria-hidden="true">›</span>
                        <span>All locations</span><span aria-hidden="true">›</span>
                        <strong class="text-slate-800">{{ $portfolioCount }} {{ Str::plural('property', $portfolioCount) }}</strong>
                    </div>
                </div>
                @if ($canCreateProperty)
                    <a href="{{ route('owner.properties.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-700">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Add property
                    </a>
                @endif
            </section>

            @if ($portfolioCount > 0)
                <section class="mt-5" aria-labelledby="location-overview-title">
                    <div class="mb-3 flex items-end justify-between gap-3">
                        <div><h2 id="location-overview-title" class="text-sm font-bold">Locations</h2><p class="mt-0.5 text-[11px] text-slate-500">Current-month occupancy across your accessible properties</p></div>
                        <span class="text-[10px] font-semibold text-slate-400">Checkout nights are available</span>
                    </div>
                    <div class="flex gap-3 overflow-x-auto pb-2">
                        <article data-testid="all-locations-card" class="min-w-[250px] flex-1 rounded-lg border border-orange-200 bg-orange-50 p-4 sm:min-w-[280px]">
                            <div class="flex items-center gap-2">
                                <span class="grid size-8 place-items-center rounded-md bg-white text-orange-600"><svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-6.2 7-12A7 7 0 1 0 5 9c0 5.8 7 12 7 12Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg></span>
                                <div><h3 class="text-sm font-bold">All Locations</h3><p class="text-[11px] text-slate-500">Across {{ $business->name }}</p></div>
                            </div>
                            <dl class="mt-4 grid grid-cols-2 gap-4"><div><dd class="text-2xl font-bold">{{ $locationSummary['all']['property_count'] }}</dd><dt class="text-[11px] text-slate-500">Properties</dt></div><div><dd class="text-2xl font-bold">{{ $locationSummary['all']['occupancy'] }}%</dd><dt class="text-[11px] text-slate-500">Avg occupancy</dt></div></dl>
                        </article>

                        @foreach ($locationSummary['locations'] as $location)
                            <article data-testid="location-card" class="min-w-[250px] flex-1 rounded-lg border border-slate-200 bg-white p-4 sm:min-w-[280px]">
                                <div class="flex items-center gap-2">
                                    <span class="grid size-8 place-items-center rounded-md bg-orange-600 text-white"><svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.8"/></svg></span>
                                    <div class="min-w-0"><h3 class="truncate text-sm font-bold">{{ $location['name'] }}</h3><p class="truncate text-[11px] text-slate-500">{{ $location['manager'] }}</p></div>
                                </div>
                                <dl class="mt-4 grid grid-cols-2 gap-4"><div><dd class="text-2xl font-bold">{{ $location['property_count'] }}</dd><dt class="text-[11px] text-slate-500">{{ Str::plural('Property', $location['property_count']) }}</dt></div><div><dd class="text-2xl font-bold">{{ $location['occupancy'] }}%</dd><dt class="text-[11px] text-slate-500">Occupancy</dt></div></dl>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Portfolio summary">
                    @foreach ([
                        ['All properties', $properties->count(), 'Across your business', 'bg-orange-50 text-orange-600'],
                        ['Published', $publishedCount, 'Live marketplace listings', 'bg-emerald-50 text-emerald-600'],
                        ['In review', $reviewCount, 'Awaiting verification', 'bg-amber-50 text-amber-600'],
                        ['Drafts', $draftCount, 'Setup still in progress', 'bg-slate-100 text-slate-600'],
                    ] as [$label, $value, $detail, $tone])
                        <article class="rounded-lg border border-slate-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div><p class="text-xs font-semibold text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold text-slate-900">{{ $value }}</p><p class="mt-1 text-[11px] text-slate-500">{{ $detail }}</p></div>
                                <span class="grid size-8 shrink-0 place-items-center rounded-md {{ $tone }}"><svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.8"/></svg></span>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div class="mt-5 flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-xs leading-5 text-slate-700">
                    <svg class="mt-0.5 size-4 shrink-0 text-blue-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <p><strong>One operational record per property.</strong> Open a property to manage its listing, documents, inventory and external calendars.</p>
                </div>

                <form method="GET" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input type="hidden" name="layout" value="{{ $filters['layout'] }}">
                    <label class="relative min-w-0 flex-1">
                        <span class="sr-only">Search properties</span>
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search property, code or location" class="w-full rounded-lg border-slate-200 bg-white py-2.5 pl-9 pr-3 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </label>
                    <select name="status" aria-label="Filter properties by publication status" class="rounded-lg border-slate-200 bg-white text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">All statuses</option>
                        @foreach (['published' => 'Published', 'pending' => 'Pending', 'draft' => 'Draft', 'unpublished' => 'Unpublished'] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-800">Search</button>
                    @if ($filters['q'] !== '' || $filters['status'] !== '')
                        <a href="{{ route('owner.properties.index', ['layout' => $filters['layout']]) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50">Reset</a>
                    @endif
                    <div class="inline-flex self-start rounded-lg border border-slate-200 bg-white p-1" aria-label="Property layout">
                        <a href="{{ route('owner.properties.index', array_filter(['q' => $filters['q'], 'status' => $filters['status'], 'layout' => 'grid'])) }}" aria-label="Grid view" class="grid size-8 place-items-center rounded-md {{ $filters['layout'] === 'grid' ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-100' }}"><svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 3h8v8H3V3Zm10 0h8v8h-8V3ZM3 13h8v8H3v-8Zm10 0h8v8h-8v-8Z"/></svg></a>
                        <a href="{{ route('owner.properties.index', array_filter(['q' => $filters['q'], 'status' => $filters['status'], 'layout' => 'list'])) }}" aria-label="List view" class="grid size-8 place-items-center rounded-md {{ $filters['layout'] === 'list' ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-100' }}"><svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 5h3v3H3V5Zm5 0h13v3H8V5ZM3 10.5h3v3H3v-3Zm5 0h13v3H8v-3ZM3 16h3v3H3v-3Zm5 0h13v3H8v-3Z"/></svg></a>
                    </div>
                </form>

                <section class="mt-5">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div><h2 class="text-sm font-bold">All properties</h2><p class="mt-0.5 text-[11px] text-slate-500">Live records and their marketplace status</p></div>
                        <span class="rounded-md border border-slate-200 bg-white px-3 py-2 text-[11px] font-semibold text-slate-500">{{ $properties->count() }} {{ Str::plural('result', $properties->count()) }}</span>
                    </div>

                    @if ($properties->isEmpty())
                        <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                            <span class="mx-auto grid size-11 place-items-center rounded-lg bg-slate-100 text-slate-400"><svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                            <h3 class="mt-4 text-sm font-bold">No matching properties</h3>
                            <p class="mt-1 text-xs text-slate-500">Change the search term or status filter to see more of your portfolio.</p>
                            <a href="{{ route('owner.properties.index', ['layout' => $filters['layout']]) }}" class="mt-4 inline-flex rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700">Clear filters</a>
                        </div>
                    @elseif ($filters['layout'] === 'grid')
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-testid="property-grid">
                        @foreach ($properties as $property)
                            @php
                                $publicationColors = ['published' => 'bg-emerald-500 text-white', 'pending' => 'bg-amber-500 text-white', 'unpublished' => 'bg-red-500 text-white', 'draft' => 'bg-slate-500 text-white'];
                                $verificationColors = ['verified' => 'bg-emerald-50 text-emerald-700', 'pending' => 'bg-amber-50 text-amber-700', 'rejected' => 'bg-red-50 text-red-700', 'unverified' => 'bg-slate-100 text-slate-600'];
                                $publication = $property->publication_status->value;
                                $verification = $property->verification_status->value;
                                $coverMedia = $property->media->firstWhere('is_primary', true) ?? $property->media->first();
                                $coverImage = $coverMedia?->external_url ?: ($coverMedia?->storage_path ? '/storage/'.ltrim($coverMedia->storage_path, '/') : '/image.png');
                            @endphp
                            <article data-testid="property-card" class="overflow-hidden rounded-lg border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-md">
                                <div class="relative h-36 overflow-hidden bg-slate-100">
                                    <img src="{{ $coverImage }}" alt="{{ $coverMedia?->alt_text ?: $property->name }}" loading="lazy" class="h-full w-full object-cover transition duration-300 hover:scale-105" onerror="this.onerror=null;this.src='/image.png'">
                                    <span class="absolute left-3 top-3 rounded-md px-2 py-1 text-[9px] font-extrabold uppercase tracking-wider {{ $publicationColors[$publication] ?? $publicationColors['draft'] }}">{{ str($publication)->title() }}</span>
                                    <span class="absolute right-3 top-3 rounded-md px-2 py-1 text-[9px] font-bold {{ $verificationColors[$verification] ?? $verificationColors['unverified'] }}">{{ str($verification)->title() }}</span>
                                </div>
                                <div class="p-4">
                                    <h3 class="truncate text-sm font-bold" title="{{ $property->name }}"><a href="{{ route('owner.properties.show', $property) }}" class="hover:text-orange-600">{{ $property->name }}</a></h3>
                                    <p class="mt-1 truncate text-[11px] text-slate-500">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }} · {{ str($property->property_type)->replace('_', ' ')->title() }}</p>
                                    <dl class="mt-4 grid grid-cols-3 gap-2 border-y border-slate-100 py-3">
                                        @foreach ([['Guests', $property->capacity], ['Beds', $property->bedrooms], ['Baths', $property->bathrooms]] as [$label, $value])
                                            <div><dt class="text-[10px] text-slate-400">{{ $label }}</dt><dd class="mt-0.5 text-xs font-bold">{{ $value }}</dd></div>
                                        @endforeach
                                    </dl>
                                    <div class="mt-3 flex items-center justify-between gap-2"><span class="truncate text-[10px] font-medium text-slate-400">{{ $property->code }}</span><a href="{{ route('owner.properties.show', $property) }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">View details →</a></div>
                                    @if ($verification === 'rejected' && data_get($property->marketplaceListing?->publication_eligibility_details, 'rejection_reason'))
                                        <div class="mt-3 rounded-lg border border-red-100 bg-red-50 p-2.5 text-xs leading-5 text-red-800"><strong>Needs correction:</strong> {{ data_get($property->marketplaceListing->publication_eligibility_details, 'rejection_reason') }}</div>
                                    @endif
                                    @if ($publication === 'draft' && $canEditProperty)
                                        <a href="{{ route('owner.properties.resume', $property) }}" class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-orange-600 px-3 py-2 text-xs font-bold text-white hover:bg-orange-700">Continue setup</a>
                                    @elseif ($publication === 'unpublished' && in_array($verification, ['unverified', 'rejected'], true) && $canManageListing)
                                        <form method="POST" action="{{ route('owner.properties.marketplace-verification.submit', $property) }}" class="mt-3">@csrf<button class="w-full rounded-lg border border-orange-300 bg-orange-50 px-3 py-2 text-xs font-bold text-orange-700 hover:bg-orange-100">Submit for verification</button></form>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                    @else
                        <div data-testid="property-list" class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[760px] text-left text-xs">
                                    <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Property</th><th class="px-3 py-3">Location</th><th class="px-3 py-3">Capacity</th><th class="px-3 py-3">Publishing</th><th class="px-3 py-3">Verification</th><th class="px-4 py-3"><span class="sr-only">Actions</span></th></tr></thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($properties as $property)
                                            @php
                                                $coverMedia = $property->media->firstWhere('is_primary', true) ?? $property->media->first();
                                                $coverImage = $coverMedia?->external_url ?: ($coverMedia?->storage_path ? '/storage/'.ltrim($coverMedia->storage_path, '/') : '/image.png');
                                                $publication = $property->publication_status->value;
                                                $verification = $property->verification_status->value;
                                            @endphp
                                            <tr class="transition hover:bg-slate-50">
                                                <td class="px-4 py-3"><div class="flex items-center gap-3"><img src="{{ $coverImage }}" alt="{{ $coverMedia?->alt_text ?: $property->name }}" class="size-12 shrink-0 rounded-lg object-cover" loading="lazy" onerror="this.onerror=null;this.src='/image.png'"><div class="min-w-0"><a href="{{ route('owner.properties.show', $property) }}" class="block max-w-56 truncate font-bold hover:text-orange-600">{{ $property->name }}</a><span class="mt-0.5 block text-[10px] text-slate-400">{{ $property->code }}</span></div></div></td>
                                                <td class="px-3 py-3"><p class="max-w-44 truncate">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }}</p><p class="mt-0.5 text-[10px] text-slate-400">{{ str($property->property_type)->replace('_', ' ')->title() }}</p></td>
                                                <td class="px-3 py-3"><strong>{{ $property->capacity }}</strong> guests<p class="mt-0.5 text-[10px] text-slate-400">{{ $property->bedrooms }} beds · {{ $property->bathrooms }} baths</p></td>
                                                <td class="px-3 py-3"><span class="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-700">{{ str($publication)->title() }}</span></td>
                                                <td class="px-3 py-3"><span class="rounded-md px-2 py-1 text-[10px] font-bold {{ $verification === 'verified' ? 'bg-emerald-50 text-emerald-700' : ($verification === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ str($verification)->title() }}</span></td>
                                                <td class="px-4 py-3 text-right"><div class="flex justify-end gap-2"><a href="{{ route('owner.properties.show', $property) }}" class="rounded-md border border-slate-200 px-3 py-2 text-[11px] font-bold hover:text-orange-600">View</a>@if ($publication === 'draft' && $canEditProperty)<a href="{{ route('owner.properties.resume', $property) }}" class="rounded-md bg-orange-600 px-3 py-2 text-[11px] font-bold text-white">Continue</a>@endif</div></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </section>
            @else
                <section class="mt-5 rounded-lg border border-slate-200 bg-white px-6 py-12 text-center">
                    <div class="mx-auto grid size-14 place-items-center rounded-lg bg-orange-50 text-orange-600"><svg class="size-7" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.8"/></svg></div>
                    <h2 class="mt-5 text-lg font-bold">Add your first property</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-500">There are no properties under {{ $business->name }} yet. Create a draft to begin adding operational details, amenities and media.</p>
                    @if ($canCreateProperty)<a href="{{ route('owner.properties.create') }}" class="mt-6 inline-flex rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-bold text-white">Add first property</a>@endif
                </section>
            @endif
        </main>
    </div>
</div>
</body>
</html>
