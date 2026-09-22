<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Team & access – {{ $business->name }}</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
@php
    $permissions = app(\App\Services\Access\BusinessPermissionService::class);
    $canChangeRoles = $permissions->allows(auth()->user(), $activeBusinessContext, 'employee.change_role');
    $canRemove = $permissions->allows(auth()->user(), $activeBusinessContext, 'employee.remove');
    $viewerIsOwner = auth()->user()->hasActiveBusinessRole('business_owner', $activeBusinessContext->membership->id);
@endphp
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased" x-data="{sidebarOpen:false,addOpen:false,createRoleOpen:false}">
<div class="flex min-h-screen">@include('partials.sidebar-nav',['active'=>'team'])<div class="min-w-0 flex-1">
<header class="border-b border-slate-200 bg-white px-4 py-4 lg:px-8"><div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div><p class="text-xs font-bold uppercase tracking-wider text-orange-600">People and permissions</p><h1 class="mt-1 text-xl font-extrabold">Team & access</h1></div><div class="flex flex-wrap gap-2">@if($viewerIsOwner)<button type="button" @click="createRoleOpen=true" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-800 shadow-sm hover:bg-slate-50">Create role</button>@endif<button type="button" @click="addOpen=true" class="rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-extrabold text-white shadow-sm hover:bg-orange-700">Add team member</button></div></div></header>
<main class="mx-auto max-w-[1200px] px-4 py-6 lg:px-6">
    @if(session('status'))<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><p class="font-extrabold">The access change could not be saved.</p><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="grid gap-4 sm:grid-cols-3"><div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Team records</p><p class="mt-2 text-3xl font-extrabold">{{ $memberships->count() }}</p></div><div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Active access</p><p class="mt-2 text-3xl font-extrabold text-emerald-700">{{ $memberships->where('status.value','active')->count() }}</p></div><div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Available roles</p><p class="mt-2 text-3xl font-extrabold text-orange-600">{{ $roles->count() }}</p></div></section>

    <details class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <summary class="cursor-pointer list-none border-b border-slate-100 px-5 py-4 transition hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
            <div class="flex flex-col justify-between gap-3 lg:flex-row lg:items-start">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-extrabold">Role capability matrix</h2>
                        <span class="rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-extrabold text-orange-700">Collapsed</span>
                    </div>
                    <p class="mt-1 max-w-3xl text-xs leading-5 text-slate-500">Functional access is controlled at role level. Property access is controlled per team member below. Use Hidden, Read only or Read & write to decide what everyone in a role can do inside this business.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-600">Owner role is always full access</span>
                    <span class="text-sm font-black text-slate-400">⌄</span>
                </div>
            </div>
        </summary>
        <div class="divide-y divide-slate-100">
            @foreach($roles as $capabilityRole)
                @php
                    $isOwnerRole = $capabilityRole->system_key === 'business_owner';
                    $customRole = $capabilityRole->business_id !== null;
                @endphp
                <details class="group">
                    <summary class="cursor-pointer list-none p-5 transition hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
                        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-extrabold text-slate-950">{{ $capabilityRole->name }}</p>
                                    @if($isOwnerRole)<span class="rounded-full bg-orange-100 px-2.5 py-1 text-[10px] font-extrabold text-orange-700">Owner super admin</span>@endif
                                    @if($customRole)<span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-extrabold text-slate-600">Custom role</span>@endif
                                </div>
                                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $capabilityRole->description ?: 'Business role configured by the property owner.' }}</p>
                            </div>
                            <span class="text-xs font-extrabold text-orange-700 group-open:hidden">Configure access</span>
                            <span class="hidden text-xs font-extrabold text-slate-400 group-open:inline">Hide access</span>
                        </div>
                    </summary>
                    <form method="POST" action="{{ route('owner.team.roles.permissions.update', $capabilityRole) }}" class="border-t border-slate-100 bg-slate-50/50 p-5">
                        @csrf
                        @method('PATCH')
                        <div class="grid gap-3 md:grid-cols-2">
                            @foreach($permissionModules as $moduleKey => $module)
                                @php
                                    $selected = $roleAccessLevels[$capabilityRole->id][$moduleKey] ?? 'hidden';
                                @endphp
                                <fieldset class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                                    <legend class="px-1 text-xs font-extrabold text-slate-800">{{ $module['label'] }}</legend>
                                    <p class="mt-1 min-h-10 text-[11px] leading-5 text-slate-500">{{ $module['description'] }}</p>
                                    @if(($module['owner_only_note'] ?? null) && !$isOwnerRole)
                                        <p class="mt-1 text-[10px] font-bold text-amber-700">{{ $module['owner_only_note'] }}</p>
                                    @endif
                                    <div class="mt-3 grid grid-cols-3 gap-1 rounded-lg bg-slate-50 p-1 text-[11px] font-bold ring-1 ring-slate-200">
                                        @foreach(['hidden' => 'Hidden', 'read' => 'Read', 'write' => 'Read & write'] as $level => $label)
                                            <label class="flex cursor-pointer items-center justify-center rounded-md px-2 py-2 text-center transition has-[:checked]:bg-orange-600 has-[:checked]:text-white {{ $isOwnerRole ? 'cursor-not-allowed opacity-70' : '' }}">
                                                <input type="radio" name="modules[{{ $moduleKey }}]" value="{{ $level }}" @checked($isOwnerRole ? $level === 'write' : $selected === $level) @disabled($isOwnerRole || !$viewerIsOwner) class="sr-only">
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endforeach
                        </div>
                        <div class="mt-4 flex justify-end">
                            @if($isOwnerRole)
                                <span class="text-xs font-bold text-slate-400">Locked</span>
                            @elseif($viewerIsOwner)
                                <button class="rounded-xl bg-slate-950 px-5 py-2.5 text-xs font-extrabold text-white hover:bg-slate-800">Save {{ $capabilityRole->name }} permissions</button>
                            @else
                                <span class="text-xs font-bold text-slate-400">Only a business owner can change role capabilities.</span>
                            @endif
                        </div>
                    </form>
                </details>
            @endforeach
        </div>
    </details>

    <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-5 py-4"><h2 class="font-extrabold">Business access</h2><p class="mt-1 text-xs text-slate-500">Roles control actions. Property assignments narrow access when a team member is scoped to selected properties.</p></div>
        <div class="divide-y divide-slate-100">@forelse($memberships as $membership)
            @php
                $assignment = $membership->roles->first(fn($item)=>$item->status->value==='active' && !$item->revoked_at);
                $role = $assignment?->role;
                $assignedIds = $membership->employee?->propertyAssignments->where('assignment_status','active')->where('status','active')->pluck('property_id')->all() ?? [];
                $active = $membership->status->value === 'active';
            @endphp
            <article class="p-5" x-data="{editOpen:false,removeOpen:false,reactivateOpen:false}"><div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start"><div class="flex min-w-0 items-start gap-3"><div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-orange-100 text-sm font-extrabold text-orange-700">{{ str($membership->user->name)->explode(' ')->map(fn($part)=>str($part)->substr(0,1))->take(2)->join('') }}</div><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h3 class="font-extrabold">{{ $membership->user->name }}</h3><span class="rounded-full px-2.5 py-1 text-[10px] font-extrabold {{ $active?'bg-emerald-100 text-emerald-800':'bg-slate-100 text-slate-600' }}">{{ str($membership->status->value)->title() }}</span></div><p class="mt-1 text-sm text-slate-500">{{ $membership->user->email }}</p><p class="mt-2 text-xs font-bold text-orange-700">{{ $role?->name ?? 'No active role' }}@if($membership->job_title) · {{ $membership->job_title }}@endif</p><div class="mt-2 flex flex-wrap gap-1.5">@forelse($membership->employee?->propertyAssignments->where('assignment_status','active')->where('status','active') ?? collect() as $propertyAssignment)<span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">{{ $propertyAssignment->property->name }}</span>@empty<span class="text-[11px] text-slate-400">Business-wide access within role permissions</span>@endforelse</div></div></div>
                <div class="flex flex-wrap gap-2"><a href="{{ route('owner.team.show', $membership) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold hover:bg-slate-50">View access</a>@if($canRemove && $active && $membership->user_id!==auth()->id())<button type="button" @click="removeOpen=true" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-50">Deactivate</button>@elseif($canRemove && !$active)<button type="button" @click="reactivateOpen=true" class="rounded-lg border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50">Reactivate</button>@endif</div></div>
                @if($canChangeRoles && $active)<form x-show="editOpen" x-cloak method="POST" action="{{ route('owner.team.update',$membership) }}" class="mt-5 rounded-xl border border-orange-200 bg-orange-50/40 p-4">@csrf @method('PATCH')<div class="grid gap-3 md:grid-cols-2"><label class="text-xs font-bold">Job title<input name="job_title" value="{{ $membership->job_title }}" maxlength="120" class="mt-1 w-full rounded-lg border-orange-200 bg-white text-sm"></label><label class="text-xs font-bold">Role<select name="role_id" required class="mt-1 w-full rounded-lg border-orange-200 bg-white text-sm">@foreach($roles as $option)@if($viewerIsOwner || $option->system_key!=='business_owner')<option value="{{ $option->id }}" @selected($role?->id===$option->id)>{{ $option->name }}</option>@endif @endforeach</select></label></div><fieldset class="mt-4"><legend class="text-xs font-bold">Property scope <span class="font-normal text-slate-400">(leave empty for business-wide access)</span></legend><div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">@foreach($properties as $property)<label class="flex items-center gap-2 rounded-lg border border-orange-100 bg-white p-2 text-xs font-semibold"><input type="checkbox" name="property_ids[]" value="{{ $property->id }}" @checked(in_array($property->id,$assignedIds,true)) class="rounded border-orange-300 text-orange-600">{{ $property->name }}</label>@endforeach</div></fieldset><div class="mt-4 flex justify-end gap-2"><button type="button" @click="editOpen=false" class="rounded-lg border px-4 py-2 text-xs font-bold">Cancel</button><button class="rounded-lg bg-orange-600 px-4 py-2 text-xs font-extrabold text-white">Save access</button></div></form>@endif
                @if($canRemove && $membership->user_id!==auth()->id())<div x-show="removeOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4"><button type="button" @click="removeOpen=false" class="absolute inset-0 bg-slate-950/60" aria-label="Close"></button><form method="POST" action="{{ route('owner.team.deactivate',$membership) }}" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">@csrf @method('DELETE')<div class="flex size-11 items-center justify-center rounded-full bg-red-100 font-extrabold text-red-700">!</div><h2 class="mt-4 text-xl font-extrabold">Deactivate {{ $membership->user->name }}?</h2><p class="mt-2 text-sm leading-6 text-slate-600">Their business context, active roles and staff access will be disabled. Historical task records remain intact.</p><div class="mt-5 flex justify-end gap-2"><button type="button" @click="removeOpen=false" class="rounded-lg border px-4 py-2 text-sm font-bold">Keep access</button><button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white">Deactivate access</button></div></form></div>@endif
                @if($canRemove && !$active)<div x-show="reactivateOpen" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4"><button type="button" @click="reactivateOpen=false" class="absolute inset-0 bg-slate-950/60" aria-label="Close"></button><form method="POST" action="{{ route('owner.team.reactivate',$membership) }}" class="relative max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">@csrf @method('PATCH')<div class="flex size-11 items-center justify-center rounded-full bg-emerald-100 font-extrabold text-emerald-700">↻</div><h2 class="mt-4 text-xl font-extrabold">Reactivate {{ $membership->user->name }}?</h2><p class="mt-2 text-sm leading-6 text-slate-600">Choose the role and property scope to restore. This avoids silently restoring access that may no longer be appropriate.</p><div class="mt-5 grid gap-3 md:grid-cols-2"><label class="text-xs font-bold">Job title<input name="job_title" value="{{ $membership->job_title }}" maxlength="120" class="mt-1 w-full rounded-lg border-slate-200 bg-white text-sm"></label><label class="text-xs font-bold">Role<select name="role_id" required class="mt-1 w-full rounded-lg border-slate-200 bg-white text-sm"><option value="">Choose role</option>@foreach($roles as $option)@if($viewerIsOwner || $option->system_key!=='business_owner')<option value="{{ $option->id }}">{{ $option->name }}</option>@endif @endforeach</select></label></div><fieldset class="mt-4"><legend class="text-xs font-bold">Property scope <span class="font-normal text-slate-400">(leave empty for business-wide access)</span></legend><div class="mt-2 grid gap-2 sm:grid-cols-2">@foreach($properties as $property)<label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white p-2 text-xs font-semibold"><input type="checkbox" name="property_ids[]" value="{{ $property->id }}" class="rounded border-emerald-300 text-emerald-600">{{ $property->name }}</label>@endforeach</div></fieldset><div class="mt-5 flex justify-end gap-2"><button type="button" @click="reactivateOpen=false" class="rounded-lg border px-4 py-2 text-sm font-bold">Cancel</button><button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white">Reactivate access</button></div></form></div>@endif
            </article>
        @empty<div class="p-12 text-center text-sm text-slate-500">No business memberships found.</div>@endforelse</div>
    </section>
