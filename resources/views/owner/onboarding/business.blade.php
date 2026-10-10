@extends('layouts.app')

@section('title', 'Set up your business - Verified Shortlet')

@section('content')
<main class="min-h-screen bg-slate-50 px-5 py-12">
    <div class="mx-auto max-w-2xl rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200 md:p-12">
        <p class="text-sm font-bold uppercase tracking-widest text-orange-600">Owner setup</p>
        <h1 class="mt-3 text-3xl font-bold text-slate-950">Tell us about your business</h1>
        <p class="mt-2 text-slate-600">This creates your private workspace. Verification happens later and will not stop you drafting properties.</p>
        @if ($errors->any())<div class="mt-6 rounded-xl bg-red-50 p-4 text-sm text-red-700"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('owner.onboarding.business.store') }}" class="mt-8 grid gap-5 md:grid-cols-2">
            @csrf
            <label class="md:col-span-2 block text-sm font-semibold text-slate-700">Business name<input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Business type<input name="business_type" value="{{ old('business_type', 'Serviced apartments') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Registration number <span class="font-normal text-slate-400">(optional)</span><input name="registration_number" value="{{ old('registration_number') }}" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Country code<input name="country_code" value="{{ old('country_code', 'NG') }}" maxlength="2" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Currency<input name="currency" value="{{ old('currency', 'NGN') }}" maxlength="3" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Time zone<input name="timezone" value="{{ old('timezone', 'Africa/Lagos') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Contact phone<input name="phone_number" value="{{ old('phone_number', auth()->user()?->phone_number) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="md:col-span-2 block text-sm font-semibold text-slate-700">Business address<input name="address_line" value="{{ old('address_line') }}" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <section class="md:col-span-2 rounded-2xl border border-orange-200 bg-orange-50 p-5">
                <h2 class="font-bold text-slate-950">Settlement identity verification</h2>
                <p class="mt-1 text-sm text-slate-600">Enter the business administrator's NIN and BVN. They are sent securely to the configured identity provider. We retain only masked digits, provider references and the result—not the complete numbers.</p>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <label class="block text-sm font-semibold text-slate-700">NIN<input name="nin" value="{{ old('nin') }}" inputmode="numeric" autocomplete="off" pattern="[0-9]{11}" maxlength="11" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900"></label>
                    <label class="block text-sm font-semibold text-slate-700">BVN<input name="bvn" value="{{ old('bvn') }}" inputmode="numeric" autocomplete="off" pattern="[0-9]{11}" maxlength="11" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900"></label>
                </div>
                <label class="mt-4 flex items-start gap-3 text-sm text-slate-700"><input type="checkbox" name="identity_consent" value="1" @checked(old('identity_consent')) required class="mt-1 rounded border-slate-300 text-orange-600"><span>I consent to submitting these identity numbers to the configured verification provider for business approval and settlement eligibility.</span></label>
            </section>
            <button class="md:col-span-2 rounded-xl bg-[#FF5A00] px-5 py-3 font-bold text-white hover:bg-[#E64F00] transition-colors">Create business workspace</button>
        </form>
        
    </div>
</main>
@endsection
