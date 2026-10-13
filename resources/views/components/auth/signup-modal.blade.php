<div x-show="$store.modals.showSignupModal" x-cloak @keydown.escape.window="$store.modals.closeAll()" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/65 px-4" role="dialog" aria-modal="true" aria-labelledby="signup-title">
    <button type="button" class="absolute inset-0" @click="$store.modals.closeAll()" aria-label="Close registration"></button>
    <section class="relative w-full max-w-xl rounded-3xl bg-white p-6 shadow-2xl sm:p-8">
        <button type="button" @click="$store.modals.closeAll()" class="absolute right-4 top-4 grid size-9 place-items-center rounded-full border border-slate-200 text-xl text-slate-600" aria-label="Close">×</button>
        <p class="text-xs font-bold uppercase tracking-[.14em] text-orange-600">Create an account</p>
        <h2 id="signup-title" class="mt-2 text-2xl font-extrabold text-slate-950">How will you use Verified Shortlet?</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">Choose a path. The next page collects only the details needed for that account.</p>
        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            <a href="{{ route('register', ['account_type' => 'individual']) }}" class="rounded-2xl border border-slate-200 p-5 transition hover:border-orange-300 hover:bg-orange-50/40"><span class="text-lg font-extrabold text-slate-950">Book a stay</span><span class="mt-2 block text-sm leading-6 text-slate-500">Create a guest account to save properties, verify your NIN and make bookings.</span><span class="mt-4 block text-sm font-bold text-orange-600">Create guest account →</span></a>
            <a href="{{ route('register', ['account_type' => 'business']) }}" class="rounded-2xl border border-slate-200 p-5 transition hover:border-orange-300 hover:bg-orange-50/40"><span class="text-lg font-extrabold text-slate-950">List a property</span><span class="mt-2 block text-sm leading-6 text-slate-500">Create a host account, complete business onboarding and submit properties.</span><span class="mt-4 block text-sm font-bold text-orange-600">Create host account →</span></a>
        </div>
        <p class="mt-6 text-center text-sm text-slate-500">Already registered? <button type="button" @click="$store.modals.showSignupModal=false; $store.modals.showLoginModal=true" class="font-bold text-slate-950 underline underline-offset-4">Sign in</button></p>
    </section>
</div>
