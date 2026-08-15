<div class="sticky bottom-4 z-30 mt-6 flex flex-col-reverse justify-between gap-3 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-xl backdrop-blur sm:flex-row sm:items-center">
    <div>@if($previousRoute)<a href="{{ route($previousRoute, $property) }}" class="inline-flex rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Back</a>@endif</div>
    <div class="flex flex-col-reverse gap-3 sm:flex-row">
        @if($optional)<button type="submit" formaction="{{ route('owner.properties.setup.skip', [$property, $step]) }}" class="rounded-xl px-5 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100">Skip for now</button>@endif
        <button type="submit" class="property-form-submit rounded-xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white hover:bg-orange-700">Save and continue</button>
    </div>
</div>
