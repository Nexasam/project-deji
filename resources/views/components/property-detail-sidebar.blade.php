{{-- Property Detail Sidebar --}}
<style>
    /* ── Sidebar slide animation ── */
    .pds-wrapper {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 100;
        overflow: hidden;
    }
    .pds-wrapper.pds-open {
        display: block;
    }
    .pds-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.3);
        opacity: 0;
        transition: opacity .3s ease;
        cursor: default;
    }
    .pds-wrapper.pds-open .pds-backdrop {
        opacity: 1;
    }
    .pds-panel {
        position: absolute;
        right: 0; top: 0; bottom: 0;
        width: 520px;
        background: #fff;
        display: flex;
        flex-direction: column;
        box-shadow: -4px 0 32px rgba(0,0,0,0.14);
        transform: translateX(100%);
        transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pds-wrapper.pds-open .pds-panel {
        transform: translateX(0);
    }
    .pds-tabs::-webkit-scrollbar { display: none; }
    .pds-tab:hover { color: #374151 !important; }
</style>

<div
    class="pds-wrapper"
    :class="{ 'pds-open': propertyDetailOpen }"
    @keydown.escape.window="propertyDetailOpen = false"
>
    {{-- Backdrop --}}
    <div class="pds-backdrop" @click="propertyDetailOpen = false"></div>

    {{-- Panel --}}
    <div class="pds-panel">

        {{-- ── HEADER ── --}}
        <div style="flex-shrink:0; padding:24px 28px 0; border-bottom:1px solid #F3F4F6; background:#fff;">

            {{-- Title row --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-family:'Inter',sans-serif; font-size:20px; font-weight:700; color:#111827; line-height:1.25; margin:0 0 4px 0;">
                        Sunset Loft, Lekki Phase 1
                    </h2>
                    <p style="font-family:'Inter',sans-serif; font-size:13px; color:#6B7280; margin:0;">
                        Egbeda, Lagos · Serviced Apartment
                    </p>
                </div>
                <button
                    @click="propertyDetailOpen = false"
                    style="flex-shrink:0; margin-left:16px; width:36px; height:36px; background:#FF5A00; border:none; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s;"
                    onmouseover="this.style.background='#E64F00'"
                    onmouseout="this.style.background='#FF5A00'"
                >
                    <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Tabs --}}
            <div class="pds-tabs" style="display:flex; align-items:center; overflow-x:auto; scrollbar-width:none; -ms-overflow-style:none;">
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; margin-right:20px; border:none; border-bottom:2px solid #FF5A00; background:none; cursor:pointer; font-size:13px; font-weight:600; color:#FF5A00; font-family:'Inter',sans-serif; white-space:nowrap;">Overview</button>
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; margin-right:20px; border:none; border-bottom:2px solid transparent; background:none; cursor:pointer; font-size:13px; font-weight:500; color:#9CA3AF; font-family:'Inter',sans-serif; white-space:nowrap;">Bookings</button>
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; margin-right:20px; border:none; border-bottom:2px solid transparent; background:none; cursor:pointer; font-size:13px; font-weight:500; color:#9CA3AF; font-family:'Inter',sans-serif; white-space:nowrap;">Finance</button>
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; margin-right:20px; border:none; border-bottom:2px solid transparent; background:none; cursor:pointer; font-size:13px; font-weight:500; color:#9CA3AF; font-family:'Inter',sans-serif; white-space:nowrap;">Operations</button>
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; margin-right:20px; border:none; border-bottom:2px solid transparent; background:none; cursor:pointer; font-size:13px; font-weight:500; color:#9CA3AF; font-family:'Inter',sans-serif; white-space:nowrap;">Assets</button>
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; margin-right:20px; border:none; border-bottom:2px solid transparent; background:none; cursor:pointer; font-size:13px; font-weight:500; color:#9CA3AF; font-family:'Inter',sans-serif; white-space:nowrap;">Documents</button>
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; margin-right:20px; border:none; border-bottom:2px solid transparent; background:none; cursor:pointer; font-size:13px; font-weight:500; color:#9CA3AF; font-family:'Inter',sans-serif; white-space:nowrap;">Reviews</button>
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; margin-right:20px; border:none; border-bottom:2px solid transparent; background:none; cursor:pointer; font-size:13px; font-weight:500; color:#9CA3AF; font-family:'Inter',sans-serif; white-space:nowrap;">Marketplace</button>
                <button class="pds-tab" style="flex-shrink:0; padding:0 0 12px; border:none; border-bottom:2px solid transparent; background:none; cursor:pointer; font-size:13px; font-weight:500; color:#9CA3AF; font-family:'Inter',sans-serif; white-space:nowrap;">History</button>
            </div>
        </div>

        {{-- ── BODY ── --}}
        <div style="flex:1; overflow-y:auto; padding:24px 28px;">

            {{-- Key Metrics --}}
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:20px;">
                <div style="background:#F9FAFB; border:1px solid #F3F4F6; border-radius:10px; padding:12px;">
                    <div style="font-size:10px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.06em; margin-bottom:7px; font-family:'Inter',sans-serif;">Occupancy</div>
                    <div style="font-size:20px; font-weight:700; color:#111827; line-height:1; font-family:'Inter',sans-serif;">84%</div>
                </div>
                <div style="background:#F9FAFB; border:1px solid #F3F4F6; border-radius:10px; padding:12px;">
                    <div style="font-size:10px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.06em; margin-bottom:7px; font-family:'Inter',sans-serif;">Revenue (30d)</div>
                    <div style="font-size:20px; font-weight:700; color:#111827; line-height:1; font-family:'Inter',sans-serif;">₦780k</div>
                </div>
                <div style="background:#F9FAFB; border:1px solid #F3F4F6; border-radius:10px; padding:12px;">
                    <div style="font-size:10px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.06em; margin-bottom:7px; font-family:'Inter',sans-serif;">ADR</div>
                    <div style="font-size:20px; font-weight:700; color:#111827; line-height:1; font-family:'Inter',sans-serif;">₦52,000</div>
                </div>
                <div style="background:#F9FAFB; border:1px solid #F3F4F6; border-radius:10px; padding:12px;">
                    <div style="font-size:10px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.06em; margin-bottom:7px; font-family:'Inter',sans-serif;">G. rating</div>
                    <div style="font-size:20px; font-weight:700; color:#111827; line-height:1; display:flex; align-items:center; gap:4px; font-family:'Inter',sans-serif;">
                        4.8
                        <svg width="13" height="13" fill="#FBBF24" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- AI Property Summary --}}
            <div style="background:#1F2937; border-radius:12px; padding:20px; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <svg width="13" height="13" fill="#FF5A00" viewBox="0 0 20 20">
                        <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"/>
                    </svg>
                    <span style="font-size:10px; font-weight:700; color:#FF5A00; text-transform:uppercase; letter-spacing:.1em; font-family:'Inter',sans-serif;">AI Property Summary</span>
                </div>
                <p style="font-size:13px; color:rgba(255,255,255,.85); line-height:1.65; margin:0; font-family:'Inter',sans-serif;">
                    Bluewater Suite 4B is outperforming the Egbeda location average by 9% in occupancy this month, driven mostly by repeat bookings. One open maintenance flag (AC unit) is at risk of affecting upcoming check-ins if not resolved by Friday.
                </p>
            </div>

            {{-- Operational Readiness --}}
            <div>
                <h3 style="font-size:14px; font-weight:600; color:#111827; margin:0 0 12px 0; font-family:'Inter',sans-serif;">Operational readiness</h3>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    <span style="display:inline-block; padding:6px 14px; border-radius:999px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#FF5A00; border:1.5px solid #FF5A00; background:#FFF5EE; font-family:'Inter',sans-serif;">
                        Cleaning: Up-to-date
                    </span>
                    <span style="display:inline-block; padding:6px 14px; border-radius:999px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#FF5A00; border:1.5px solid #FF5A00; background:#FFF5EE; font-family:'Inter',sans-serif;">
                        Maintenance: 1 Open
                    </span>
                    <span style="display:inline-block; padding:6px 14px; border-radius:999px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#FF5A00; border:1.5px solid #FF5A00; background:#FFF5EE; font-family:'Inter',sans-serif;">
                        Inspection: Passed 28 Jul
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>
