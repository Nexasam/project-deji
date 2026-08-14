<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECECEC] font-sans antialiased" x-data="{ sidebarOpen: false, viewMode: 'grid' }">
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <aside class="w-[200px] bg-white border-r border-gray-200 flex-shrink-0 hidden md:flex flex-col">
            {{-- Logo Section --}}
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-[#FF5A00] rounded flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">VS</span>
                    </div>
                    <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
                </div>
            </div>

            {{-- User Profile --}}
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-[#FF5A00] rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">SM</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-gray-900 text-xs font-semibold truncate">Sonic Movers</p>
                        <p class="text-gray-500 text-[10px] truncate">Owino workspace</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 p-3 overflow-y-auto">
                <div class="mb-6">
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider px-2 mb-2">Overview</p>
                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        <span class="text-sm">Dashboard</span>
                    </a>
                    
                    <a href="/properties" class="flex items-center gap-3 px-3 py-2 text-[#FF5A00] bg-orange-50 rounded-lg mb-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                        <span class="text-sm font-medium">Properties</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1 relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm">Bookings</span>
                        <span class="ml-auto bg-[#FF5A00] text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">3</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm">Calendar</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-sm">Guests</span>
                    </a>
                </div>

                <div class="mb-6">
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider px-2 mb-2">Manage</p>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                        <span class="text-sm">Finance</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1 relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-sm">Operations</span>
                        <span class="ml-auto bg-[#FF5A00] text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">1</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-sm">Reports</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm">Documents</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-sm">Marketplace</span>
                    </a>
                </div>

                <div>
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider px-2 mb-2">System</p>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span class="text-sm">AI Assistant</span>
                    </a>
                    
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm">Settings</span>
                    </a>
                </div>
            </nav>
        </aside>

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
                            <button class="flex items-center gap-2 px-4 py-2 bg-[#FF5A00] text-white rounded-lg hover:bg-[#E64F00] text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Add property</span>
                            </button>
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

                        {{-- Property Card 2 --}}
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

                        {{-- Property Card 3 --}}
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

                        {{-- Property Card 4 --}}
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

                        {{-- Property Card 5 --}}
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

                        {{-- Property Card 6 --}}
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

                        {{-- Property Card 7 --}}
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

                        {{-- Property Card 8 --}}
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
