@extends('layouts.admin', ['title' => 'Business directory', 'pageTitle' => 'Business directory'])

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <div><p class="text-sm font-semibold text-slate-500">Verify operators, inspect their footprint and act on platform risk.</p><p class="mt-1 text-xs text-slate-400">{{ number_format($businesses->total()) }} registered businesses</p></div>
    <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_12rem_12rem_auto]">
        <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email or registration number" class="rounded-xl border-slate-200 text-sm focus:border-orange-400 focus:ring-orange-200">
        <select name="verification_status" class="rounded-xl border-slate-200 text-sm"><option value="">All verification</option>@foreach(['unverified','pending','verified','rejected'] as $status)<option value="{{ $status }}" @selected(($filters['verification_status'] ?? '')===$status)>{{ str($status)->title() }}</option>@endforeach</select>
        <select name="status" class="rounded-xl border-slate-200 text-sm"><option value="">All account states</option>@foreach(['active','inactive','suspended'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ str($status)->title() }}</option>@endforeach</select>
        <button class="rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-black text-white hover:bg-orange-600">Apply filters</button>
    </form>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden grid-cols-[1.3fr_.8fr_.65fr_.65fr_auto] gap-4 border-b border-slate-100 bg-slate-50 px-5 py-3 text-[11px] font-black uppercase tracking-wider text-slate-500 md:grid"><span>Business</span><span>Verification</span><span>Portfolio</span><span>Bookings</span><span></span></div>
        <div class="divide-y divide-slate-100">
            @forelse($businesses as $business)
                @php $verification=$business->verification_status->value; $state=$business->status->value; @endphp
                <article class="grid gap-4 px-5 py-4 hover:bg-slate-50 md:grid-cols-[1.3fr_.8fr_.65fr_.65fr_auto] md:items-center">
                    <div><h2 class="font-black">{{ $business->name }}</h2><p class="mt-1 text-xs text-slate-500">{{ $business->email ?: 'No email' }} · {{ $business->registration_number ?: 'No registration number' }}</p></div>
                    <div class="flex flex-wrap gap-1.5"><span class="rounded-full px-2.5 py-1 text-[11px] font-black {{ $verification==='verified'?'bg-emerald-100 text-emerald-800':($verification==='rejected'?'bg-red-100 text-red-800':'bg-amber-100 text-amber-800') }}">{{ str($verification)->title() }}</span>@if($state==='suspended')<span class="rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-black text-red-800">Suspended</span>@endif</div>
                    <p class="text-sm font-bold">{{ $business->properties_count }} <span class="text-xs font-medium text-slate-400">properties</span></p>
                    <p class="text-sm font-bold">{{ $business->bookings_count }} <span class="text-xs font-medium text-slate-400">total</span></p>
                    <a href="{{ route('admin.businesses.show',$business) }}" class="rounded-xl bg-orange-600 px-4 py-2 text-center text-sm font-black text-white">Inspect</a>
                </article>
            @empty<div class="p-12 text-center"><p class="font-bold text-slate-700">No businesses match these filters.</p></div>@endforelse
        </div>
    </section>
    {{ $businesses->links() }}
</div>
@endsection
