<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Platform administration' }} · Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{ adminNavOpen: false }">
    <div class="min-h-screen lg:grid lg:grid-cols-[17rem_minmax(0,1fr)]">
        <div x-cloak x-show="adminNavOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/45 backdrop-blur-sm lg:hidden" @click="adminNavOpen=false"></div>
        <aside class="fixed inset-y-0 left-0 z-50 flex w-[17rem] -translate-x-full flex-col border-r border-slate-800 bg-slate-950 text-white transition-transform duration-200 lg:translate-x-0" :class="adminNavOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            @include('partials.admin-sidebar')
        </aside>

        <div class="min-w-0 lg:col-start-2">
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
                <div class="flex h-18 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" class="grid size-10 shrink-0 place-items-center rounded-xl border border-slate-200 text-slate-700 lg:hidden" @click="adminNavOpen=true" aria-label="Open navigation">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                        </button>
                        <div class="min-w-0">
                            <p class="text-[11px] font-extrabold uppercase tracking-[.18em] text-orange-600">Platform administration</p>
                            <h1 class="truncate text-lg font-black text-slate-950">{{ $pageTitle ?? 'Operations centre' }}</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="max-w-48 truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm hover:border-slate-300 hover:bg-slate-50">Log out</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                @if(session('status'))
                    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
