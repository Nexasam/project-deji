<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard demo – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F4F4F4] font-sans text-slate-950 antialiased">
    <div class="flex min-h-screen">
        @include('partials.sidebar-nav', ['active' => 'dashboard'])

        <div class="min-w-0 flex-1">
            <header class="border-b border-slate-200 bg-white px-5 py-3 lg:px-6">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-amber-800">Sample data</span>
                            <span class="text-xs text-slate-500">Presentation mode</span>
                        </div>
                        <h1 class="mt-1 text-lg font-extrabold">Owner dashboard preview</h1>
                    </div>
                    <x-owner.view-switch route-name="owner.dashboard" mode="demo" />
                </div>
            </header>

            <main class="p-5 lg:p-6">
                <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <strong>Client presentation:</strong> every metric, property, activity and AI insight below is sample data and does not affect {{ $business->name }}.
                </div>
                @include('partials.dashboard-content')
            </main>
        </div>
    </div>
</body>
</html>
