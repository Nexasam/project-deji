<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business->name }} – Project Nexus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        @include('partials.sidebar-nav', ['active' => 'dashboard'])

        <div class="min-w-0 flex-1">
            <header class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-5 px-5 py-4 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Open navigation">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                        <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-600">Owner workspace</p>
                        <h1 class="truncate text-xl font-extrabold tracking-tight">{{ $business->name }}</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-owner.view-switch route-name="owner.dashboard" mode="real" />
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-bold text-slate-900">{{ auth()->user()?->name ?? 'Guest' }}</p>
                            <p class="text-xs text-slate-500">Business owner</p>
                        </div>
                        <div class="flex size-10 items-center justify-center rounded-full bg-orange-600 text-sm font-extrabold text-white">
                            {{ auth()->user() ? str(auth()->user()->name)->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->join('') : 'VS' }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-950">Log out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-5 py-8 lg:px-8 lg:py-10">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
                @endif

                @if ($properties->isEmpty())
                    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="grid items-center gap-8 px-6 py-10 sm:px-10 lg:grid-cols-[1fr_360px] lg:px-14 lg:py-16">
                            <div class="max-w-2xl">
                                <div class="mb-6 flex size-14 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 ring-1 ring-orange-100">
                                    <svg class="size-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M9 20v-6h6v6M18 6v3" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Add your first property</h2>
                                <p class="mt-4 max-w-xl text-base leading-7 text-slate-600">
                                    Your business workspace is ready. Add the first serviced apartment or short-let managed by {{ $business->name }}. You can save it as a draft and complete media, amenities and verification later.
                                </p>
                                <div class="mt-7 flex flex-wrap items-center gap-4">
                                    <a href="{{ route('owner.properties.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                        Add first property
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                    <span class="text-sm text-slate-500">No verification required to start a draft.</span>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-slate-950 p-6 text-white">
                                <h3 class="text-sm font-bold">What happens next?</h3>
                                <ol class="mt-5 space-y-5 text-sm text-slate-300">
                                    @foreach ([['1', 'Add the basic property details'], ['2', 'Complete amenities and media'], ['3', 'Submit the listing for verification']] as [$number, $label])
                                        <li class="flex items-center gap-3">
                                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-orange-600 text-xs font-extrabold text-white">{{ $number }}</span>
                                            <span>{{ $label }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </section>
                @else
                    <section>
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                            <div class="flex items-center gap-3">
                                <h2 class="text-xl font-extrabold tracking-tight">Your properties</h2>
                                <span class="rounded-full bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-700">{{ $properties->count() }} {{ Str::plural('property', $properties->count()) }}</span>
                            </div>
                            <a href="{{ route('owner.properties.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-700"><span class="text-base leading-none">+</span> Add property</a>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Real portfolio · {{ $business->name }}</p>

                        <div data-testid="dashboard-property-grid" class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                            @foreach ($properties as $property)
                                <article data-testid="dashboard-property-card" class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-sm font-extrabold text-orange-600">{{ str($property->name)->substr(0, 2)->upper() }}</div>
                                        <div class="flex flex-wrap justify-end gap-1.5">
                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-700">{{ str($property->publication_status->value)->title() }}</span>
                                            <span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-700">{{ str($property->verification_status->value)->title() }}</span>
                                        </div>
                                    </div>
                                    <h3 class="mt-3 truncate font-extrabold text-slate-950" title="{{ $property->name }}">{{ $property->name }}</h3>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ data_get($property->address, 'city') }}, {{ data_get($property->address, 'state') }} · {{ str($property->property_type)->replace('_', ' ')->title() }}</p>
                                    <p class="mt-2 truncate font-mono text-[10px] text-slate-400" title="{{ $property->code }}">{{ $property->code }}</p>
                                    <div class="mt-auto pt-4">
                                        @if ($property->publication_status->value === 'draft')
                                            <a href="{{ route('owner.properties.resume', $property) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-orange-600 px-3 py-2 text-xs font-bold text-white hover:bg-orange-700">Continue setup</a>
                                        @elseif ($property->publication_status->value === 'unpublished' && $property->verification_status->value === 'unverified')
                                            <form method="POST" action="{{ route('owner.properties.marketplace-verification.submit', $property) }}">@csrf<button class="w-full rounded-lg bg-orange-50 px-3 py-2 text-xs font-bold text-orange-700 hover:bg-orange-100">Submit to marketplace</button></form>
                                        @else
                                            <a href="{{ route('owner.properties.index') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">View properties</a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="mt-12 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <div class="flex flex-col justify-between gap-3 px-5 py-4 sm:flex-row sm:items-center lg:px-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-slate-200 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-600">Sample data</span>
                                <h2 class="text-sm font-extrabold text-slate-800">Operational dashboard preview</h2>
                            </div>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Preview data only. Your real business records remain above.</p>
                        </div>
                        <a href="{{ route('owner.dashboard', ['view' => 'demo']) }}" class="shrink-0 text-xs font-bold text-slate-600 underline decoration-slate-300 underline-offset-4">Open presentation view</a>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
