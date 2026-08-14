<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200">
                <th class="px-3 py-3 text-xs font-semibold text-gray-600 text-center">MON</th>
                <th class="px-3 py-3 text-xs font-semibold text-gray-600 text-center">TUE</th>
                <th class="px-3 py-3 text-xs font-semibold text-gray-600 text-center">WED</th>
                <th class="px-3 py-3 text-xs font-semibold text-gray-600 text-center">THU</th>
                <th class="px-3 py-3 text-xs font-semibold text-gray-600 text-center">FRI</th>
                <th class="px-3 py-3 text-xs font-semibold text-gray-600 text-center">SAT</th>
                <th class="px-3 py-3 text-xs font-semibold text-gray-600 text-center">SUN</th>
            </tr>
        </thead>
        <tbody>

            {{-- Week 1 (Jul 27 - Aug 2) --}}
            <tr class="border-b border-gray-200">
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200" style="height:120px;"><div class="text-xs text-gray-400 mb-1">27</div></td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">28</div></td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">29</div></td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">30</div></td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">31</div></td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-1">1</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
                <td class="p-2 align-top">
                    <div class="text-xs text-gray-900 mb-1">2</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
            </tr>

            {{-- Week 2 (Aug 3-9) --}}
            <tr class="border-b border-gray-200">
                <td class="p-2 align-top border-r border-gray-200" style="height:120px;">
                    <div class="text-xs text-gray-900 mb-1">3</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">4</div>
                    <div class="space-y-1">
                        @php $b1 = json_encode(['dayLabel'=>'Tuesday','dateLabel'=>'Tuesday, 4 August','bookings'=>[['property'=>'Bluewater Suite 4B','source'=>'Verified Shortlet · Targe Hubare','type'=>'locked','note'=>'🔒 System confirmed via Verified Shortlet. Not editable — this reflects a real, confirmed booking.'],['property'=>'Sunset Apartment','source'=>'WhatsApp · Marie Ilmaire','type'=>'self','note'=>'📲 Entered manually. WhatsApp has no booking API to sync, even in Pro.']]]) @endphp
                        <button class="w-full text-left px-2 py-1 bg-[#FF5A00] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                            @click="openBooking({{ $b1 }})">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                            <span>VS · Bluewater</span>
                        </button>
                        <button class="w-full text-left px-2 py-0.5 bg-white border-2 border-dashed border-[#10B981] text-gray-700 text-[10px] rounded flex items-center gap-1 cursor-pointer hover:opacity-80"
                            @click="openBooking({{ $b1 }})">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            <span>WA · Emerald</span>
                        </button>
                    </div>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-1">5</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">6</div>
                    @php $b6 = json_encode(['dayLabel'=>'Thursday','dateLabel'=>'Thursday, 6 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Booking confirmed','type'=>'locked','note'=>'🔒 Confirmed via Airbnb iCal sync. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#1E40AF] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b6 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Abg · Emerald</span>
                    </button>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-1">7</div>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">8</div>
                    @php $b8 = json_encode(['dayLabel'=>'Saturday','dateLabel'=>'Saturday, 8 August','bookings'=>[['property'=>'Bluewater Suite 4B','source'=>'Verified Shortlet · Confirmed','type'=>'locked','note'=>'🔒 System confirmed via Verified Shortlet. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#FF5A00] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b8 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>VS · Bluewater</span>
                    </button>
                </td>
                <td class="p-2 align-top">
                    <div class="text-xs text-gray-900 mb-2">9</div>
                    @php $b9 = json_encode(['dayLabel'=>'Sunday','dateLabel'=>'Sunday, 9 August','bookings'=>[['property'=>'Highrise Apartment','source'=>'Booking.com · Verified','type'=>'locked','note'=>'🔒 Confirmed via Booking.com. Not editable.'],['property'=>'Bluewater Suite 4B','source'=>'Walk-in · Self entered','type'=>'self','note'=>'✏️ Walk-in guest. Entered manually — you can edit or remove this block.']]]) @endphp
                    <div class="space-y-1">
                        <button class="w-full text-left px-2 py-1 bg-[#EA580C] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                            @click="openBooking({{ $b9 }})">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                            <span>VS · Highrise</span>
                        </button>
                        <button class="w-full text-left px-2 py-0.5 bg-white border-2 border-dashed border-gray-400 text-gray-700 text-[10px] rounded flex items-center gap-1 cursor-pointer hover:opacity-80"
                            @click="openBooking({{ $b9 }})">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                            <span>Walk in · Bluewater</span>
                        </button>
                    </div>
                </td>
            </tr>

            {{-- Week 3 (Aug 10-16) --}}
            <tr class="border-b border-gray-200">
                <td class="p-2 align-top border-r border-gray-200" style="height:120px;">
                    <div class="text-xs text-gray-900 mb-2">10</div>
                    @php $b10 = json_encode(['dayLabel'=>'Monday','dateLabel'=>'Monday, 10 August','bookings'=>[['property'=>'Sunset Loft','source'=>'Airbnb · Tunde Balogun','type'=>'locked','note'=>'🔒 Confirmed via Airbnb. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#DC2626] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b10 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Air · Sunset</span>
                    </button>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-1">11</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">12</div>
                    @php $b12 = json_encode(['dayLabel'=>'Wednesday','dateLabel'=>'Wednesday, 12 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Airbnb.'],['property'=>'Highrise Apartment','source'=>'Booking.com · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Booking.com.']]]) @endphp
                    <div class="space-y-1">
                        <button class="w-full text-left px-2 py-1 bg-[#1E40AF] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                            @click="openBooking({{ $b12 }})">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                            <span>Abg · Emerald</span>
                        </button>
                        <button class="w-full text-left px-2 py-1 bg-[#EA580C] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                            @click="openBooking({{ $b12 }})">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                            <span>Blg · Highrise</span>
                        </button>
                    </div>
                </td>
                <td class="p-2 align-top border-r border-gray-200"><div class="text-xs text-gray-900 mb-1">13</div></td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">14</div>
                    @php $b14 = json_encode(['dayLabel'=>'Friday','dateLabel'=>'Friday, 14 August','bookings'=>[['property'=>'Highrise Apartment','source'=>'Verified Shortlet · Confirmed','type'=>'locked','note'=>'🔒 System confirmed via Verified Shortlet. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#FF5A00] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b14 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>VS · Highrise</span>
                    </button>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">15</div>
                    @php $b15 = json_encode(['dayLabel'=>'Saturday','dateLabel'=>'Saturday, 15 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Akin Falade','type'=>'locked','note'=>'🔒 Confirmed via Airbnb iCal. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#DC2626] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b15 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Air · Emerald</span>
                    </button>
                </td>
                <td class="p-2 align-top">
                    <div class="text-xs text-gray-900 mb-1">16</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
            </tr>

            {{-- Week 4 (Aug 17-23) --}}
            <tr class="border-b border-gray-200">
                <td class="p-2 align-top border-r border-gray-200" style="height:120px;"><div class="text-xs text-gray-900 mb-1">17</div></td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">18</div>
                    @php $b18 = json_encode(['dayLabel'=>'Tuesday','dateLabel'=>'Tuesday, 18 August','bookings'=>[['property'=>'Sapphire Residences','source'=>'WhatsApp · Self entered','type'=>'self','note'=>'📲 Entered via WhatsApp manually. You can edit or remove this block.'],['property'=>'Highrise Apartment','source'=>'Booking.com · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Booking.com.']]]) @endphp
                    <div class="space-y-1">
                        <button class="w-full text-left px-2 py-0.5 bg-white border-2 border-dashed border-[#10B981] text-gray-700 text-[10px] rounded flex items-center gap-1 cursor-pointer hover:opacity-80"
                            @click="openBooking({{ $b18 }})">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            <span>WA · Sapres</span>
                        </button>
                        <button class="w-full text-left px-2 py-1 bg-[#EA580C] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                            @click="openBooking({{ $b18 }})">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                            <span>Blg · Highrise</span>
                        </button>
                    </div>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-1">19</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">20</div>
                    @php $b20 = json_encode(['dayLabel'=>'Thursday','dateLabel'=>'Thursday, 20 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Booking confirmed','type'=>'locked','note'=>'🔒 Confirmed via Airbnb iCal sync. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#1E40AF] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b20 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Abg · Emerald</span>
                    </button>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-1">21</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">22</div>
                    @php $b22 = json_encode(['dayLabel'=>'Saturday','dateLabel'=>'Saturday, 22 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Airbnb. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#DC2626] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b22 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Air · Emerald</span>
                    </button>
                </td>
                <td class="p-2 align-top">
                    <div class="text-xs text-gray-900 mb-1">23</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
            </tr>

            {{-- Week 5 (Aug 24-30) --}}
            <tr class="border-b border-gray-200">
                <td class="p-2 align-top border-r border-gray-200" style="height:120px;">
                    <div class="text-xs text-gray-900 mb-2">24</div>
                    @php $b24 = json_encode(['dayLabel'=>'Monday','dateLabel'=>'Monday, 24 August','bookings'=>[['property'=>'Highrise Apartment','source'=>'Verified Shortlet · Confirmed','type'=>'locked','note'=>'🔒 System confirmed via Verified Shortlet. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#FF5A00] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b24 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>VS · Highrise</span>
                    </button>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">25</div>
                    @php $b25 = json_encode(['dayLabel'=>'Tuesday','dateLabel'=>'Tuesday, 25 August','bookings'=>[['property'=>'Highrise Apartment','source'=>'Booking.com · Confirmed','type'=>'locked','note'=>'🔒 Confirmed via Booking.com iCal. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#EA580C] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b25 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Blg · Highrise</span>
                    </button>
                </td>
                <td class="p-2 align-top border-r border-gray-200"><div class="text-xs text-gray-900 mb-1">26</div></td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">27</div>
                    @php $b27 = json_encode(['dayLabel'=>'Thursday','dateLabel'=>'Thursday, 27 August','bookings'=>[['property'=>'Bluewater Suite 4B','source'=>'Walk-in · Self entered','type'=>'self','note'=>'✏️ Walk-in guest entered manually. You can edit or remove this block.']]]) @endphp
                    <button class="w-full text-left px-2 py-0.5 bg-white border-2 border-dashed border-gray-400 text-gray-700 text-[10px] rounded flex items-center gap-1 cursor-pointer hover:opacity-80"
                        @click="openBooking({{ $b27 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Walkin · Bluewater</span>
                    </button>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-2">28</div>
                    @php $b28 = json_encode(['dayLabel'=>'Friday','dateLabel'=>'Friday, 28 August','bookings'=>[['property'=>'Emerald Suites','source'=>'Airbnb · Oba Martins','type'=>'locked','note'=>'🔒 Confirmed via Airbnb. Not editable.']]]) @endphp
                    <button class="w-full text-left px-2 py-1 bg-[#DC2626] text-white text-[10px] font-bold rounded flex items-center gap-1 cursor-pointer hover:opacity-90"
                        @click="openBooking({{ $b28 }})">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        <span>Oba · Emerald</span>
                    </button>
                </td>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-1">29</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
                <td class="p-2 align-top"><div class="text-xs text-gray-900 mb-1">30</div></td>
            </tr>

            {{-- Week 6 (Aug 31 - Sep 6) --}}
            <tr>
                <td class="p-2 align-top border-r border-gray-200">
                    <div class="text-xs text-gray-900 mb-1">31</div>
                    <div class="text-xs text-gray-400">Open</div>
                </td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">1</div></td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">2</div></td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">3</div></td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">4</div></td>
                <td class="p-2 align-top bg-gray-50 border-r border-gray-200"><div class="text-xs text-gray-400 mb-1">5</div></td>
                <td class="p-2 align-top bg-gray-50"><div class="text-xs text-gray-400 mb-1">6</div></td>
            </tr>

        </tbody>
    </table>
</div>
