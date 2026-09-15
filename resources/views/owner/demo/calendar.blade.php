<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Figma calendar preview – {{ $business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="min-h-screen bg-[#ECECEC] font-sans text-gray-900 antialiased"
    x-data="{
        sidebarOpen: false,
        bookingDetailOpen: false,
        selectedBooking: null,
        openBooking(booking) {
            this.selectedBooking = booking;
            this.bookingDetailOpen = true;
        }
    }"
>
<div class="flex min-h-screen">
    @include('partials.sidebar-nav', ['active' => 'calendar'])

    <div class="min-w-0 flex-1">
        <header class="border-b border-gray-200 bg-white">
            <div class="flex min-h-14 items-center justify-between gap-3 px-4 py-2 lg:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 md:hidden" aria-label="Open navigation">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h1 class="truncate text-sm font-bold">Figma calendar preview</h1>
                            <span class="rounded-md bg-amber-100 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-800">Sample data</span>
                        </div>
                        <p class="hidden text-[11px] text-gray-500 sm:block">Original approved presentation layout</p>
                    </div>
                </div>
                <x-owner.view-switch route-name="owner.calendar" mode="demo" />
            </div>
        </header>

        <main class="px-4 py-5 lg:px-6">
            <div class="mb-4 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-900">
                <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p><strong>Sample data:</strong> this tab preserves the original Figma presentation. It is read-only and never creates, edits, or removes live bookings.</p>
            </div>

            <section class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                <div>
                    <h2 class="text-xl font-bold tracking-tight">Calendar</h2>
                    <p class="mt-1 text-xs text-gray-500">Track every booking — verified locks vs self-reported blocks</p>
                </div>
                <button type="button" disabled title="Presentation-only control" class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-[#FF5A00] px-4 py-2.5 text-xs font-bold text-white opacity-70">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Add booking / block date
                </button>
            </section>

            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-2">
                        <button type="button" disabled class="grid size-8 cursor-not-allowed place-items-center rounded-md border border-gray-200 text-gray-400" aria-label="Previous month">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <h3 class="min-w-28 text-center text-sm font-bold">August 2026</h3>
                        <button type="button" disabled class="grid size-8 cursor-not-allowed place-items-center rounded-md border border-gray-200 text-gray-400" aria-label="Next month">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button type="button" disabled class="ml-1 cursor-not-allowed rounded-md border border-gray-200 px-3 py-2 text-[11px] font-semibold text-gray-500">Today</button>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="inline-flex rounded-md bg-gray-100 p-1 text-[11px] font-semibold">
                            <span class="rounded bg-white px-3 py-1.5 text-gray-900 shadow-sm">Month</span>
                            <span class="px-3 py-1.5 text-gray-500">List</span>
                        </div>
                        <select disabled class="rounded-md border-gray-200 bg-white py-2 pl-3 pr-8 text-[11px] font-semibold text-gray-600">
                            <option>All properties</option>
                        </select>
                        <select disabled class="rounded-md border-gray-200 bg-white py-2 pl-3 pr-8 text-[11px] font-semibold text-gray-600">
                            <option>All entries</option>
                        </select>
                    </div>
                </div>

                @include('partials.calendar-grid')

                <div class="grid gap-4 border-t border-gray-200 bg-gray-50 px-4 py-4 text-[11px] sm:grid-cols-2">
                    <div>
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">LOCK STATE</p>
                        <div class="flex flex-wrap gap-x-5 gap-y-2 text-gray-600">
                            <span class="inline-flex items-center gap-2"><span class="h-3 w-5 rounded-sm bg-[#FF5A00]"></span>Verified and locked</span>
                            <span class="inline-flex items-center gap-2"><span class="h-3 w-5 rounded-sm border-2 border-dashed border-emerald-500 bg-white"></span>Self-reported and editable</span>
                        </div>
                    </div>
                    <div>
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">CHANNELS</p>
                        <div class="flex flex-wrap gap-x-4 gap-y-2 text-gray-600">
                            <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-[#FF5A00]"></span>Verified Shortlet</span>
                            <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-red-600"></span>Airbnb</span>
                            <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-blue-800"></span>Booking.com</span>
                            <span class="inline-flex items-center gap-1.5"><span class="size-2 rounded-full bg-emerald-500"></span>WhatsApp / walk-in</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

@include('components.calendar.booking-detail-sidebar')
</body>
</html>
