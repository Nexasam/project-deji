<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media – {{ $property->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased">
    <div class="flex min-h-screen">
        @include('partials.sidebar-nav', ['active' => 'properties'])
        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="flex items-center justify-between px-5 py-4 lg:px-8">
                    <div><p class="text-xs font-bold text-slate-500">{{ $property->name }}</p><h1 class="mt-1 text-lg font-extrabold">Property media</h1></div>
                    <a href="{{ route('owner.dashboard') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50">Exit setup</a>
                </div>
                <div class="border-t border-slate-100 px-5 py-3 lg:px-8"><div class="mb-2 flex justify-between text-xs"><span class="font-extrabold text-orange-600">Step 3 of 9 · Media</span><span class="text-slate-400">Next: House rules</span></div><div class="h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full w-1/3 rounded-full bg-orange-600"></div></div></div>
            </header>

            <main class="mx-auto max-w-6xl px-5 py-8 lg:px-8 lg:py-10">
                <div class="mb-7 max-w-3xl"><p class="text-xs font-extrabold uppercase tracking-[0.16em] text-orange-600">Step 3 · Media</p><h2 class="mt-2 text-3xl font-extrabold tracking-tight">Show guests the property</h2><p class="mt-2 text-sm leading-6 text-slate-600">Upload clear images and short videos. The first image becomes the primary property image.</p></div>

                @if (session('status'))<div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
                @if ($errors->any())<div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><p class="font-extrabold">Some files could not be uploaded.</p><ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

                @if ($mediaItems->isNotEmpty())
                    <section class="property-form-section mb-6">
                        <div class="border-b border-slate-100 px-6 py-5 sm:px-8"><h3 class="font-extrabold">Saved media</h3><p class="mt-1 text-sm text-slate-500">{{ $mediaItems->count() }} {{ Str::plural('item', $mediaItems->count()) }} currently attached.</p></div>
                        <div class="grid gap-4 p-6 sm:grid-cols-2 sm:p-8 lg:grid-cols-3">
                            @foreach ($mediaItems as $item)
                                <article class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                    <div class="aspect-video bg-slate-200">
                                        @if ($item->media_type->value === 'image')
                                            <img src="{{ Storage::disk($item->storage_disk)->url($item->storage_path) }}" alt="{{ $item->alt_text ?: $property->name }}" class="h-full w-full object-cover">
                                        @else
                                            <video src="{{ Storage::disk($item->storage_disk)->url($item->storage_path) }}" controls preload="metadata" class="h-full w-full object-cover"></video>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between gap-3 p-3"><div class="min-w-0"><p class="truncate text-xs font-bold text-slate-700">{{ $item->title }}</p>@if ($item->is_primary)<span class="text-[10px] font-extrabold uppercase text-orange-600">Primary image</span>@endif</div><form method="POST" action="{{ route('owner.properties.setup.media.destroy', [$property, $item]) }}">@csrf @method('DELETE')<button class="text-xs font-bold text-red-600 hover:text-red-700">Remove</button></form></div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                <form method="POST" action="{{ route('owner.properties.setup.media.store', $property) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <section class="property-form-section p-6 sm:p-8">
                        <label class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center transition hover:border-orange-400 hover:bg-orange-50/40">
                            <span class="flex size-14 items-center justify-center rounded-2xl bg-white text-orange-600 shadow-sm"><svg class="size-7" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 16V5m0 0L8 9m4-4 4 4M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <span class="mt-4 text-base font-extrabold text-slate-900">Choose images or videos</span>
                            <span class="mt-1 text-xs leading-5 text-slate-500">JPEG, PNG, WebP, MP4, MOV or WebM · up to 10 files · 50 MB each</span>
                            <input type="file" name="media[]" accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/webm" multiple class="mt-5 block max-w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-orange-600 file:px-4 file:py-2 file:font-bold file:text-white">
                        </label>
                    </section>

                    <div class="sticky bottom-4 z-30 flex flex-col-reverse justify-between gap-3 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-xl shadow-slate-200/60 backdrop-blur sm:flex-row sm:items-center"><a href="{{ route('owner.properties.setup.amenities', $property) }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-50">Back to amenities</a><button type="submit" class="property-form-submit rounded-xl bg-orange-600 px-6 py-3 text-sm font-extrabold text-white hover:bg-orange-700">Save and continue</button></div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
