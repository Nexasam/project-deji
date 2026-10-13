<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify business administrator - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex min-h-screen">
    @include('partials.sidebar-nav', ['active' => 'dashboard'])
    <div class="min-w-0 flex-1">
        <header class="border-b border-slate-200 bg-white px-4 py-4 lg:px-8">
            <button type="button" @click="sidebarOpen=true" class="mb-3 grid size-10 place-items-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 md:hidden" aria-label="Open navigation"><x-ui.icon name="menu" /></button>
            <p class="text-xs font-semibold uppercase tracking-wider text-orange-600">Business verification</p>
            <h1 class="mt-1 text-2xl">Verify administrator identity</h1>
        </header>
        <main>
            <section class="mx-auto max-w-2xl rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200 md:p-10">
                <h2 class="text-xl">Verify the administrator for {{ $business->name }}</h2>
                <p class="mt-2 text-sm text-slate-600">Required before business approval, property publication and owner settlements.</p>
                <div class="mt-5 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">We keep only masked digits, the provider reference and the verification result.</div>
                @if ($errors->any())<div class="mt-6 rounded-xl bg-red-50 p-4 text-sm text-red-700"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('owner.onboarding.identity.update') }}" class="mt-7 space-y-5">
                    @csrf
                    <label class="block text-sm text-slate-700">NIN<input name="nin" value="{{ old('nin') }}" inputmode="numeric" autocomplete="off" pattern="[0-9]{11}" maxlength="11" required class="mt-2 w-full"><span class="mt-2 block text-xs font-normal text-slate-500">Any verification problem will be shown here.</span></label>
                    <label class="flex items-start gap-3 text-sm text-slate-700"><input type="checkbox" name="identity_consent" value="1" @checked(old('identity_consent')) required class="mt-1"><span>I consent to NIN verification.</span></label>
                    <div class="flex justify-end gap-3"><x-ui.button :href="route('owner.dashboard')" variant="secondary">Not now</x-ui.button><x-ui.button type="submit">Verify NIN</x-ui.button></div>
                </form>
            </section>
        </main>
    </div>
</div>
</body>
</html>
