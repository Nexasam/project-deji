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

        {{-- New Calendar Entry Modal --}}
        <div
            x-show="addBookingOpen"
            x-cloak
            @keydown.escape.window="addBookingOpen = false"
            class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm"
            x-data="{
                entryType: 'block',
                checkIn: '',
                checkOut: '',
                blockReason: '',
                guestName: '',
                guestPhone: '',
                bookingSource: 'direct',
                dateError: false,
                selectedProperty: 'Sunset Loft, Lekki Phase 1',
                selectedPropertyLocation: 'Egbeda, Lagos',
                get startingDate() {
                    if (!this.checkIn) return 'Aug 6, 2026';
                    return new Date(this.checkIn).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
                },
                get propertyInitials() {
                    return this.selectedProperty.split(',')[0].split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase();
                },
                validateDates() {
                    this.dateError = !!(this.checkIn && this.checkOut && this.checkOut <= this.checkIn);
                },
                save() {
                    this.validateDates();
                    if (!this.dateError) { this.addBookingOpen = false; }
                }
            }"
        >
            {{-- Sheet --}}
            <div
                @click.away="addBookingOpen = false"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-8"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-8"
                class="w-full bg-white sm:rounded-2xl sm:max-w-[460px] sm:mx-4 flex flex-col"
                style="border-radius:20px 20px 0 0; max-height:94dvh; font-family:'Inter',sans-serif;"
            >
                {{-- Drag handle (mobile only) --}}
                <div class="flex justify-center pt-3 pb-0 sm:hidden flex-shrink-0">
                    <div class="w-9 h-1 bg-gray-200 rounded-full"></div>
                </div>

                {{-- Scrollable body --}}
                <div class="flex-1 overflow-y-auto px-5 pt-4 pb-2">

                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-1">
                        <div>
                            <h2 class="text-[17px] font-bold text-gray-900 leading-snug">New Calendar Entry</h2>
                            <p class="text-[12px] text-gray-400 mt-0.5" x-text="'Starting ' + startingDate"></p>
                        </div>
                        <button @click="addBookingOpen = false"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors mt-0.5 flex-shrink-0 ml-3">
                            <svg width="13" height="13" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <hr class="border-gray-100 my-4" />

                    {{-- Property card --}}
                    <div class="flex items-center gap-3 bg-gray-50 rounded-2xl px-4 py-3 mb-5">
                        <div class="w-12 h-12 rounded-xl bg-[#FF5A00] flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold" x-text="propertyInitials"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[14px] font-bold text-gray-900 leading-tight truncate" x-text="selectedProperty"></p>
                            <p class="text-[12px] text-gray-500 mt-0.5" x-text="selectedPropertyLocation"></p>
                        </div>
                        <button class="w-7 h-7 flex items-center justify-center rounded-full bg-white border border-gray-200 hover:border-gray-300 transition-colors flex-shrink-0">
                            <svg width="13" height="13" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Check-in / Check-out --}}
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-2">Check-in</label>
                            <div class="relative">
                                <input type="date" x-model="checkIn" @change="validateDates"
                                    class="w-full px-3 py-3 pr-10 bg-white border-2 rounded-xl text-[13px] font-semibold text-gray-900 outline-none transition-colors cursor-pointer"
                                    :class="dateError ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-800'"
                                />
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16" height="16" fill="#111" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-2">Check-out</label>
                            <div class="relative">
                                <input type="date" x-model="checkOut" @change="validateDates"
                                    class="w-full px-3 py-3 pr-10 bg-white border-2 rounded-xl text-[13px] font-semibold text-gray-900 outline-none transition-colors cursor-pointer"
                                    :class="dateError ? 'border-red-300 bg-red-50' : 'border-gray-200 focus:border-gray-800'"
                                />
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16" height="16" fill="#111" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Date error --}}
                    <div x-show="dateError" style="display:none"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="flex items-center gap-2 bg-red-50 text-red-500 text-[12px] font-medium rounded-full px-4 py-2 mt-3 w-fit">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Check-out must be after check-in
                    </div>

                    {{-- Entry type toggle --}}
                    <div class="grid grid-cols-2 gap-3 mt-5 mb-5">

                        {{-- Block dates --}}
                        <button type="button" @click="entryType = 'block'"
                            class="flex flex-col items-center justify-center gap-3 py-5 rounded-2xl border-2 transition-all"
                            :class="entryType === 'block'
                                ? 'border-gray-200 bg-gray-50'
                                : 'border-gray-100 bg-gray-50 opacity-60 hover:opacity-80'"
                        >
                            <div class="w-11 h-11 rounded-full flex items-center justify-center bg-white">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" d="M5.636 5.636l12.728 12.728"/>
                                </svg>
                            </div>
                            <span class="text-[13px] font-bold"
                                :class="entryType === 'block' ? 'text-gray-900' : 'text-gray-400'">
                                Block dates
                            </span>
                        </button>

                        {{-- New reservation --}}
                        <button type="button" @click="entryType = 'reservation'"
                            class="flex flex-col items-center justify-center gap-3 py-5 rounded-2xl border-2 transition-all"
                            :class="entryType === 'reservation'
                                ? 'border-[#FF5A00] bg-orange-50'
                                : 'border-gray-100 bg-gray-50 opacity-60 hover:opacity-80'"
                        >
                            <div class="w-11 h-11 rounded-full flex items-center justify-center bg-white">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="#111827">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>
                            </div>
                            <span class="text-[13px] font-bold"
                                :class="entryType === 'reservation' ? 'text-gray-900' : 'text-gray-400'">
                                New reservation
                            </span>
                        </button>
                    </div>

                    {{-- Block: reason --}}
                    <div x-show="entryType === 'block'" style="display:none"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="mb-4"
                    >
                        <label class="block text-[13px] font-semibold text-gray-700 mb-2">Reason for blocking</label>
                        <textarea x-model="blockReason" rows="3" placeholder="Maintenance"
                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-[13px] text-gray-900 outline-none resize-none transition-colors focus:border-gray-400 placeholder-gray-300"
                        ></textarea>
                    </div>

                    {{-- Reservation: guest fields --}}
                    <div x-show="entryType === 'reservation'" style="display:none"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="space-y-4 mb-4"
                    >
                        {{-- Guest name --}}
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-2">Guest name</label>
                            <input type="text" x-model="guestName" placeholder="e.g Ngozi Eze"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-[13px] text-gray-900 outline-none transition-colors focus:border-gray-400 placeholder-gray-300"
                            />
                        </div>

                        {{-- Phone number --}}
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-2">Phone number</label>
                            <input type="tel" x-model="guestPhone" placeholder="+2348105550555"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-[13px] text-gray-900 outline-none transition-colors focus:border-gray-400 placeholder-gray-300"
                            />
                        </div>

                        {{-- Booking source pills --}}
                        <div>
                            <label class="block text-[13px] font-semibold text-gray-700 mb-3">Booking source</label>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $sources = [
                                        ['value' => 'whatsapp',  'label' => 'WhatsApp',  'active_bg' => 'bg-[#25D366] border-[#25D366] text-white', 'idle_bg' => 'bg-white border-gray-200 text-gray-600'],
                                        ['value' => 'direct',    'label' => 'Direct',    'active_bg' => 'bg-[#FF5A00] border-[#FF5A00] text-white', 'idle_bg' => 'bg-white border-gray-200 text-gray-600'],
                                        ['value' => 'airbnb',    'label' => 'Airbnb',    'active_bg' => 'bg-[#FF385C] border-[#FF385C] text-white', 'idle_bg' => 'bg-white border-gray-200 text-gray-600'],
                                        ['value' => 'booking',   'label' => 'Booking.com','active_bg'=> 'bg-[#003580] border-[#003580] text-white', 'idle_bg' => 'bg-white border-gray-200 text-gray-600'],
                                        ['value' => 'walkin',    'label' => 'Walk-in',   'active_bg' => 'bg-gray-800 border-gray-800 text-white',   'idle_bg' => 'bg-white border-gray-200 text-gray-600'],
                                    ];
                                @endphp
                                @foreach($sources as $src)
                                <button
                                    type="button"
                                    @click="bookingSource = '{{ $src['value'] }}'"
                                    class="px-4 py-2 rounded-full border text-[13px] font-semibold transition-all"
                                    :class="bookingSource === '{{ $src['value'] }}' ? '{{ $src['active_bg'] }}' : '{{ $src['idle_bg'] }} hover:border-gray-300'"
                                >
                                    {{ $src['label'] }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>{{-- end scrollable body --}}

                {{-- Sticky footer --}}
                <div class="flex-shrink-0 border-t border-gray-100 px-5 py-4 grid grid-cols-2 gap-3">
                    <button @click="addBookingOpen = false"
                        class="py-3.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-[14px] font-bold text-gray-700 transition-colors">
                        Close
                    </button>
                    <button @click="save()"
                        class="py-3.5 rounded-xl text-[14px] font-bold text-white transition-colors"
                        :class="entryType === 'reservation' ? 'bg-[#FF5A00] hover:bg-[#E64F00]' : 'bg-gray-900 hover:bg-black'">
                        <span x-text="entryType === 'block' ? 'Block dates' : 'Confirm reservation'"></span>
                    </button>
                </div>

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
