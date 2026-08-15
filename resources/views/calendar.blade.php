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
    calView: 'month',
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
            class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm"
        >
            <div
                @click.away="addBookingOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                style="width:100%; max-width:500px; background:#fff; border-radius:16px 16px 0 0; padding:24px 20px; position:relative; font-family:'Inter',sans-serif;"
                class="sm:rounded-2xl sm:mx-4 sm:mb-0"
            >
                {{-- Drag handle (mobile) --}}
                <div class="w-10 h-1 bg-gray-300 rounded-full mx-auto mb-4 sm:hidden"></div>

                {{-- Header --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                    <h2 style="font-size:16px; font-weight:700; color:#111827; margin:0;">Add booking / block date</h2>
                    <button @click="addBookingOpen = false"
                        style="width:32px; height:32px; background:#FF5A00; border:none; border-radius:7px; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0;"
                        onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'">
                        <svg width="12" height="12" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Property --}}
                <div style="margin-bottom:16px;">
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
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Check-in</label>
                        <input type="date"
                            style="width:100%; padding:10px 14px; background:#F9FAFB; border:1.5px solid #E5E7EB; border-radius:10px; font-size:13px; color:#111827; font-family:'Inter',sans-serif; outline:none; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#FF5A00'" onblur="this.style.borderColor='#E5E7EB'" />
                    </div>
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Check-out</label>
                        <input type="date"
                            style="width:100%; padding:10px 14px; background:#F9FAFB; border:1.5px solid #E5E7EB; border-radius:10px; font-size:13px; color:#111827; font-family:'Inter',sans-serif; outline:none; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#FF5A00'" onblur="this.style.borderColor='#E5E7EB'" />
                    </div>
                </div>

                {{-- Booking channel --}}
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Booking channel</label>
                    <div style="display:flex; align-items:center; gap:10px;">
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

                {{-- Notice --}}
                <div x-show="bookingChannel === 'vs'"
                    style="background:#FFF5EE; border:1.5px solid #FDBA74; border-radius:10px; padding:10px 14px; margin-bottom:16px; font-size:12px; color:#9A3412; font-family:'Inter',sans-serif; line-height:1.5;">
                    ⚠ This will show as a <strong>Verified Lock</strong> — system-confirmed and not editable once saved.
                </div>
                <div x-show="bookingChannel !== 'vs'"
                    style="background:#F0FDF4; border:1.5px solid #86EFAC; border-radius:10px; padding:10px 14px; margin-bottom:16px; font-size:12px; color:#166534; font-family:'Inter',sans-serif; line-height:1.5;">
                    ✏ This will show as a <strong>self-reported block</strong> — you can edit or remove it later.
                </div>

                {{-- Guest name --}}
                <div style="margin-bottom:20px;">
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
            <header class="bg-white border-b border-gray-200 h-14 flex items-center px-4 md:px-6">
                <div class="flex items-center justify-between w-full gap-3">
                    <div class="flex items-center gap-2 md:gap-4">
                        {{-- Hamburger --}}
                        <button @click="sidebarOpen = true"
                                class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="relative hidden sm:block">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" placeholder="Search bookings..."
                                   class="w-56 lg:w-72 pl-10 pr-4 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] focus:border-[#FF5A00]"/>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 md:gap-3">
                        <button class="relative w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-0.5 right-0.5 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <button class="flex items-center gap-1.5 hover:bg-gray-50 rounded-lg px-2 py-1">
                            <div class="w-7 h-7 bg-[#FF5A00] rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-semibold">SM</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-auto bg-[#ECECEC]">
                <div class="p-4 md:p-6">

                    {{-- Page Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 mb-0.5">Calendar</h1>
                            <p class="text-xs text-gray-600">Track every booking — verified locks vs self-reported blocks</p>
                        </div>
                        <button @click="addBookingOpen = true"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#FF5A00] text-white rounded-lg hover:bg-[#E64F00] text-sm font-medium flex-shrink-0 self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add booking
                        </button>
                    </div>

                    {{-- Calendar Card --}}
                    <div class="bg-white rounded-xl border border-gray-200 mb-5">

                        {{-- Controls --}}
                        <div class="px-4 md:px-6 py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center gap-3">

                            {{-- Month navigation --}}
                            <div class="flex items-center gap-2 flex-1">
                                <button class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <span class="text-sm font-bold text-gray-900 whitespace-nowrap">August 2026</span>
                                <button class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <button class="px-3 py-1.5 text-xs font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 ml-1">
                                    Today
                                </button>
                            </div>

                            {{-- View + Filters --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                {{-- Month/List toggle --}}
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                    <button @click="calView = 'month'"
                                            :class="calView === 'month' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                            class="px-3 py-1.5 text-xs font-medium transition-colors">Month</button>
                                    <button @click="calView = 'list'"
                                            :class="calView === 'list' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                            class="px-3 py-1.5 text-xs font-medium transition-colors border-l border-gray-300">List</button>
                                </div>

                                <select x-model="selectedProperty"
                                        class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#FF5A00] bg-white">
                                    <option>All properties</option>
                                    <option>Egbeda Properties</option>
                                    <option>Island Properties</option>
                                </select>

                                <select x-model="selectedStatus"
                                        class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#FF5A00] bg-white">
                                    <option>All entries</option>
                                    <option>Bookings only</option>
                                    <option>Blocks only</option>
                                </select>
                            </div>
                        </div>

                        {{-- Calendar Grid --}}
                        <div x-show="calView === 'month'">
                            @include('partials.calendar-grid')
                        </div>

                        {{-- List View --}}
                        <div x-show="calView === 'list'" style="display:none">
                            <div class="divide-y divide-gray-100">
                                @foreach([
                                    ['Aug 4',  'Bluewater Suite 4B',  'Verified Shortlet','#FF5A00', 'Targe Hubare',   'Confirmed'],
                                    ['Aug 4',  'Sunset Apartment',    'WhatsApp',         '#10B981', 'Marie Ilmaire',  'Self-reported'],
                                    ['Aug 6',  'Emerald Suites',      'Airbnb',           '#1E40AF', 'Booking confirm','Confirmed'],
                                    ['Aug 8',  'Bluewater Suite 4B',  'Verified Shortlet','#FF5A00', 'Confirmed',      'Confirmed'],
                                    ['Aug 9',  'Highrise Apartment',  'Booking.com',      '#EA580C', 'Verified',       'Confirmed'],
                                    ['Aug 10', 'Sunset Loft',         'Airbnb',           '#DC2626', 'Tunde Balogun',  'Confirmed'],
                                    ['Aug 14', 'Highrise Apartment',  'Verified Shortlet','#FF5A00', 'Confirmed',      'Confirmed'],
                                    ['Aug 15', 'Emerald Suites',      'Airbnb',           '#DC2626', 'Akin Falade',    'Confirmed'],
                                    ['Aug 22', 'Emerald Suites',      'Airbnb',           '#DC2626', 'Confirmed',      'Confirmed'],
                                    ['Aug 24', 'Highrise Apartment',  'Verified Shortlet','#FF5A00', 'Confirmed',      'Confirmed'],
                                    ['Aug 25', 'Highrise Apartment',  'Booking.com',      '#EA580C', 'Confirmed',      'Confirmed'],
                                    ['Aug 27', 'Bluewater Suite 4B',  'Walk-in',          '#9CA3AF', 'Self entered',   'Self-reported'],
                                    ['Aug 28', 'Emerald Suites',      'Airbnb',           '#DC2626', 'Oba Martins',    'Confirmed'],
                                ] as [$date, $property, $channel, $color, $guest, $status])
                                <div class="flex items-center gap-3 px-4 md:px-6 py-3 hover:bg-gray-50 cursor-pointer"
                                     @click="openBooking({dateLabel: '{{ $date }}', bookings: [{property:'{{ $property }}', source:'{{ $channel }} · {{ $guest }}', type: '{{ $status === "Confirmed" ? "locked" : "self" }}', note: ''}]})">
                                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $color }}"></div>
                                    <div class="w-12 text-xs font-semibold text-gray-500 flex-shrink-0">{{ $date }}</div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $property }}</div>
                                        <div class="text-xs text-gray-500">{{ $channel }} · {{ $guest }}</div>
                                    </div>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0 {{ $status === 'Confirmed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $status }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white rounded-xl border border-gray-200 p-4">
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Lock State</h3>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-[#FF5A00] rounded flex-shrink-0"></div>
                                    <span class="text-xs text-gray-700">Solid = Verified live booking (Airbnb, VS, etc.)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 border-2 border-dashed border-[#10B981] rounded flex-shrink-0"></div>
                                    <span class="text-xs text-gray-700">Dashed = Self-reported block (editable)</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-4">
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">Channel</h3>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                                @foreach([['#DC2626','Verified Shortlet'],['#1E40AF','Airbnb'],['#EA580C','Booking.com'],['#10B981','WhatsApp'],['#9CA3AF','Walk-in / Phone']] as [$c,$label])
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded flex-shrink-0" style="background:{{ $c }}"></div>
                                    <span class="text-xs text-gray-700">{{ $label }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</body>
</html>
