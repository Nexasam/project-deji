{{-- Property Detail Sidebar --}}
<style>
    .pds-wrapper { display:none; position:fixed; inset:0; z-index:100; overflow:hidden; }
    .pds-wrapper.pds-open { display:block; }
    .pds-backdrop { position:absolute; inset:0; background:rgba(0,0,0,0.3); opacity:0; transition:opacity .3s ease; cursor:default; }
    .pds-wrapper.pds-open .pds-backdrop { opacity:1; }
    .pds-panel { position:absolute; right:0; top:0; bottom:0; width:520px; background:#fff; display:flex; flex-direction:column; box-shadow:-4px 0 32px rgba(0,0,0,0.14); transform:translateX(100%); transition:transform .3s cubic-bezier(0.4,0,0.2,1); }
    .pds-wrapper.pds-open .pds-panel { transform:translateX(0); }
    .pds-tabs::-webkit-scrollbar { display:none; }
    .pds-row { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #F3F4F6; }
    .pds-row:last-child { border-bottom:none; }
    .pds-label { font-size:12px; color:#6B7280; font-family:'Inter',sans-serif; }
    .pds-value { font-size:13px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; }
    .pds-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; font-family:'Inter',sans-serif; }
    .pds-badge-green { background:#DCFCE7; color:#16A34A; }
    .pds-badge-orange { background:#FFF5EE; color:#FF5A00; border:1px solid #FF5A00; }
    .pds-badge-red { background:#FEF2F2; color:#DC2626; }
    .pds-badge-gray { background:#F3F4F6; color:#6B7280; }
    .pds-section { margin-bottom:20px; }
    .pds-section-title { font-size:10px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:.09em; margin:0 0 10px 0; padding-top:18px; font-family:'Inter',sans-serif; }
    .pds-card { background:#F9FAFB; border:1px solid #F3F4F6; border-radius:10px; padding:14px; margin-bottom:10px; }
    .pds-btn { padding:7px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif; border:none; }
    .pds-btn-primary { background:#FF5A00; color:#fff; }
    .pds-btn-secondary { background:#F3F4F6; color:#374151; }
</style>

<div
    class="pds-wrapper"
    :class="{ 'pds-open': propertyDetailOpen }"
    x-data="{ activeTab: 'overview' }"
    @keydown.escape.window="propertyDetailOpen = false"
    x-init="$watch('propertyDetailOpen', v => { if(v) activeTab = 'overview' })"
>
    <div class="pds-backdrop" @click="propertyDetailOpen = false"></div>

    <div class="pds-panel">

        {{-- HEADER --}}
        <div style="flex-shrink:0; padding:32px 32px 0; border-bottom:1px solid #E5E7EB;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px;">
                <div>
                    <h2 style="font-family:'Inter',sans-serif; font-size:18px; font-weight:700; color:#111827; margin:0 0 5px; line-height:1.2;">Sunset Loft, Lekki Phase 1</h2>
                    <p style="font-family:'Inter',sans-serif; font-size:12px; color:#6B7280; margin:0; font-weight:400;">Egbeda, Lagos · Serviced Apartment</p>
                </div>
                <button @click="propertyDetailOpen = false"
                    style="flex-shrink:0; margin-left:16px; width:38px; height:38px; background:#FF5A00; border:none; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                    onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'">
                    <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- TABS --}}
            <div class="pds-tabs" style="display:flex; overflow-x:auto; scrollbar-width:none; -ms-overflow-style:none; gap:0;">
                @foreach(['overview'=>'Overview','bookings'=>'Bookings','finance'=>'Finance','operations'=>'Operations','assets'=>'Assets','documents'=>'Documents','reviews'=>'Reviews','marketplace'=>'Marketplace','history'=>'History'] as $key => $label)
                <button
                    @click="activeTab = '{{ $key }}'"
                    :style="activeTab === '{{ $key }}' ? 'color:#FF5A00; font-weight:500; border-bottom:2px solid #FF5A00;' : 'color:#6B7280; font-weight:400; border-bottom:2px solid transparent;'"
                    style="flex-shrink:0; padding:0 0 12px; margin-right:22px; border-top:none; border-left:none; border-right:none; background:none; cursor:pointer; font-size:12px; font-family:'Inter',sans-serif; white-space:nowrap; transition:color .15s; letter-spacing:0.01em;">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- BODY --}}
        <div style="flex:1; overflow-y:auto; padding:28px 32px;">

            {{-- ── OVERVIEW ── --}}
            <div x-show="activeTab === 'overview'">
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:20px;">
                    <div style="background:#F0F4F8; border-radius:10px; padding:14px 12px;">
                        <div style="font-size:11px; font-weight:400; color:#6B7280; margin-bottom:8px; font-family:'Inter',sans-serif;">Occupancy</div>
                        <div style="font-size:20px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">84%</div>
                    </div>
                    <div style="background:#F0F4F8; border-radius:10px; padding:14px 12px;">
                        <div style="font-size:11px; font-weight:400; color:#6B7280; margin-bottom:8px; font-family:'Inter',sans-serif;">Revenue (30d)</div>
                        <div style="font-size:20px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦780k</div>
                    </div>
                    <div style="background:#F0F4F8; border-radius:10px; padding:14px 12px;">
                        <div style="font-size:11px; font-weight:400; color:#6B7280; margin-bottom:8px; font-family:'Inter',sans-serif;">ADR</div>
                        <div style="font-size:20px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦52,000</div>
                    </div>
                    <div style="background:#F0F4F8; border-radius:10px; padding:14px 12px;">
                        <div style="font-size:11px; font-weight:400; color:#6B7280; margin-bottom:8px; font-family:'Inter',sans-serif;">Guest rating</div>
                        <div style="font-size:20px; font-weight:700; color:#111827; font-family:'Inter',sans-serif; display:flex; align-items:center; gap:3px;">4.8 <span style="color:#FBBF24;">★</span></div>
                    </div>
                </div>
                <div style="background:#1F2937; border-radius:10px; padding:18px; margin-bottom:18px;">
                    <div style="font-size:10px; font-weight:700; color:#FF5A00; text-transform:uppercase; letter-spacing:.1em; margin-bottom:8px; font-family:'Inter',sans-serif;">✦ AI Summary</div>
                    <p style="font-size:13px; color:rgba(255,255,255,.85); line-height:1.6; margin:0; font-family:'Inter',sans-serif;">Outperforming Egbeda average by 9% this month. One open maintenance flag (AC unit) may affect upcoming check-ins if not resolved by Friday.</p>
                </div>
                <div class="pds-section-title">Operational readiness</div>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    <span class="pds-badge pds-badge-orange">Cleaning: Up-to-date</span>
                    <span class="pds-badge pds-badge-red">Maintenance: 1 Open</span>
                    <span class="pds-badge pds-badge-green">Inspection: Passed</span>
                </div>
            </div>

            {{-- ── BOOKINGS ── --}}
            <div x-show="activeTab === 'bookings'">
                <div class="pds-section-title" style="padding-top:0;">Upcoming bookings</div>
                @foreach([
                    ['guest'=>'Sarah Johnson','channel'=>'Airbnb','checkin'=>'Aug 15','checkout'=>'Aug 18','status'=>'Confirmed','nights'=>3,'amount'=>'₦156,000'],
                    ['guest'=>'Tunde Balogun','channel'=>'Verified Shortlet','checkin'=>'Aug 20','checkout'=>'Aug 23','status'=>'Confirmed','nights'=>3,'amount'=>'₦162,000'],
                    ['guest'=>'Amaka Obi','channel'=>'WhatsApp','checkin'=>'Aug 28','checkout'=>'Aug 30','status'=>'Pending','nights'=>2,'amount'=>'₦104,000'],
                ] as $b)
                <div class="pds-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                        <div>
                            <div style="font-size:13px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">{{ $b['guest'] }}</div>
                            <div style="font-size:11px; color:#6B7280; font-family:'Inter',sans-serif;">{{ $b['channel'] }}</div>
                        </div>
                        <span class="pds-badge {{ $b['status'] === 'Confirmed' ? 'pds-badge-green' : 'pds-badge-orange' }}">{{ $b['status'] }}</span>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
                        <div><div class="pds-label">Check-in</div><div class="pds-value">{{ $b['checkin'] }}</div></div>
                        <div><div class="pds-label">Check-out</div><div class="pds-value">{{ $b['checkout'] }}</div></div>
                        <div><div class="pds-label">Amount</div><div class="pds-value">{{ $b['amount'] }}</div></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ── FINANCE ── --}}
            <div x-show="activeTab === 'finance'">
                {{-- 4 metric cards --}}
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:28px;">
                    <div style="background:#F0F4F8; border-radius:10px; padding:14px 12px;">
                        <div style="font-size:11px; color:#6B7280; margin-bottom:8px; font-family:'Inter',sans-serif;">Revenue (30d)</div>
                        <div style="font-size:17px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦780,000</div>
                    </div>
                    <div style="background:#F0F4F8; border-radius:10px; padding:14px 12px;">
                        <div style="font-size:11px; color:#6B7280; margin-bottom:8px; font-family:'Inter',sans-serif;">Expenses (30d)</div>
                        <div style="font-size:17px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦145,000</div>
                    </div>
                    <div style="background:#F0F4F8; border-radius:10px; padding:14px 12px;">
                        <div style="font-size:11px; color:#6B7280; margin-bottom:8px; font-family:'Inter',sans-serif;">Net profit</div>
                        <div style="font-size:17px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦635,000</div>
                    </div>
                    <div style="background:#F0F4F8; border-radius:10px; padding:14px 12px;">
                        <div style="font-size:11px; color:#6B7280; margin-bottom:8px; font-family:'Inter',sans-serif;">Commission paid</div>
                        <div style="font-size:17px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦39,000</div>
                    </div>
                </div>

                {{-- Bar chart --}}
                <div style="margin-bottom:16px;">
                    {{-- Y-axis labels + bars --}}
                    <div style="display:flex; gap:0;">
                        {{-- Y labels --}}
                        <div style="display:flex; flex-direction:column; justify-content:space-between; padding-bottom:28px; margin-right:8px; flex-shrink:0;">
                            @foreach(['₦250k','₦200k','₦150k','₦100k','₦50k','₦0k'] as $l)
                            <div style="font-size:10px; color:#9CA3AF; font-family:'Inter',sans-serif; text-align:right;">{{ $l }}</div>
                            @endforeach
                        </div>

                        {{-- Chart area --}}
                        <div style="flex:1; position:relative;">
                            {{-- Grid lines --}}
                            @foreach([0,20,40,60,80,100] as $pct)
                            <div style="position:absolute; left:0; right:0; top:{{ $pct }}%; border-top:1px solid #F3F4F6;"></div>
                            @endforeach

                            {{-- Bars --}}
                            <div style="display:flex; align-items:flex-end; height:220px; gap:0; padding-bottom:0; position:relative; z-index:1;">
                                @foreach([
                                    ['wk'=>'Wk1','rev'=>72,'exp'=>14],
                                    ['wk'=>'Wk2','rev'=>82,'exp'=>16],
                                    ['wk'=>'Wk3','rev'=>76,'exp'=>13],
                                    ['wk'=>'Wk4','rev'=>82,'exp'=>16],
                                ] as $bar)
                                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:0;">
                                    <div style="width:100%; display:flex; align-items:flex-end; justify-content:center; gap:4px; height:210px;">
                                        <div style="width:38%; height:{{ $bar['rev'] }}%; background:#FF5A00; border-radius:3px 3px 0 0;"></div>
                                        <div style="width:22%; height:{{ $bar['exp'] }}%; background:#D1CBC0; border-radius:3px 3px 0 0;"></div>
                                    </div>
                                    <div style="font-size:11px; color:#6B7280; font-family:'Inter',sans-serif; margin-top:8px;">{{ $bar['wk'] }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div style="display:flex; justify-content:center; gap:24px; margin-top:20px;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div style="width:14px; height:14px; background:#FF5A00; border-radius:3px;"></div>
                            <span style="font-size:12px; color:#374151; font-family:'Inter',sans-serif;">Revenue</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div style="width:14px; height:14px; background:#D1CBC0; border-radius:3px; border:1px solid #9CA3AF;"></div>
                            <span style="font-size:12px; color:#374151; font-family:'Inter',sans-serif;">Expenses</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── OPERATIONS ── --}}
            <div x-show="activeTab === 'operations'">
                <table style="width:100%; border-collapse:separate; border-spacing:0; font-family:'Inter',sans-serif;">
                    {{-- Header row --}}
                    <thead>
                        <tr style="background:#F0F4F8;">
                            <th style="text-align:left; font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; padding:12px 16px; border-radius:8px 0 0 8px;">Task</th>
                            <th style="text-align:left; font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; padding:12px 12px;">Type</th>
                            <th style="text-align:left; font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; padding:12px 12px;">Assigned to</th>
                            <th style="text-align:left; font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; padding:12px 16px; border-radius:0 8px 8px 0;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([
                            ['task'=>'Post-checkout deep clean','type'=>'Cleaning',    'person'=>'Ngozi', 'status'=>'Done',        'color'=>'#16A34A'],
                            ['task'=>'AC unit repair',           'type'=>'Maintenance','person'=>'Tunde', 'status'=>'In progress', 'color'=>'#FF5A00'],
                            ['task'=>'Monthly inspection',       'type'=>'Inspection', 'person'=>'Fatima','status'=>'Done',        'color'=>'#16A34A'],
                            ['task'=>'Restock toiletries',       'type'=>'Supplies',   'person'=>'Ngozi', 'status'=>'Scheduled',   'color'=>'#DC2626'],
                        ] as $o)
                        <tr style="border-bottom:1px solid #F3F4F6;">
                            <td style="padding:16px 16px; font-size:13px; color:#111827;">{{ $o['task'] }}</td>
                            <td style="padding:16px 12px; font-size:13px; color:#374151;">{{ $o['type'] }}</td>
                            <td style="padding:16px 12px; font-size:13px; color:#374151;">{{ $o['person'] }}</td>
                            <td style="padding:16px 16px; font-size:13px; font-weight:600; color:{{ $o['color'] }};">{{ $o['status'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ── ASSETS ── --}}
            <div x-show="activeTab === 'assets'">
                <div class="pds-section-title" style="padding-top:0;">Property assets</div>
                @foreach([
                    ['name'=>'LG Split AC (2 units)','condition'=>'Fair','last'=>'Jul 2026'],
                    ['name'=>'Samsung 55" Smart TV','condition'=>'Good','last'=>'Jan 2026'],
                    ['name'=>'Refrigerator (Hisense)','condition'=>'Good','last'=>'Mar 2026'],
                    ['name'=>'Washing Machine','condition'=>'Good','last'=>'Dec 2025'],
                    ['name'=>'Generator (7.5KVA)','condition'=>'Fair','last'=>'Jun 2026'],
                    ['name'=>'Water Heater','condition'=>'Good','last'=>'Apr 2026'],
                ] as $a)
                <div class="pds-row">
                    <div>
                        <div class="pds-value" style="font-size:13px;">{{ $a['name'] }}</div>
                        <div class="pds-label">Last serviced: {{ $a['last'] }}</div>
                    </div>
                    <span class="pds-badge {{ $a['condition']==='Good' ? 'pds-badge-green' : 'pds-badge-orange' }}">{{ $a['condition'] }}</span>
                </div>
                @endforeach
            </div>

            {{-- ── DOCUMENTS ── --}}
            <div x-show="activeTab === 'documents'">
                @foreach([
                    ['name'=>'Tenancy/lease agreement.pdf',          'size'=>'2.1 MB'],
                    ['name'=>'Property inspection report — Jul 2026.pdf', 'size'=>'840 KB'],
                    ['name'=>'Insurance certificate.pdf',             'size'=>'1.4 MB'],
                ] as $d)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 0; border-bottom:1px solid #F3F4F6;">
                    <div style="display:flex; align-items:center; gap:14px;">
                        {{-- File icon --}}
                        <div style="width:38px; height:38px; background:#EEF2F7; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" fill="#1F2937"/>
                                <path d="M14 2v6h6" fill="none" stroke="#6B7280" stroke-width="1.5"/>
                                <path d="M8 13h8M8 17h5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; margin-bottom:3px;">{{ $d['name'] }}</div>
                            <div style="font-size:12px; color:#9CA3AF; font-family:'Inter',sans-serif;">{{ $d['size'] }}</div>
                        </div>
                    </div>
                    <button style="padding:8px 20px; background:#F3F4F6; border:1.5px solid #E5E7EB; border-radius:8px; font-size:13px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; cursor:pointer; white-space:nowrap;"
                        onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
                        Download
                    </button>
                </div>
                @endforeach
            </div>

            {{-- ── REVIEWS ── --}}
            <div x-show="activeTab === 'reviews'">
                @php
                $reviews = [
                    ['guest'=>'Tariye F.', 'rating'=>5, 'comment'=>'"Exactly as pictured, super clean and the host responded fast. Would book again."'],
                    ['guest'=>'Kelechi N.', 'rating'=>4, 'comment'=>'"Great location, AC was a little noisy but otherwise a solid stay."'],
                    ['guest'=>'Tariye F.', 'rating'=>3, 'comment'=>'"Exactly as pictured, super clean and the host responded fast. Would book again."'],
                ];
                @endphp
                <div style="display:flex; flex-direction:column; gap:12px;">
                    @foreach($reviews as $rv)
                    <div style="border:1.5px solid #E5E7EB; border-radius:12px; padding:18px 20px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                            <span style="font-size:14px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">{{ $rv['guest'] }}</span>
                            <span style="font-size:18px; letter-spacing:1px; line-height:1;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rv['rating'])
                                        <span style="color:#111827;">★</span>
                                    @else
                                        <span style="color:#D1D5DB;">★</span>
                                    @endif
                                @endfor
                            </span>
                        </div>
                        <p style="font-size:13px; color:#374151; font-family:'Inter',sans-serif; margin:0; line-height:1.55;">{{ $rv['comment'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── MARKETPLACE ── --}}
            <div x-show="activeTab === 'marketplace'">
                <div style="border:1.5px solid #E5E7EB; border-radius:12px; padding:22px 24px;">
                    <div style="font-size:14px; font-weight:700; color:#111827; font-family:'Inter',sans-serif; margin-bottom:10px;">
                        Marketplace listing status
                    </div>
                    <p style="font-size:13px; color:#374151; font-family:'Inter',sans-serif; line-height:1.6; margin:0 0 20px;">
                        This property is live on the Verified Shortlet marketplace with a Verified badge. 214 views this month, 12 saves.
                    </p>
                    <button
                        style="padding:11px 24px; background:#FF5A00; border:none; border-radius:999px; font-size:13px; font-weight:600; color:#fff; font-family:'Inter',sans-serif; cursor:pointer; transition:background .15s;"
                        onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'"
                    >
                        Edit history
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
