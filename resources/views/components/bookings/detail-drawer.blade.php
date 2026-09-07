{{-- Booking Detail Drawer --}}
{{-- Requires parent x-data with: bookingDrawerOpen (bool), selectedBookingDetail (obj) --}}

<div
    x-show="bookingDrawerOpen"
    x-cloak
    class="fixed inset-0 z-[300]"
    @keydown.escape.window="bookingDrawerOpen = false"
    style="display:none;"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/30"
        @click="bookingDrawerOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    {{-- Panel --}}
    <div
        class="absolute right-0 top-0 bottom-0 bg-white flex flex-col shadow-2xl"
        style="width:min(560px,100vw); font-family:'Inter',sans-serif;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @click.stop
        x-data="{
            tab: 'overview',
            msg: '',
            init() {
                this.$watch('bookingDrawerOpen', v => { if (v) this.tab = 'overview'; });
            }
        }"
    >
        {{-- ── Fixed header ── --}}
        <div class="flex-shrink-0 border-b border-gray-100">

            {{-- Title row --}}
            <div class="flex items-start justify-between px-5 pt-5 pb-3">
                <div>
                    <h2 class="text-[17px] font-bold text-gray-900 leading-snug"
                        x-text="selectedBookingDetail?.guest ?? 'Booking Details'"></h2>
                    <p class="text-[12px] text-gray-400 mt-0.5"
                       x-text="(selectedBookingDetail?.id ?? '') + ' · ' + (selectedBookingDetail?.property ?? '')"></p>
                </div>
                <button @click="bookingDrawerOpen = false"
                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#FF5A00] hover:bg-[#E64F00] transition-colors flex-shrink-0 ml-3 mt-0.5">
                    <svg width="13" height="13" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Badges row --}}
            <div class="flex items-center gap-2 px-5 pb-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold"
                    :class="{
                        'bg-[#FF385C] text-white': selectedBookingDetail?.channel_style === 'airbnb',
                        'bg-[#003580] text-white': selectedBookingDetail?.channel_style === 'bookingcom',
                        'bg-[#FF5A00] text-white': selectedBookingDetail?.channel_style === 'direct-walkin' || selectedBookingDetail?.channel_style === 'direct-phonein',
                        'bg-[#25D366] text-white': selectedBookingDetail?.channel_style === 'whatsapp',
                        'bg-gray-500 text-white':  selectedBookingDetail?.channel_style === 'manual',
                        'bg-gray-200 text-gray-700': !selectedBookingDetail?.channel_style,
                    }"
                    x-text="selectedBookingDetail?.channel ?? 'Channel'"></span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold"
                    :class="{
                        'bg-amber-100 text-amber-700':     selectedBookingDetail?.status === 'Pending',
                        'bg-blue-100 text-blue-700':       selectedBookingDetail?.status === 'Confirmed',
                        'bg-emerald-100 text-emerald-700': selectedBookingDetail?.status === 'Completed',
                        'bg-red-100 text-red-500':         selectedBookingDetail?.status === 'Cancelled',
                    }"
                    x-text="selectedBookingDetail?.status ?? 'Status'"></span>
            </div>

            {{-- Tab bar --}}
            <div class="flex overflow-x-auto px-5 gap-0" style="scrollbar-width:none;">
                @foreach([
                    'overview'      => 'Overview',
                    'guest'         => 'Guest',
                    'property'      => 'Property',
                    'payments'      => 'Payments',
                    'invoices'      => 'Invoices',
                    'communication' => 'Communication',
                    'tasks'         => 'Tasks',
                    'reviews'       => 'Reviews',
                    'history'       => 'History',
                ] as $key => $label)
                <button
                    @click="tab = '{{ $key }}'"
                    class="flex-shrink-0 px-3 py-2.5 text-[13px] font-medium border-b-2 transition-colors whitespace-nowrap -mb-px"
                    :class="tab === '{{ $key }}'
                        ? 'border-[#FF5A00] text-[#FF5A00] font-semibold'
                        : 'border-transparent text-gray-500 hover:text-gray-700'"
                >{{ $label }}</button>
                @endforeach
            </div>
        </div>

        {{-- ── Scrollable tab body ── --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 min-h-0">

            {{-- OVERVIEW --}}
            <div x-show="tab === 'overview'">
                <div class="bd-ai-block">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 rounded-full bg-[#FF5A00]"></span>
                        <span class="text-[11px] font-bold tracking-widest uppercase text-gray-400">AI Insights</span>
                    </div>
                    <p class="text-[13px] text-gray-300 leading-relaxed mb-4">
                        Bluewater Suite 4B is outperforming the Egbeda location average by 9% in occupancy this month, driven mostly by repeat bookings. One open maintenance flag (AC unit) is at risk of affecting upcoming check-ins if not resolved by Friday.
                    </p>
                    <div class="space-y-3">
                        <div class="bd-insight-card">
                            <div class="flex items-center gap-2 mb-1.5">
                                <svg width="12" height="12" fill="#f59e0b" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <span class="text-[11px] font-semibold text-gray-300">Risk assessment — Low</span>
                            </div>
                            <p class="text-[12px] text-gray-400 mb-2.5">Repeat-city guest, verified device, no dispute history on Airbnb.</p>
                            <div class="h-1.5 rounded-full bg-gray-700 overflow-hidden">
                                <div class="h-full rounded-full bg-[#FF5A00]" style="width:20%"></div>
                            </div>
                        </div>
                        <div class="bd-insight-card">
                            <div class="flex items-center gap-2 mb-1.5">
                                <svg width="12" height="12" fill="#a78bfa" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                <span class="text-[11px] font-semibold text-gray-300">Suggested next step</span>
                            </div>
                            <p class="text-[12px] text-gray-400">Consider offering a late-checkout upsell — this listing has 68% acceptance on similar bookings.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GUEST --}}
            <div x-show="tab === 'guest'">
                <p class="bd-section-label">Guest identity</p>
                <div class="bd-info-table">
                    <div class="bd-info-row">
                        <span class="bd-info-key">Name</span>
                        <span class="bd-info-val" x-text="selectedBookingDetail?.guest ?? '—'"></span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-key">Phone</span>
                        <span class="bd-info-val">+234 810 552 9910</span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-key">Email</span>
                        <span class="bd-info-val">chineduobi@hotmail.com</span>
                    </div>
                </div>
                <div class="mt-4 border border-gray-200 rounded-xl px-4 py-3 text-[13px] text-gray-400 italic">
                    ID verification not yet submitted.
                </div>
            </div>

            {{-- PROPERTY --}}
            <div x-show="tab === 'property'">
                <p class="bd-section-label">Property details</p>
                <div class="bd-info-table">
                    <div class="bd-info-row">
                        <span class="bd-info-key">Property</span>
                        <span class="bd-info-val" x-text="selectedBookingDetail?.property ?? '—'"></span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-key">Location</span>
                        <span class="bd-info-val" x-text="selectedBookingDetail?.location ?? '—'"></span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-key">Check-in / out</span>
                        <span class="bd-info-val" x-text="selectedBookingDetail?.dates ?? '—'"></span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-key">Duration</span>
                        <span class="bd-info-val" x-text="selectedBookingDetail?.nights ?? '—'"></span>
                    </div>
                </div>
            </div>

            {{-- PAYMENTS --}}
            <div x-show="tab === 'payments'">
                <p class="bd-section-label">Charge breakdown</p>
                <div class="bd-info-table">
                    <div class="bd-info-row">
                        <span class="bd-info-key">Nightly rate</span>
                        <span class="bd-info-val">₦41,000 × 2 nights</span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-key">Cleaning fee</span>
                        <span class="bd-info-val">₦5,000</span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-key">Platform fee (5%)</span>
                        <span class="bd-info-val text-red-500">−₦4,100</span>
                    </div>
                    <div class="bd-info-row" style="border-top:1px solid #e5e7eb;">
                        <span class="bd-info-key font-semibold text-gray-800">Total charged</span>
                        <span class="bd-info-val font-bold text-gray-900" x-text="selectedBookingDetail?.amount ?? '—'"></span>
                    </div>
                    <div class="bd-info-row">
                        <span class="bd-info-key">Payment status</span>
                        <span class="bd-info-val font-semibold"
                            :class="selectedBookingDetail?.paid === 'Paid in full'
                                ? 'text-emerald-600'
                                : selectedBookingDetail?.paid === 'Unpaid'
                                    ? 'text-red-500'
                                    : 'text-amber-600'"
                            x-text="selectedBookingDetail?.paid ?? '—'"></span>
                    </div>
                </div>
            </div>

            {{-- INVOICES --}}
            <div x-show="tab === 'invoices'">
                <p class="bd-section-label">Invoices</p>
                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-10 h-10 bg-gray-200 rounded-xl flex-shrink-0 flex items-center justify-center">
                        <svg width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-semibold text-gray-900">INV-10001</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Jul 15, 2026 · ₦180,000 · Paid</p>
                    </div>
                    <button class="text-[12px] font-semibold text-[#FF5A00] hover:underline flex-shrink-0">Download</button>
                </div>
            </div>

            {{-- COMMUNICATION --}}
            <div x-show="tab === 'communication'" class="flex flex-col h-full">
                <p class="bd-section-label">Message thread</p>
                <div class="space-y-3 mb-5">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 text-[12px] font-bold text-gray-500">G</div>
                        <div class="bg-gray-100 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%]">
                            <p class="text-[11px] font-bold text-gray-600 mb-1">Guest</p>
                            <p class="text-[13px] text-gray-700">Hi! Excited for our stay, is early check-in possible?</p>
                            <p class="text-[11px] text-gray-400 mt-1.5 text-right">Jul 20, 10:02am</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 justify-end">
                        <div class="bg-orange-50 rounded-2xl rounded-tr-sm px-4 py-3 max-w-[85%]">
                            <p class="text-[13px] text-gray-700">Hi Ngozi — early check-in from 12pm works, no charge. See you soon!</p>
                            <p class="text-[11px] text-gray-400 mt-1.5 text-right">Jul 20, 11:02am</p>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-gray-100 flex-shrink-0 flex items-center justify-center">
                            <svg width="14" height="14" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                    </div>
                </div>
                {{-- Compose --}}
                <div class="flex items-end gap-2 mt-4">
                    <textarea x-model="msg" placeholder="Type a message…" rows="2"
                        class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-[13px] text-gray-900 outline-none resize-none focus:border-gray-400 placeholder-gray-300 transition-colors"
                    ></textarea>
                    <button class="w-11 h-11 rounded-full bg-[#FF5A00] hover:bg-[#E64F00] flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </div>
            </div>

            {{-- TASKS --}}
            <div x-show="tab === 'tasks'">
                <p class="bd-section-label">Linked tasks for this stay</p>
                <div class="mb-4">
                    <div class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wide flex-shrink-0">Done</span>
                        <span class="text-[13px] text-gray-700 flex-1">Pre-arrival cleaning</span>
                        <span class="text-[12px] text-gray-400 flex-shrink-0">Jul 31, 2026</span>
                    </div>
                </div>
                <button class="w-full py-3 border border-gray-200 rounded-xl text-[13px] font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                    + Log an issue / task for this booking
                </button>
            </div>

            {{-- REVIEWS --}}
            <div x-show="tab === 'reviews'">
                <div class="flex items-center justify-between mb-4">
                    <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg text-[12px] font-medium text-gray-600 hover:border-gray-300 transition-colors">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                        All dates
                        <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <button class="text-[12px] font-semibold text-[#FF5A00] hover:underline flex items-center gap-1">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </button>
                </div>

                <div class="space-y-3">
                    @foreach([
                        [
                            'initials'  => 'TF',
                            'name'      => 'Tariye F.',
                            'avatar'    => null,
                            'date'      => '18th July 2026 · 3:28PM',
                            'stars'     => 5,
                            'quote'     => '"Exactly as pictured, super clean and the host responded fast. Would book again."',
                            'reply'     => 'Thank you so much, Fatima — you were a pleasure to host. Welcome back anytime!',
                        ],
                        [
                            'initials'  => 'ST',
                            'name'      => 'Samson T',
                            'avatar'    => 'samson',
                            'date'      => '18th July 2026 · 3:28PM',
                            'stars'     => 5,
                            'quote'     => '" Beautiful apartment, spotless, and the host was very responsive. Would book again."',
                            'reply'     => 'Thank you so much, Samson — you were a pleasure to host. Welcome back anytime!',
                        ],
                    ] as $review)
                    <div class="border border-gray-150 rounded-2xl p-4 bg-white">
                        {{-- Reviewer row --}}
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex items-center gap-3">
                                {{-- Avatar --}}
                                <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-[13px] font-bold
                                    {{ $review['avatar'] ? 'overflow-hidden bg-gray-300' : 'bg-[#FF5A00] text-white' }}">
                                    @if($review['avatar'])
                                        {{-- placeholder person icon for avatar --}}
                                        <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    @else
                                        {{ $review['initials'] }}
                                    @endif
                                </div>
                                <div>
                                    <p class="text-[13px] font-bold text-gray-900 leading-tight">{{ $review['name'] }}</p>
                                    {{-- Stars --}}
                                    <div class="flex items-center gap-0.5 mt-0.5">
                                        @for($s = 0; $s < $review['stars']; $s++)
                                        <svg width="12" height="12" fill="#FF5A00" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <span class="text-[11px] text-gray-400 flex-shrink-0 mt-1">{{ $review['date'] }}</span>
                        </div>

                        {{-- Quote --}}
                        <p class="text-[13px] text-gray-700 leading-relaxed mb-3">{{ $review['quote'] }}</p>

                        {{-- Host reply --}}
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-[11px] font-bold text-gray-500 mb-1">Your reply</p>
                            <p class="text-[13px] text-gray-600 leading-relaxed">{{ $review['reply'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- HISTORY --}}
            <div x-show="tab === 'history'">
                <div class="flex items-center justify-between mb-4">
                    <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg text-[12px] font-medium text-gray-600 hover:border-gray-300 transition-colors">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                        All dates
                        <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <button class="text-[12px] font-semibold text-[#FF5A00] hover:underline flex items-center gap-1">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </button>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach([
                        ['date' => '5 Aug', 'event' => 'Maintenance task created — AC unit'],
                        ['date' => '6 Aug', 'event' => 'Booking confirmed — Tariye Fubara, 3 nights'],
                        ['date' => '8 Aug', 'event' => 'Inspection Passed'],
                    ] as $entry)
                    <div class="flex items-start gap-6 py-3.5">
                        <span class="text-[12px] font-semibold text-gray-400 w-12 flex-shrink-0">{{ $entry['date'] }}</span>
                        <span class="text-[13px] text-gray-700">{{ $entry['event'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                    <p class="text-[12px] text-gray-400">Showing <span class="font-semibold text-gray-600">3</span> of <span class="font-semibold text-gray-600">20</span> history</p>
                    <div class="flex items-center gap-1">
                        <button class="w-6 h-6 flex items-center justify-center rounded border border-gray-200 text-gray-400 opacity-40" disabled>
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button class="w-6 h-6 flex items-center justify-center rounded border border-gray-200 text-gray-400 hover:text-gray-600">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>{{-- end scrollable body --}}

        {{-- ── Sticky footer ── --}}
        <div class="flex-shrink-0 border-t border-gray-100 px-5 py-4">
            {{-- Reviews tab: just a Close button --}}
            <div x-show="tab === 'reviews'">
                <button @click="bookingDrawerOpen = false"
                    class="w-full py-3 rounded-xl bg-gray-900 hover:bg-black text-[14px] font-bold text-white transition-colors">
                    Close
                </button>
            </div>
            {{-- All other tabs: Decline + Confirm --}}
            <div x-show="tab !== 'reviews'" class="grid grid-cols-2 gap-3">
                <button class="py-3 rounded-xl border-2 border-red-200 text-[14px] font-bold text-red-500 hover:bg-red-50 transition-colors">
                    Decline
                </button>
                <button class="py-3 rounded-xl bg-[#FF5A00] hover:bg-[#E64F00] text-[14px] font-bold text-white transition-colors"
                    x-text="selectedBookingDetail?.status === 'Confirmed' ? 'Mark completed' : 'Confirming booking'">
                </button>
            </div>
        </div>

    </div>{{-- end panel --}}
</div>

<style>
.bd-ai-block   { background:#111827; border-radius:14px; padding:20px; }
.bd-insight-card { background:#1f2937; border-radius:10px; padding:14px 16px; }
.bd-section-label { font-size:10px; font-weight:800; letter-spacing:.09em; text-transform:uppercase; color:#9ca3af; margin-bottom:12px; display:block; }
.bd-info-table { background:#f9fafb; border:1px solid #f3f4f6; border-radius:14px; overflow:hidden; }
.bd-info-row   { display:flex; align-items:center; justify-content:space-between; padding:11px 16px; border-bottom:1px solid #f3f4f6; gap:12px; }
.bd-info-row:last-child { border-bottom:none; }
.bd-info-key   { font-size:13px; color:#6b7280; font-weight:400; flex-shrink:0; }
.bd-info-val   { font-size:13px; font-weight:600; color:#111827; text-align:right; }
</style>
