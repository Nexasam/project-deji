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
    .pds-section-title { font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:.07em; margin-bottom:10px; font-family:'Inter',sans-serif; }
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
        <div style="flex-shrink:0; padding:28px 28px 0; border-bottom:1px solid #F3F4F6;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-family:'Inter',sans-serif; font-size:16px; font-weight:700; color:#111827; margin:0 0 5px; line-height:1.3;">Sunset Loft, Lekki Phase 1</h2>
                    <p style="font-family:'Inter',sans-serif; font-size:12px; color:#9CA3AF; margin:0;">Egbeda, Lagos · Serviced Apartment</p>
                </div>
                <button @click="propertyDetailOpen = false"
                    style="flex-shrink:0; margin-left:14px; width:34px; height:34px; background:#FF5A00; border:none; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                    onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'">
                    <svg width="13" height="13" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- TABS --}}
            <div class="pds-tabs" style="display:flex; overflow-x:auto; scrollbar-width:none; -ms-overflow-style:none; gap:0;">
                @foreach(['overview'=>'Overview','bookings'=>'Bookings','finance'=>'Finance','operations'=>'Operations','assets'=>'Assets','documents'=>'Documents','reviews'=>'Reviews','marketplace'=>'Marketplace'] as $key => $label)
                <button
                    @click="activeTab = '{{ $key }}'"
                    :style="activeTab === '{{ $key }}' ? 'border-bottom:2px solid #FF5A00; color:#FF5A00; font-weight:600;' : 'border-bottom:2px solid transparent; color:#9CA3AF; font-weight:500;'"
                    style="flex-shrink:0; padding:0 0 14px; margin-right:22px; border-top:none; border-left:none; border-right:none; background:none; cursor:pointer; font-size:12px; font-family:'Inter',sans-serif; white-space:nowrap; transition:color .15s; letter-spacing:.01em;">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- BODY --}}
        <div style="flex:1; overflow-y:auto; padding:22px 24px;">

            {{-- ── OVERVIEW ── --}}
            <div x-show="activeTab === 'overview'">
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:18px;">
                    <div class="pds-card" style="padding:10px;">
                        <div style="font-size:10px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; font-family:'Inter',sans-serif;">Occupancy</div>
                        <div style="font-size:20px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">84%</div>
                    </div>
                    <div class="pds-card" style="padding:10px;">
                        <div style="font-size:10px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; font-family:'Inter',sans-serif;">Revenue</div>
                        <div style="font-size:20px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦780k</div>
                    </div>
                    <div class="pds-card" style="padding:10px;">
                        <div style="font-size:10px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; font-family:'Inter',sans-serif;">ADR</div>
                        <div style="font-size:20px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦52k</div>
                    </div>
                    <div class="pds-card" style="padding:10px;">
                        <div style="font-size:10px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; font-family:'Inter',sans-serif;">Rating</div>
                        <div style="font-size:20px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">4.8 ★</div>
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
                <div class="pds-section-title">Upcoming bookings</div>
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
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:18px;">
                    <div class="pds-card">
                        <div class="pds-label" style="margin-bottom:4px;">Monthly Revenue</div>
                        <div style="font-size:22px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦780,000</div>
                        <div style="font-size:11px; color:#16A34A; margin-top:3px; font-family:'Inter',sans-serif;">↑ 12% vs last month</div>
                    </div>
                    <div class="pds-card">
                        <div class="pds-label" style="margin-bottom:4px;">Expenses</div>
                        <div style="font-size:22px; font-weight:700; color:#111827; font-family:'Inter',sans-serif;">₦142,000</div>
                        <div style="font-size:11px; color:#DC2626; margin-top:3px; font-family:'Inter',sans-serif;">↑ 3% vs last month</div>
                    </div>
                </div>
                <div class="pds-section-title">Recent transactions</div>
                @foreach([
                    ['desc'=>'Airbnb payout','date'=>'Aug 12','amount'=>'+₦156,000','type'=>'in'],
                    ['desc'=>'Cleaning service','date'=>'Aug 11','amount'=>'-₦18,000','type'=>'out'],
                    ['desc'=>'VS payout','date'=>'Aug 8','amount'=>'+₦162,000','type'=>'in'],
                    ['desc'=>'Maintenance (AC)','date'=>'Aug 7','amount'=>'-₦45,000','type'=>'out'],
                    ['desc'=>'Booking.com payout','date'=>'Aug 3','amount'=>'+₦104,000','type'=>'in'],
                ] as $t)
                <div class="pds-row">
                    <div>
                        <div class="pds-value" style="font-size:13px;">{{ $t['desc'] }}</div>
                        <div class="pds-label">{{ $t['date'] }}</div>
                    </div>
                    <div style="font-size:13px; font-weight:700; color:{{ $t['type']==='in' ? '#16A34A' : '#DC2626' }}; font-family:'Inter',sans-serif;">{{ $t['amount'] }}</div>
                </div>
                @endforeach
            </div>

            {{-- ── OPERATIONS ── --}}
            <div x-show="activeTab === 'operations'">
                <div class="pds-section-title">Open tasks</div>
                @foreach([
                    ['task'=>'AC unit repair','priority'=>'High','due'=>'Aug 15','status'=>'Open'],
                    ['task'=>'Deep cleaning scheduled','priority'=>'Medium','due'=>'Aug 14','status'=>'Scheduled'],
                    ['task'=>'Replace shower curtain','priority'=>'Low','due'=>'Aug 20','status'=>'Open'],
                ] as $o)
                <div class="pds-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div style="font-size:13px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; margin-bottom:3px;">{{ $o['task'] }}</div>
                            <div class="pds-label">Due: {{ $o['due'] }}</div>
                        </div>
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                            <span class="pds-badge {{ $o['priority']==='High' ? 'pds-badge-red' : ($o['priority']==='Medium' ? 'pds-badge-orange' : 'pds-badge-gray') }}">{{ $o['priority'] }}</span>
                            <span class="pds-badge pds-badge-gray" style="font-size:10px;">{{ $o['status'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="pds-section-title" style="margin-top:16px;">Cleaning schedule</div>
                <div class="pds-row"><span class="pds-label">Last cleaned</span><span class="pds-value">Aug 10, 2026</span></div>
                <div class="pds-row"><span class="pds-label">Next scheduled</span><span class="pds-value">Aug 14, 2026</span></div>
                <div class="pds-row"><span class="pds-label">Assigned to</span><span class="pds-value">CleanPro Lagos</span></div>
            </div>

            {{-- ── ASSETS ── --}}
            <div x-show="activeTab === 'assets'">
                <div class="pds-section-title">Property assets</div>
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
                <div class="pds-section-title">Property documents</div>
                @foreach([
                    ['name'=>'Certificate of Occupancy','type'=>'Legal','date'=>'Jan 2024','icon'=>'📄'],
                    ['name'=>'Lease Agreement 2026','type'=>'Contract','date'=>'Mar 2026','icon'=>'📋'],
                    ['name'=>'Insurance Policy','type'=>'Insurance','date'=>'Feb 2026','icon'=>'🛡️'],
                    ['name'=>'Inspection Report Q2','type'=>'Report','date'=>'Jun 2026','icon'=>'🔍'],
                    ['name'=>'Property Photos Pack','type'=>'Media','date'=>'Apr 2026','icon'=>'📷'],
                ] as $d)
                <div class="pds-row">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:20px;">{{ $d['icon'] }}</span>
                        <div>
                            <div class="pds-value" style="font-size:13px;">{{ $d['name'] }}</div>
                            <div class="pds-label">{{ $d['type'] }} · {{ $d['date'] }}</div>
                        </div>
                    </div>
                    <button class="pds-btn pds-btn-secondary" style="font-size:11px; padding:5px 12px;">View</button>
                </div>
                @endforeach
            </div>

            {{-- ── REVIEWS ── --}}
            <div x-show="activeTab === 'reviews'">
                <div style="display:flex; align-items:center; gap:16px; margin-bottom:18px;">
                    <div style="text-align:center;">
                        <div style="font-size:36px; font-weight:800; color:#111827; font-family:'Inter',sans-serif; line-height:1;">4.8</div>
                        <div style="color:#FBBF24; font-size:16px; margin:2px 0;">★★★★★</div>
                        <div class="pds-label">24 reviews</div>
                    </div>
                    <div style="flex:1;">
                        @foreach([['label'=>'Cleanliness','val'=>96],['label'=>'Accuracy','val'=>92],['label'=>'Communication','val'=>98],['label'=>'Value','val'=>88]] as $r)
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                            <div class="pds-label" style="width:100px; flex-shrink:0;">{{ $r['label'] }}</div>
                            <div style="flex:1; background:#F3F4F6; border-radius:999px; height:6px;">
                                <div style="width:{{ $r['val'] }}%; height:6px; background:#FF5A00; border-radius:999px;"></div>
                            </div>
                            <div style="font-size:12px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; width:30px;">{{ $r['val'] }}%</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="pds-section-title">Recent reviews</div>
                @foreach([
                    ['guest'=>'Sarah J.','rating'=>5,'date'=>'Aug 10','comment'=>'Fantastic stay! Very clean and exactly as described. Will definitely return.'],
                    ['guest'=>'Tunde B.','rating'=>5,'date'=>'Jul 28','comment'=>'Great location, responsive host. AC was a bit slow but overall excellent.'],
                    ['guest'=>'Amaka O.','rating'=>4,'date'=>'Jul 15','comment'=>'Nice apartment, comfortable beds. Minor issue with hot water but was fixed quickly.'],
                ] as $rv)
                <div class="pds-card">
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <div style="font-size:13px; font-weight:600; color:#111827; font-family:'Inter',sans-serif;">{{ $rv['guest'] }}</div>
                        <div style="font-size:12px; color:#FBBF24;">{{ str_repeat('★', $rv['rating']) }}</div>
                    </div>
                    <div class="pds-label" style="margin-bottom:6px;">{{ $rv['date'] }}</div>
                    <p style="font-size:13px; color:#374151; font-family:'Inter',sans-serif; margin:0; line-height:1.5;">{{ $rv['comment'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- ── MARKETPLACE ── --}}
            <div x-show="activeTab === 'marketplace'">
                <div class="pds-section-title">Listing status</div>
                @foreach([
                    ['platform'=>'Verified Shortlet','status'=>'Live','price'=>'₦52,000/night','icon'=>'🟠'],
                    ['platform'=>'Airbnb','status'=>'Live','price'=>'₦55,000/night','icon'=>'🔴'],
                    ['platform'=>'Booking.com','status'=>'Live','price'=>'₦50,000/night','icon'=>'🔵'],
                    ['platform'=>'WhatsApp Direct','status'=>'Active','price'=>'₦48,000/night','icon'=>'🟢'],
                ] as $m)
                <div class="pds-row">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:18px;">{{ $m['icon'] }}</span>
                        <div>
                            <div class="pds-value" style="font-size:13px;">{{ $m['platform'] }}</div>
                            <div class="pds-label">{{ $m['price'] }}</div>
                        </div>
                    </div>
                    <span class="pds-badge pds-badge-green">{{ $m['status'] }}</span>
                </div>
                @endforeach
                <div class="pds-section-title" style="margin-top:18px;">Promotions</div>
                <div class="pds-card">
                    <div style="font-size:13px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; margin-bottom:4px;">Weekend discount — 10% off</div>
                    <div class="pds-label">Active · Expires Aug 31, 2026</div>
                </div>
                <div class="pds-card">
                    <div style="font-size:13px; font-weight:600; color:#111827; font-family:'Inter',sans-serif; margin-bottom:4px;">Early bird — 7% off (7+ days)</div>
                    <div class="pds-label">Active · No expiry</div>
                </div>
            </div>

        </div>
    </div>
</div>
