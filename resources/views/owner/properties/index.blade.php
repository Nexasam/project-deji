<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased">
    <div class="flex min-h-screen">
        @include('partials.sidebar-nav', ['active' => 'properties'])

        <div class="min-w-0 flex-1">
            <header class="border-b border-slate-200 bg-white px-5 py-4 lg:px-8">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div><p class="text-xs font-bold uppercase tracking-wider text-orange-600">{{ $business->name }}</p><h1 class="mt-1 text-xl font-extrabold">Properties</h1></div>
                    <x-owner.view-switch route-name="owner.properties.index" mode="real" />
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-5 py-8 lg:px-8">
                @if (session('status'))<div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
                @if ($errors->any())<div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
                @if ($properties->isEmpty())
                    <section class="rounded-3xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm sm:px-10">
                        <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                            <svg class="size-8" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.8"/></svg>
                        </div>
                        <h2 class="mt-6 text-3xl font-extrabold tracking-tight">Add your first property</h2>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600">There are no properties under {{ $business->name }} yet. Create the first draft to begin adding operational details, amenities and media.</p>
                        <a href="{{ route('owner.properties.create') }}" class="mt-7 inline-flex rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white hover:bg-orange-700">Add first property</a>
                    </section>
                @else
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                        <div><p class="text-sm font-semibold text-orange-600">{{ $properties->count() }} {{ Str::plural('property', $properties->count()) }}</p><h2 class="mt-1 text-3xl font-extrabold tracking-tight">Property portfolio</h2><p class="mt-2 text-sm text-slate-600">Live records belonging to {{ $business->name }}.</p></div>
                        <a href="{{ route('owner.properties.create') }}" class="rounded-xl bg-orange-600 px-5 py-3 text-center text-sm font-bold text-white hover:bg-orange-700">Add property</a>
                    </div>

                    <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($properties as $property)
                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-4"><div class="flex size-12 items-center justify-center rounded-xl bg-orange-50 font-extrabold text-orange-600">{{ str($property->name)->substr(0, 2)->upper() }}</div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ str($property->publication_status->value)->title() }}</span></div>
                                <h3 class="mt-5 text-lg font-extrabold">{{ $property->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }}</p>
                                <dl class="mt-5 grid grid-cols-3 gap-3 border-t border-slate-100 pt-4 text-sm"><div><dt class="text-xs text-slate-400">Guests</dt><dd class="mt-1 font-bold">{{ $property->capacity }}</dd></div><div><dt class="text-xs text-slate-400">Bedrooms</dt><dd class="mt-1 font-bold">{{ $property->bedrooms }}</dd></div><div><dt class="text-xs text-slate-400">Bathrooms</dt><dd class="mt-1 font-bold">{{ $property->bathrooms }}</dd></div></dl>
                                <div class="mt-5 flex items-center justify-between"><span class="text-xs font-semibold text-amber-700">{{ str($property->verification_status->value)->title() }}</span><span class="text-xs text-slate-400">{{ $property->code }}</span></div>
                                @if ($property->publication_status->value === 'draft')
                                    <a href="{{ route('owner.properties.resume', $property) }}" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-extrabold text-white hover:bg-orange-700">
                                        Continue setup
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                @elseif ($property->publication_status->value === 'unpublished' && $property->verification_status->value === 'unverified')
                                    <form method="POST" action="{{ route('owner.properties.marketplace-verification.submit', $property) }}" class="mt-5">@csrf<button class="w-full rounded-xl border border-orange-300 bg-orange-50 px-4 py-2.5 text-sm font-extrabold text-orange-700 hover:bg-orange-100">Submit for marketplace verification</button></form>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </main>
        </div>
    </div>
</body>
</html>
