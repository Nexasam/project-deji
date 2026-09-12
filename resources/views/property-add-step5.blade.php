<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 5 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{previews:[],videoUrl:'',videoDuration:'',previewFiles(event){this.previews.forEach(item=>URL.revokeObjectURL(item.url));this.previews=Array.from(event.target.files).filter(file=>file.type.startsWith('image/')).map(file=>({name:file.name,url:URL.createObjectURL(file)}))}}">
    <div class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-7 w-auto" />
                    <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">Step 5 of 10</span>
                    <a href="/owner/properties" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Save & exit</a>
                </div>
            </div>
            <div class="h-0.5 bg-gray-200"><div class="h-0.5 bg-[#FF5A00]" style="width: 55.55%"></div></div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-7">
            <form method="POST" enctype="multipart/form-data" action="{{ route('owner.properties.wizard.store', ['property'=>$property,'step'=>5]) }}" class="w-full max-w-4xl mx-auto">
                @csrf
                @if($errors->any())<div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
                {{-- Title Section --}}
                <div class="mb-5">
                    <p class="text-[#FF5A00] text-xs font-bold uppercase tracking-wider mb-1.5">SHOW IT OFF</p>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1.5">Add your photos</h1>
                    <p class="text-gray-600 text-sm text-gray-500">
                        Listings with great photos get booked faster. Add at least 5 — the first one becomes your cover image.
                    </p>
                </div>

                {{-- Photo Upload Area --}}
                <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
                    {{-- Upload Zone --}}
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 mb-4 text-center hover:border-[#FF5A00] transition-colors cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-10 h-10 text-[#FF5A00] mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                            </svg>
                            <label class="text-sm font-semibold text-[#FF5A00] hover:underline cursor-pointer">Click to upload<input type="file" name="media[]" accept="image/*,video/mp4,video/quicktime" multiple class="sr-only" @change="previewFiles($event)"></label>
                            <p class="text-sm text-gray-600">PNG or JPG, up to 10 photos. First photo becomes the cover image.</p>
                        </div>
                    </div>

                    {{-- Photo Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                        <template x-for="preview in previews" :key="preview.url"><div class="relative overflow-hidden rounded-lg border-2 border-dashed border-orange-300 bg-orange-50"><img :src="preview.url" :alt="preview.name" class="h-36 w-full object-cover"><div class="absolute inset-x-0 bottom-0 truncate bg-black/65 px-2 py-1 text-xs font-semibold text-white" x-text="preview.name"></div><span class="absolute left-2 top-2 rounded bg-white/90 px-2 py-1 text-[10px] font-bold text-orange-700">READY TO UPLOAD</span></div></template>
                        @foreach($property->media->where('media_type', 'image') as $photo)
                            <div class="relative group rounded-lg overflow-hidden border-2 transition-all {{ $photo->is_primary ? 'border-[#FF5A00]' : 'border-gray-200' }}">
                                <img src="{{ Storage::disk($photo->storage_disk)->url($photo->storage_path) }}" alt="{{ $photo->alt_text ?: 'Property photo' }}" class="w-full h-36 object-cover" />
                                <div class="absolute inset-x-0 bottom-0 truncate bg-black/65 px-2 py-1 text-xs font-semibold text-white">{{ $photo->title }}</div>
                                
                                {{-- Cover Badge --}}
                                @if($photo->is_primary)<div class="absolute top-2 left-2 bg-[#FF5A00] text-white text-xs font-bold px-2 py-1 rounded">
                                    Cover
                                </div>@endif
                                
                                {{-- Delete Button --}}
                                <button type="submit" form="delete-media-{{ $photo->id }}" aria-label="Remove photo"
                                        class="absolute bottom-2 right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Success Message --}}
                    <div class="flex items-center gap-2 text-green-600 mb-6">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">{{ $property->media->where('media_type', 'image')->count() }} photos added — nice!</span>
                    </div>

                    {{-- Video Section --}}
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-base font-bold text-gray-900 mb-2">Video <span class="text-sm font-normal text-gray-500">(optional, 1 max)</span></h3>
                        
                        {{-- Video Upload Zone --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 mb-4 text-center hover:border-[#FF5A00] transition-colors cursor-pointer">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-[#FF5A00] mb-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                                </svg>
                                <p class="text-sm font-semibold text-gray-900 mb-1">Click to upload a walkthrough video</p>
                                <p class="text-sm text-gray-600">One video only, up to 10 seconds and 100MB. Fits keeps things fast for guests on mobile data.</p>
                            </div>
                        </div>

                        {{-- Uploaded Video --}}
                        <div x-show="videoUrl" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="videoUrl"></p>
                                    <p class="text-xs text-gray-600" x-text="videoDuration"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="text-sm font-medium text-gray-700 hover:text-gray-900">39MB</button>
                                <button @click="videoUrl = ''; videoDuration = ''" class="text-gray-500 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('owner.properties.wizard.step', ['property'=>$property,'step'=>4]) }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors underline">Back</a>
                    <button type="submit" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-2.5 px-8 rounded-lg text-sm transition-colors shadow-md inline-block">Next Step</button>
                </div>
            </form>
            @foreach($property->media->where('media_type', 'image') as $photo)
                <form id="delete-media-{{ $photo->id }}" method="POST" action="{{ route('owner.properties.wizard.media.destroy', ['property'=>$property,'media'=>$photo]) }}" class="hidden">@csrf @method('DELETE')</form>
            @endforeach
        </main>
    </div>
</body>
</html>
