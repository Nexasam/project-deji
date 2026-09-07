<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bookings – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F4F4] font-sans antialiased" x-data="{
    sidebarOpen: false,
    bookingDrawerOpen: false,
    selectedBookingDetail: null,
    openBookingDetail(booking) {
        this.selectedBookingDetail = booking;
        this.bookingDrawerOpen = true;
    }
}">
<div class="flex h-screen overflow-hidden">

    {{-- Booking detail drawer --}}
    <x-bookings.detail-drawer />

    {{-- Sidebar --}}
    @include('partials.sidebar-nav', ['active' => 'bookings'])

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Top header --}}
        @include('partials.header')

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="p-6 max-w-[1200px] mx-auto space-y-5">

                {{-- Page title + CTA --}}
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Bookings</h1>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $business->name }}
                            <span class="mx-1 text-gray-300">›</span>
                            Lekki Waterview Suites
                            <span class="mx-1 text-gray-300">›</span>
                            Bookings
                        </p>
                    </div>
                    <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF5A00] hover:bg-[#E64F00] text-white text-sm font-bold rounded-lg transition-colors flex-shrink-0">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        New booking
                    </button>
                </div>

                {{-- KPI row --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3">
                    @foreach($stats as $stat)
                    <div class="bg-white rounded-xl border border-gray-100 px-4 py-3.5">
                        <p class="text-[22px] font-extrabold leading-none {{ $stat['highlight'] ? 'text-[#FF5A00]' : 'text-gray-900' }}">
                            {{ $stat['value'] }}
                        </p>
                        <p class="text-[12px] text-gray-400 mt-1.5 font-medium leading-tight">{{ $stat['label'] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Filter bar --}}
                <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 flex items-center gap-3 flex-wrap"
                     x-data="{ property: 'Lekki Waterview Suites' }">

                    {{-- Property picker --}}
                    <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 text-sm cursor-pointer hover:border-gray-300 transition-colors min-w-[200px]">
                        <div class="w-5 h-5 rounded bg-[#FF5A00] flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-[9px] font-bold">LW</span>
                        </div>
                        <span class="text-gray-700 font-medium flex-1 text-[13px]" x-text="property"></span>
                        <svg width="12" height="12" fill="none" stroke="#6b7280" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    <div class="flex-1"></div>

                    {{-- Date filter --}}
                    <div class="relative">
                        <button class="bk-filter-btn">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                            All Dates
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    {{-- Status filter --}}
                    <div class="relative">
                        <select class="bk-filter-btn appearance-none pr-8 cursor-pointer">
                            <option>Any listing status</option>
                            <option>Pending</option>
                            <option>Confirmed</option>
                            <option>Completed</option>
                            <option>Cancelled</option>
                        </select>
                        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" width="10" height="10" fill="none" stroke="#6b7280" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    {{-- Channel filter --}}
                    <div class="relative">
                        <select class="bk-filter-btn appearance-none pr-8 cursor-pointer">
                            <option>Any channel</option>
                            <option>Direct</option>
                            <option>Airbnb</option>
                            <option>Booking.com</option>
                            <option>WhatsApp</option>
                            <option>Walk-in</option>
                        </select>
                        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" width="10" height="10" fill="none" stroke="#6b7280" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    {{-- Sort --}}
                    <div class="relative">
                        <select class="bk-filter-btn appearance-none pr-8 cursor-pointer">
                            <option>Sort: Name (A-Z)</option>
                            <option>Sort: Date (newest)</option>
                            <option>Sort: Amount (high)</option>
                            <option>Sort: Status</option>
                        </select>
                        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" width="10" height="10" fill="none" stroke="#6b7280" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                {{-- Bookings table --}}
                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[900px]">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/60">
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider py-3 pl-5 pr-3">Guest</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider py-3 px-3">Property</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider py-3 px-3">Dates</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider py-3 px-3">Channel</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider py-3 px-3">Status</th>
                                    <th class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider py-3 px-3">Amount</th>
                                    <th class="py-3 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($bookings as $booking)
                                @php
                                    // Channel pill styles
                                    $channelStyles = [
                                        'direct-walkin'  => 'bg-[#FF5A00] text-white',
                                        'direct-phonein' => 'bg-[#FF5A00] text-white',
                                        'bookingcom'     => 'bg-[#003580] text-white',
                                        'airbnb'         => 'bg-[#FF385C] text-white',
                                        'manual'         => 'bg-gray-500 text-white',
                                        'whatsapp'       => 'bg-[#25D366] text-white',
                                    ];
                                    $channelClass = $channelStyles[$booking['channel_style']] ?? 'bg-gray-200 text-gray-700';

                                    // Status pill styles
                                    $statusStyles = [
                                        'Pending'   => 'bg-amber-100 text-amber-700',
                                        'Confirmed' => 'bg-blue-100 text-blue-700',
                                        'Completed' => 'bg-emerald-100 text-emerald-700',
                                        'Cancelled' => 'bg-red-100 text-red-500',
                                    ];
                                    $statusClass = $statusStyles[$booking['status']] ?? 'bg-gray-100 text-gray-600';

                                    // Avatar initials
                                    $initials = collect(explode(' ', $booking['guest']))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                                    $isGuest = str_contains(strtolower($booking['guest']), 'guest');
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition-colors group">

                                    {{-- Guest --}}
                                    <td class="py-3.5 pl-5 pr-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[11px] font-bold
                                                {{ $isGuest ? 'bg-gray-100 text-gray-400' : 'bg-orange-100 text-[#FF5A00]' }}">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <p class="text-[13px] font-semibold text-gray-900 leading-tight">{{ $booking['guest'] }}</p>
                                                <p class="text-[11px] text-gray-400">{{ $booking['id'] }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Property --}}
                                    <td class="py-3.5 px-3">
                                        <p class="text-[13px] font-medium text-gray-800 leading-tight">{{ $booking['property'] }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $booking['location'] }}</p>
                                    </td>

                                    {{-- Dates --}}
                                    <td class="py-3.5 px-3">
                                        <p class="text-[13px] text-gray-800 font-medium whitespace-nowrap">{{ $booking['dates'] }}</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $booking['nights'] }}</p>
                                    </td>

                                    {{-- Channel --}}
                                    <td class="py-3.5 px-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap {{ $channelClass }}">
                                            {{ $booking['channel'] }}
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $statusClass }}">
                                            {{ $booking['status'] }}
                                        </span>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="py-3.5 px-3">
                                        <p class="text-[13px] font-bold text-gray-900 whitespace-nowrap">{{ $booking['amount'] }}</p>
                                        <p class="text-[11px] mt-0.5
                                            {{ $booking['paid'] === 'Paid in full' ? 'text-emerald-600' : ($booking['paid'] === 'Unpaid' ? 'text-red-400' : 'text-amber-600') }}">
                                            {{ $booking['paid'] }}
                                        </p>
                                    </td>

                                    {{-- Action --}}
                                    <td class="py-3.5 pr-4 pl-3">
                                        <button
                                            @click="openBookingDetail({{ json_encode($booking) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-[12px] font-semibold rounded-lg transition-colors whitespace-nowrap opacity-0 group-hover:opacity-100">
                                            View details
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination footer --}}
                    <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
                        <p class="text-[12px] text-gray-400">Showing <span class="font-semibold text-gray-600">{{ count($bookings) }}</span> of <span class="font-semibold text-gray-600">20</span> bookings</p>
                        <div class="flex items-center gap-1">
                            <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-600 transition-colors disabled:opacity-40" disabled>
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-600 transition-colors">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="h-2"></div>
            </div>
        </main>
    </div>
</div>

@push('styles')
<style>
.bk-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    white-space: nowrap;
    transition: border-color .15s;
    font-family: inherit;
}
.bk-filter-btn:hover { border-color: #d1d5db; }
</style>
@endpush

</body>
</html>
