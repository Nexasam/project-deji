<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 9 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-8 w-auto" />
                    <span class="text-gray-900 text-lg font-semibold">Verified Shortlet</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">Step 9 of 9</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Save & exit</button>
                </div>
            </div>
            <div class="h-1 bg-[#FF5A00]" style="width: 100%"></div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-12">
            <div class="w-full max-w-4xl mx-auto">
                {{-- Title Section --}}
                <div class="mb-8">
                    <p class="text-[#FF5A00] text-sm font-bold uppercase tracking-wider mb-2">LAST LOOK</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">Review your listing</h1>
                    <p class="text-gray-600 text-base">
                        Everything look right? You can jump back to fix anything, or send it off for review.
                    </p>
                </div>

                {{-- Review Sections --}}
                <div class="space-y-4 mb-6">
                    {{-- Location --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-gray-900 font-bold text-base">Location</h3>
                            <button class="text-[#FF5A00] text-sm font-semibold hover:text-[#E55000] transition-colors">Edit</button>
                        </div>
                        <div class="space-y-2">
                            <p class="text-gray-900 font-semibold text-base">Dynasty · Egbeda</p>
                            <p class="text-gray-600 text-sm">Lagos, Lekki, Igbo-Elerin (Ecowas Roard Road)</p>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-900">Pin set — <span class="text-gray-600">John Wick Igbosere Street, Lekki Phase I, Ilabo</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- The basics --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-gray-900 font-bold text-base">The basics</h3>
                            <button class="text-[#FF5A00] text-sm font-semibold hover:text-[#E55000] transition-colors">Edit</button>
                        </div>
                        <div class="space-y-1">
                            <p class="text-gray-900 font-semibold text-base">Self-contained · entire</p>
                            <p class="text-gray-600 text-sm">2 Guests · 1 bed · 1 bath</p>
                        </div>
                    </div>

                    {{-- Amenities --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-gray-900 font-bold text-base">Amenities</h3>
                            <button class="text-[#FF5A00] text-sm font-semibold hover:text-[#E55000] transition-colors">Edit</button>
                        </div>
                        <p class="text-gray-600 text-sm">5 amenities selected</p>
                    </div>

                    {{-- Photos & video --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-gray-900 font-bold text-base">Photos & video</h3>
                            <button class="text-[#FF5A00] text-sm font-semibold hover:text-[#E55000] transition-colors">Edit</button>
                        </div>
                        <div class="mb-3">
                            <p class="text-gray-600 text-sm mb-3">8 photos · 1 video</p>
                            <div class="flex gap-2">
                                <img src="/api/placeholder/100/100" alt="Property photo 1" class="w-16 h-16 rounded-lg object-cover" />
                                <img src="/api/placeholder/100/100" alt="Property photo 2" class="w-16 h-16 rounded-lg object-cover" />
                                <img src="/api/placeholder/100/100" alt="Property photo 3" class="w-16 h-16 rounded-lg object-cover" />
                                <img src="/api/placeholder/100/100" alt="Property photo 4" class="w-16 h-16 rounded-lg object-cover" />
                                <img src="/api/placeholder/100/100" alt="Property photo 5" class="w-16 h-16 rounded-lg object-cover" />
                            </div>
                        </div>
                    </div>

                    {{-- Assets --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-gray-900 font-bold text-base">Assets</h3>
                            <button class="text-[#FF5A00] text-sm font-semibold hover:text-[#E55000] transition-colors">Edit</button>
                        </div>
                        <p class="text-gray-600 text-sm">Smart TV, Air conditioner, Dining set, Sofa set</p>
                    </div>

                    {{-- Documents --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-gray-900 font-bold text-base">Documents</h3>
                            <button class="text-[#FF5A00] text-sm font-semibold hover:text-[#E55000] transition-colors">Edit</button>
                        </div>
                        <p class="text-gray-600 text-sm">3 file(s) attached</p>
                    </div>

                    {{-- Name & price --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-gray-900 font-bold text-base">Name & price</h3>
                            <button class="text-[#FF5A00] text-sm font-semibold hover:text-[#E55000] transition-colors">Edit</button>
                        </div>
                        <div class="space-y-1">
                            <p class="text-gray-900 font-semibold text-base">SomeMovels Property</p>
                            <p class="text-gray-600 text-sm">₦80,000 / night</p>
                        </div>
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-gray-700 text-sm">
                        Typically submits your listing for a quick andflawless review — usually within 24 hours — before it appears on the marketplace. You can also save it as a draft and publish when you're ready.
                    </p>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="/property/add/step8" class="text-gray-600 font-medium text-base hover:text-gray-900 transition-colors underline">Back</a>
                    <button class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-3 px-10 rounded-lg text-base transition-colors shadow-lg">Review listing</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
