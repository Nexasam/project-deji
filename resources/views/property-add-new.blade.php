<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - Add new property (Basics)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECECEC] font-sans antialiased" x-data="{ showNewLocation: true }">
<div class="min-h-screen flex">

    {{-- Sidebar --}}
    @include('partials.sidebar-nav', ['active' => 'properties'])

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top Header --}}
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

        {{-- Page content --}}
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

                {{-- Step tabs --}}
                <div class="flex items-center border-b border-gray-200 px-6">
                    <a href="/property/add/new"         class="py-4 mr-8 border-b-2 border-[#FF5A00] text-[#FF5A00] text-sm font-semibold">Basics</a>
                    <a href="/property/add/amenities"   class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Amenities</a>
                    <a href="/property/add/documents"   class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Documents &amp; Assets</a>
                    <a href="/property/add/marketplace" class="py-4 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Marketplace</a>
                </div>

                <div class="p-6 space-y-6">

                    {{-- Property photos --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-3">Property photos</label>
                        <div class="border-2 border-dashed border-[#FF5A00] rounded-xl p-10 text-center bg-orange-50/30 cursor-pointer hover:bg-orange-50 transition-colors">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                </svg>
                                <p class="text-sm text-gray-700">
                                    <span class="font-bold text-gray-900">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PNG or JPG, up to 10 photos. First photo becomes the cover image.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Company / Location --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Company/Location (business)</label>
                        <div class="relative mb-3">
                            <select class="w-full px-4 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm text-gray-700 appearance-none focus:outline-none focus:ring-1 focus:ring-[#FF5A00] cursor-pointer">
                                <option>+ Create new location...</option>
                                <option>Egbeda</option>
                                <option>Island</option>
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        {{-- New location fields --}}
                        <div class="bg-[#F0F4F8] rounded-lg p-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">New location name</label>
                                <input type="text" placeholder="e.g Lekki"
                                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] placeholder:text-gray-400"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Assign a manager</label>
                                <div class="relative">
                                    <select class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-500 appearance-none focus:outline-none focus:ring-1 focus:ring-[#FF5A00] cursor-pointer">
                                        <option>Unassigned</option>
                                        <option>Tarifa Isaacs</option>
                                        <option>Halima Youlf</option>
                                    </select>
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property name --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Property name</label>
                        <input type="text" placeholder="e.g Bluewater Suite"
                            class="w-full px-4 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] placeholder:text-gray-400"/>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Description</label>
                        <textarea rows="3" placeholder="Describe the property for guests — layout, what makes it a great stay, nearby landmarks..."
                            class="w-full px-4 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] placeholder:text-gray-400 resize-none"></textarea>
                    </div>

                    {{-- Property address --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Property address</label>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="relative">
                                <select class="w-full px-4 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm text-gray-500 appearance-none focus:outline-none focus:ring-1 focus:ring-[#FF5A00] cursor-pointer">
                                    <option value="">Select state</option>
                                    <option>Lagos</option>
                                    <option>Abuja</option>
                                    <option>Rivers</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            <div class="relative">
                                <select class="w-full px-4 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm text-gray-500 appearance-none focus:outline-none focus:ring-1 focus:ring-[#FF5A00] cursor-pointer">
                                    <option value="">Select state first</option>
                                    <option>Lekki</option>
                                    <option>Ikeja</option>
                                    <option>Victoria Island</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <input type="text" placeholder="Street / estate name (optional)"
                            class="w-full px-4 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] placeholder:text-gray-400 mb-2"/>
                        <p class="text-xs text-gray-500 flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Guests use this for context, but marketplace search and filtering rely on the State and Zone selected above — not free-typed text — so listings stay findable regardless of spelling.
                        </p>
                    </div>

                    {{-- Capacity & size --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Capacity &amp; size</label>
                        <div class="grid grid-cols-4 gap-3">
                            @foreach(['Guests','Bedrooms','Bathrooms','Stay (sqm)'] as $cap)
                            <div class="relative">
                                <select class="w-full px-3 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm text-gray-600 appearance-none focus:outline-none focus:ring-1 focus:ring-[#FF5A00] cursor-pointer">
                                    <option>{{ $cap }}</option>
                                    @for($i=1;$i<=10;$i++) <option>{{ $i }}</option> @endfor
                                </select>
                                <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Property type & Nightly rate --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Property type</label>
                            <div class="relative">
                                <select class="w-full px-4 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm text-gray-700 appearance-none focus:outline-none focus:ring-1 focus:ring-[#FF5A00] cursor-pointer">
                                    <option>Serviced apartment</option>
                                    <option>Shortlet</option>
                                    <option>Boutique Suite</option>
                                    <option>Self-contained</option>
                                    <option>Duplex</option>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Nightly rate (₦)</label>
                            <input type="number" placeholder="5000"
                                class="w-full px-4 py-2.5 bg-[#F0F4F8] border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] placeholder:text-gray-400"/>
                        </div>
                    </div>

                    {{-- Multiple bookable rooms --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="multiple-rooms" class="w-4 h-4 rounded border-gray-300 accent-[#FF5A00] cursor-pointer"/>
                        <label for="multiple-rooms" class="text-sm text-gray-700 cursor-pointer">This property has multiple bookable rooms</label>
                    </div>

                    {{-- Next button --}}
                    <div class="flex justify-end pt-2">
                        <a href="/property/add/amenities"
                            class="px-8 py-2.5 bg-[#FF5A00] hover:bg-[#E64F00] text-white text-sm font-semibold rounded-lg transition-colors">
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
