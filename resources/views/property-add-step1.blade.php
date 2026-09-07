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
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-7 w-auto" />
                    <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">Step 1 of 10</span>
                    <a href="/owner/properties" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Save & exit
                    </a>
                </div>
            </div>
            <div class="h-0.5 bg-gray-200">
                <div class="h-0.5 bg-[#FF5A00]" style="width: 11.11%"></div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 flex items-center justify-center px-4 py-8">
            <div class="w-full max-w-4xl">
                {{-- Title --}}
                <div class="mb-6">
                    <p class="text-[#FF5A00] text-xs font-bold uppercase tracking-wider mb-2">Let's get your place listed</p>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">What are we adding today?</h1>
                    <p class="text-gray-500 text-sm">We'll walk you through it one question at a time — takes about 10 minutes, and you can save your progress anytime.</p>
                </div>

                <form method="POST" action="{{ route('owner.properties.wizard.start') }}">
                @csrf
                <input type="hidden" name="property_kind" value="brand_new">
                {{-- Selection Cards --}}
                <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Brand New Property --}}
                        <div class="border-2 border-[#FF5A00] rounded-xl overflow-hidden bg-white cursor-pointer hover:shadow-md transition-shadow">
                            <div class="aspect-[16/9] overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=450&fit=crop"
                                     alt="Brand new property" class="w-full h-full object-cover" />
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-bold text-gray-900 mb-1">A brand-new property</h3>
                                <p class="text-gray-500 text-sm mb-3">Not in your portfolio yet — we'll set everything up from scratch.</p>
                                <div class="border border-dashed border-[#FF5A00] rounded-lg py-2 bg-orange-50">
                                    <p class="text-[#FF5A00] text-xs font-bold text-center tracking-wider">SELECTED</p>
                                </div>
                            </div>
                        </div>

                        {{-- Another Flat --}}
                        <div class="border-2 border-gray-200 rounded-xl overflow-hidden bg-white cursor-pointer hover:border-gray-300 hover:shadow-md transition-all">
                            <div class="aspect-[16/9] overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=450&fit=crop"
                                     alt="Another flat" class="w-full h-full object-cover" />
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-bold text-gray-900 mb-1">Another flat in a property</h3>
                                <p class="text-gray-500 text-sm mb-3">e.g. adding "3C" to a Bluewater building you already list.</p>
                                <button type="button" disabled class="w-full bg-gray-300 text-gray-600 text-xs font-bold py-2.5 px-4 rounded-lg cursor-not-allowed tracking-wider">
                                    COMING SOON
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="flex items-center justify-between">
                    <button class="text-sm text-gray-500 hover:text-gray-700 transition-colors underline">Back</button>
                    <button type="submit" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-2.5 px-8 rounded-lg text-sm transition-colors shadow-md inline-block">
                        Next Step
                    </button>
                </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
