{{-- Booking Detail Sidebar --}}
<style>
    .bds-wrapper {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 100;
        overflow: hidden;
    }
    .bds-wrapper.bds-open { display: block; }

    .bds-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.25);
        opacity: 0;
        transition: opacity .3s ease;
        cursor: default;
    }
    .bds-wrapper.bds-open .bds-backdrop { opacity: 1; }

    .bds-panel {
        position: absolute;
        right: 0; top: 0; bottom: 0;
        width: 420px;
        background: #fff;
        display: flex;
        flex-direction: column;
        box-shadow: -4px 0 24px rgba(0,0,0,0.1);
        transform: translateX(100%);
        transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto;
    }
    .bds-wrapper.bds-open .bds-panel { transform: translateX(0); }

    .bds-locked-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: #FF5A00; color: #fff;
        font-size: 10px; font-weight: 700;
        padding: 4px 10px; border-radius: 6px;
        text-transform: uppercase; letter-spacing: .06em;
        font-family: 'Inter', sans-serif;
    }
    .bds-self-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: #fff; color: #16A34A;
        font-size: 10px; font-weight: 700;
        padding: 3px 9px; border-radius: 6px;
        border: 1.5px dashed #16A34A;
        text-transform: uppercase; letter-spacing: .06em;
        font-family: 'Inter', sans-serif;
    }
    .bds-info-box {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12px;
        color: #1D4ED8;
        font-family: 'Inter', sans-serif;
        line-height: 1.5;
    }
    .bds-info-box.orange {
        background: #FFF7ED;
        border-color: #FED7AA;
        color: #9A3412;
    }
    .bds-btn-edit {
        padding: 7px 18px; border-radius: 7px;
        border: 1.5px solid #D1D5DB; background: #fff;
        font-size: 12px; font-weight: 600; color: #374151;
        font-family: 'Inter', sans-serif; cursor: pointer;
        transition: background .15s;
    }
    .bds-btn-edit:hover { background: #F9FAFB; }
    .bds-btn-remove {
        padding: 7px 18px; border-radius: 7px;
        border: 1.5px solid #FECACA; background: #FEF2F2;
        font-size: 12px; font-weight: 600; color: #DC2626;
        font-family: 'Inter', sans-serif; cursor: pointer;
        transition: background .15s;
    }
    .bds-btn-remove:hover { background: #FEE2E2; }
</style>

<div
    class="bds-wrapper"
    :class="{ 'bds-open': bookingDetailOpen }"
    @keydown.escape.window="bookingDetailOpen = false"
>
    <div class="bds-backdrop" @click="bookingDetailOpen = false"></div>

    <div class="bds-panel" x-show="selectedBooking">

        {{-- Header --}}
        <div style="padding: 22px 24px 18px; border-bottom: 1px solid #F3F4F6; flex-shrink: 0;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="font-family:'Inter',sans-serif; font-size:11px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.07em; margin-bottom:4px;"
                         x-text="selectedBooking?.dayLabel"></div>
                    <h2 style="font-family:'Inter',sans-serif; font-size:20px; font-weight:700; color:#111827; margin:0; line-height:1.2;"
                        x-text="selectedBooking?.dateLabel"></h2>
                </div>
                <button
                    @click="bookingDetailOpen = false"
                    style="width:34px;height:34px;background:#FF5A00;border:none;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-left:12px;"
                    onmouseover="this.style.background='#E64F00'" onmouseout="this.style.background='#FF5A00'"
                >
                    <svg width="13" height="13" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Booking cards --}}
        <div style="padding: 16px 24px; display: flex; flex-direction: column; gap: 12px;">
            <template x-for="(booking, i) in (selectedBooking?.bookings || [])" :key="i">
                <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 16px; background: #fff;">
                    {{-- Card header --}}
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:12px;">
                        <div>
                            <div style="font-family:'Inter',sans-serif; font-size:15px; font-weight:700; color:#111827; margin-bottom:3px;"
                                 x-text="booking.property"></div>
                            <div style="font-family:'Inter',sans-serif; font-size:11px; color:#6B7280;"
                                 x-text="booking.source"></div>
                        </div>
                        <span x-show="booking.type === 'locked'" class="bds-locked-badge">
                            <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Locked
                        </span>
                        <span x-show="booking.type === 'self'" class="bds-self-badge">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Self
                        </span>
                    </div>

                    {{-- Info box --}}
                    <div class="bds-info-box" :class="booking.type === 'self' ? 'orange' : ''" x-text="booking.note"></div>

                    {{-- Actions (only for self-reported) --}}
                    <div x-show="booking.type === 'self'" style="display:flex; gap:8px; margin-top:12px;">
                        <button class="bds-btn-edit">Edit</button>
                        <button class="bds-btn-remove">Remove</button>
                    </div>
                </div>
            </template>
        </div>

    </div>
</div>
