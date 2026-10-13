@extends('layouts.app')

@section('title', 'Set up your business - Verified Shortlet')

@section('content')
<main class="min-h-screen bg-slate-50 px-5 py-12">
    <div class="mx-auto max-w-2xl rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200 md:p-12">
        <p class="text-sm font-semibold text-orange-600">Host setup</p>
        <h1 class="mt-3 text-3xl font-semibold text-slate-950">Tell us about your business</h1>
        <p class="mt-2 text-slate-600">This creates your private workspace. Verification happens later and will not stop you drafting properties.</p>
        @if ($errors->any())<div class="mt-6 rounded-xl bg-red-50 p-4 text-sm text-red-700"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('owner.onboarding.business.store') }}" class="mt-8 grid gap-5 md:grid-cols-2" x-data="{ country: @js(old('country_code', 'NG')), currency: @js(old('currency', 'NGN')), timezone: @js(old('timezone', 'Africa/Lagos')), presets: { NG: ['NGN','Africa/Lagos'], GH: ['GHS','Africa/Accra'], KE: ['KES','Africa/Nairobi'], ZA: ['ZAR','Africa/Johannesburg'], GB: ['GBP','Europe/London'], US: ['USD','America/New_York'], CA: ['CAD','America/Toronto'], AE: ['AED','Asia/Dubai'] }, applyCountry() { const preset=this.presets[this.country]; if(preset){ this.currency=preset[0]; this.timezone=preset[1]; } } }">
            @csrf
            <label class="md:col-span-2 block text-sm font-semibold text-slate-700">Business name<input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Business type<input name="business_type" value="{{ old('business_type', 'Serviced apartments') }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Registration number <span class="font-normal text-slate-400">(optional)</span><input name="registration_number" value="{{ old('registration_number') }}" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="block text-sm font-semibold text-slate-700">Country<select name="country_code" x-model="country" @change="applyCountry" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900"><option value="NG">Nigeria</option><option value="GH">Ghana</option><option value="KE">Kenya</option><option value="ZA">South Africa</option><option value="GB">United Kingdom</option><option value="US">United States</option><option value="CA">Canada</option><option value="AE">United Arab Emirates</option></select></label>
            <label class="block text-sm font-semibold text-slate-700">Currency<input name="currency" x-model="currency" maxlength="3" required readonly class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-700"></label>
            <label class="block text-sm font-semibold text-slate-700">Time zone<input name="timezone" x-model="timezone" required readonly class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-700"></label>
            <label class="block text-sm font-semibold text-slate-700">Contact phone<input name="phone_number" value="{{ old('phone_number', auth()->user()?->phone_number) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <label class="md:col-span-2 block text-sm font-semibold text-slate-700">Business address<input name="address_line" value="{{ old('address_line') }}" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"></label>
            <section class="md:col-span-2 rounded-2xl border border-orange-200 bg-orange-50 p-5">
                <h2 class="font-semibold text-slate-950">Administrator identity verification</h2>
                <p class="mt-1 text-sm text-slate-600">Enter the business administrator's NIN. It is sent securely to the configured identity provider. We retain only masked digits, the provider reference and the result—not the complete number.</p>
                <label class="mt-4 block text-sm font-semibold text-slate-700">NIN<input name="nin" value="{{ old('nin') }}" inputmode="numeric" autocomplete="off" pattern="[0-9]{11}" maxlength="11" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900"><span class="mt-2 block text-xs font-normal text-slate-500">If verification fails, we will show the provider's reason and you can correct the number or contact support.</span></label>
                <label class="mt-4 flex items-start gap-3 text-sm text-slate-700"><input type="checkbox" name="identity_consent" value="1" @checked(old('identity_consent')) required class="mt-1 rounded border-slate-300 text-orange-600"><span>I consent to submitting this NIN to the configured verification provider for business approval and settlement eligibility.</span></label>
            </section>
            <button class="md:col-span-2 rounded-xl bg-[#FF5A00] px-5 py-3 font-bold text-white hover:bg-[#E64F00] transition-colors">Create business workspace</button>
        </form>
        
    </div>
</main>
@endsection
