<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amenities – {{ $property->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased">
    <div class="flex min-h-screen">
        @include('partials.sidebar-nav', ['active' => 'properties'])

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="flex items-center justify-between gap-4 px-5 py-4 lg:px-8">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <a href="{{ route('owner.properties.index') }}" class="font-semibold hover:text-orange-700">Properties</a>
                            <span>/</span><span class="truncate">{{ $property->name }}</span>
                        </div>
                        <h1 class="mt-1 truncate text-lg font-extrabold">Property amenities</h1>
                    </div>
                    <a href="{{ route('owner.dashboard') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">Exit setup</a>
                </div>
                <div class="border-t border-slate-100 px-5 py-3 lg:px-8">
                    <div class="mb-2 flex justify-between text-xs"><span class="font-extrabold text-orange-600">Step 2 of 9 · Amenities</span><span class="text-slate-400">Next: Media</span></div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full w-[22.22%] rounded-full bg-orange-600"></div></div>
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-5 py-8 lg:px-8 lg:py-10">
                <div class="mb-8 max-w-3xl">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-orange-600">{{ $property->name }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">What does the property offer?</h2>
                    <p class="mt-3 text-base leading-7 text-slate-600">Select only amenities currently available to guests. You can return and update this list while the property remains a draft.</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">Please review the selected amenities and try again.</div>
                @endif

                <form id="amenities-skip" method="POST" action="{{ route('owner.properties.setup.skip', [$property, 'amenities']) }}" class="hidden">@csrf</form>
                <form method="POST" action="{{ route('owner.properties.setup.amenities.update', $property) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @forelse ($amenities as $category => $items)
                        <section class="property-form-section">
                            <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                                <h3 class="font-extrabold text-slate-950">{{ str($category)->replace('_', ' ')->title() }}</h3>
                                <p class="mt-1 text-sm text-slate-500">Choose every option that applies to this property.</p>
                            </div>
                            <div class="grid gap-3 p-6 sm:grid-cols-2 sm:p-8 lg:grid-cols-3">
                                @foreach ($items as $amenity)
                                    @php($checked = in_array($amenity->id, old('amenities', $selectedAmenityIds), true))
                                    <label class="flex min-h-14 cursor-pointer items-center gap-3 rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-slate-400 hover:bg-white has-checked:border-orange-500 has-checked:bg-orange-50 has-checked:text-orange-900">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="size-5 shrink-0 rounded border-slate-400 text-orange-600 focus:ring-orange-500" @checked($checked)>
                                            {{ $amenity->name }}
                                    </label>
                                @endforeach
                            </div>
                        </section>
                    @empty
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">
                            The amenity catalog is empty. Run <code class="font-bold">php artisan db:seed --class=AmenitySeeder</code>, then reload this page.
                        </div>
                    @endforelse

                    <div class="sticky bottom-4 z-30 flex flex-col-reverse justify-between gap-3 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-xl shadow-slate-200/60 backdrop-blur sm:flex-row sm:items-center">
                        <a href="{{ route('owner.properties.edit', $property) }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-50">Back to basics</a>
                        <div class="flex gap-3"><button type="submit" form="amenities-skip" class="rounded-xl px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100">Skip for now</button><button type="submit" @disabled($amenities->isEmpty()) class="property-form-submit rounded-xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white hover:bg-orange-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none">Save and continue</button></div>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
