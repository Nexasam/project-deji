@extends('layouts.admin', ['title' => 'User directory', 'pageTitle' => 'User directory'])

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <div><p class="text-sm font-semibold text-slate-500">Inspect guest, operator and platform identities without crossing role boundaries.</p><p class="mt-1 text-xs text-slate-400">{{ number_format($users->total()) }} accounts</p></div>
    <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_12rem_12rem_auto]">
        <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email or phone" class="rounded-xl border-slate-200 text-sm focus:border-orange-400 focus:ring-orange-200">
        <select name="status" class="rounded-xl border-slate-200 text-sm"><option value="">All statuses</option>@foreach(['active','inactive','suspended'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ str($status)->title() }}</option>@endforeach</select>
        <select name="access" class="rounded-xl border-slate-200 text-sm"><option value="">All access types</option>@foreach(['locked'=>'Locked','platform'=>'Platform admin','business'=>'Business member','guest'=>'Guest only'] as $value=>$label)<option value="{{ $value }}" @selected(($filters['access'] ?? '')===$value)>{{ $label }}</option>@endforeach</select>
        <button class="rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-black text-white hover:bg-orange-600">Apply filters</button>
    </form>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="hidden grid-cols-[1.2fr_.9fr_.7fr_.6fr_auto] gap-4 border-b border-slate-100 bg-slate-50 px-5 py-3 text-[11px] font-black uppercase tracking-wider text-slate-500 md:grid"><span>User</span><span>Access</span><span>Businesses</span><span>Status</span><span></span></div><div class="divide-y divide-slate-100">
        @forelse($users as $user)
            @php $platformRole=$user->roleAssignments->first(fn($assignment)=>$assignment->role?->scope->value==='platform' && $assignment->status->value==='active')?->role; @endphp
            <article class="grid gap-4 px-5 py-4 hover:bg-slate-50 md:grid-cols-[1.2fr_.9fr_.7fr_.6fr_auto] md:items-center"><div><h2 class="font-black">{{ $user->name }}</h2><p class="mt-1 text-xs text-slate-500">{{ $user->email }}</p></div><div><p class="text-sm font-bold">{{ $platformRole?->name ?? ($user->business_memberships_count ? 'Business member' : 'Guest') }}</p><p class="mt-1 text-xs text-slate-400">{{ $user->bookings_count }} bookings</p></div><p class="text-sm font-bold">{{ $user->business_memberships_count }}</p><div class="flex flex-wrap gap-1"><span class="rounded-full px-2.5 py-1 text-[11px] font-black {{ $user->status->value==='active'?'bg-emerald-100 text-emerald-800':'bg-red-100 text-red-800' }}">{{ str($user->status->value)->title() }}</span>@if($user->locked_until?->isFuture())<span class="rounded-full bg-slate-900 px-2.5 py-1 text-[11px] font-black text-white">Locked</span>@endif</div><a href="{{ route('admin.users.show',$user) }}" class="rounded-xl bg-orange-600 px-4 py-2 text-center text-sm font-black text-white">Inspect</a></article>
        @empty<div class="p-12 text-center text-sm font-bold text-slate-500">No accounts match these filters.</div>@endforelse
    </div></section>
    {{ $users->links() }}
</div>
@endsection
