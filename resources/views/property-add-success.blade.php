<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Property Submitted - Verified Shortlet</title>
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
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-2xl">
                {{-- Success Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                    {{-- Hourglass Icon --}}
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm10 14.5V20H8v-3.5l4-4 4 4zm-4-5l-4-4V4h8v3.5l-4 4z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Pending approval</h1>

                    {{-- Description --}}
                    <p class="text-gray-600 text-base leading-relaxed mb-8 max-w-lg mx-auto">
                        Your listing has been submitted — nice work! We're verifying the details and photos now, which usually takes less than 24 hours. Once it's approved, it'll go live on your dashboard and the Verified Shortlet marketplace.
                    </p>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="/dashboard" class="w-full sm:w-auto px-8 py-3 border-2 border-gray-900 text-gray-900 font-bold rounded-lg hover:bg-gray-900 hover:text-white transition-colors text-center">
                            Back to Properties
                        </a>
                        <a href="/property/add/step1" class="w-full sm:w-auto px-8 py-3 bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold rounded-lg transition-colors shadow-lg text-center">
                            Add another property
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
