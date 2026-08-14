<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 1 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
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
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-5xl">
                {{-- Title Section --}}
                <div class="mb-12 text-center">
                    <p class="text-[#FF5A00] text-sm font-bold uppercase tracking-wider mb-3">LET'S GET YOUR PLACE LISTED</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">What are we adding today?</h1>
                    <p class="text-gray-600 text-lg">
                        Either way, we'll walk you through it one question at a time GÇö it takes about 10 minutes, and you can save your progress and finish later.
                    </p>
                </div>

                {{-- Selection Cards --}}
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Brand New Property Card --}}
                        <div class="border-2 border-[#FF5A00] rounded-2xl overflow-hidden bg-white cursor-pointer hover:shadow-lg transition-shadow">
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
                                <div class="border-2 border-dashed border-[#FF5A00] rounded-lg p-4 bg-orange-50">
                                    <p class="text-[#FF5A00] text-sm font-bold text-center">SELECTED</p>
                                </div>
                            </div>
                        </div>

                        {{-- Another Flat Card --}}
                        <div class="border-2 border-gray-200 rounded-2xl overflow-hidden bg-white cursor-pointer hover:border-gray-300 hover:shadow-lg transition-all">
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
                                <button class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                                    ADD ANOTHER FLAT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <button class="text-gray-600 font-medium text-lg hover:text-gray-900 transition-colors underline">
                        Back
                    </button>
                    <a href="/property/add/step2" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-4 px-12 rounded-xl text-lg transition-colors shadow-lg inline-block">
                        Next Step
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
