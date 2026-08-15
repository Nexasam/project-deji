<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECECEC] font-sans antialiased" x-data="{ sidebarOpen: false, viewMode: 'grid', propertyDetailOpen: false, selectedProperty: null }">
    <div class="fixed inset-x-0 top-0 z-[100] flex items-center justify-between gap-4 border-b border-amber-200 bg-amber-50 px-4 py-2 text-xs text-amber-900 shadow-sm">
        <span><strong>Sample data:</strong> this properties portfolio is for client presentation only.</span>
        <x-owner.view-switch route-name="owner.properties.index" mode="demo" />
    </div>
    <div class="min-h-screen flex pt-[57px]">
        {{-- Property Detail Sidebar --}}
        <x-property-detail-sidebar />

        {{-- Sidebar --}}
        @include('partials.sidebar-nav', ['active' => 'properties'])

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top Header --}}
            <header class="bg-white border-b border-gray-200 h-14 flex items-center px-6">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" placeholder="Search your settings, bookings..."
                                   class="w-72 pl-10 pr-4 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] focus:border-[#FF5A00]"/>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button class="relative w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
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

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-auto bg-[#ECECEC]">
                <div class="p-6">
                    {{-- Page Header --}}
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 mb-1">Properties</h1>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <a href="#" class="hover:text-[#FF5A00]">Sonic Movers</a>
                                    <span>></span>
                                    <a href="#" class="hover:text-[#FF5A00]">All locations</a>
                                    <span>></span>
                                    <span class="text-gray-900">12 properties</span>
                                </div>
                            </div>
                            <a href="{{ route('owner.properties.create') }}" class="flex items-center gap-2 px-4 py-2 bg-[#FF5A00] text-white rounded-lg hover:bg-[#E64F00] text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Add property</span>
                            </a>
                        </div>

                        {{-- Location Cards --}}
                        <div class="grid grid-cols-3 gap-4 mb-5">
                            {{-- All Locations --}}
                            <div class="bg-[#FFE8D9] rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 bg-white rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">All Locations</div>
                                        <div class="text-xs text-gray-600">Across Sonic Movers</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900">12</div>
                                        <div class="text-xs text-gray-600">Properties</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900">78%</div>
                                        <div class="text-xs text-gray-600">Avg occupancy</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Egbeda --}}
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 bg-[#FF5A00] rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Egbeda</div>
                                        <div class="text-xs text-gray-600">Manager: Tarifa Isaacs</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900">7</div>
                                        <div class="text-xs text-gray-600">Properties</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900">76%</div>
                                        <div class="text-xs text-gray-600">Occupancy</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Island --}}
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 bg-[#FF5A00] rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Island</div>
                                        <div class="text-xs text-gray-600">Manager: Halima Youlf</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900">5</div>
                                        <div class="text-xs text-gray-600">Properties</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-gray-900">52%</div>
                                        <div class="text-xs text-gray-600">Occupancy</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Alert --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-5 flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <p class="text-xs text-gray-700">
                            <span class="font-medium">New locations are created automatically</span> the first time you add a property there - use "+ Add property" and pick a state and area
                        </p>
                    </div>

                    {{-- Search and Filters --}}
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex-1 relative">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" placeholder="Search properties..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] focus:border-transparent"/>
                        </div>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] focus:border-transparent bg-white">
                            <option>All statuses</option>
                            <option>Available</option>
                            <option>Occupied</option>
                            <option>Cleaning</option>
                            <option>Inspection</option>
                            <option>Blocked</option>
                        </select>
                        <div class="flex items-center gap-1 border border-gray-300 rounded-lg p-0.5 bg-white">
                            <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                    class="p-2 rounded transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                                </svg>
                            </button>
                            <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                    class="p-2 rounded transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Property Grid --}}
                    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        {{-- Property Card 1 --}}
                        <div @click="propertyDetailOpen = true">
                            <x-property-dashboard-card
                                image="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400"
                                status="OCCUPIED"
                                statusColor="orange"
                                name="Sunset Loft, Lekki Phase 1"
                                location="Egbeda, Lagos · Serviced Apartment"
                                occupancy="84%"
                                revenue="₦770k"
                                revenueChange="30d"
                            />
                        </div>

                        {{-- Property Card 2 --}}
                        <div @click="propertyDetailOpen = true">
                            <x-property-dashboard-card
                                image="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400"
                                status="AVAILABLE"
                                statusColor="green"
                                name="Bluewater Suite 2A"
                                location="Egbeda, Lagos · Serviced Apartment"
                                occupancy="94%"
                                revenue="₦600k"
                                revenueChange="30d"
                            />
                        </div>

                        {{-- Property Card 3 --}}
                        <div @click="propertyDetailOpen = true">
                            <x-property-dashboard-card
                                image="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400"
                                status="CLEANING"
                                statusColor="blue"
                                name="Bluewater Suite 4B"
                                location="Egbeda, Lagos · Shortlet"
                                occupancy="50%"
                                revenue="₦410k"
                                revenueChange="OFG"
                            />
                        </div>

                        {{-- Property Card 4 --}}
                        <div @click="propertyDetailOpen = true">
                            <x-property-dashboard-card
                                image="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400"
                                status="INSPECTION"
                                statusColor="purple"
                                name="Palm Court 1"
                                location="Island, Lagos · Boutique Suite"
                                occupancy="84%"
                                revenue="₦780k"
                                revenueChange="30d"
                            />
                        </div>

                        {{-- Property Card 5 --}}
                        <div @click="propertyDetailOpen = true">
                            <x-property-dashboard-card
                                image="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400"
                                status="BLOCKED"
                                statusColor="gray"
                                name="Sunset Loft, Lekki Phase 1"
                                location="Egbeda, Lagos · Serviced Apartment"
                                occupancy="84%"
                                revenue="₦770k"
                                revenueChange="30d"
                            />
                        </div>

                        {{-- Property Card 6 --}}
                        <div @click="propertyDetailOpen = true">
                            <x-property-dashboard-card
                                image="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400"
                                status="AVAILABLE"
                                statusColor="green"
                                name="Bluewater Suite 2A"
                                location="Egbeda, Lagos · Serviced Apartment"
                                occupancy="94%"
                                revenue="₦600k"
                                revenueChange="30d"
                            />
                        </div>

                        {{-- Property Card 7 --}}
                        <div @click="propertyDetailOpen = true">
                            <x-property-dashboard-card
                                image="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400"
                                status="OCCUPIED"
                                statusColor="orange"
                                name="Sunset Loft, Lekki Phase 1"
                                location="Egbeda, Lagos · Serviced Apartment"
                                occupancy="84%"
                                revenue="₦780k"
                                revenueChange="30d"
                            />
                        </div>

                        {{-- Property Card 8 --}}
                        <div @click="propertyDetailOpen = true">
                            <x-property-dashboard-card
                                image="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400"
                                status="CLEANING"
                                statusColor="blue"
                                name="Bluewater Suite 4B"
                                location="Egbeda, Lagos · Shortlet"
                                occupancy="80%"
                                revenue="₦410k"
                                revenueChange="30d"
                            />
                        </div>
                    </div>

                    {{-- List View --}}
                    <div x-show="viewMode === 'list'" class="bg-white rounded-lg border border-gray-200 overflow-hidden" style="display: none;">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Property</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Occupancy</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Revenue</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=100" class="w-12 h-12 rounded-lg object-cover">
                                            <div>
                                                <div class="font-semibold text-gray-900">Sunset Loft, Lekki Phase 1</div>
                                                <div class="text-sm text-gray-500">Serviced Apartment</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Egbeda, Lagos</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">OCCUPIED</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">84%</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">₦770k</div>
                                        <div class="text-xs text-gray-500">30d</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
