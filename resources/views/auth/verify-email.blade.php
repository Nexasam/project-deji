@extends('layouts.app')

@section('title', 'Verify your email – Project Nexus')

@section('content')
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 px-5 py-10 sm:px-8">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-64 bg-gradient-to-b from-orange-50 to-transparent"></div>

        <section class="relative w-full max-w-lg">
            <a href="{{ route('home') }}" class="mx-auto mb-8 flex w-fit items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-4">
                <span class="flex size-10 items-center justify-center rounded-xl bg-orange-600 text-white shadow-sm shadow-orange-200">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 17.5V9.25L12 5l7 4.25v8.25L12 21l-7-3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="m8.5 11 3.5 2 3.5-2M12 13v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="text-xl font-extrabold tracking-tight text-slate-950">Project Nexus</span>
            </a>

            <div class="rounded-3xl border border-slate-200 bg-white px-6 py-9 text-center shadow-xl shadow-slate-200/60 sm:px-10 sm:py-11">
                <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 ring-1 ring-orange-100">
                    <svg class="size-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m5 8 7 5 7-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h1 class="mt-6 text-3xl font-extrabold tracking-tight text-slate-950">Check your email</h1>
                <p class="mx-auto mt-3 max-w-sm text-sm leading-6 text-slate-600 sm:text-base">
                    We sent a verification link to
                    <strong class="block break-all font-bold text-slate-900">{{ auth()->user()->email }}</strong>
                </p>
                <p class="mx-auto mt-4 max-w-sm text-sm leading-6 text-slate-500">
                    Open the link to verify your account and continue setting up your workspace. It may take a minute to arrive.
                </p>

                @if (session('status') === 'verification-link-sent')
                    <div role="status" class="mt-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-left text-sm leading-5 text-emerald-800">
                        <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m5 12 4.5 4.5L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>A fresh verification link has been sent to your email address.</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="mt-7">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 7v5h-5M4 17v-5h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.1 9A7 7 0 0 1 18.5 7.5L20 12M4 12l1.5 4.5A7 7 0 0 0 17.9 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Resend verification email
                    </button>
                </form>

                <div class="mt-6 border-t border-slate-100 pt-6">
                    <p class="text-xs leading-5 text-slate-500">Cannot find it? Check your spam or junk folder.</p>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="rounded-lg px-2 py-1 text-sm font-semibold text-slate-600 transition hover:text-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                            Use a different account
                        </button>
                    </form>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">Secure property operations, from onboarding to checkout.</p>
        </section>
    </main>
@endsection
