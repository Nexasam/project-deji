@extends('layouts.app')

@section('title', 'Settings – Verified Shortlet')

@section('content')
<x-navbar />
<main class="min-h-screen bg-[#f3f3f2] px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-[1380px]">
        <h1 class="text-3xl font-black text-slate-950">Settings</h1>
        <p class="mt-1 text-sm text-slate-500">Verified Shortlet &gt; Settings</p>

        @if(session('status') === 'profile-updated')<div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">Your settings were updated successfully.</div>@endif
        @if(session('status') === 'identity-verified')<div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">Your NIN was verified successfully.</div>@endif

        <div class="mt-6 grid gap-5 lg:grid-cols-[290px_minmax(0,1fr)]">
            <aside class="h-fit overflow-hidden rounded-2xl border border-slate-200 bg-white py-3 shadow-sm">
                <a href="#profile" class="flex items-center gap-4 border-l-4 border-orange-600 bg-slate-100 px-7 py-5 font-black text-slate-950"><span class="text-xl text-orange-600">♙</span> Profile</a>
                <a href="#security" class="flex items-center gap-4 px-8 py-5 font-semibold text-slate-700 hover:bg-slate-50"><span class="text-xl text-slate-400">♢</span> Account &amp; security</a>
                <a href="#notifications" class="flex items-center gap-4 px-8 py-5 font-semibold text-slate-700 hover:bg-slate-50"><span class="text-xl text-slate-400">♧</span> Notifications</a>
                <span class="flex items-center gap-4 px-8 py-5 font-semibold text-slate-400"><span class="text-xl">☷</span> Preferences <small class="ml-auto rounded-full bg-slate-100 px-2 py-1 text-[10px]">Soon</small></span>
                <div class="mt-3 border-t border-slate-200 px-8 pt-4"><form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full py-3 text-left font-semibold text-slate-700">↪ &nbsp; Log out</button></form></div>
            </aside>

            <div class="space-y-5">
                <section id="profile" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <form method="post" action="{{ route('guest.settings.update') }}">@csrf @method('patch')
                        <div class="flex flex-col gap-5 border-b border-slate-100 pb-6 sm:flex-row sm:items-start sm:justify-between">
                            <div><h2 class="text-xl font-black text-slate-950">Profile</h2><p class="mt-1 text-sm text-slate-500">Your details help hosts confirm and verify your bookings.</p></div>
                            <button class="rounded-xl bg-orange-600 px-6 py-3 text-sm font-black text-white shadow-sm hover:bg-orange-700">Save changes</button>
                        </div>
                        <div class="mt-6 grid gap-6 lg:grid-cols-[170px_1fr]">
                            <div class="text-center"><div class="mx-auto grid size-24 place-items-center rounded-full bg-orange-100 text-3xl font-black text-slate-950 ring-1 ring-orange-200">{{ str($user->name)->squish()->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->join('') }}</div><p class="mt-4 text-xs text-slate-400">Profile photo upload<br>coming later</p></div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <label class="text-xs font-black uppercase tracking-wide text-slate-500">Full name<input name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" class="mt-2 block w-full" placeholder="Your full name"><x-input-error class="mt-2" :messages="$errors->get('name')" /></label>
                                <label class="text-xs font-black uppercase tracking-wide text-slate-500">Email<input name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="mt-2 block w-full" placeholder="you@example.com"><x-input-error class="mt-2" :messages="$errors->get('email')" /></label>
                                <label class="text-xs font-black uppercase tracking-wide text-slate-500">Phone number<input name="phone_number" type="tel" value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel" class="mt-2 block w-full" placeholder="+234 801 234 5678"><x-input-error class="mt-2" :messages="$errors->get('phone_number')" /></label>
                                <label class="text-xs font-black uppercase tracking-wide text-slate-500">NIN verification
                                    <span class="mt-2 flex overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm transition focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-100">
                                        <input name="nin" value="" inputmode="numeric" maxlength="11" autocomplete="off" class="min-w-0 flex-1 border-0 bg-transparent px-4 py-3 text-sm font-medium text-slate-900 outline-none ring-0 focus:ring-0" placeholder="{{ $user->identity_verification_status->value === 'verified' ? 'Verified · enter only to update' : 'Enter your 11-digit NIN' }}">
                                        <button type="submit" class="m-1.5 shrink-0 rounded-lg bg-slate-950 px-4 py-2 text-xs font-black normal-case tracking-normal text-white transition hover:bg-orange-600">Verify</button>
                                    </span>
                                    <x-input-error class="mt-2" :messages="$errors->get('nin')" />
                                    <span class="mt-2 inline-flex rounded-full px-3 py-1 text-[11px] font-black {{ $user->identity_verification_status->value === 'verified' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ str($user->identity_verification_status->value)->title() }}</span>
                                </label>
                            </div>
                        </div>

                        <section id="notifications" class="mt-7 border-t border-slate-100 pt-7">
                            <h2 class="text-xl font-black text-slate-950">Notifications</h2><p class="mt-1 text-sm text-slate-500">Choose what Verified Shortlet should notify you about. Delivery uses in-app now and email/SMS when connected.</p>
                            @php($notificationPrefs = data_get($user->marketing_preferences, 'notifications', []))
                            <div class="mt-5 divide-y divide-slate-100">
                                @foreach([
                                    'booking_updates' => ['Booking updates','Confirmations, changes and reminders'],
                                    'host_messages' => ['Messages from hosts','Get notified when a host replies'],
                                    'saved_stay_price_drops' => ['Price drops on saved stays','Alerts when a saved stay gets cheaper'],
                                    'promotions' => ['Promotions and travel tips','Offers and destination suggestions'],
                                ] as $key => [$title,$copy])
                                    <label class="group flex cursor-pointer items-center justify-between gap-5 py-4">
                                        <span><span class="block font-black text-slate-900">{{ $title }}</span><span class="text-sm text-slate-500">{{ $copy }}</span></span>
                                        <input type="hidden" name="notifications[{{ $key }}]" value="0">
                                        <span class="relative inline-flex h-7 w-12 shrink-0 items-center rounded-full border border-slate-200 bg-slate-200 p-0.5 transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-500">
                                            <input type="checkbox" name="notifications[{{ $key }}]" value="1" @checked((bool) data_get($notificationPrefs, $key, true)) class="peer sr-only">
                                            <span class="size-5 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5"></span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </section>
                    </form>
                </section>

                <section id="security" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <h2 class="text-xl font-black text-slate-950">Security</h2><p class="mt-1 text-sm text-slate-500">Use a strong, unique password for your account.</p>
                    <div class="mt-6">@include('profile.partials.update-password-form', ['compact' => true])</div>
                </section>
            </div>
        </div>
    </div>
</main>
@endsection
