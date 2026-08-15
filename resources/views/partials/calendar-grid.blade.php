<style>
    .cal-table { width:100%; border-collapse:collapse; }
    .cal-table th { padding:8px 4px; font-size:11px; font-weight:600; color:#6B7280; text-align:center; border-bottom:1px solid #E5E7EB; }
    .cal-table td { padding:6px 4px; vertical-align:top; border-right:1px solid #F3F4F6; border-bottom:1px solid #F3F4F6; min-width:40px; height:90px; }
    .cal-table td:last-child { border-right:none; }
    .cal-table tr:last-child td { border-bottom:none; }
    .cal-table .day-num { font-size:11px; font-weight:600; color:#111827; margin-bottom:4px; line-height:1; }
    .cal-table .day-num-dim { font-size:11px; color:#D1D5DB; margin-bottom:4px; }
    .cal-chip { display:flex; align-items:center; gap:3px; width:100%; text-align:left; padding:3px 5px; border-radius:4px; font-size:9px; font-weight:700; margin-bottom:3px; cursor:pointer; border:none; line-height:1.2; word-break:break-all; }
    .cal-chip svg { flex-shrink:0; }
    @media (min-width: 640px) {
        .cal-table th { padding:10px 8px; font-size:12px; }
        .cal-table td { padding:8px 6px; height:110px; min-width:60px; }
        .cal-chip { font-size:10px; padding:4px 6px; }
    }
    @media (min-width: 768px) {
        .cal-table td { height:120px; }
    }
</style>

<div class="overflow-x-auto">
    <table class="cal-table">
        <thead>
            <tr>
                <th><span class="sm:hidden">M</span><span class="hidden sm:inline">MON</span></th>
                <th><span class="sm:hidden">T</span><span class="hidden sm:inline">TUE</span></th>
                <th><span class="sm:hidden">W</span><span class="hidden sm:inline">WED</span></th>
                <th><span class="sm:hidden">T</span><span class="hidden sm:inline">THU</span></th>
                <th><span class="sm:hidden">F</span><span class="hidden sm:inline">FRI</span></th>
                <th><span class="sm:hidden">S</span><span class="hidden sm:inline">SAT</span></th>
                <th><span class="sm:hidden">S</span><span class="hidden sm:inline">SUN</span></th>
            </tr>
        </thead>
        <tbody>

            {{-- Week 1 (Jul 27 – Aug 2) --}}
            <tr>
                <td class="bg-gray-50"><div class="day-num-dim">27</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">28</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">29</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">30</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">31</div></td>
                <td><div class="day-num">1</div><div class="text-[10px] text-gray-400">Open</div></td>
                <td><div class="day-num">2</div><div class="text-[10px] text-gray-400">Open</div></td>
            </tr>

            {{-- Week 2 (Aug 3–9) --}}
            <tr>
                <td><div class="day-num">3</div><div class="text-[10px] text-gray-400">Open</div></td>
                <td>
                    <div class="day-num">4</div>
                    @php $b1 = json_encode(['dayLabel'=>'Tuesday','dateLabel'=>'Tuesday, 4 August','bookings'=>[['property'=>'Bluewater Suite 4B','source'=>'Verified Shortlet · Targe Hubare','type'=>'locked','note'=>'🔒 System confirmed via Verified Shortlet. Not editable — this reflects a real, confirmed booking.'],['property'=>'Sunset Apartment','source'=>'WhatsApp · Marie Ilmaire','type'=>'self','note'=>'📲 Entered manually. WhatsApp has no booking API to sync, even in Pro.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#FF5A00" @click="openBooking({{ $b1 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>VS·Blue</span>
                    </button>
                    <button class="cal-chip text-gray-700" style="background:#fff; border:2px dashed #10B981;" @click="openBooking({{ $b1 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                        <span>WA·Emr</span>
                    </button>
                </td>
                <td><div class="day-num">5</div><div class="text-[10px] text-gray-400">Open</div></td>
                <td>
                    <div class="day-num">6</div>
                    @php $b6 = json_encode(['dayLabel'=>'Thursday','dateLabel'=>'Thursday, 6 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Booking confirmed','type'=>'locked','note'=>'🔒 Confirmed via Airbnb iCal sync. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#1E40AF" @click="openBooking({{ $b6 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Abg·Emr</span>
                    </button>
                </td>
                <td><div class="day-num">7</div></td>
                <td>
                    <div class="day-num">8</div>
                    @php $b8 = json_encode(['dayLabel'=>'Saturday','dateLabel'=>'Saturday, 8 August','bookings'=>[['property'=>'Bluewater Suite 4B','source'=>'Verified Shortlet · Confirmed','type'=>'locked','note'=>'🔒 System confirmed via Verified Shortlet. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#FF5A00" @click="openBooking({{ $b8 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>VS·Blue</span>
                    </button>
                </td>
                <td>
                    <div class="day-num">9</div>
                    @php $b9 = json_encode(['dayLabel'=>'Sunday','dateLabel'=>'Sunday, 9 August','bookings'=>[['property'=>'Highrise Apartment','source'=>'Booking.com · Verified','type'=>'locked','note'=>'🔒 Confirmed via Booking.com. Not editable.'],['property'=>'Bluewater Suite 4B','source'=>'Walk-in · Self entered','type'=>'self','note'=>'✏️ Walk-in guest. Entered manually — you can edit or remove this block.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#EA580C" @click="openBooking({{ $b9 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Blg·Hi</span>
                    </button>
                    <button class="cal-chip text-gray-700" style="background:#fff; border:2px dashed #9CA3AF;" @click="openBooking({{ $b9 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>WI·Blue</span>
                    </button>
                </td>
            </tr>

            {{-- Week 3 (Aug 10–16) --}}
            <tr>
                <td>
                    <div class="day-num">10</div>
                    @php $b10 = json_encode(['dayLabel'=>'Monday','dateLabel'=>'Monday, 10 August','bookings'=>[['property'=>'Sunset Loft','source'=>'Airbnb · Tunde Balogun','type'=>'locked','note'=>'🔒 Confirmed via Airbnb. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#DC2626" @click="openBooking({{ $b10 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Air·Sun</span>
                    </button>
                </td>
                <td><div class="day-num">11</div><div class="text-[10px] text-gray-400">Open</div></td>
                <td>
                    <div class="day-num">12</div>
                    @php $b12 = json_encode(['dayLabel'=>'Wednesday','dateLabel'=>'Wednesday, 12 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Airbnb.'],['property'=>'Highrise Apartment','source'=>'Booking.com · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Booking.com.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#1E40AF" @click="openBooking({{ $b12 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Abg·Emr</span>
                    </button>
                    <button class="cal-chip text-white" style="background:#EA580C" @click="openBooking({{ $b12 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Blg·Hi</span>
                    </button>
                </td>
                <td><div class="day-num">13</div></td>
                <td>
                    <div class="day-num">14</div>
                    @php $b14 = json_encode(['dayLabel'=>'Friday','dateLabel'=>'Friday, 14 August','bookings'=>[['property'=>'Highrise Apartment','source'=>'Verified Shortlet · Confirmed','type'=>'locked','note'=>'🔒 System confirmed via Verified Shortlet. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#FF5A00" @click="openBooking({{ $b14 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>VS·Hi</span>
                    </button>
                </td>
                <td>
                    <div class="day-num">15</div>
                    @php $b15 = json_encode(['dayLabel'=>'Saturday','dateLabel'=>'Saturday, 15 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Akin Falade','type'=>'locked','note'=>'🔒 Confirmed via Airbnb iCal. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#DC2626" @click="openBooking({{ $b15 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Air·Emr</span>
                    </button>
                </td>
                <td><div class="day-num">16</div><div class="text-[10px] text-gray-400">Open</div></td>
            </tr>

            {{-- Week 4 (Aug 17–23) --}}
            <tr>
                <td><div class="day-num">17</div></td>
                <td>
                    <div class="day-num">18</div>
                    @php $b18 = json_encode(['dayLabel'=>'Tuesday','dateLabel'=>'Tuesday, 18 August','bookings'=>[['property'=>'Sapphire Residences','source'=>'WhatsApp · Self entered','type'=>'self','note'=>'📲 Entered via WhatsApp manually. You can edit or remove this block.'],['property'=>'Highrise Apartment','source'=>'Booking.com · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Booking.com.']]]) @endphp
                    <button class="cal-chip text-gray-700" style="background:#fff; border:2px dashed #10B981;" @click="openBooking({{ $b18 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                        <span>WA·Sap</span>
                    </button>
                    <button class="cal-chip text-white" style="background:#EA580C" @click="openBooking({{ $b18 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Blg·Hi</span>
                    </button>
                </td>
                <td><div class="day-num">19</div><div class="text-[10px] text-gray-400">Open</div></td>
                <td>
                    <div class="day-num">20</div>
                    @php $b20 = json_encode(['dayLabel'=>'Thursday','dateLabel'=>'Thursday, 20 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Booking confirmed','type'=>'locked','note'=>'🔒 Confirmed via Airbnb iCal sync. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#1E40AF" @click="openBooking({{ $b20 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Abg·Emr</span>
                    </button>
                </td>
                <td><div class="day-num">21</div><div class="text-[10px] text-gray-400">Open</div></td>
                <td>
                    <div class="day-num">22</div>
                    @php $b22 = json_encode(['dayLabel'=>'Saturday','dateLabel'=>'Saturday, 22 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Airbnb. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#DC2626" @click="openBooking({{ $b22 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Air·Emr</span>
                    </button>
                </td>
                <td><div class="day-num">23</div><div class="text-[10px] text-gray-400">Open</div></td>
            </tr>

            {{-- Week 5 (Aug 24–30) --}}
            <tr>
                <td>
                    <div class="day-num">24</div>
                    @php $b24 = json_encode(['dayLabel'=>'Monday','dateLabel'=>'Monday, 24 August','bookings'=>[['property'=>'Highrise Apartment','source'=>'Verified Shortlet · Confirmed','type'=>'locked','note'=>'🔒 System confirmed via Verified Shortlet. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#FF5A00" @click="openBooking({{ $b24 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>VS·Hi</span>
                    </button>
                </td>
                <td>
                    <div class="day-num">25</div>
                    @php $b25 = json_encode(['dayLabel'=>'Tuesday','dateLabel'=>'Tuesday, 25 August','bookings'=>[['property'=>'Highrise Apartment','source'=>'Booking.com · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Booking.com iCal. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#EA580C" @click="openBooking({{ $b25 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Blg·Hi</span>
                    </button>
                </td>
                <td><div class="day-num">26</div></td>
                <td>
                    <div class="day-num">27</div>
                    @php $b27 = json_encode(['dayLabel'=>'Thursday','dateLabel'=>'Thursday, 27 August','bookings'=>[['property'=>'Bluewater Suite 4B','source'=>'Walk-in · Self entered','type'=>'self','note'=>'✏️ Walk-in guest entered manually. You can edit or remove this block.']]]) @endphp
                    <button class="cal-chip text-gray-700" style="background:#fff; border:2px dashed #9CA3AF;" @click="openBooking({{ $b27 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>WI·Blue</span>
                    </button>
                </td>
                <td>
                    <div class="day-num">28</div>
                    @php $b28 = json_encode(['dayLabel'=>'Friday','dateLabel'=>'Friday, 28 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Oba Martins','type'=>'locked','note'=>'🔒 Confirmed via Airbnb. Not editable.']]]) @endphp
                    <button class="cal-chip text-white" style="background:#DC2626" @click="openBooking({{ $b28 }})">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Oba·Emr</span>
                    </button>
                </td>
                <td><div class="day-num">29</div><div class="text-[10px] text-gray-400">Open</div></td>
                <td><div class="day-num">30</div></td>
            </tr>

            {{-- Week 6 (Aug 31 – Sep 6) --}}
            <tr>
                <td><div class="day-num">31</div><div class="text-[10px] text-gray-400">Open</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">1</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">2</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">3</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">4</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">5</div></td>
                <td class="bg-gray-50"><div class="day-num-dim">6</div></td>
            </tr>

        </tbody>
    </table>
</div>
