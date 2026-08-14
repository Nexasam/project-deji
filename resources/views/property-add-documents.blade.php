<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - Add new property (Documents & Assets)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECECEC] font-sans antialiased">
<div class="min-h-screen flex">

    @include('partials.sidebar-nav', ['active' => 'properties'])

    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 h-14 flex items-center px-6">
            <div class="flex items-center justify-between w-full">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" placeholder="Search your listings, bookings..."
                        class="w-72 pl-10 pr-4 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"/>
                </div>
                <div class="flex items-center gap-3">
                    <button class="relative w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-0.5 right-0.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <button class="flex items-center gap-2 hover:bg-gray-50 rounded-lg px-2 py-1">
                        <div class="w-7 h-7 bg-[#FF5A00] rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-semibold">SM</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-auto bg-[#ECECEC] p-6">

            {{-- Page heading --}}
            <div class="mb-5">
                <h1 class="text-xl font-bold text-gray-900 mb-1">Add a property</h1>
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <a href="/dashboard" class="hover:text-[#FF5A00]">Sonic Movers</a>
                    <span>›</span>
                    <a href="/properties" class="hover:text-[#FF5A00]">Properties</a>
                    <span>›</span>
                    <span class="text-gray-900">Add property</span>
                </div>
            </div>

            {{-- Form card --}}
            <div class="bg-white rounded-xl border border-gray-200">

                {{-- Tabs --}}
                <div class="flex items-center border-b border-gray-200 px-6">
                    <a href="/property/add/new"         class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Basics</a>
                    <a href="/property/add/amenities"   class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Amenities</a>
                    <a href="/property/add/documents"   class="py-4 mr-8 border-b-2 border-[#FF5A00] text-[#FF5A00] text-sm font-semibold">Documents &amp; Assets</a>
                    <a href="/property/add/marketplace" class="py-4 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Marketplace</a>
                </div>

                <div class="p-6">

                    {{-- Property documents --}}
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">Property documents</label>
                        <div class="border-2 border-dashed border-[#FF5A00] rounded-xl p-14 text-center bg-[#F5F8FA] cursor-pointer hover:bg-orange-50/40 transition-colors">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                </svg>
                                <p class="text-sm text-gray-800 mt-1">
                                    <span class="font-bold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">
                                    Lease agreements, insurance, inspection reports — PDF, DOC or image, up to 10 files.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Nav --}}
                    <div class="flex items-center justify-between">
                        <a href="/property/add/amenities"
                            class="px-8 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                            Back
                        </a>
                        <a href="/property/add/marketplace"
                            class="px-10 py-2.5 bg-[#FF5A00] hover:bg-[#E64F00] text-white text-sm font-semibold rounded-lg transition-colors">
                            Next
                        </a>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
