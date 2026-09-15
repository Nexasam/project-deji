@extends('layouts.admin', ['title' => 'Platform configuration', 'pageTitle' => 'Platform configuration'])

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <section class="rounded-3xl bg-slate-950 p-6 text-white shadow-xl sm:p-8"><p class="text-xs font-black uppercase tracking-[.18em] text-orange-400">Controlled MVP settings</p><h2 class="mt-2 text-2xl font-black">Operational policy, in one auditable place.</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">Only approved definitions are editable here. Payment credentials, provider secrets and infrastructure configuration remain environment-managed.</p></section>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">@csrf @method('PATCH')
        @foreach($settings as $group => $items)
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-5 py-4 sm:px-6"><h3 class="font-black">{{ str($group)->title() }}</h3></div><div class="divide-y divide-slate-100">
                @foreach($items as $setting)
                    <div class="grid gap-4 px-5 py-5 sm:grid-cols-[1fr_16rem] sm:items-center sm:px-6"><div><label for="setting-{{ str($setting['key'])->slug() }}" class="text-sm font-black text-slate-900">{{ $setting['label'] }}</label><p class="mt-1 text-xs leading-5 text-slate-500">{{ $setting['description'] }}</p>@if($setting['updated_at'])<p class="mt-1 text-[11px] font-semibold text-slate-400">Last updated {{ $setting['updated_at']->diffForHumans() }} by {{ $setting['updated_by'] }}</p>@endif</div><div>
                        @php [$groupKey,$fieldKey]=explode('.',$setting['key'],2); $name="settings[{$groupKey}][{$fieldKey}]"; @endphp
                        @if($setting['type']==='boolean')<input type="hidden" name="{{ $name }}" value="0"><label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold"><span>{{ $setting['value'] ? 'Enabled' : 'Disabled' }}</span><input id="setting-{{ str($setting['key'])->slug() }}" type="checkbox" name="{{ $name }}" value="1" @checked($setting['value']) class="rounded border-slate-300 text-orange-600 focus:ring-orange-500"></label>
                        @elseif($setting['type']==='integer')<input id="setting-{{ str($setting['key'])->slug() }}" type="number" name="{{ $name }}" value="{{ old("settings.{$groupKey}.{$fieldKey}",$setting['value']) }}" class="block w-full rounded-xl border-slate-200 text-sm focus:border-orange-400 focus:ring-orange-200">
                        @else<input id="setting-{{ str($setting['key'])->slug() }}" type="email" name="{{ $name }}" value="{{ old("settings.{$groupKey}.{$fieldKey}",$setting['value']) }}" class="block w-full rounded-xl border-slate-200 text-sm focus:border-orange-400 focus:ring-orange-200">@endif
                    </div></div>
                @endforeach
            </div></section>
        @endforeach
        <section class="sticky bottom-4 rounded-2xl border border-orange-200 bg-orange-50 p-4 shadow-xl"><div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end"><label class="text-sm font-black text-orange-950">Change reason<textarea name="reason" required minlength="10" maxlength="1000" rows="2" class="mt-2 block w-full rounded-xl border-orange-200 bg-white text-sm" placeholder="Explain why these platform settings are changing…">{{ old('reason') }}</textarea></label><button class="rounded-xl bg-orange-600 px-6 py-3 text-sm font-black text-white">Save configuration</button></div></section>
    </form>
</div>
@endsection
