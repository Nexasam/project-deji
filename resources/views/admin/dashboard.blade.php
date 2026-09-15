@extends('layouts.admin', ['title' => 'Platform overview', 'pageTitle' => 'Platform overview'])

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="overflow-hidden rounded-3xl bg-slate-950 px-6 py-7 text-white shadow-xl shadow-slate-200 sm:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-black uppercase tracking-[.22em] text-orange-400">Today’s control room</p>
            <h2 class="mt-3 text-2xl font-black sm:text-3xl">Keep verification, safety and guest support moving.</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">Every number below is an action queue, not vanity analytics. Open the areas your role permits and resolve what needs attention.</p>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Awaiting verification', 'value' => $summary['businesses_awaiting_verification'], 'tone' => 'amber', 'route' => Route::has('admin.businesses.index') ? route('admin.businesses.index', ['verification_status' => 'pending']) : null],
                ['label' => 'Properties to review', 'value' => $summary['properties_awaiting_review'], 'tone' => 'orange', 'route' => route('admin.properties.index', ['status' => 'pending'])],
                ['label' => 'Active disputes', 'value' => $summary['active_disputes'], 'tone' => 'red', 'route' => Route::has('admin.disputes.index') ? route('admin.disputes.index', ['status' => 'open']) : null],
                ['label' => 'Restricted users', 'value' => $summary['restricted_users'], 'tone' => 'slate', 'route' => Route::has('admin.users.index') ? route('admin.users.index', ['status' => 'suspended']) : null],
                ['label' => 'Suspended businesses', 'value' => $summary['suspended_businesses'], 'tone' => 'red', 'route' => Route::has('admin.businesses.index') ? route('admin.businesses.index', ['status' => 'suspended']) : null],
                ['label' => 'Review attention', 'value' => $summary['reviews_requiring_attention'], 'tone' => 'amber', 'route' => Route::has('admin.reviews.index') ? route('admin.reviews.index', ['moderation_status' => 'hidden']) : null],
                ['label' => 'Calendar warnings', 'value' => $summary['calendar_connections_requiring_attention'], 'tone' => 'orange', 'route' => null],
                ['label' => 'Overdue tasks', 'value' => $summary['overdue_tasks'], 'tone' => 'slate', 'route' => null],
            ];
            $tones = ['amber' => 'bg-amber-50 text-amber-700 ring-amber-100', 'orange' => 'bg-orange-50 text-orange-700 ring-orange-100', 'red' => 'bg-red-50 text-red-700 ring-red-100', 'slate' => 'bg-slate-100 text-slate-700 ring-slate-200'];
        @endphp
        @foreach($cards as $card)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-sm font-bold text-slate-500">{{ $card['label'] }}</p><p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($card['value']) }}</p></div>
                    <span class="grid size-9 place-items-center rounded-xl text-sm font-black ring-1 {{ $tones[$card['tone']] }}">{{ $card['value'] > 0 ? '!' : '✓' }}</span>
                </div>
                @if($card['route'])<a href="{{ $card['route'] }}" class="mt-4 inline-flex text-xs font-black text-orange-600 hover:text-orange-700">Open queue →</a>@else<p class="mt-4 text-xs font-semibold text-slate-400">Operational signal</p>@endif
            </article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6"><div><h2 class="font-black">Recent sensitive actions</h2><p class="mt-1 text-xs text-slate-500">Immutable activity from the platform workspace.</p></div>@if(Route::has('admin.audit.index'))<a href="{{ route('admin.audit.index') }}" class="text-xs font-black text-orange-600">View audit history</a>@endif</div>
        <div class="divide-y divide-slate-100">
            @forelse($summary['recent_audits'] as $event)
                <div class="grid gap-2 px-5 py-4 sm:grid-cols-[1fr_auto] sm:px-6"><div><p class="text-sm font-bold text-slate-900">{{ $event->description }}</p><p class="mt-1 text-xs text-slate-500">{{ $event->actor?->name ?? 'System' }} · {{ str($event->event_type)->replace('.', ' ')->title() }}</p></div><time class="text-xs font-semibold text-slate-400">{{ $event->occurred_at?->diffForHumans() }}</time></div>
            @empty
                <div class="px-6 py-12 text-center"><p class="font-bold text-slate-700">No platform actions yet</p><p class="mt-1 text-sm text-slate-500">Verification and support decisions will appear here automatically.</p></div>
            @endforelse
        </div>
    </section>
</div>
@endsection
