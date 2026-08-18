<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 3 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{
    type: 'apartment',
    booking: 'entire',
    guests: 2,
    bedrooms: 2,
    bathrooms: 2,
    size: ''
}">
<div class="min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <img src="/logo1.png" alt="VS Logo" class="h-7 w-auto" />
                <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500">Step 3 of 9</span>
                <a href="/owner/properties" class="px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Save &amp; exit</a>
            </div>
        </div>
        <div class="h-0.5 bg-gray-200"><div class="h-0.5 bg-[#FF5A00]" style="width:33%"></div></div>
    </header>

    <main class="flex-1 py-7 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="mb-5">
                <p class="text-[#FF5A00] text-xs font-bold uppercase tracking-widest mb-1.5">The Basics</p>
                <h1 class="text-2xl font-bold text-gray-900 mb-1.5">Tell us about the place</h1>
                <p class="text-gray-500 text-sm">A few quick facts guests use to decide if it fits them.</p>
            </div>

            {{-- Property type --}}
            <div class="mb-2">
                <p class="text-sm font-semibold text-gray-800 mb-4">What kind of place is it?</p>
                <div class="grid grid-cols-3 gap-4">
                    @foreach([
                        ['val'=>'apartment',  'label'=>'Apartment',     'icon'=>'M4 21V9l8-6 8 6v12h-5v-7H9v7z'],
                        ['val'=>'self',       'label'=>'Self-contained', 'icon'=>'M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z'],
                        ['val'=>'duplex',     'label'=>'Duplex',         'icon'=>'M3 21V9.5L12 3l9 6.5V21h-6v-5H9v5z M7 21V13h10v8'],
                        ['val'=>'bungalow',   'label'=>'Bungalow',       'icon'=>'M19 9.3V4h-3v2.6L12 3 2 12h3v8h5v-5h4v5h5v-8h3z'],
                        ['val'=>'boutique',   'label'=>'Boutique suite', 'icon'=>'M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4z'],
                        ['val'=>'villa',      'label'=>'Villa',          'icon'=>'M17 11h3c1.11 0 2-.9 2-2V5c0-1.11-.89-2-2-2h-3c-1.11 0-2 .9-2 2v1H9V5c0-1.11-.9-2-2-2H4c-1.11 0-2 .9-2 2v4c0 1.11.89 2 2 2h3c1.11 0 2-.9 2-2V8h2v7H9.01C7.35 15 6 16.35 6 18v2h12v-2c0-1.65-1.34-3-3-3H13V8h2v1c0 1.11.89 2 2 2z'],
                    ] as $t)
                    <button @click="type = '{{ $t['val'] }}'"
                        class="flex flex-col items-start p-3 bg-white border-2 rounded-xl transition-all text-left"
                        :class="type === '{{ $t['val'] }}' ? 'border-[#FF5A00]' : 'border-gray-200 hover:border-gray-300'">
                        <svg class="w-9 h-9 mb-3 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                            <path d="{{ $t['icon'] }}"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-900 mb-3">{{ $t['label'] }}</span>
                        <span x-show="type === '{{ $t['val'] }}'"
                            class="text-[10px] font-bold text-[#FF5A00] border border-[#FF5A00] rounded px-2 py-0.5 tracking-wider">
                            SELECTED
                        </span>
                        <span x-show="type !== '{{ $t['val'] }}'"
                            class="text-[10px] font-bold text-white bg-gray-900 rounded px-3 py-1 tracking-wider">
                            SELECT
                        </span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- White card: booking type + capacity --}}
            <div class="bg-white rounded-xl border border-gray-200 mt-8">

                {{-- Booking type tabs --}}
                <div class="px-6 pt-6 pb-4">
                    <p class="text-base font-bold text-gray-900 mb-4">Will guests book the entire place, or just part of it?</p>
                    <div class="flex border border-gray-200 rounded-lg overflow-hidden">
                        @foreach(['entire'=>'Entire place','private'=>'Private room','shared'=>'Shared room'] as $val => $label)
                        <button @click="booking = '{{ $val }}'"
                            class="flex-1 py-2.5 text-sm font-medium transition-colors"
                            :class="booking === '{{ $val }}' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6">

                    {{-- Section title --}}
                    <p class="text-sm font-bold text-gray-800 pt-5 pb-2">Capacity &amp; size</p>

                    {{-- Guests --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Guests</p>
                            <p class="text-xs text-gray-500 mt-0.5">Maximum number of guests allowed</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="guests = Math.max(1, guests-1)"
                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-700 hover:border-gray-500 text-lg font-light">−</button>
                            <span class="w-6 text-center text-sm font-semibold" x-text="guests"></span>
                            <button @click="guests++"
                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-700 hover:border-gray-500 text-lg font-light">+</button>
                        </div>
                    </div>

                    {{-- Bedrooms --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900">Bedrooms</p>
                        <div class="flex items-center gap-3">
                            <button @click="bedrooms = Math.max(0, bedrooms-1)"
                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-700 hover:border-gray-500 text-lg font-light">−</button>
                            <span class="w-6 text-center text-sm font-semibold" x-text="bedrooms"></span>
                            <button @click="bedrooms++"
                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-700 hover:border-gray-500 text-lg font-light">+</button>
                        </div>
                    </div>

                    {{-- Bathrooms --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Bathrooms</p>
                            <p class="text-xs text-gray-500 mt-0.5">Add .5 for a visitor's toilet with no bath</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="bathrooms = Math.max(0, bathrooms-1)"
                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-700 hover:border-gray-500 text-lg font-light">−</button>
                            <span class="w-6 text-center text-sm font-semibold" x-text="bathrooms"></span>
                            <button @click="bathrooms++"
                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-700 hover:border-gray-500 text-lg font-light">+</button>
                        </div>
                    </div>

                    {{-- Size --}}
                    <div class="flex items-center justify-between py-4">
                        <p class="text-sm font-semibold text-gray-900">Size <span class="font-normal text-gray-400">(optional)</span></p>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="size" placeholder=""
                                class="w-20 text-right px-2 py-1.5 border-b border-gray-300 text-sm focus:outline-none focus:border-[#FF5A00] bg-transparent"/>
                            <span class="text-sm text-gray-500">sqm</span>
                        </div>
                    </div>

                </div>

                {{-- Nav --}}
                <div class="flex items-center justify-between px-6 py-5 border-t border-gray-100">
                    <a href="/owner/properties/create/step2" class="text-sm text-gray-500 hover:text-gray-700 transition-colors underline">Back</a>
                    <a href="/owner/properties/create/step4"
                        class="px-8 py-2.5 bg-[#FF5A00] hover:bg-[#E64F00] text-white text-sm font-bold rounded-lg transition-colors">
                        Next Step
                    </a>
                </div>

            </div>
        </div>
    </main>
</div>
</body>
</html>

