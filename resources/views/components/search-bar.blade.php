@props(['filters' => []])
<div class="search-bar-outer">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 search-bar-wrap">
        <form
            id="search-form"
            class="search-bar"
            action="{{ route('home') }}"
            method="GET"
            x-data="searchBar(@js($filters))"
        >
            {{-- Where Field --}}
            <div class="search-field search-field-where">
                <label class="search-field-label">WHERE</label>
                <input
                    id="search-where"
                    name="q"
                    type="text"
                    placeholder="Lekki, Ikoyi, V.I…"
                    x-model="where"
                    class="search-field-input"
                    autocomplete="off"
                />
            </div>

            <div class="search-divider"></div>

            {{-- Dates Field --}}
            <div class="search-field search-field-dates" @click="datePickerOpen = !datePickerOpen">
                <label class="search-field-label">CHECK IN / OUT</label>
                <div class="search-field-value" :class="datesDisplay ? 'has-value' : ''">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="search-field-icon"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span x-text="datesDisplay || 'Add dates'"></span>
                </div>
            </div>
            <input type="hidden" name="check_in" :value="checkIn">
            <input type="hidden" name="check_out" :value="checkOut">

            <div class="search-divider"></div>

            {{-- Guests Field --}}
            <div class="search-field search-field-guests">
                <label class="search-field-label">GUESTS</label>
                <div class="search-guests-control">
                    <button
                        type="button"
                        @click="decrementGuests"
                        aria-label="Remove guest"
                        class="guest-btn"
                        :disabled="guests <= 1"
                    >−</button>
                    <span x-text="guests + ' guest' + (guests > 1 ? 's' : '')" class="guest-count"></span>
                    <input type="hidden" name="guests" :value="guests">
                    <button
                        type="button"
                        @click="incrementGuests"
                        aria-label="Add guest"
                        class="guest-btn"
                        :disabled="guests >= 20"
                    >+</button>
                </div>
            </div>

            {{-- Search Button --}}
            <button type="submit" class="search-btn">
                <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
                <span>Search</span>
            </button>
        </form>

        {{-- Date Picker Dropdown --}}
        <div
            x-show="datePickerOpen"
            @click.away="datePickerOpen = false"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            style="display:none;"
            class="date-picker-dropdown"
        >
            <p class="date-picker-title">Select your dates</p>
            <div class="date-picker-row">
                <div class="date-picker-field">
                    <label class="date-picker-label">Check In</label>
                    <input type="date" x-model="checkIn" class="date-picker-input" />
                </div>
                <div class="date-picker-sep">→</div>
                <div class="date-picker-field">
                    <label class="date-picker-label">Check Out</label>
                    <input type="date" x-model="checkOut" class="date-picker-input" />
                </div>
            </div>
            <button @click="applyDates" class="date-picker-apply">Apply dates</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function searchBar(filters = {}) {
    return {
        where: filters.q || '',
        guests: Number(filters.guests || 2),
        checkIn: filters.check_in || '',
        checkOut: filters.check_out || '',
        datePickerOpen: false,
        datesDisplay: '',

        incrementGuests() { if (this.guests < 20) this.guests++; },
        decrementGuests() { if (this.guests > 1) this.guests--; },

        applyDates() {
            if (this.checkIn && this.checkOut) {
                const fmt = d => new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                this.datesDisplay = fmt(this.checkIn) + ' – ' + fmt(this.checkOut);
                this.datePickerOpen = false;
            }
        },

        init() { this.applyDates(); }
    }
}
</script>
@endpush
