<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create account – {{ config('app.name', 'Project Nexus') }}</title>
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
            <p class="relative z-10 text-xs text-slate-500">One secure identity across Project Nexus.</p>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-8 sm:px-8 lg:px-12 xl:px-20">
            <div class="w-full max-w-lg" x-data="{ showPassword: false, showConfirmation: false }">
                <div class="mb-8 flex items-center justify-between lg:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5"><img src="{{ asset('logo1.png') }}" alt="" class="size-10 object-contain"><span class="font-extrabold">Project Nexus</span></a>
                    <a href="{{ route('home') }}" class="text-sm font-bold text-slate-500 hover:text-orange-600">Back home</a>
                </div>

                <div><h2 class="text-3xl font-extrabold tracking-[-0.025em] sm:text-4xl">Create your account</h2><p class="mt-3 text-sm leading-6 text-slate-600">Get started as a guest. You can add an owner workspace later.</p></div>

                <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-4">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label for="name" class="property-form-label sm:col-span-2">Full name
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your full name" class="property-form-control mt-2 @error('name') is-invalid @enderror">
                            @error('name')<span class="property-form-error">{{ $message }}</span>@enderror
                        </label>

                        <label for="email" class="property-form-label">Email address
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com" class="property-form-control mt-2 @error('email') is-invalid @enderror">
                            @error('email')<span class="property-form-error">{{ $message }}</span>@enderror
                        </label>

                        <label for="phone_number" class="property-form-label">Phone number <span class="property-form-optional">(optional)</span>
                            <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}" autocomplete="tel" placeholder="+234 800 000 0000" class="property-form-control mt-2 @error('phone_number') is-invalid @enderror">
                            @error('phone_number')<span class="property-form-error">{{ $message }}</span>@enderror
                        </label>

                        <label for="password" class="property-form-label">Password
                            <span class="property-form-control-wrap"><input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Create a password" class="property-form-control pr-14 @error('password') is-invalid @enderror"><button type="button" @click="showPassword = ! showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs font-bold text-slate-500 hover:bg-slate-100" x-text="showPassword ? 'Hide' : 'Show'"></button></span>
                            @error('password')<span class="property-form-error">{{ $message }}</span>@enderror
                        </label>

                        <label for="password_confirmation" class="property-form-label">Confirm password
                            <span class="property-form-control-wrap"><input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password" class="property-form-control pr-14"><button type="button" @click="showConfirmation = ! showConfirmation" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg px-2 py-1 text-xs font-bold text-slate-500 hover:bg-slate-100" x-text="showConfirmation ? 'Hide' : 'Show'"></button></span>
                        </label>
                    </div>

                    <label for="terms" class="flex cursor-pointer items-start gap-3 rounded-xl border @error('terms') border-red-300 bg-red-50 @else border-slate-200 bg-slate-50 @enderror px-4 py-3 text-sm leading-5 text-slate-600">
                        <input id="terms" type="checkbox" name="terms" value="1" required @checked(old('terms')) class="mt-0.5 size-4 shrink-0 rounded border-slate-400 text-orange-600 focus:ring-orange-500">
                        <span>I agree to the <strong class="text-slate-900">Terms of Service</strong> and <strong class="text-slate-900">Privacy Policy</strong>.</span>
                    </label>
                    @error('terms')<span class="property-form-error">{{ $message }}</span>@enderror

                    <button type="submit" class="property-form-submit flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-3.5 text-sm font-extrabold text-white hover:bg-orange-700 focus:outline-none focus:ring-4 focus:ring-orange-100">Create account<svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </form>

                <p class="mt-7 text-center text-sm text-slate-600">Already have an account? <a href="{{ route('login') }}" class="font-extrabold text-orange-600 hover:text-orange-700">Sign in</a></p>
            </div>
        </section>
    </main>
</body>
</html>
