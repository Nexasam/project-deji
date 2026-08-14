<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 4 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ amenities: ['wifi', 'power-backup', 'smart-tv', 'swimming-pool', '24-7-security', 'free-parking'] }">
    <div class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-8 w-auto" />
                    <span class="text-gray-900 text-lg font-semibold">Verified Shortlet</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">Step 4 of 9</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Save & exit</button>
                </div>
            </div>
            <div class="h-1 bg-gray-200"><div class="h-1 bg-[#FF5A00]" style="width: 44.44%"></div></div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-12">
            <div class="w-full max-w-5xl mx-auto">
                <div class="mb-8">
                    <p class="text-[#FF5A00] text-sm font-bold uppercase tracking-wider mb-2">MAKE IT STAND OUT</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">What does the place offer?</h1>
                    <p class="text-gray-600 text-base">Select everything that applies — these become filters guests use to search, so accuracy here helps you get found.</p>
                </div>

                {{-- Amenities Selection --}}
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                    {{-- ESSENTIALS --}}
                    <div class="mb-8">
                        <h2 class="text-base font-bold text-gray-900 mb-4">ESSENTIALS</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('wifi') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('wifi') ? amenities = amenities.filter(a => a !== 'wifi') : amenities.push('wifi')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('wifi') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('wifi')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">WiFi</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('ac') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('ac') ? amenities = amenities.filter(a => a !== 'ac') : amenities.push('ac')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('ac') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('ac')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Air conditioning</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('kitchen') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('kitchen') ? amenities = amenities.filter(a => a !== 'kitchen') : amenities.push('kitchen')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('kitchen') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('kitchen')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Kitchen</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('washing-machine') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('washing-machine') ? amenities = amenities.filter(a => a !== 'washing-machine') : amenities.push('washing-machine')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('washing-machine') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('washing-machine')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Washing Machine</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('water-heater') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('water-heater') ? amenities = amenities.filter(a => a !== 'water-heater') : amenities.push('water-heater')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('water-heater') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('water-heater')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Water heater</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('power-backup') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('power-backup') ? amenities = amenities.filter(a => a !== 'power-backup') : amenities.push('power-backup')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('power-backup') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('power-backup')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Power backup / Generator</span>
                            </label>
                        </div>
                    </div>

                    {{-- ENTERTAINMENT --}}
                    <div class="mb-8">
                        <h2 class="text-base font-bold text-gray-900 mb-4">ENTERTAINMENT</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('smart-tv') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('smart-tv') ? amenities = amenities.filter(a => a !== 'smart-tv') : amenities.push('smart-tv')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('smart-tv') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('smart-tv')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Smart TV</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('gaming') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('gaming') ? amenities = amenities.filter(a => a !== 'gaming') : amenities.push('gaming')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('gaming') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('gaming')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">PS5 / Gaming console</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('sound-system') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('sound-system') ? amenities = amenities.filter(a => a !== 'sound-system') : amenities.push('sound-system')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('sound-system') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('sound-system')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Sound system</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('netflix') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('netflix') ? amenities = amenities.filter(a => a !== 'netflix') : amenities.push('netflix')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('netflix') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('netflix')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Netflix / Streaming</span>
                            </label>
                        </div>
                    </div>

                    {{-- LEISURE --}}
                    <div class="mb-8">
                        <h2 class="text-base font-bold text-gray-900 mb-4">LEISURE</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('swimming-pool') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('swimming-pool') ? amenities = amenities.filter(a => a !== 'swimming-pool') : amenities.push('swimming-pool')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('swimming-pool') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('swimming-pool')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Swimming pool</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('gym') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('gym') ? amenities = amenities.filter(a => a !== 'gym') : amenities.push('gym')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('gym') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('gym')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Gym</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('bbq') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('bbq') ? amenities = amenities.filter(a => a !== 'bbq') : amenities.push('bbq')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('bbq') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('bbq')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">BBQ/ Garden</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('balcony') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('balcony') ? amenities = amenities.filter(a => a !== 'balcony') : amenities.push('balcony')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('balcony') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('balcony')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Balcony / Terrace</span>
                            </label>
                        </div>
                    </div>

                    {{-- SAFETY & PARKING --}}
                    <div>
                        <h2 class="text-base font-bold text-gray-900 mb-4">SAFETY & PARKING</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('24-7-security') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('24-7-security') ? amenities = amenities.filter(a => a !== '24-7-security') : amenities.push('24-7-security')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('24-7-security') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('24-7-security')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">24 /7 Security</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('cctv') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('cctv') ? amenities = amenities.filter(a => a !== 'cctv') : amenities.push('cctv')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('cctv') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('cctv')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">CCTV</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('smoke-detector') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('smoke-detector') ? amenities = amenities.filter(a => a !== 'smoke-detector') : amenities.push('smoke-detector')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('smoke-detector') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('smoke-detector')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Smoke Detector</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all" :class="amenities.includes('free-parking') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="checkbox" class="sr-only" @change="amenities.includes('free-parking') ? amenities = amenities.filter(a => a !== 'free-parking') : amenities.push('free-parking')" />
                                <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0" :class="amenities.includes('free-parking') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                    <svg x-show="amenities.includes('free-parking')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium text-gray-900">Free Parking</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="flex items-center justify-between">
                    <a href="/property/add/step3" class="text-gray-600 font-medium text-base hover:text-gray-900 transition-colors underline">Back</a>
                    <button class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-3 px-10 rounded-lg text-base transition-colors shadow-lg">Next Step</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
