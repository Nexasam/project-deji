<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Calendar - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECECEC] font-sans antialiased" x-data="{ 
    sidebarOpen: false,
    currentMonth: 'August 2026',
    selectedProperty: 'All properties',
    selectedStatus: 'All entries',
    bookingDetailOpen: false,
    selectedBooking: null,
    openBooking(booking) {
        this.selectedBooking = booking;
        this.bookingDetailOpen = true;
    }
}">
    <div class="min-h-screen flex">

        {{-- Booking Detail Sidebar --}}
        <x-calendar.booking-detail-sidebar />
        {{-- Sidebar --}}
        @include('partials.sidebar-nav', ['active' => 'calendar'])

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
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 mb-1">Calendar</h1>
                            <p class="text-xs text-gray-600">Track every booking — verified locks vs self-reported blocks</p>
                        </div>
                        <button class="flex items-center gap-2 px-4 py-2 bg-[#FF5A00] text-white rounded-lg hover:bg-[#E64F00] text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add booking /block date</span>
                        </button>
                    </div>

                    {{-- Calendar Controls --}}
                    <div class="bg-white rounded-lg border border-gray-200 mb-5">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <button class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-gray-900">August 2026</span>
                                    <button class="w-6 h-6 flex items-center justify-center hover:bg-gray-100 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>
                                <button class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <button class="px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 rounded hover:bg-gray-50">
                                    Today
                                </button>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 border border-gray-300 rounded">
                                    <button class="px-4 py-1.5 text-xs font-medium bg-gray-900 text-white rounded-l">Month</button>
                                    <button class="px-4 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">List</button>
                                </div>
                                <select x-model="selectedProperty" class="px-3 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-[#FF5A00]">
                                    <option>All properties</option>
                                    <option>Egbeda Properties</option>
                                    <option>Island Properties</option>
                                </select>
                                <select x-model="selectedStatus" class="px-3 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-[#FF5A00]">
                                    <option>All entries</option>
                                    <option>Bookings only</option>
                                    <option>Blocks only</option>
                                </select>
                            </div>
                        </div>

                        {{-- Calendar Grid --}}
                        @include('partials.calendar-grid')
                    </div>

                    {{-- Legend --}}
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">LOCK STATE</h3>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-[#FF5A00] rounded"></div>
                                    <span class="text-xs text-gray-700">Solid = Verified Live booking in Airbnb (or other, via iCal)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 border-2 border-[#10B981] border-dashed rounded"></div>
                                    <span class="text-xs text-gray-700">Dashed = Self-reported (Guest = offline, or Untils)</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">CHANNEL</h3>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-[#DC2626] rounded"></div>
                                    <span class="text-xs text-gray-700">Verified Shortlet</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-[#1E40AF] rounded"></div>
                                    <span class="text-xs text-gray-700">Airbnb</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-[#EA580C] rounded"></div>
                                    <span class="text-xs text-gray-700">Booking.com</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-[#10B981] rounded"></div>
                                    <span class="text-xs text-gray-700">Whatsapp</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-gray-400 rounded"></div>
                                    <span class="text-xs text-gray-700">Walk in / phone</span>
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
