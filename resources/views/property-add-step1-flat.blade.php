<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 1 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ selectedProperty: 'bluewater' }">
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
                    <span class="text-sm text-gray-600">Step 1 of 9</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Save & exit
                    </button>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="h-1 bg-gray-200">
                <div class="h-1 bg-[#FF5A00]" style="width: 11.11%"></div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-12">
            <div class="w-full max-w-5xl mx-auto">
                {{-- Title Section --}}
                <div class="mb-8">
                    <p class="text-[#FF5A00] text-sm font-bold uppercase tracking-wider mb-3">LET'S GET YOUR PLACE LISTED</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">What are we adding today?</h1>
                    <p class="text-gray-600 text-base">
                        Either way, we'll walk you through it one question at a time — it takes about 10 minutes, and you can save your progress and finish later.
                    </p>
                </div>

                {{-- Selection Cards --}}
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        {{-- Brand New Property Card --}}
                        <div class="border-2 border-gray-200 rounded-2xl overflow-hidden bg-white cursor-pointer hover:border-gray-300 hover:shadow-lg transition-all">
                            <div class="aspect-[4/3] overflow-hidden">
                                <img 
                                    src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop" 
                                    alt="Brand new property" 
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">A brand-new property</h3>
                                <p class="text-gray-600 mb-4">Not in your portfolio yet - we'll setup everything from scratch</p>
                                <button class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                                    ADD NEW PROPERTY
                                </button>
                            </div>
                        </div>

                        {{-- Another Flat Card --}}
                        <div class="border-2 border-[#FF5A00] rounded-2xl overflow-hidden bg-white cursor-pointer hover:shadow-lg transition-shadow">
                            <div class="aspect-[4/3] overflow-hidden bg-gray-100 flex items-center justify-center">
                                <img 
                                    src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop" 
                                    alt="Another flat in a property" 
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Another flat in a property</h3>
                                <p class="text-gray-600 mb-4">e.g adding "3C" to a Bluewater building you already list</p>
                                <div class="border-2 border-dashed border-[#FF5A00] rounded-lg p-4 bg-orange-50">
                                    <p class="text-[#FF5A00] text-sm font-bold text-center">SELECTED</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Selection Section --}}
                    <div class="border-t border-gray-200 pt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Which property is this flat for?</h2>
                        
                        <div class="space-y-3">
                            {{-- Bluewater Option --}}
                            <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="selectedProperty === 'bluewater' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                         :class="selectedProperty === 'bluewater' ? 'border-[#FF5A00]' : 'border-gray-300'">
                                        <div class="w-3 h-3 rounded-full bg-[#FF5A00]" x-show="selectedProperty === 'bluewater'"></div>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900">Bluewater</span>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600">Egbeda, Lagos</span>
                                <input type="radio" name="property" value="bluewater" class="sr-only" @click="selectedProperty = 'bluewater'" checked />
                            </label>

                            {{-- Palm Court Option --}}
                            <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="selectedProperty === 'palm-court' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                         :class="selectedProperty === 'palm-court' ? 'border-[#FF5A00]' : 'border-gray-300'">
                                        <div class="w-3 h-3 rounded-full bg-[#FF5A00]" x-show="selectedProperty === 'palm-court'"></div>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900">Palm Court</span>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600">Egbeda, Lagos</span>
                                <input type="radio" name="property" value="palm-court" class="sr-only" @click="selectedProperty = 'palm-court'" />
                            </label>

                            {{-- Sunset Option --}}
                            <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="selectedProperty === 'sunset' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                         :class="selectedProperty === 'sunset' ? 'border-[#FF5A00]' : 'border-gray-300'">
                                        <div class="w-3 h-3 rounded-full bg-[#FF5A00]" x-show="selectedProperty === 'sunset'"></div>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900">Sunset</span>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600">Island, Lagos</span>
                                <input type="radio" name="property" value="sunset" class="sr-only" @click="selectedProperty = 'sunset'" />
                            </label>

                            {{-- Emerald Loft Option --}}
                            <label class="flex items-center justify-between p-4 border-2 rounded-lg cursor-pointer transition-all"
                                   :class="selectedProperty === 'emerald' ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                         :class="selectedProperty === 'emerald' ? 'border-[#FF5A00]' : 'border-gray-300'">
                                        <div class="w-3 h-3 rounded-full bg-[#FF5A00]" x-show="selectedProperty === 'emerald'"></div>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900">Emerald Loft</span>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600">Island, Lagos</span>
                                <input type="radio" name="property" value="emerald" class="sr-only" @click="selectedProperty = 'emerald'" />
                            </label>
                        </div>

                        {{-- Add New Property Link --}}
                        <div class="mt-4 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-[#FF5A00] hover:bg-orange-50 transition-all cursor-pointer">
                            <p class="text-sm text-gray-600">
                                Property not listed here? <a href="/property/add/step1" class="text-[#FF5A00] font-semibold hover:underline">Add new property</a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <button class="text-gray-600 font-medium text-base hover:text-gray-900 transition-colors underline">
                        Back
                    </button>
                    <button class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-3 px-10 rounded-lg text-base transition-colors shadow-lg">
                        Next Step
                    </button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
