{{-- Property Detail Sidebar --}}
<style>
    .pds-wrapper { display:none; position:fixed; inset:0; z-index:100; overflow:hidden; }
    .pds-wrapper.pds-open { display:block; }
    .pds-backdrop { position:absolute; inset:0; background:rgba(0,0,0,0.3); opacity:0; transition:opacity .3s ease; cursor:default; }
    .pds-wrapper.pds-open .pds-backdrop { opacity:1; }

    /* Panel: full-width on mobile, 520px on md+ */
    .pds-panel {
        position:absolute; right:0; top:0; bottom:0;
        width:100%;
        background:#fff;
        display:flex; flex-direction:column;
        box-shadow:-4px 0 32px rgba(0,0,0,0.14);
        transform:translateX(100%);
        transition:transform .3s cubic-bezier(0.4,0,0.2,1);
    }
    @media (min-width: 640px) {
        .pds-panel { width: 520px; }
    }
    .pds-wrapper.pds-open .pds-panel { transform:translateX(0); }

    .pds-tabs::-webkit-scrollbar { display:none; }
    .pds-row { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #F3F4F6; }
    .pds-row:last-child { border-bottom:none; }
    .pds-label { font-size:12px; color:#6B7280; font-family:'Inter',sans-serif; }
    .pds-value { font-size:13px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; }
    .pds-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; font-family:'Inter',sans-serif; white-space:nowrap; }
    .pds-badge-green  { background:#DCFCE7; color:#16A34A; }
    .pds-badge-orange { background:#FFF5EE; color:#FF5A00; border:1px solid #FF5A00; }
    .pds-badge-red    { background:#FEF2F2; color:#DC2626; }
    .pds-badge-gray   { background:#F3F4F6; color:#6B7280; }
    .pds-section { margin-bottom:20px; }
    .pds-section-title { font-size:10px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:.09em; margin:0 0 10px 0; padding-top:18px; font-family:'Inter',sans-serif; }
    .pds-card { background:#F9FAFB; border:1px solid #F3F4F6; border-radius:10px; padding:14px; margin-bottom:10px; }
    .pds-btn { padding:7px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif; border:none; }
    .pds-btn-primary   { background:#FF5A00; color:#fff; }
    .pds-btn-secondary { background:#F3F4F6; color:#374151; }

    /* Metric grids: 2-col on mobile, 4-col on sm+ */
    .pds-metrics-4 { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:20px; }
    @media (min-width: 480px) { .pds-metrics-4 { grid-template-columns:repeat(4,1fr); } }

    /* Operations table: scrollable on mobile */
    .pds-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .pds-table-wrap table { min-width:380px; }
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
        <div style="flex-shrink:0; padding:20px 20px 0; border-bottom:1px solid #E5E7EB;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px; gap:12px;">
                <div style="min-width:0;">
                    <h2 style="font-family:'Inter',sans-serif; font-size:16px; font-weight:700; color:#111827; margin:0 0 4px; line-height:1.3; word-break:break-word;">Sunset Loft, Lekki Phase 1</h2>
                    <p style="font-family:'Inter',sans-serif; font-size:12px; color:#6B7280; margin:0; font-weight:400;">Egbeda, Lagos · Serviced Apartment</p>
                </div>
                <button @click="propertyDetailOpen = false"
                    style="flex-shrink:0; width:36px; height:36px; background:#FF5A00; border:none; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                    onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'">
                    <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- TABS --}}
            <div class="pds-tabs" style="display:flex; overflow-x:auto; scrollbar-width:none; -ms-overflow-style:none; gap:0;">
                @foreach(['overview'=>'Overview','bookings'=>'Bookings','finance'=>'Finance','operations'=>'Ops','assets'=>'Assets','documents'=>'Docs','reviews'=>'Reviews','marketplace'=>'Market','history'=>'History'] as $key => $label)
                <button
                    @click="activeTab = '{{ $key }}'"
                    :style="activeTab === '{{ $key }}' ? 'color:#FF5A00; font-weight:600; border-bottom:2px solid #FF5A00;' : 'color:#6B7280; font-weight:400; border-bottom:2px solid transparent;'"
                    style="flex-shrink:0; padding:0 0 12px; margin-right:16px; border-top:none; border-left:none; border-right:none; background:none; cursor:pointer; font-size:12px; font-family:'Inter',sans-serif; white-space:nowrap; transition:color .15s;">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- BODY --}}
        <div style="flex:1; overflow-y:auto; padding:20px;">

            {{-- ── OVERVIEW ── --}}
            <div x-show="activeTab === 'overview'">
                <div class="pds-metrics-4">
                    @foreach([['Occupancy','84%'],['Revenue (30d)','₦780k'],['ADR','₦52,000'],['Rating','4.8 ★']] as [$label,$val])
                    <div style="background:#F0F4F8; border-radius:10px; padding:12px;">
                        <div style="font-size:11px; color:#6B7280; margin-bottom:6px; font-family:'Inter',sans-serif;">{{ $label }}</div>
                        <div style="font-size:18px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>

                <div style="background:#1F2937; border-radius:10px; padding:16px; margin-bottom:16px;">
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
                    ['guest'=>'Sarah Johnson', 'channel'=>'Airbnb',             'checkin'=>'Aug 15','checkout'=>'Aug 18','status'=>'Confirmed','amount'=>'₦156,000'],
                    ['guest'=>'Tunde Balogun', 'channel'=>'Verified Shortlet',  'checkin'=>'Aug 20','checkout'=>'Aug 23','status'=>'Confirmed','amount'=>'₦162,000'],
                    ['guest'=>'Amaka Obi',     'channel'=>'WhatsApp',           'checkin'=>'Aug 28','checkout'=>'Aug 30','status'=>'Pending',  'amount'=>'₦104,000'],
                ] as $b)
                <div class="pds-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; gap:8px;">
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
                <div class="pds-metrics-4" style="margin-bottom:20px;">
                    @foreach([['Revenue (30d)','₦780,000'],['Expenses (30d)','₦145,000'],['Net profit','₦635,000'],['Commission','₦39,000']] as [$label,$val])
                    <div style="background:#F0F4F8; border-radius:10px; padding:12px;">
                        <div style="font-size:11px; color:#6B7280; margin-bottom:6px; font-family:'Inter',sans-serif;">{{ $label }}</div>
                        <div style="font-size:16px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>

                {{-- Bar chart --}}
                <div style="display:flex; gap:0; margin-bottom:8px;">
                    <div style="display:flex; flex-direction:column; justify-content:space-between; padding-bottom:28px; margin-right:8px; flex-shrink:0;">
                        @foreach(['₦250k','₦200k','₦150k','₦100k','₦50k','₦0k'] as $l)
                        <div style="font-size:10px; color:#9CA3AF; font-family:'Inter',sans-serif; text-align:right;">{{ $l }}</div>
                        @endforeach
                    </div>
                    <div style="flex:1; position:relative;">
                        @foreach([0,20,40,60,80,100] as $pct)
                        <div style="position:absolute; left:0; right:0; top:{{ $pct }}%; border-top:1px solid #F3F4F6;"></div>
                        @endforeach
                        <div style="display:flex; align-items:flex-end; height:180px; position:relative; z-index:1;">
                            @foreach([['Wk1',72,14],['Wk2',82,16],['Wk3',76,13],['Wk4',82,16]] as [$wk,$rev,$exp])
                            <div style="flex:1; display:flex; flex-direction:column; align-items:center;">
                                <div style="width:100%; display:flex; align-items:flex-end; justify-content:center; gap:3px; height:170px;">
                                    <div style="width:36%; height:{{ $rev }}%; background:#FF5A00; border-radius:3px 3px 0 0;"></div>
                                    <div style="width:22%; height:{{ $exp }}%; background:#D1CBC0; border-radius:3px 3px 0 0;"></div>
                                </div>
                                <div style="font-size:11px; color:#6B7280; font-family:'Inter',sans-serif; margin-top:6px;">{{ $wk }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div style="display:flex; justify-content:center; gap:20px; margin-top:16px;">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <div style="width:12px; height:12px; background:#FF5A00; border-radius:3px;"></div>
                        <span style="font-size:12px; color:#374151; font-family:'Inter',sans-serif;">Revenue</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <div style="width:12px; height:12px; background:#D1CBC0; border-radius:3px; border:1px solid #9CA3AF;"></div>
                        <span style="font-size:12px; color:#374151; font-family:'Inter',sans-serif;">Expenses</span>
                    </div>
                </div>
            </div>

            {{-- ── OPERATIONS ── --}}
            <div x-show="activeTab === 'operations'">
                <div class="pds-table-wrap">
                    <table style="width:100%; border-collapse:separate; border-spacing:0; font-family:'Inter',sans-serif;">
                        <thead>
                            <tr style="background:#F0F4F8;">
                                <th style="text-align:left; font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; padding:10px 14px; border-radius:8px 0 0 8px; white-space:nowrap;">Task</th>
                                <th style="text-align:left; font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; padding:10px 12px; white-space:nowrap;">Type</th>
                                <th style="text-align:left; font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; padding:10px 12px; white-space:nowrap;">Assigned</th>
                                <th style="text-align:left; font-size:11px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; padding:10px 14px; border-radius:0 8px 8px 0; white-space:nowrap;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([
                                ['Post-checkout deep clean','Cleaning',   'Ngozi', 'Done',        '#16A34A'],
                                ['AC unit repair',          'Maintenance','Tunde', 'In progress',  '#FF5A00'],
                                ['Monthly inspection',      'Inspection', 'Fatima','Done',         '#16A34A'],
                                ['Restock toiletries',      'Supplies',   'Ngozi', 'Scheduled',    '#DC2626'],
                            ] as [$task,$type,$person,$status,$color])
                            <tr style="border-bottom:1px solid #F3F4F6;">
                                <td style="padding:14px; font-size:13px; color:#111827;">{{ $task }}</td>
                                <td style="padding:14px 12px; font-size:13px; color:#374151; white-space:nowrap;">{{ $type }}</td>
                                <td style="padding:14px 12px; font-size:13px; color:#374151;">{{ $person }}</td>
                                <td style="padding:14px; font-size:13px; font-weight:600; color:{{ $color }}; white-space:nowrap;">{{ $status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── ASSETS ── --}}
            <div x-show="activeTab === 'assets'">
                <div class="pds-section-title" style="padding-top:0;">Property assets</div>
                @foreach([
                    ['LG Split AC (2 units)',   'Fair','Jul 2026'],
                    ['Samsung 55" Smart TV',    'Good','Jan 2026'],
                    ['Refrigerator (Hisense)',  'Good','Mar 2026'],
                    ['Washing Machine',         'Good','Dec 2025'],
                    ['Generator (7.5KVA)',      'Fair','Jun 2026'],
                    ['Water Heater',            'Good','Apr 2026'],
                ] as [$name,$cond,$last])
                <div class="pds-row">
                    <div style="min-width:0; margin-right:8px;">
                        <div class="pds-value" style="font-size:13px; word-break:break-word;">{{ $name }}</div>
                        <div class="pds-label">Last serviced: {{ $last }}</div>
                    </div>
                    <span class="pds-badge {{ $cond === 'Good' ? 'pds-badge-green' : 'pds-badge-orange' }}">{{ $cond }}</span>
                </div>
                @endforeach
            </div>

            {{-- ── DOCUMENTS ── --}}
            <div x-show="activeTab === 'documents'">
                @foreach([
                    ['Tenancy/lease agreement.pdf',                 '2.1 MB'],
                    ['Property inspection report — Jul 2026.pdf',   '840 KB'],
                    ['Insurance certificate.pdf',                   '1.4 MB'],
                ] as [$name,$size])
                <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 0; border-bottom:1px solid #F3F4F6; gap:12px;">
                    <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                        <div style="width:36px; height:36px; background:#EEF2F7; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" fill="#1F2937"/>
                                <path d="M14 2v6h6" fill="none" stroke="#6B7280" stroke-width="1.5"/>
                                <path d="M8 13h8M8 17h5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-size:12px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; word-break:break-word; line-height:1.4;">{{ $name }}</div>
                            <div style="font-size:11px; color:#9CA3AF; font-family:'Inter',sans-serif; margin-top:2px;">{{ $size }}</div>
                        </div>
                    </div>
                    <button style="padding:7px 16px; background:#F3F4F6; border:1.5px solid #E5E7EB; border-radius:8px; font-size:12px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; cursor:pointer; white-space:nowrap; flex-shrink:0;"
                        onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
                        Download
                    </button>
                </div>
                @endforeach
            </div>

            {{-- ── REVIEWS ── --}}
            <div x-show="activeTab === 'reviews'">
                <div style="display:flex; flex-direction:column; gap:12px;">
                    @foreach([
                        ['Tariye F.',  5, '"Exactly as pictured, super clean and the host responded fast. Would book again."'],
                        ['Kelechi N.', 4, '"Great location, AC was a little noisy but otherwise a solid stay."'],
                        ['Amaka O.',   3, '"Room was decent but the check-in process was a bit slow."'],
                    ] as [$guest,$rating,$comment])
                    <div style="border:1.5px solid #E5E7EB; border-radius:12px; padding:16px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; gap:8px;">
                            <span style="font-size:13px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">{{ $guest }}</span>
                            <span style="font-size:16px; letter-spacing:1px; line-height:1; flex-shrink:0;">
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="color:{{ $i <= $rating ? '#111827' : '#D1D5DB' }};">★</span>
                                @endfor
                            </span>
                        </div>
                        <p style="font-size:13px; color:#374151; font-family:'Inter',sans-serif; margin:0; line-height:1.55;">{{ $comment }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── MARKETPLACE ── --}}
            <div x-show="activeTab === 'marketplace'">
                <div style="border:1.5px solid #E5E7EB; border-radius:12px; padding:20px;">
                    <div style="font-size:14px; font-weight:700; color:#111827; font-family:'Inter',sans-serif; margin-bottom:10px;">Marketplace listing status</div>
                    <p style="font-size:13px; color:#374151; font-family:'Inter',sans-serif; line-height:1.6; margin:0 0 20px;">
                        This property is live on the Verified Shortlet marketplace with a Verified badge. 214 views this month, 12 saves.
                    </p>
                    <button
                        style="padding:10px 24px; background:#FF5A00; border:none; border-radius:999px; font-size:13px; font-weight:600; color:#fff; font-family:'Inter',sans-serif; cursor:pointer;"
                        onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'">
                        Edit listing
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
