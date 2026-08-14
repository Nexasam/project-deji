<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 3 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ 
    propertyType: 'apartment',
    bookingType: 'entire',
    guests: 2,
    bedrooms: 2,
    bathrooms: 2,
    size: ''
}">
    <div class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                {{-- Logo --}}
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-8 w-auto" />
                    <span class="text-gray-900 text-lg font-semibold">Verified Shortlet</span>
                </div>

                {{-- Progress --}}
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">Step 3 of 9</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Save & exit
                    </button>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="h-1 bg-gray-200">
                <div class="h-1 bg-[#FF5A00]" style="width: 33.33%"></div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-12">
            <div class="w-full max-w-3xl mx-auto">
                {{-- Title Section --}}
                <div class="mb-8">
                    <p class="text-[#FF5A00] text-sm font-bold uppercase tracking-wider mb-2">THE BASICS</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">Tell us about the place</h1>
                    <p class="text-gray-600 text-base">
                        A few quick facts guests use to decide if it fits them.
                    </p>
                </div>

                {{-- Property Type Section --}}
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">What kind of place is it?</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        {{-- Apartment --}}
                        <button @click="propertyType = 'apartment'" 
                                class="flex flex-col items-center p-6 border-2 rounded-xl transition-all"
                                :class="propertyType === 'apartment' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <svg class="w-12 h-12 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3L2 9v12h6v-6h8v6h6V9L12 3zm0 2.5L20 10v9h-2v-6H6v6H4v-9l8-4.5z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">Apartment</span>
                            <span x-show="propertyType === 'apartment'" class="text-xs text-[#FF5A00] font-bold mt-2">SELECTED</span>
                        </button>

                        {{-- Self-contained --}}
                        <button @click="propertyType = 'self-contained'" 
                                class="flex flex-col items-center p-6 border-2 rounded-xl transition-all"
                                :class="propertyType === 'self-contained' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <svg class="w-12 h-12 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">Self-contained</span>
                            <button x-show="propertyType !== 'self-contained'" class="mt-2 px-4 py-1 bg-gray-900 text-white text-xs font-semibold rounded hover:bg-gray-800 transition-colors">SELECT</button>
                        </button>

                        {{-- Duplex --}}
                        <button @click="propertyType = 'duplex'" 
                                class="flex flex-col items-center p-6 border-2 rounded-xl transition-all"
                                :class="propertyType === 'duplex' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <svg class="w-12 h-12 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 8v14h8v-8h4v8h8V8L12 2zm0 2.5L20 9v11h-4v-8H8v8H4V9l8-4.5z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">Duplex</span>
                            <button x-show="propertyType !== 'duplex'" class="mt-2 px-4 py-1 bg-gray-900 text-white text-xs font-semibold rounded hover:bg-gray-800 transition-colors">SELECT</button>
                        </button>

                        {{-- Bungalow --}}
                        <button @click="propertyType = 'bungalow'" 
                                class="flex flex-col items-center p-6 border-2 rounded-xl transition-all"
                                :class="propertyType === 'bungalow' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <svg class="w-12 h-12 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9.3V4h-3v2.6L12 3 2 12h3v8h5v-6h4v6h5v-8h3l-3-2.7zm-9 .7c0-1.1.9-2 2-2s2 .9 2 2h-4z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">Bungalow</span>
                            <button x-show="propertyType !== 'bungalow'" class="mt-2 px-4 py-1 bg-gray-900 text-white text-xs font-semibold rounded hover:bg-gray-800 transition-colors">SELECT</button>
                        </button>

                        {{-- Boutique suite --}}
                        <button @click="propertyType = 'boutique'" 
                                class="flex flex-col items-center p-6 border-2 rounded-xl transition-all"
                                :class="propertyType === 'boutique' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <svg class="w-12 h-12 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">Boutique suite</span>
                            <button x-show="propertyType !== 'boutique'" class="mt-2 px-4 py-1 bg-gray-900 text-white text-xs font-semibold rounded hover:bg-gray-800 transition-colors">SELECT</button>
                        </button>

                        {{-- Villa --}}
                        <button @click="propertyType = 'villa'" 
                                class="flex flex-col items-center p-6 border-2 rounded-xl transition-all"
                                :class="propertyType === 'villa' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <svg class="w-12 h-12 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 11h3c1.11 0 2-.9 2-2V5c0-1.11-.89-2-2-2h-3c-1.11 0-2 .9-2 2v1H9.01V5c0-1.11-.9-2-2-2H4c-1.11 0-2 .9-2 2v4c0 1.11.89 2 2 2h3c1.11 0 2-.9 2-2V8h2v7.01c0 1.65 1.34 2.99 2.99 2.99H15v2c0 1.11.89 2 2 2h2c1.11 0 2-.89 2-2v-2c0-1.11-.89-2-2-2h-2v-2h.01c1.65 0 2.99-1.34 2.99-2.99V8h-2v1c0 1.11-.89 2-2 2z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">Villa</span>
                            <button x-show="propertyType !== 'villa'" class="mt-2 px-4 py-1 bg-gray-900 text-white text-xs font-semibold rounded hover:bg-gray-800 transition-colors">SELECT</button>
                        </button>
                    </div>
                </div>

                {{-- Booking Type Section --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Will guests book the entire place, or just part of it?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button @click="bookingType = 'entire'" 
                                class="p-4 border-2 rounded-lg transition-all text-center"
                                :class="bookingType === 'entire' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <span class="font-semibold text-gray-900">Entire place</span>
                        </button>
                        <button @click="bookingType = 'private'" 
                                class="p-4 border-2 rounded-lg transition-all text-center"
                                :class="bookingType === 'private' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <span class="font-semibold text-gray-900">Private room</span>
                        </button>
                        <button @click="bookingType = 'shared'" 
                                class="p-4 border-2 rounded-lg transition-all text-center"
                                :class="bookingType === 'shared' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300 bg-white'">
                            <span class="font-semibold text-gray-900">Shared room</span>
                        </button>
                    </div>
                </div>

                {{-- Capacity & Size Section --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Capacity & size</h2>
                    
                    {{-- Guests --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-200">
                        <div>
                            <div class="font-semibold text-gray-900">Guests</div>
                            <div class="text-sm text-gray-600">Maximum number of guests allowed</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="guests = Math.max(1, guests - 1)" 
                                    class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full hover:border-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span class="text-lg font-semibold text-gray-900 w-8 text-center" x-text="guests"></span>
                            <button @click="guests++" 
                                    class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full hover:border-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Bedrooms --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-200">
                        <div>
                            <div class="font-semibold text-gray-900">Bedrooms</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="bedrooms = Math.max(0, bedrooms - 1)" 
                                    class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full hover:border-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span class="text-lg font-semibold text-gray-900 w-8 text-center" x-text="bedrooms"></span>
                            <button @click="bedrooms++" 
                                    class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full hover:border-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Bathrooms --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-200">
                        <div>
                            <div class="font-semibold text-gray-900">Bathrooms</div>
                            <div class="text-sm text-gray-600">Add .5 for a visitor's toilet with no bath</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="bathrooms = Math.max(0, bathrooms - 1)" 
                                    class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full hover:border-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span class="text-lg font-semibold text-gray-900 w-8 text-center" x-text="bathrooms"></span>
                            <button @click="bathrooms++" 
                                    class="w-10 h-10 flex items-center justify-center border-2 border-gray-300 rounded-full hover:border-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Size --}}
                    <div class="flex items-center justify-between py-4">
                        <div>
                            <div class="font-semibold text-gray-900">Size <span class="text-sm text-gray-500 font-normal">(optional)</span></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input 
                                type="number" 
                                x-model="size"
                                placeholder=""
                                class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors text-center"
                            />
                            <span class="text-sm text-gray-600">sqm</span>
                        </div>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="/property/add/step2" class="text-gray-600 font-medium text-base hover:text-gray-900 transition-colors underline">
                        Back
                    </a>
                    <button class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-3 px-10 rounded-lg text-base transition-colors shadow-lg">
                        Next Step
                    </button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
