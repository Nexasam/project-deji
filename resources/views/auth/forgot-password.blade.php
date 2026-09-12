<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset password – {{ config('app.name', 'Project Nexus') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-surface min-h-screen bg-white text-slate-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[minmax(360px,0.86fr)_minmax(540px,1.14fr)]">
        <section class="relative hidden overflow-hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col xl:p-14">
            <div class="auth-story-media" aria-hidden="true"><span class="auth-story-frame" style="background-image:url('{{ asset('hero1.jpg') }}')"></span><span class="auth-story-frame" style="background-image:url('{{ asset('hero2.jpg') }}')"></span><span class="auth-story-frame" style="background-image:url('{{ asset('hero3.jpg') }}')"></span><span class="auth-story-shade"></span></div>
            <a href="{{ route('home') }}" class="relative z-10 inline-flex w-fit items-center gap-3" aria-label="Project Nexus home">
                <img src="{{ asset('logo1.png') }}" alt="" class="size-11 object-contain">
                <div><p class="text-base font-extrabold tracking-tight">Project Nexus</p><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Verified Shortlet</p></div>
            </a>
            <div class="relative z-10 my-auto max-w-lg py-16">
                <h1 class="text-4xl font-extrabold leading-[1.12] tracking-[-0.035em] xl:text-5xl">One platform. Every hospitality journey.</h1>
                <p class="mt-6 max-w-md text-base leading-7 text-slate-200">Recover secure access to your guest or owner workspace without losing your account context.</p>
            </div>
            <p class="relative z-10 text-xs text-slate-500">Secure access to your Project Nexus workspace.</p>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-8 sm:px-8 lg:px-12 xl:px-20">
            <div class="w-full max-w-md">
                <div class="mb-10 flex items-center justify-between lg:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5"><img src="{{ asset('logo1.png') }}" alt="" class="size-10 object-contain"><span class="font-extrabold">Project Nexus</span></a>
                    <a href="{{ route('home') }}" class="text-sm font-bold text-slate-500 hover:text-orange-600">Back home</a>
                </div>

                <h2 class="text-3xl font-extrabold tracking-[-0.025em] sm:text-4xl">Reset your password</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Enter your account email and we’ll send you a secure password reset link.</p>
                <x-auth-session-status class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
                    @csrf
                    <label for="email" class="property-form-label">Email address
                        <span class="property-form-control-wrap">
                            <svg class="property-form-control-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@example.com" class="property-form-control has-leading-icon @error('email') is-invalid @enderror">
                        </span>
                        @error('email')<span class="property-form-error">{{ $message }}</span>@enderror
                    </label>
                    <button type="submit" class="property-form-submit flex w-full items-center justify-center rounded-xl bg-orange-600 px-5 py-3.5 text-sm font-extrabold text-white hover:bg-orange-700 focus:outline-none focus:ring-4 focus:ring-orange-100">Send reset link</button>
                </form>
                <p class="mt-7 text-center text-sm text-slate-600"><a href="{{ route('login') }}" class="font-extrabold text-orange-600 hover:text-orange-700">Back to sign in</a></p>
            </div>
        </section>
    </main>
</body>
</html>