</main></div></div>

@if($viewerIsOwner)
<div x-show="createRoleOpen" x-cloak @keydown.escape.window="createRoleOpen=false" class="fixed inset-0 z-[90] flex items-center justify-center p-4">
    <button type="button" @click="createRoleOpen=false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" aria-label="Close"></button>
    <form method="POST" action="{{ route('owner.team.roles.store') }}" class="relative w-full max-w-lg overflow-hidden rounded-[2rem] bg-white shadow-2xl ring-1 ring-slate-200">
        @csrf
        <div class="bg-gradient-to-br from-slate-950 to-slate-800 px-6 py-6 text-white">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-orange-300">Custom role</p>
                    <h2 class="mt-2 text-2xl font-extrabold">Create a business role</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-300">Create roles like Night Supervisor, Inventory Officer or Senior Cleaner, then configure what that role can see and edit.</p>
                </div>
                <button type="button" @click="createRoleOpen=false" class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white/10 text-xl text-white hover:bg-white/20">×</button>
            </div>
        </div>
        <div class="space-y-5 p-6">
            <label class="block text-xs font-extrabold uppercase tracking-wide text-slate-500">
                Role name
                <input name="name" required maxlength="120" value="{{ old('name') }}" placeholder="e.g. Night Supervisor" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-950 shadow-inner outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">
            </label>
            <label class="block text-xs font-extrabold uppercase tracking-wide text-slate-500">
                Description <span class="font-semibold normal-case tracking-normal text-slate-400">(optional)</span>
                <textarea name="description" rows="4" maxlength="500" placeholder="Describe what this role is responsible for." class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-950 shadow-inner outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">{{ old('description') }}</textarea>
            </label>
            <div class="rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-xs font-semibold leading-5 text-orange-800">After creating the role, expand it in the capability matrix and choose Hidden, Read only or Read & write for each module.</div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="createRoleOpen=false" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold hover:bg-slate-50">Cancel</button>
                <button class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-950/20 hover:bg-slate-800">Create role</button>
            </div>
        </div>
    </form>
