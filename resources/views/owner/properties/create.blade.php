<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($editing = filled($property))
    <title>{{ $editing ? 'Continue property setup' : 'Add property' }} – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        @include('partials.sidebar-nav', ['active' => 'properties'])

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="flex items-center justify-between gap-4 px-4 py-3 lg:px-8">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Hamburger – mobile only --}}
                        <button @click="sidebarOpen = true"
                                class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <a href="{{ route('owner.properties.index') }}" class="font-semibold hover:text-orange-700">Properties</a>
                                <span>/</span>
                                <span>{{ $editing ? $property->name : 'New property' }}</span>
                            </div>
                            <h1 class="mt-1 truncate text-lg font-extrabold tracking-tight">{{ $editing ? 'Continue property setup' : 'Add property' }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-bold text-slate-900">{{ $business->name }}</p>
                            <p class="text-xs text-slate-500">Draft property setup</p>
                        </div>
                        <div class="flex size-9 items-center justify-center rounded-full bg-orange-600 text-xs font-extrabold text-white">
                            {{ str(auth()->user()->name)->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->join('') }}
                        </div>
                        <a href="{{ route('owner.dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-950">
                            Exit setup
                        </a>
                    </div>
                </div>

                <div class="border-t border-slate-100 px-5 py-3 lg:px-8">
                    <div class="flex items-center justify-between gap-5">
                        <div class="min-w-0 flex-1">
                            <div class="mb-2 flex items-center justify-between text-xs">
                                <span class="font-extrabold text-orange-600">Step 1 of 9 · Property basics</span>
                                <span class="hidden text-slate-400 sm:block">Next: Amenities</span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full w-[11.11%] rounded-full bg-orange-600"></div>
                            </div>
                        </div>
                        <div class="hidden items-center gap-2 xl:flex">
                            @foreach (['Basics', 'Amenities', 'Media', 'House rules', 'Operations', 'Assets', 'Documents', 'Marketplace', 'Review'] as $index => $step)
                                <span class="text-[10px] font-bold {{ $index === 0 ? 'text-orange-600' : 'text-slate-300' }}">{{ $step }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-5 py-8 lg:px-8 lg:py-10">
                <div class="mb-6 max-w-3xl">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-orange-600">Step 1 · Property basics</p>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Property basics</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        @if ($editing)
                            Update the essential information for <strong class="text-slate-900">{{ $property->name }}</strong>.
                        @else
                            Add the essential information to create a property draft under <strong class="text-slate-900">{{ $business->name }}</strong>.
                        @endif
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                        <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v5m0 3.5v.5M10.3 4.9 3.5 17a2 2 0 0 0 1.75 3h13.5a2 2 0 0 0 1.75-3L13.7 4.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <div><p class="font-extrabold">Some information needs your attention.</p><p class="mt-1 text-red-600">Check the highlighted fields below and try again.</p></div>
                    </div>
                @endif

                <form id="property-create-form" method="POST" action="{{ $editing ? route('owner.properties.update', $property) : route('owner.properties.store') }}" class="space-y-6">
                    @csrf
                    @if ($editing) @method('PATCH') @endif

                    <section class="property-form-section">
                        <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                            <div class="flex items-start gap-4">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.8"/></svg>
                                </span>
                                <div><h3 class="font-extrabold text-slate-950">Property identity</h3><p class="mt-1 text-sm text-slate-500">How this accommodation is identified across your workspace.</p></div>
                            </div>
                        </div>
                        <div class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
                            <label class="property-form-label sm:col-span-2">Property name <span class="property-form-required">*</span>
                                <span class="property-form-control-wrap">
                                    <svg class="property-form-control-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.8"/></svg>
                                    <input name="name" value="{{ old('name', $property?->name) }}" required placeholder="e.g. Harbour View Apartment" class="property-form-control has-leading-icon @error('name') is-invalid @enderror" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                                </span>
                                <span class="property-form-help">Use the name guests and your team will recognise.</span>
                                @error('name')<span id="name-error" class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <label class="property-form-label">Property type <span class="property-form-required">*</span>
                                <span class="property-form-control-wrap">
                                <select name="property_type" required class="property-form-control has-select-chevron @error('property_type') is-invalid @enderror" @error('property_type') aria-invalid="true" @enderror>
                                    @foreach (['apartment' => 'Serviced apartment', 'short_let' => 'Short-let'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('property_type', $property?->property_type) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <svg class="property-form-select-chevron" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m6 8 4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                @error('property_type')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <label class="property-form-label">Business workspace
                                <span class="property-form-control-wrap">
                                    <svg class="property-form-control-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8l8-4 8 4v12M9 20v-5h6v5M8 10h.01M12 10h.01M16 10h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <input value="{{ $business->name }}" disabled class="property-form-control has-leading-icon">
                                </span>
                                <span class="property-form-help">Assigned securely from your active workspace.</span>
                            </label>
                            <label class="property-form-label sm:col-span-2">Description <span class="property-form-optional">(optional)</span>
                                <textarea name="description" rows="4" class="property-form-control mt-2 @error('description') is-invalid @enderror" placeholder="Describe the property, its atmosphere and what makes it distinctive.">{{ old('description', $property?->description) }}</textarea>
                                <span class="property-form-help">A concise operational summary is enough for now.</span>
                                @error('description')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                        </div>
                    </section>

                    <section class="property-form-section">
                        <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                            <div class="flex items-start gap-4">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-6.25 7-12a7 7 0 1 0-14 0c0 5.75 7 12 7 12Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                                </span>
                                <div><h3 class="font-extrabold text-slate-950">Location</h3><p class="mt-1 text-sm text-slate-500">Where guests and your operational team can find the property.</p></div>
                            </div>
                        </div>
                        <div class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
                            <label class="property-form-label sm:col-span-2">Street address <span class="property-form-required">*</span>
                                <span class="property-form-control-wrap"><svg class="property-form-control-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-6.25 7-12a7 7 0 1 0-14 0c0 5.75 7 12 7 12Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg><input name="address_line" value="{{ old('address_line', data_get($property?->address, 'line_1')) }}" required placeholder="Street, estate or nearby landmark" class="property-form-control has-leading-icon @error('address_line') is-invalid @enderror"></span>
                                @error('address_line')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <label class="property-form-label">City / area <span class="property-form-required">*</span>
                                <input name="city" value="{{ old('city', data_get($property?->address, 'city')) }}" required placeholder="e.g. Lekki" class="property-form-control mt-2 @error('city') is-invalid @enderror">
                                @error('city')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <label class="property-form-label">State <span class="property-form-required">*</span>
                                <input name="state" value="{{ old('state', data_get($property?->address, 'state')) }}" required placeholder="e.g. Lagos" class="property-form-control mt-2 @error('state') is-invalid @enderror">
                                @error('state')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <label class="property-form-label">Country code <span class="property-form-required">*</span>
                                <input name="country_code" maxlength="2" value="{{ old('country_code', data_get($property?->address, 'country_code', $business->country_code)) }}" required class="property-form-control mt-2 uppercase @error('country_code') is-invalid @enderror">
                                <span class="property-form-help">Two-letter ISO code, such as NG or GH.</span>
                                @error('country_code')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <div class="flex items-center rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-xs leading-5 text-blue-800">Map coordinates and precise guest directions can be added during the later details step.</div>
                        </div>
                    </section>

                    <section class="property-form-section">
                        <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                            <div class="flex items-start gap-4">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19v-7h16v7M6 12V8a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v4M7 19v2m10-2v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <div><h3 class="font-extrabold text-slate-950">Capacity & pricing</h3><p class="mt-1 text-sm text-slate-500">Set the basic sleeping capacity and an optional starting price.</p></div>
                            </div>
                        </div>
                        <div class="grid gap-5 p-6 sm:grid-cols-2 lg:grid-cols-4 sm:p-8">
                            <label class="property-form-label">Guest capacity <span class="property-form-required">*</span>
                                <input type="number" name="capacity" min="1" value="{{ old('capacity', $property?->capacity ?? 1) }}" required class="property-form-control mt-2 @error('capacity') is-invalid @enderror">
                                <span class="property-form-help">Maximum overnight guests.</span>
                                @error('capacity')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <label class="property-form-label">Bedrooms <span class="property-form-required">*</span>
                                <input type="number" name="bedrooms" min="0" value="{{ old('bedrooms', $property?->bedrooms ?? 1) }}" required class="property-form-control mt-2 @error('bedrooms') is-invalid @enderror">
                                @error('bedrooms')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <label class="property-form-label">Bathrooms <span class="property-form-required">*</span>
                                <input type="number" name="bathrooms" min="0" step="0.5" value="{{ old('bathrooms', $property?->bathrooms ?? 1) }}" required class="property-form-control mt-2 @error('bathrooms') is-invalid @enderror">
                                @error('bathrooms')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                            <label class="property-form-label">Nightly price <span class="property-form-optional">(optional)</span>
                                <span class="property-form-control-wrap"><span class="property-form-addon">{{ $business->currency }}</span><input type="number" name="default_nightly_price" min="0" step="0.01" value="{{ old('default_nightly_price', $property?->default_nightly_price) }}" placeholder="0.00" class="property-form-control has-leading-addon @error('default_nightly_price') is-invalid @enderror"></span>
                                <span class="property-form-help">Your starting base rate before fees.</span>
                                @error('default_nightly_price')<span class="property-form-error">{{ $message }}</span>@enderror
                            </label>
                        </div>
                    </section>

                    <div class="sticky bottom-4 z-30 flex flex-col-reverse justify-between gap-3 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-xl shadow-slate-200/60 backdrop-blur sm:flex-row sm:items-center">
                        <p class="hidden text-xs text-slate-500 sm:block">Your property will remain private until it is completed, verified and published.</p>
                        <div class="flex flex-col-reverse gap-3 sm:flex-row">
                            <a href="{{ route('owner.properties.index') }}" class="rounded-xl px-5 py-3 text-center text-sm font-bold text-slate-600 hover:bg-slate-100">Cancel</a>
                            <button type="submit" class="property-form-submit inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                Save and continue
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
