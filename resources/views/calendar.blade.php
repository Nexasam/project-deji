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
    addBookingOpen: false,
    bookingChannel: 'vs',
    openBooking(booking) {
        this.selectedBooking = booking;
        this.bookingDetailOpen = true;
    }
}">
    <div class="min-h-screen flex">

        {{-- Booking Detail Sidebar --}}
        <x-calendar.booking-detail-sidebar />

        {{-- Add Booking Modal --}}
        <div
            x-show="addBookingOpen"
            x-cloak
            @keydown.escape.window="addBookingOpen = false"
            class="fixed inset-0 z-[200] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
        >
            <div
                @click.away="addBookingOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                style="width:100%; max-width:500px; background:#fff; border-radius:16px; padding:32px; position:relative; font-family:'Inter',sans-serif;"
            >
                {{-- Header --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
                    <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">Add booking/ block date</h2>
                    <button @click="addBookingOpen = false"
                        style="width:32px; height:32px; background:#FF5A00; border:none; border-radius:7px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                        onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'">
                        <svg width="12" height="12" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Property --}}
                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Property</label>
                    <div style="position:relative;">
                        <select style="width:100%; padding:10px 36px 10px 14px; background:#F9FAFB; border:1.5px solid #E5E7EB; border-radius:10px; font-size:13px; color:#111827; font-family:'Inter',sans-serif; outline:none; appearance:none; box-sizing:border-box; cursor:pointer;"
                            onfocus="this.style.borderColor='#FF5A00'" onblur="this.style.borderColor='#E5E7EB'">
                            <option value="">Select property</option>
                            <option>Sunset Loft, Lekki Phase 1</option>
                            <option>Bluewater Suite 4B</option>
                            <option>Highrise Apartment</option>
                            <option>Emerald Suites</option>
                        </select>
                        <svg style="position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none;" width="14" height="14" fill="none" stroke="#6B7280" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Check-in / Check-out --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:18px;">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Check-in</label>
                        <div style="position:relative;">
                            <input type="date" placeholder="DD/MM/YYYY"
                                style="width:100%; padding:10px 36px 10px 14px; background:#F9FAFB; border:1.5px solid #E5E7EB; border-radius:10px; font-size:13px; color:#111827; font-family:'Inter',sans-serif; outline:none; box-sizing:border-box;"
                                onfocus="this.style.borderColor='#FF5A00'" onblur="this.style.borderColor='#E5E7EB'" />
                        </div>
                    </div>
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Check-out</label>
                        <div style="position:relative;">
                            <input type="date" placeholder="DD/MM/YYYY"
                                style="width:100%; padding:10px 36px 10px 14px; background:#F9FAFB; border:1.5px solid #E5E7EB; border-radius:10px; font-size:13px; color:#111827; font-family:'Inter',sans-serif; outline:none; box-sizing:border-box;"
                                onfocus="this.style.borderColor='#FF5A00'" onblur="this.style.borderColor='#E5E7EB'" />
                        </div>
                    </div>
                </div>

                {{-- Booking channel --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Booking channel</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        {{-- Colour dot --}}
                        <div style="width:14px; height:14px; border-radius:50%; flex-shrink:0; background:#FF5A00;"
                             :style="bookingChannel === 'vs' ? 'background:#FF5A00' : bookingChannel === 'airbnb' ? 'background:#DC2626' : bookingChannel === 'booking' ? 'background:#1E40AF' : bookingChannel === 'whatsapp' ? 'background:#10B981' : 'background:#6B7280'"></div>
                        <div style="position:relative; flex:1;">
                            <select x-model="bookingChannel"
                                style="width:100%; padding:10px 36px 10px 14px; background:#F9FAFB; border:1.5px solid #E5E7EB; border-radius:10px; font-size:13px; color:#111827; font-family:'Inter',sans-serif; outline:none; appearance:none; box-sizing:border-box; cursor:pointer;"
                                onfocus="this.style.borderColor='#FF5A00'" onblur="this.style.borderColor='#E5E7EB'">
                                <option value="vs">Verified Shortlet (direct)</option>
                                <option value="airbnb">Airbnb</option>
                                <option value="booking">Booking.com</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="walkin">Walk-in / Phone</option>
                            </select>
                            <svg style="position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none;" width="14" height="14" fill="none" stroke="#6B7280" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Info notice --}}
                <div x-show="bookingChannel === 'vs'"
                    style="background:#FFF5EE; border:1.5px solid #FDBA74; border-radius:10px; padding:10px 14px; margin-bottom:18px; font-size:12px; color:#9A3412; font-family:'Inter',sans-serif; line-height:1.5;">
                    ⚠ This will show as a <strong>Verified Lock (Verified Shortlet)</strong> — system-confirmed and not editable once saved.
                </div>
                <div x-show="bookingChannel !== 'vs'"
                    style="background:#F0FDF4; border:1.5px solid #86EFAC; border-radius:10px; padding:10px 14px; margin-bottom:18px; font-size:12px; color:#166534; font-family:'Inter',sans-serif; line-height:1.5;">
                    ✏ This will show as a <strong>self-reported block</strong> — you can edit or remove it later.
                </div>

                {{-- Guest name --}}
                <div style="margin-bottom:24px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Guest name (optional)</label>
                    <input type="text" placeholder="e.g Tariye Fabora"
                        style="width:100%; padding:10px 14px; background:#F9FAFB; border:1.5px solid #E5E7EB; border-radius:10px; font-size:13px; color:#111827; font-family:'Inter',sans-serif; outline:none; box-sizing:border-box;"
                        onfocus="this.style.borderColor='#FF5A00';this.style.background='#fff'"
                        onblur="this.style.borderColor='#E5E7EB';this.style.background='#F9FAFB'" />
                </div>

                {{-- Save --}}
                <button @click="addBookingOpen = false"
                    style="width:100%; padding:14px; background:#FF5A00; border:none; border-radius:10px; font-size:14px; font-weight:700; color:#fff; font-family:'Inter',sans-serif; cursor:pointer; transition:background .15s;"
                    onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'">
                    Save entry
                </button>
            </div>
        </div>
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
                        <button @click="addBookingOpen = true" class="flex items-center gap-2 px-4 py-2 bg-[#FF5A00] text-white rounded-lg hover:bg-[#E64F00] text-sm font-medium">
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
