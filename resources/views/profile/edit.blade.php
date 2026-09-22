@extends('layouts.app')

@section('title', 'Profile – Verified Shortlet')

@section('content')
<x-navbar />

<main class="bg-slate-50">
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 sm:py-14">
        <div class="mb-6">
            <a href="{{ route('guest.dashboard') }}" class="text-sm font-bold text-orange-600 hover:text-orange-700">← Dashboard</a>
            <h1 class="mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Profile</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Keep your contact details current so property teams can coordinate arrival and urgent stay updates.</p>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">Profile updated successfully.</div>
        @endif

        <div class="grid gap-5 lg:grid-cols-[1fr_.82fr]">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                @include('profile.partials.update-profile-information-form')
            </section>

            <div class="space-y-5">
                <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    @include('profile.partials.update-password-form')
                </section>

                {{-- Delete account is intentionally hidden for the MVP client review. --}}
            </div>
        </div>
    </section>
</main>
@endsection
