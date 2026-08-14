<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 2 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ showNewLocation: false }">
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
                    <span class="text-sm text-gray-600">Step 2 of 9</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Save & exit
                    </button>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="h-1 bg-gray-200">
                <div class="h-1 bg-[#FF5A00]" style="width: 22.22%"></div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-12">
            <div class="w-full max-w-3xl mx-auto">
                {{-- Title Section --}}
                <div class="mb-8">
                    <p class="text-[#FF5A00] text-sm font-bold uppercase tracking-wider mb-2">LOCATION</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">Where is it ?</h1>
                    <p class="text-gray-600 text-base">
                        Guests find your place by state and area, so let's get that locked in first.
                    </p>
                </div>

                {{-- Company/Location Dropdown --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <label class="block text-sm font-bold text-gray-900 mb-3">Company/Location (business)</label>
                    <div class="relative">
                        <button 
                            @click="showNewLocation = !showNewLocation"
                            class="w-full px-4 py-3 text-left bg-white border border-gray-300 rounded-lg hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors flex items-center justify-between"
                        >
                            <span class="text-gray-900">+ Create new location...</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- New Location Form --}}
                    <div x-show="showNewLocation" x-cloak class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New location name</label>
                                <input 
                                    type="text" 
                                    placeholder="e.g Lekki"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Assign a manager</label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors appearance-none bg-white">
                                    <option>Unassigned</option>
                                    <option>Manager 1</option>
                                    <option>Manager 2</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map Section --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4">Find it on the map</h3>
                    
                    {{-- Search Input --}}
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            placeholder="Start typing a street, estate or landmark..."
                            class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors"
                        />
                        <button class="absolute inset-y-0 right-0 pr-4 flex items-center">
                            <svg class="w-5 h-5 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Map Placeholder --}}
                    <div class="relative w-full h-64 bg-gray-300 rounded-lg overflow-hidden">
                        <div class="absolute inset-0 flex items-center justify-center">
                            {{-- Map Pin --}}
                            <div class="relative">
                                <svg class="w-12 h-12 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </div>
                        </div>
                        {{-- Overlay Text --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="bg-black/60 text-white px-6 py-3 rounded-lg text-center max-w-sm">
                                <p class="text-sm font-medium">Search an address above (or use your location)</p>
                                <p class="text-xs mt-1">and we'll drop a pin here</p>
                            </div>
                        </div>
                        {{-- Watermark --}}
                        <div class="absolute bottom-2 right-2 text-xs text-gray-600">
                            Google Maps
                        </div>
                    </div>
                </div>

                {{-- Property Address Section --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <h3 class="text-base font-bold text-gray-900 mb-4">Property address</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors appearance-none bg-white text-gray-500">
                                <option>Select state</option>
                                <option>Lagos</option>
                                <option>Abuja</option>
                                <option>Rivers</option>
                            </select>
                        </div>
                        <div>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors appearance-none bg-white text-gray-500">
                                <option>Select state first</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <input 
                            type="text" 
                            placeholder="Street / estate name (optional)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors"
                        />
                    </div>

                    {{-- Info Notice --}}
                    <div class="mt-4 flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <p class="text-sm text-gray-700">
                            Guests see this for context, but marketplace search and filtering rely on the State and Area selected above — not free-typed text — so listings stay findable regardless of spelling.
                        </p>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="/property/add/step1" class="text-gray-600 font-medium text-base hover:text-gray-900 transition-colors underline">
                        Back
                    </a>
                    <a href="/property/add/step3" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-3 px-10 rounded-lg text-base transition-colors shadow-lg inline-block">
                        Next Step
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