</div>
@endif

<div x-show="addOpen" x-cloak @keydown.escape.window="addOpen=false" class="fixed inset-0 z-[90] flex items-center justify-center p-4">
    <button type="button" @click="addOpen=false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" aria-label="Close"></button>
    <form method="POST" action="{{ route('owner.team.store') }}" class="relative max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl ring-1 ring-slate-200">
        @csrf
        <div class="border-b border-slate-100 bg-gradient-to-br from-orange-50 via-white to-slate-50 px-6 py-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-orange-600">Team access</p>
                    <h2 class="mt-2 text-2xl font-extrabold">Add a team member</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Invite a person, assign their role, then limit them to specific properties if needed.</p>
                </div>
                <button type="button" @click="addOpen=false" class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white text-xl shadow-sm ring-1 ring-slate-200 hover:bg-slate-50">×</button>
            </div>
        </div>
        <div class="p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Full name<input name="name" required maxlength="120" value="{{ old('name') }}" placeholder="e.g. Amina Yusuf" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-950 shadow-inner outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100"></label>
                <label class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Email address<input name="email" type="email" required value="{{ old('email') }}" placeholder="name@example.com" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-950 shadow-inner outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100"></label>
                <label class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Job title<input name="job_title" maxlength="120" value="{{ old('job_title') }}" placeholder="e.g. Senior Cleaner" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-950 shadow-inner outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100"></label>
                <label class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Role<select name="role_id" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-950 shadow-inner outline-none transition focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100"><option value="">Choose role</option>@foreach($roles as $role)@if($viewerIsOwner || $role->system_key!=='business_owner')<option value="{{ $role->id }}">{{ $role->name }}</option>@endif @endforeach</select></label>
            </div>
            <fieldset class="mt-6 rounded-3xl border border-slate-200 bg-slate-50/70 p-4">
                <legend class="px-2 text-xs font-extrabold uppercase tracking-wide text-slate-500">Assign specific properties <span class="font-semibold normal-case tracking-normal text-slate-400">(optional)</span></legend>
                <p class="mt-1 text-xs leading-5 text-slate-500">Selecting properties limits property-specific access to those records. Leave empty for business-wide access within the chosen role.</p>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">@foreach($properties as $property)<label class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white p-3 text-sm font-semibold shadow-sm transition hover:border-orange-200 hover:bg-orange-50/40"><input type="checkbox" name="property_ids[]" value="{{ $property->id }}" class="rounded border-orange-300 text-orange-600 focus:ring-orange-500">{{ $property->name }}</label>@endforeach</div>
            </fieldset>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="addOpen=false" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold hover:bg-slate-50">Cancel</button>
                <button class="rounded-xl bg-orange-600 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-orange-600/20 hover:bg-orange-700">Add member & send setup</button>
            </div>
        </div>
    </form>
</div>
</body></html>
