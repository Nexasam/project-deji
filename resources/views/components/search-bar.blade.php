<div style="background:#fff;position:relative;z-index:40;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 search-bar-wrap">
        <form 
            id="search-form" 
            class="search-bar" 
            x-data="searchBar()" 
            @submit.prevent="handleSearch"
        >
            {{-- Where Field --}}
            <div class="search-field search-field-where">
                <div style="font-family:'Inter',sans-serif;font-size:11px;font-weight:800;letter-spacing:0.07em;text-transform:uppercase;color:#111;margin-bottom:3px;">WHERE</div>
                <input 
                    id="search-where" 
                    type="text" 
                    placeholder="Lekki, Ikoyi, Victoria Island..."
                    x-model="where"
                    style="font-family:'Inter',sans-serif;width:100%;font-size:14px;color:#374151;border:none;outline:none;background:transparent;"
                    autocomplete="off"
                />
            </div>

            {{-- Dates Field --}}
            <div class="search-field search-field-dates">
                <div style="font-family:'Inter',sans-serif;font-size:11px;font-weight:800;letter-spacing:0.07em;text-transform:uppercase;color:#111;margin-bottom:3px;">CHECK IN / OUT</div>
                <input 
                    id="search-dates" 
                    type="text" 
                    placeholder="Add dates"
                    x-model="datesDisplay"
                    @click="datePickerOpen = !datePickerOpen"
                    class="date-input" 
                    readonly
                    style="font-size:14px;"
                />
            </div>

            {{-- Guests Field --}}
            <div class="search-field search-field-guests">
                <div style="font-family:'Inter',sans-serif;font-size:11px;font-weight:800;letter-spacing:0.07em;text-transform:uppercase;color:#111;margin-bottom:3px;">GUESTS</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <button 
                        type="button" 
                        @click="decrementGuests"
                        aria-label="Remove guest" 
                        style="width:24px;height:24px;border-radius:50%;border:1.5px solid #d1d5db;display:flex;align-items:center;justify-content:center;font-size:16px;color:#6b7280;cursor:pointer;background:none;transition:border-color .15s,color .15s;"
                    >−</button>
                    <span x-text="guests" style="font-family:'Inter',sans-serif;font-size:14px;font-weight:600;color:#111;min-width:14px;text-align:center;"></span>
                    <button 
                        type="button" 
                        @click="incrementGuests"
                        aria-label="Add guest" 
                        style="width:24px;height:24px;border-radius:50%;border:1.5px solid #d1d5db;display:flex;align-items:center;justify-content:center;font-size:16px;color:#6b7280;cursor:pointer;background:none;transition:border-color .15s,color .15s;"
                    >+</button>
                </div>
            </div>

            {{-- Search Button --}}
            <button type="submit" class="search-btn">
                <svg width="17" height="17" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
                Search
            </button>
        </form>

        {{-- Date Picker Dropdown --}}
        <div 
            x-show="datePickerOpen" 
            @click.away="datePickerOpen = false"
            x-transition
            style="display:none;position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%);background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,0.15);padding:20px;z-index:50;width:320px;"
            x-bind:style="datePickerOpen ? 'display: block;' : 'display: none;'"
        >
            <div class="flex gap-3 mb-3">
                <div style="flex:1;">
                    <label style="font-size:11px;font-weight:700;color:#111;letter-spacing:.06em;text-transform:uppercase;display:block;margin-bottom:6px;">Check In</label>
                    <input 
                        type="date" 
                        x-model="checkIn"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-400 transition-colors"
                    />
                </div>
                <div style="flex:1;">
                    <label style="font-size:11px;font-weight:700;color:#111;letter-spacing:.06em;text-transform:uppercase;display:block;margin-bottom:6px;">Check Out</label>
                    <input 
                        type="date" 
                        x-model="checkOut"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-400 transition-colors"
                    />
                </div>
            </div>
            <button 
                @click="applyDates" 
                class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors"
            >
                Apply dates
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function searchBar() {
    return {
        where: '',
        guests: 2,
        checkIn: '',
        checkOut: '',
        datePickerOpen: false,
        datesDisplay: '',

        incrementGuests() {
            if (this.guests < 20) this.guests++;
        },

        decrementGuests() {
            if (this.guests > 1) this.guests--;
        },

        applyDates() {
            if (this.checkIn && this.checkOut) {
                const checkInDate = new Date(this.checkIn);
                const checkOutDate = new Date(this.checkOut);
                this.datesDisplay = `${checkInDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${checkOutDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`;
                this.datePickerOpen = false;
            }
        },

        handleSearch() {
            console.log('Search:', {
                where: this.where,
                checkIn: this.checkIn,
                checkOut: this.checkOut,
                guests: this.guests
            });
            // Add your search logic here
            window.showToast('Search functionality coming soon!');
        }
    }
}
</script>
@endpush
