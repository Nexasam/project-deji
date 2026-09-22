<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{{ $membership->user->name }} – Team access</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
@php
    $permissions = app(\App\Services\Access\BusinessPermissionService::class);
    $canChangeRoles = $permissions->allows(auth()->user(), $activeBusinessContext, 'employee.change_role');
    $canRemove = $permissions->allows(auth()->user(), $activeBusinessContext, 'employee.remove');
    $viewerIsOwner = auth()->user()->hasActiveBusinessRole('business_owner', $activeBusinessContext->membership->id);
    $assignment = $membership->roles->first(fn($item) => $item->status->value === 'active' && ! $item->revoked_at);
    $role = $assignment?->role;
    $assignedIds = $membership->employee?->propertyAssignments->where('assignment_status', 'active')->where('status', 'active')->pluck('property_id')->all() ?? [];
    $active = $membership->status->value === 'active';
    $initials = str($membership->user->name)->explode(' ')->map(fn($part) => str($part)->substr(0, 1))->take(2)->join('');
@endphp
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{sidebarOpen:false,removeOpen:false,reactivateOpen:false}">
<div class="flex min-h-screen">@include('partials.sidebar-nav',['active'=>'team'])<div class="min-w-0 flex-1">
<header class="border-b border-slate-200 bg-white px-4 py-4 lg:px-8">
    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
        <div>
            <a href="{{ route('owner.team.index') }}" class="text-xs font-bold text-slate-500 hover:text-orange-600">← Team & access</a>
            <p class="mt-3 text-xs font-bold uppercase tracking-wider text-orange-600">Dedicated team member access</p>
            <h1 class="mt-1 text-2xl font-extrabold">{{ $membership->user->name }}</h1>
        </div>
        <div class="flex gap-2">
            @if($canRemove && $active && $membership->user_id !== auth()->id())
                <button type="button" @click="removeOpen=true" class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-700 hover:bg-red-50">Deactivate access</button>
            @elseif($canRemove && ! $active)
                <button type="button" @click="reactivateOpen=true" class="rounded-xl border border-emerald-200 px-4 py-2.5 text-sm font-bold text-emerald-700 hover:bg-emerald-50">Reactivate access</button>
            @endif
        </div>
    </div>
