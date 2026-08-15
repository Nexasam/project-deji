<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in – {{ config('app.name', 'Project Nexus') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-surface min-h-screen bg-white text-slate-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[minmax(360px,0.86fr)_minmax(540px,1.14fr)]">
        <section class="relative hidden overflow-hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col xl:p-14">
            <div class="auth-story-media" aria-hidden="true"><span class="auth-story-frame"></span><span class="auth-story-frame"></span><span class="auth-story-frame"></span><span class="auth-story-shade"></span></div>

            <a href="{{ route('home') }}" class="relative z-10 inline-flex w-fit items-center gap-3" aria-label="Project Nexus home">
                <img src="{{ asset('logo1.png') }}" alt="" class="size-11 object-contain">
                <div><p class="text-base font-extrabold tracking-tight">Project Nexus</p><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Verified Shortlet</p></div>
            </a>

            <div class="relative z-10 my-auto max-w-lg py-16">
                <h1 class="text-4xl font-extrabold leading-[1.12] tracking-[-0.035em] xl:text-5xl">One platform. Every hospitality journey.</h1>
                <p class="mt-6 max-w-md text-base leading-7 text-slate-200">Whether you’re booking a stay, managing a property, supporting guests or overseeing the platform, Project Nexus keeps your work connected.</p>

                <div class="mt-10 space-y-5 border-t border-white/10 pt-8">
                    @foreach ([
                        'Book and manage stays with confidence.',
                        'Coordinate people, properties, payments and tasks.',
                        'Switch between roles without losing context.',
                    ] as $benefit)
                        <div class="flex items-start gap-3 text-sm leading-6 text-slate-300"><span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-orange-600 text-white"><svg class="size-3" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3 8 3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span>{{ $benefit }}</span></div>
                    @endforeach
                </div>
            </div>

            <p class="relative z-10 text-xs text-slate-500">Secure access to your Project Nexus workspace.</p>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-8 sm:px-8 lg:px-12 xl:px-20">
            <div class="w-full max-w-md" x-data="{ showPassword: false }">
                <div class="mb-10 flex items-center justify-between lg:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5"><img src="{{ asset('logo1.png') }}" alt="" class="size-10 object-contain"><span class="font-extrabold">Project Nexus</span></a>
                    <a href="{{ route('home') }}" class="text-sm font-bold text-slate-500 hover:text-orange-600">Back home</a>
                </div>

                <div>
                    <h2 class="text-3xl font-extrabold tracking-[-0.025em] sm:text-4xl">Welcome back</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Sign in to continue to your owner or guest workspace.</p>
                </div>

                <x-auth-session-status class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                    @csrf

                    <label for="email" class="property-form-label">Email address
                        <span class="property-form-control-wrap">
                            <svg class="property-form-control-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com" class="property-form-control has-leading-icon @error('email') is-invalid @enderror" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                        </span>
                        @error('email')<span id="email-error" class="property-form-error">{{ $message }}</span>@enderror
                    </label>

                    <label for="password" class="property-form-label">Password
                        <span class="property-form-control-wrap">
                            <svg class="property-form-control-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8"/></svg>
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Enter your password" class="property-form-control has-leading-icon pr-14 @error('password') is-invalid @enderror" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                            <button type="button" @click="showPassword = ! showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs font-bold text-slate-500 hover:bg-slate-100 hover:text-slate-900" :aria-label="showPassword ? 'Hide password' : 'Show password'" x-text="showPassword ? 'Hide' : 'Show'"></button>
                        </span>
                        @error('password')<span id="password-error" class="property-form-error">{{ $message }}</span>@enderror
                    </label>

                    <div class="flex items-center justify-between gap-4">
                        <label for="remember_me" class="flex cursor-pointer items-center gap-2.5 text-sm font-semibold text-slate-600"><input id="remember_me" type="checkbox" name="remember" class="size-4 rounded border-slate-400 text-orange-600 focus:ring-orange-500">Remember me</label>
                        @if (Route::has('password.request'))<a href="{{ route('password.request') }}" class="text-sm font-bold text-orange-600 hover:text-orange-700">Forgot password?</a>@endif
                    </div>

                    <button type="submit" class="property-form-submit flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-3.5 text-sm font-extrabold text-white hover:bg-orange-700 focus:outline-none focus:ring-4 focus:ring-orange-100">Sign in<svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </form>

                @if (Route::has('register'))
                    <p class="mt-8 text-center text-sm text-slate-600">New to Project Nexus? <a href="{{ route('register') }}" class="font-extrabold text-orange-600 hover:text-orange-700">Create an account</a></p>
                @endif
                <p class="mt-10 text-center text-xs leading-5 text-slate-400">By continuing, you agree to the platform terms and privacy policy.</p>
            </div>
        </section>
    </main>
</body>
</html>
