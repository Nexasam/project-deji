@extends('layouts.admin', ['title' => 'Maintenance', 'pageTitle' => 'Maintenance'])

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    @if(session('maintenance_error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
            <p class="font-black">Command needs attention</p>
            <pre class="mt-2 max-h-56 overflow-auto whitespace-pre-wrap rounded-xl bg-white p-3 text-xs text-red-900">{{ session('maintenance_error') }}</pre>
        </div>
    @endif

    <section class="rounded-3xl bg-slate-950 p-6 text-white shadow-xl sm:p-8">
        <p class="text-xs font-black uppercase tracking-[.18em] text-orange-400">Platform operations</p>
        <h2 class="mt-2 text-2xl font-black">Maintenance console</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">Run fixed operational actions without SSH. Every action requires a reason and is written to platform audit history.</p>
    </section>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(360px,.65fr)]">
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.16em] text-orange-600">Logs</p>
                    <h3 class="mt-1 text-xl font-black">View application logs</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ $logPath }}</p>
                </div>
                <form method="GET" action="{{ route('admin.maintenance.index') }}">
                    <select name="log" onchange="this.form.submit()" class="rounded-xl border-slate-300 text-sm font-bold">
                        @foreach($logs as $key => $path)
                            <option value="{{ $key }}" @selected($selectedLog === $key)>{{ str($key)->title() }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <pre class="max-h-[34rem] overflow-auto bg-slate-950 p-5 text-xs leading-6 text-slate-100">{{ $logContent }}</pre>
            <form method="POST" action="{{ route('admin.maintenance.logs.clear') }}" class="grid gap-3 border-t border-slate-100 p-5 sm:grid-cols-[1fr_auto]">@csrf
                <input type="hidden" name="log" value="{{ $selectedLog }}">
                <label class="text-sm font-black text-slate-800">Reason for clearing this log
                    <textarea name="reason" required minlength="10" maxlength="1000" rows="2" class="mt-2 block w-full rounded-xl border-slate-300 text-sm" placeholder="e.g. Cleared after exporting logs for review.">{{ old('reason') }}</textarea>
                </label>
                <button class="self-end rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-black text-red-700 hover:bg-red-100">Clear selected log</button>
            </form>
        </section>

        <aside class="space-y-5">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[.16em] text-orange-600">Deploy</p>
                <h3 class="mt-1 text-xl font-black">Pull latest dev branch</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Runs exactly: <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">git pull origin dev</code></p>
                <form method="POST" action="{{ route('admin.maintenance.git-pull') }}" class="mt-4 space-y-3">@csrf
                    <textarea name="reason" required minlength="10" maxlength="1000" rows="3" class="block w-full rounded-xl border-slate-300 text-sm" placeholder="Why are you pulling the latest dev branch?">{{ old('reason') }}</textarea>
                    <button class="w-full rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white hover:bg-orange-600">Run git pull origin dev</button>
                </form>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-[.16em] text-orange-600">Cache</p>
                <h3 class="mt-1 text-xl font-black">Clear Laravel optimization cache</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Runs <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">php artisan optimize:clear</code>.</p>
                <form method="POST" action="{{ route('admin.maintenance.optimize-clear') }}" class="mt-4 space-y-3">@csrf
                    <textarea name="reason" required minlength="10" maxlength="1000" rows="3" class="block w-full rounded-xl border-slate-300 text-sm" placeholder="Why are caches being cleared?">{{ old('reason') }}</textarea>
                    <button class="w-full rounded-xl bg-orange-600 px-5 py-3 text-sm font-black text-white hover:bg-orange-700">Run optimize:clear</button>
                </form>
            </section>
        </aside>
    </div>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div>
            <p class="text-xs font-black uppercase tracking-[.16em] text-orange-600">Scheduled jobs</p>
            <h3 class="mt-1 text-xl font-black">Run scheduler commands now</h3>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Use this when you need to sync calendars or send operational reminders immediately instead of waiting for the next cron run.</p>
        </div>
        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($commands as $command => $label)
                <form method="POST" action="{{ route('admin.maintenance.commands.run') }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4">@csrf
                    <input type="hidden" name="command" value="{{ $command }}">
                    <p class="font-black text-slate-950">{{ $label }}</p>
                    <p class="mt-1 font-mono text-xs text-slate-500">{{ $command }}</p>
                    <textarea name="reason" required minlength="10" maxlength="1000" rows="3" class="mt-4 block w-full rounded-xl border-slate-300 bg-white text-sm" placeholder="Reason for manual run">{{ old('reason') }}</textarea>
                    <button class="mt-3 w-full rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white hover:bg-orange-600">Run now</button>
                </form>
            @endforeach
        </div>
    </section>
</div>
@endsection
