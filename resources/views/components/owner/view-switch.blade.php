@props(['routeName', 'mode' => 'real'])

<div {{ $attributes->class(['inline-flex items-center rounded-xl border border-slate-200 bg-white p-1 shadow-sm']) }} aria-label="Presentation data mode">
    <a href="{{ route($routeName) }}"
       class="rounded-lg px-3 py-2 text-xs font-bold transition {{ $mode === 'real' ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
        Real view
    </a>
    <a href="{{ route($routeName, ['view' => 'demo']) }}"
       class="rounded-lg px-3 py-2 text-xs font-bold transition {{ $mode === 'demo' ? 'bg-orange-600 text-white' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700' }}">
        Demo view
    </a>
</div>