</header>
<main class="mx-auto max-w-[1100px] px-4 py-6 lg:px-6">
    @if(session('status'))<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><p class="font-extrabold">The access change could not be saved.</p><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="grid gap-5 lg:grid-cols-[340px_1fr]">
        <aside class="space-y-5">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-orange-100 text-lg font-extrabold text-orange-700">{{ $initials }}</div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-extrabold">{{ $membership->user->name }}</h2>
                        <p class="mt-1 break-all text-sm text-slate-500">{{ $membership->user->email }}</p>
                        <span class="mt-3 inline-flex rounded-full px-3 py-1 text-[11px] font-extrabold {{ $active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ str($membership->status->value)->title() }}</span>
                    </div>
                </div>
                <dl class="mt-6 space-y-4 text-sm">
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Role</dt><dd class="mt-1 font-extrabold text-slate-900">{{ $role?->name ?? 'No active role' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Job title</dt><dd class="mt-1 font-semibold text-slate-700">{{ $membership->job_title ?: 'Not set' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Joined</dt><dd class="mt-1 font-semibold text-slate-700">{{ $membership->joined_at?->format('d M Y') ?: 'Pending' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-extrabold">Property scope</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">This controls where the team member can use their role permissions.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse($membership->employee?->propertyAssignments->where('assignment_status','active')->where('status','active') ?? collect() as $propertyAssignment)
                        <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700">{{ $propertyAssignment->property->name }}</span>
                    @empty
                        <span class="rounded-full bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-700">Business-wide within role permissions</span>
                    @endforelse
                </div>
            </div>
        </aside>

        <section class="space-y-5">
            @if($canChangeRoles && $active)
                <form method="POST" action="{{ route('owner.team.update', $membership) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PATCH')
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-orange-600">Access assignment</p>
                        <h2 class="mt-1 text-xl font-extrabold">Role and property access</h2>
                        <p class="mt-1 text-sm text-slate-500">Role controls functionality. Property scope controls which property records this user can touch.</p>
                    </div>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <label class="text-xs font-bold">Job title<input name="job_title" value="{{ $membership->job_title }}" maxlength="120" class="mt-1 w-full rounded-xl border-slate-200 bg-white text-sm"></label>
                        <label class="text-xs font-bold">Role<select name="role_id" required class="mt-1 w-full rounded-xl border-slate-200 bg-white text-sm">@foreach($roles as $option)@if($viewerIsOwner || $option->system_key !== 'business_owner')<option value="{{ $option->id }}" @selected($role?->id === $option->id)>{{ $option->name }}</option>@endif @endforeach</select></label>
                    </div>
                    <fieldset class="mt-5">
                        <legend class="text-xs font-bold">Assigned properties <span class="font-normal text-slate-400">(leave empty for business-wide access)</span></legend>
                        <div class="mt-3 grid gap-2 sm:grid-cols-2">
                            @foreach($properties as $property)
                                <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold">
                                    <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" @checked(in_array($property->id, $assignedIds, true)) class="rounded border-orange-300 text-orange-600">
                                    {{ $property->name }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <div class="mt-6 flex justify-end"><button class="rounded-xl bg-orange-600 px-5 py-3 text-sm font-extrabold text-white">Save member access</button></div>
                </form>
            @endif

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-orange-600">Functional access from role</p>
                    <h2 class="mt-1 text-xl font-extrabold">{{ $role?->name ?? 'No active role' }} capabilities</h2>
                    <p class="mt-1 text-sm text-slate-500">This is the role-level permission profile. Change it from the collapsed Role capability matrix on the Team page.</p>
                </div>
                <div class="mt-5 grid gap-3 md:grid-cols-2">
                    @foreach($permissionModules as $moduleKey => $module)
                        @php
                            $level = $role ? ($roleAccessLevels[$role->id][$moduleKey] ?? 'hidden') : 'hidden';
                        @endphp
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-extrabold">{{ $module['label'] }}</h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">{{ $module['description'] }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-3 py-1 text-[10px] font-extrabold {{ $level === 'write' ? 'bg-emerald-100 text-emerald-800' : ($level === 'read' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-600') }}">{{ $level === 'write' ? 'Read & write' : str($level)->title() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </section>
</main>
</div></div>

@if($canRemove && $membership->user_id !== auth()->id())
<div x-show="removeOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4"><button type="button" @click="removeOpen=false" class="absolute inset-0 bg-slate-950/60" aria-label="Close"></button><form method="POST" action="{{ route('owner.team.deactivate', $membership) }}" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">@csrf @method('DELETE')<div class="flex size-11 items-center justify-center rounded-full bg-red-100 font-extrabold text-red-700">!</div><h2 class="mt-4 text-xl font-extrabold">Deactivate {{ $membership->user->name }}?</h2><p class="mt-2 text-sm leading-6 text-slate-600">Their business context, active roles and staff access will be disabled. Historical task records remain intact.</p><div class="mt-5 flex justify-end gap-2"><button type="button" @click="removeOpen=false" class="rounded-lg border px-4 py-2 text-sm font-bold">Keep access</button><button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white">Deactivate access</button></div></form></div>
@endif
@if($canRemove && ! $active)
<div x-show="reactivateOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4"><button type="button" @click="reactivateOpen=false" class="absolute inset-0 bg-slate-950/60" aria-label="Close"></button><form method="POST" action="{{ route('owner.team.reactivate', $membership) }}" class="relative max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">@csrf @method('PATCH')<div class="flex size-11 items-center justify-center rounded-full bg-emerald-100 font-extrabold text-emerald-700">↻</div><h2 class="mt-4 text-xl font-extrabold">Reactivate {{ $membership->user->name }}?</h2><p class="mt-2 text-sm leading-6 text-slate-600">Choose the role and property scope to restore. This avoids silently restoring access that may no longer be appropriate.</p><div class="mt-5 grid gap-3 md:grid-cols-2"><label class="text-xs font-bold">Job title<input name="job_title" value="{{ $membership->job_title }}" maxlength="120" class="mt-1 w-full rounded-lg border-slate-200 bg-white text-sm"></label><label class="text-xs font-bold">Role<select name="role_id" required class="mt-1 w-full rounded-lg border-slate-200 bg-white text-sm"><option value="">Choose role</option>@foreach($roles as $option)@if($viewerIsOwner || $option->system_key !== 'business_owner')<option value="{{ $option->id }}">{{ $option->name }}</option>@endif @endforeach</select></label></div><fieldset class="mt-4"><legend class="text-xs font-bold">Property scope <span class="font-normal text-slate-400">(leave empty for business-wide access)</span></legend><div class="mt-2 grid gap-2 sm:grid-cols-2">@foreach($properties as $property)<label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white p-2 text-xs font-semibold"><input type="checkbox" name="property_ids[]" value="{{ $property->id }}" class="rounded border-emerald-300 text-emerald-600">{{ $property->name }}</label>@endforeach</div></fieldset><div class="mt-5 flex justify-end gap-2"><button type="button" @click="reactivateOpen=false" class="rounded-lg border px-4 py-2 text-sm font-bold">Cancel</button><button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white">Reactivate access</button></div></form></div>
@endif
</body>
</html>
