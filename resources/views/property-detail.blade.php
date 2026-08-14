<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sunset Loft, Lekki Phase 1 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F4F4] font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top Header --}}
            @include('partials.header')

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-auto">
                <div class="flex h-full">
                    {{-- Left Side - Blurred Background --}}
                    <div class="flex-1 p-6 blur-sm pointer-events-none">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 mb-1">Properties</h1>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <a href="#" class="hover:text-gray-900">Sonic Movers</a>
                                    <span>></span>
                                    <a href="#" class="hover:text-gray-900">All locations</a>
                                    <span>></span>
                                    <span class="text-gray-900">12 properties</span>
                                </div>
                            </div>
                        </div>

                        {{-- Location Stats Cards --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-white rounded-lg p-4 border-2 border-gray-900">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-gray-900 rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-gray-900">All Locations</h3>
                                        <p class="text-xs text-gray-600">Across Sonic Movers</p>
                                    </div>
                                </div>
                                <div class="flex items-end gap-4">
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">12</p>
                                        <p class="text-xs text-gray-600">Properties</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">78%</p>
                                        <p class="text-xs text-gray-600">Avg occupancy</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-[#FF5A00] rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-gray-900">Egbeda</h3>
                                        <p class="text-xs text-gray-600">Manages Egbeda Bazaars</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-[#FF5A00] rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-gray-900">Island</h3>
                                        <p class="text-xs text-gray-600">Manages Helena Yousif</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Property Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            <div class="bg-white rounded-xl overflow-hidden shadow-sm">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop" 
                                         alt="Property" class="w-full h-48 object-cover"/>
                                    <span class="absolute top-3 left-3 bg-[#FF5A00] text-white text-xs font-bold px-3 py-1 rounded">OCCUPIED</span>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-base text-gray-900 mb-1">Sunset Loft, Lekki Phase 1</h3>
                                    <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl overflow-hidden shadow-sm">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop" 
                                         alt="Property" class="w-full h-48 object-cover"/>
                                    <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded">AVAILABLE</span>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 2A</h3>
                                    <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side - Property Detail Panel --}}
                    <div class="w-[500px] bg-white border-l border-gray-200 overflow-y-auto flex-shrink-0">
                        {{-- Panel Header --}}
                        <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between z-10">
                            <h2 class="text-lg font-bold text-gray-900">Sunset Loft, Lekki Phase 1</h2>
                            <a href="/properties" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 text-[#FF5A00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </div>

                        <div class="p-6">
                            {{-- Property Subtitle --}}
                            <p class="text-sm text-gray-600 mb-4">Egbeda, Lagos · Serviced Apartment</p>

                            {{-- Tab Navigation --}}
                            <div class="flex gap-1 mb-6 border-b border-gray-200 overflow-x-auto">
                                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Overview</a>
                                <a href="#" class="px-4 py-2 text-sm font-medium text-[#FF5A00] border-b-2 border-[#FF5A00] whitespace-nowrap">Bookings</a>
                                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Finance</a>
                                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Operations</a>
                                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Assets</a>
                                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Documents</a>
                                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Reviews</a>
                                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Marketplace</a>
                                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">History</a>
                            </div>

                            {{-- Bookings Table --}}
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-2 text-xs font-bold text-gray-700 uppercase">Guest</th>
                                            <th class="text-left py-3 px-2 text-xs font-bold text-gray-700 uppercase">Dates</th>
                                            <th class="text-left py-3 px-2 text-xs font-bold text-gray-700 uppercase">Channel</th>
                                            <th class="text-left py-3 px-2 text-xs font-bold text-gray-700 uppercase">Status</th>
                                            <th class="text-right py-3 px-2 text-xs font-bold text-gray-700 uppercase">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Booking Row 1 --}}
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-2">
                                                <p class="text-sm font-medium text-gray-900">Toheej Rasizx</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <p class="text-sm text-gray-900">4–7 Aug</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <p class="text-sm text-gray-900">Verified Shortlet</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded">Confirmed</span>
                                            </td>
                                            <td class="py-3 px-2 text-right">
                                                <p class="text-sm font-semibold text-gray-900">₦105,000</p>
                                            </td>
                                        </tr>

                                        {{-- Booking Row 2 --}}
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-2">
                                                <p class="text-sm font-medium text-gray-900">Kakosbi Nemso</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <p class="text-sm text-gray-900">10–12 Aug</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <p class="text-sm text-gray-900">Airbnb</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded">Confirmed</span>
                                            </td>
                                            <td class="py-3 px-2 text-right">
                                                <p class="text-sm font-semibold text-gray-900">₦134,000</p>
                                            </td>
                                        </tr>

                                        {{-- Booking Row 3 --}}
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-2">
                                                <p class="text-sm font-medium text-gray-900">Amass Dhalvse</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <p class="text-sm text-gray-900">19–20 Aug</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <p class="text-sm text-gray-900">Verified Shortlet</p>
                                            </td>
                                            <td class="py-3 px-2">
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-orange-700 bg-orange-100 rounded">Pending</span>
                                            </td>
                                            <td class="py-3 px-2 text-right">
                                                <p class="text-sm font-semibold text-gray-900">₦200,000</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
