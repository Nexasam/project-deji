<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F4F4] font-sans antialiased" x-data="{ sidebarOpen: false, view: 'grid', showPanel: false, activeTab: 'overview' }">
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
                    {{-- Properties List --}}
                    <div class="flex-1 p-6 transition-all" :class="showPanel ? 'blur-sm' : ''">
                        {{-- Page Header --}}
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
                            <a href="/property/add/step1" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold px-6 py-3 rounded-lg flex items-center gap-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add property
                            </a>
                        </div>

                        {{-- Location Stats Cards --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            {{-- All Locations --}}
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

                            {{-- Egbeda --}}
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
                                <div class="flex items-end gap-4">
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">7</p>
                                        <p class="text-xs text-gray-600">Properties</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">76%</p>
                                        <p class="text-xs text-gray-600">Occupancy</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Island --}}
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
                                <div class="flex items-end gap-4">
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">5</p>
                                        <p class="text-xs text-gray-600">Properties</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">82%</p>
                                        <p class="text-xs text-gray-600">Occupancy</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Info Alert --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-gray-700">New locations are created automatically the first time you add a property there — use "+ Add property" and pick a state and area</p>
                        </div>

                        {{-- Search and Filter Bar --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex-1 relative">
                                <input type="text" placeholder="Search properties..." 
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00]"/>
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <select class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00]">
                                <option>All statuses</option>
                                <option>Available</option>
                                <option>Occupied</option>
                                <option>Cleaning</option>
                                <option>Blocked</option>
                                <option>Inspection</option>
                            </select>
                            <div class="flex gap-2 border border-gray-300 rounded-lg p-1">
                                <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900'" 
                                        class="px-3 py-1.5 rounded flex items-center gap-2 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                                    </svg>
                                    Grid
                                </button>
                                <button @click="view = 'list'" :class="view === 'list' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900'"
                                        class="px-3 py-1.5 rounded flex items-center gap-2 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                                    </svg>
                                    List
                                </button>
                            </div>
                        </div>

                        {{-- Properties Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            {{-- Property Cards - clicking opens panel --}}
                            @for($i = 0; $i < 8; $i++)
                            <div @click="showPanel = true; activeTab = 'overview'" class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-{{ $i % 2 == 0 ? '1522708323590-d24dbb6b0267' : '1600585154340-be6161a56a0c' }}?w=400&h=300&fit=crop" 
                                         alt="Property" class="w-full h-48 object-cover"/>
                                    <span class="absolute top-3 left-3 {{ $i % 4 == 0 ? 'bg-[#FF5A00]' : ($i % 4 == 1 ? 'bg-green-500' : ($i % 4 == 2 ? 'bg-blue-500' : 'bg-purple-500')) }} text-white text-xs font-bold px-3 py-1 rounded">
                                        {{ $i % 4 == 0 ? 'OCCUPIED' : ($i % 4 == 1 ? 'AVAILABLE' : ($i % 4 == 2 ? 'CLEANING' : 'INSPECTION')) }}
                                    </span>
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-base text-gray-900 mb-1">{{ $i % 2 == 0 ? 'Sunset Loft, Lekki Phase 1' : 'Bluewater Suite 2A' }}</h3>
                                    <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                                    <div class="flex items-center justify-between text-xs">
                                        <div>
                                            <p class="text-gray-600 mb-0.5">Occupancy</p>
                                            <p class="font-bold text-gray-900">84%</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                            <p class="font-bold text-gray-900">₦{{ $i % 2 == 0 ? '700' : '600' }}k</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Property Detail Panel (slides in from right) --}}
                    <div x-show="showPanel" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="w-[500px] bg-white border-l border-gray-200 overflow-y-auto flex-shrink-0 absolute right-0 top-0 bottom-0">
                        @include('partials.property-panel')
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
                {{-- Page Header --}}
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
                    <a href="/property/add/step1" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold px-6 py-3 rounded-lg flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add property
                    </a>
                </div>

                {{-- Location Stats Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    {{-- All Locations --}}
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

                    {{-- Egbeda --}}
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
                        <div class="flex items-end gap-4">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">7</p>
                                <p class="text-xs text-gray-600">Properties</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">76%</p>
                                <p class="text-xs text-gray-600">Occupancy</p>
                            </div>
                        </div>
                    </div>

                    {{-- Island --}}
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
                        <div class="flex items-end gap-4">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">5</p>
                                <p class="text-xs text-gray-600">Properties</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">82%</p>
                                <p class="text-xs text-gray-600">Occupancy</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Alert --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-gray-700">New locations are created automatically the first time you add a property there — use "+ Add property" and pick a state and area</p>
                </div>

                {{-- Search and Filter Bar --}}
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-1 relative">
                        <input type="text" placeholder="Search properties..." 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00]"/>
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <select class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00]">
                        <option>All statuses</option>
                        <option>Available</option>
                        <option>Occupied</option>
                        <option>Cleaning</option>
                        <option>Blocked</option>
                        <option>Inspection</option>
                    </select>
                    <div class="flex gap-2 border border-gray-300 rounded-lg p-1">
                        <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900'" 
                                class="px-3 py-1.5 rounded flex items-center gap-2 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                            </svg>
                            Grid
                        </button>
                        <button @click="view = 'list'" :class="view === 'list' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded flex items-center gap-2 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                            </svg>
                            List
                        </button>
                    </div>
                </div>

                {{-- Properties Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    {{-- Property Card 1 - OCCUPIED --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-[#FF5A00] text-white text-xs font-bold px-3 py-1 rounded">OCCUPIED</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Sunset Loft, Lekki Phase 1</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦700k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 2 - AVAILABLE --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded">AVAILABLE</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 2A</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦600k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 3 - CLEANING --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1502672260066-6bc05c107956?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded">CLEANING</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 4B</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Shortlet</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">90%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦410k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 4 - INSPECTION --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-purple-500 text-white text-xs font-bold px-3 py-1 rounded">INSPECTION</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Palm Court 1</h3>
                            <p class="text-xs text-gray-600 mb-3">Island, Lagos · Boutique Suite</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦780k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 5 - BLOCKED --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-gray-800 text-white text-xs font-bold px-3 py-1 rounded">BLOCKED</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Sunset Loft, Lekki Phase 1</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦700k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 6 - AVAILABLE --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded">AVAILABLE</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 2A</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦600k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 7 - OCCUPIED --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1502672260066-6bc05c107956?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-[#FF5A00] text-white text-xs font-bold px-3 py-1 rounded">OCCUPIED</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Sunset Loft, Lekki Phase 1</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦700k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 8 - CLEANING --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded">CLEANING</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 4B</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Shortlet</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">80%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦410k</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
                        {{-- Page Header --}}
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
                            <a href="/property/add/step1" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold px-6 py-3 rounded-lg flex items-center gap-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add property
                            </a>
                        </div>

                        {{-- Location Stats Cards --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            {{-- All Locations --}}
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

                            {{-- Egbeda --}}
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
                                <div class="flex items-end gap-4">
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">7</p>
                                        <p class="text-xs text-gray-600">Properties</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">76%</p>
                                        <p class="text-xs text-gray-600">Occupancy</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Island --}}
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
                                <div class="flex items-end gap-4">
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">5</p>
                                        <p class="text-xs text-gray-600">Properties</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">82%</p>
                                        <p class="text-xs text-gray-600">Occupancy</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Info Alert --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-gray-700">New locations are created automatically the first time you add a property there — use "+ Add property" and pick a state and area</p>
                        </div>

                        {{-- Search and Filter Bar --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex-1 relative">
                                <input type="text" placeholder="Search properties..." 
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00]"/>
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <select class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00]">
                                <option>All statuses</option>
                                <option>Available</option>
                                <option>Occupied</option>
                                <option>Cleaning</option>
                                <option>Blocked</option>
                                <option>Inspection</option>
                            </select>
                            <div class="flex gap-2 border border-gray-300 rounded-lg p-1">
                                <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900'" 
                                        class="px-3 py-1.5 rounded flex items-center gap-2 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                                    </svg>
                                    Grid
                                </button>
                                <button @click="view = 'list'" :class="view === 'list' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900'"
                                        class="px-3 py-1.5 rounded flex items-center gap-2 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                                    </svg>
                                    List
                                </button>
                            </div>
                        </div>

                        {{-- Properties Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            {{-- Property Card 1 - OCCUPIED --}}
                            <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow cursor-pointer" @click="propertyPanel = true">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop" 
                                         alt="Property" class="w-full h-48 object-cover"/>
                                    <span class="absolute top-3 left-3 bg-[#FF5A00] text-white text-xs font-bold px-3 py-1 rounded">OCCUPIED</span>
                                    <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-base text-gray-900 mb-1">Sunset Loft, Lekki Phase 1</h3>
                                    <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                                    <div class="flex items-center justify-between text-xs">
                                        <div>
                                            <p class="text-gray-600 mb-0.5">Occupancy</p>
                                            <p class="font-bold text-gray-900">84%</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                            <p class="font-bold text-gray-900">₦700k</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Repeat similar cards... (abbreviated for brevity) --}}
                        </div>
                    </div>

                    {{-- Right Side - Property Detail Panel --}}
                    <div x-show="propertyPanel" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="w-[500px] bg-white border-l border-gray-200 overflow-y-auto flex-shrink-0">
                        
                        {{-- Panel Header --}}
                        <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between z-10">
                            <h2 class="text-lg font-bold text-gray-900">Sunset Loft, Lekki Phase 1</h2>
                            <button @click="propertyPanel = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="p-6">
                            {{-- Property Title --}}
                            <p class="text-sm text-gray-600 mb-4">Egbeda, Lagos · Serviced Apartment</p>

                            {{-- Tab Navigation --}}
                            <div class="flex gap-2 mb-6 border-b border-gray-200 overflow-x-auto">
                                <button class="px-4 py-2 text-sm font-medium text-[#FF5A00] border-b-2 border-[#FF5A00] whitespace-nowrap">Overview</button>
                                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Bookings</button>
                                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Finance</button>
                                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Operations</button>
                                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Assets</button>
                                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Documents</button>
                                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Reviews</button>
                                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">Marketplace</button>
                                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 whitespace-nowrap">History</button>
                            </div>

                            {{-- Stats Grid --}}
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Occupancy</p>
                                    <p class="text-xl font-bold text-gray-900">84%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Revenue (30d)</p>
                                    <p class="text-xl font-bold text-gray-900">₦780k</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">ADR</p>
                                    <p class="text-xl font-bold text-gray-900">₦42,000</p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <p class="text-xs text-gray-600 mb-1">AI reporting</p>
                                <div class="flex items-center gap-1">
                                    <span class="text-xl font-bold text-gray-900">4.8</span>
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- AI Property Summary --}}
                            <div class="bg-gray-900 text-white rounded-lg p-4 mb-6">
                                <div class="flex items-start gap-2 mb-2">
                                    <span class="text-[#FF5A00] font-bold text-sm">⚡</span>
                                    <h3 class="text-sm font-bold text-[#FF5A00]">AI PROPERTY SUMMARY</h3>
                                </div>
                                <p class="text-sm leading-relaxed mb-3">
                                    Bluewater Suite 4B is outperforming the Egbeda location average by 9% in occupancy this month, 
                                    driven mostly by repeat bookings. One open maintenance flag (AC unit) is at risk of affecting 
                                    upcoming check-ins if not resolved by Friday.
                                </p>
                                <button class="text-[#FF5A00] text-xs font-semibold hover:underline">
                                    Ask Claude AI for more details about upcoming issues...
                                </button>
                            </div>

                            {{-- Operational Readiness --}}
                            <div class="mb-6">
                                <h3 class="text-sm font-bold text-gray-900 mb-3">Operational readiness</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1.5 rounded-full">CLEANING: UP-TO-DATE</span>
                                    <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full">MAINTENANCE: 1 OPEN</span>
                                    <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1.5 rounded-full">INSPECTION: PASSED 29 JUL</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
                {{-- Page Header --}}
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
                    <a href="/property/add/step1" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold px-6 py-3 rounded-lg flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add property
                    </a>
                </div>

                {{-- Location Stats Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    {{-- All Locations --}}
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

                    {{-- Egbeda --}}
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
                        <div class="flex items-end gap-4">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">7</p>
                                <p class="text-xs text-gray-600">Properties</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">76%</p>
                                <p class="text-xs text-gray-600">Occupancy</p>
                            </div>
                        </div>
                    </div>

                    {{-- Island --}}
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
                        <div class="flex items-end gap-4">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">5</p>
                                <p class="text-xs text-gray-600">Properties</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">82%</p>
                                <p class="text-xs text-gray-600">Occupancy</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Alert --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-gray-700">New locations are created automatically the first time you add a property there — use "+ Add property" and pick a state and area</p>
                </div>

                {{-- Search and Filter Bar --}}
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-1 relative">
                        <input type="text" placeholder="Search properties..." 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00]"/>
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <select class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00]">
                        <option>All statuses</option>
                        <option>Available</option>
                        <option>Occupied</option>
                        <option>Cleaning</option>
                        <option>Blocked</option>
                        <option>Inspection</option>
                    </select>
                    <div class="flex gap-2 border border-gray-300 rounded-lg p-1">
                        <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900'" 
                                class="px-3 py-1.5 rounded flex items-center gap-2 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                            </svg>
                            Grid
                        </button>
                        <button @click="view = 'list'" :class="view === 'list' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded flex items-center gap-2 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
                            </svg>
                            List
                        </button>
                    </div>
                </div>

                {{-- Properties Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    {{-- Property Card 1 - OCCUPIED --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-[#FF5A00] text-white text-xs font-bold px-3 py-1 rounded">OCCUPIED</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Sunset Loft, Lekki Phase 1</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦700k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 2 - AVAILABLE --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded">AVAILABLE</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 2A</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦600k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 3 - CLEANING --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1502672260066-6bc05c107956?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded">CLEANING</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 4B</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Shortlet</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">90%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦410k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 4 - INSPECTION --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-purple-500 text-white text-xs font-bold px-3 py-1 rounded">INSPECTION</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Palm Court 1</h3>
                            <p class="text-xs text-gray-600 mb-3">Island, Lagos · Boutique Suite</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦780k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 5 - BLOCKED --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-gray-800 text-white text-xs font-bold px-3 py-1 rounded">BLOCKED</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Sunset Loft, Lekki Phase 1</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦700k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 6 - AVAILABLE --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded">AVAILABLE</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 2A</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦600k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 7 - OCCUPIED --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1502672260066-6bc05c107956?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-[#FF5A00] text-white text-xs font-bold px-3 py-1 rounded">OCCUPIED</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Sunset Loft, Lekki Phase 1</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Serviced Apartment</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">84%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦700k</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Card 8 - CLEANING --}}
                    <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop" 
                                 alt="Property" class="w-full h-48 object-cover"/>
                            <span class="absolute top-3 left-3 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded">CLEANING</span>
                            <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-base text-gray-900 mb-1">Bluewater Suite 4B</h3>
                            <p class="text-xs text-gray-600 mb-3">Egbeda, Lagos · Shortlet</p>
                            <div class="flex items-center justify-between text-xs">
                                <div>
                                    <p class="text-gray-600 mb-0.5">Occupancy</p>
                                    <p class="font-bold text-gray-900">80%</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600 mb-0.5">Revenue (30d)</p>
                                    <p class="font-bold text-gray-900">₦410k</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
