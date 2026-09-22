@props(['filters' => []])
<div class="search-bar-outer">
    <div
        class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 search-bar-wrap"
        x-data="searchBar(@js($filters))"
    >
        <button
            type="button"
            class="search-mobile-toggle"
            :class="mobileExpanded ? 'is-expanded' : ''"
            :aria-expanded="mobileExpanded.toString()"
            aria-controls="search-form"
            @click="mobileExpanded = !mobileExpanded"
        >
            <span class="search-mobile-icon">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </span>
            <span class="search-mobile-copy">
                <strong>Search stays</strong>
                <small x-text="mobileSummary"></small>
            </span>
            <span class="search-mobile-chevron" aria-hidden="true">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </span>
        </button>

        <form
            id="search-form"
            class="search-bar"
            :class="mobileExpanded ? 'is-expanded' : ''"
            action="{{ route('home').'#marketplace' }}"
            method="GET"
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
            <div
                class="search-field search-field-dates"
                role="button"
                tabindex="0"
                aria-haspopup="dialog"
                :aria-expanded="datePickerOpen.toString()"
                @click="datePickerOpen = !datePickerOpen"
                @keydown.enter.prevent="datePickerOpen = !datePickerOpen"
                @keydown.space.prevent="datePickerOpen = !datePickerOpen"
            >
                <label class="search-field-label">CHECK IN / OUT</label>
                <div class="search-field-value" :class="datesDisplay ? 'has-value' : ''">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="search-field-icon"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span x-text="datesDisplay || 'Add dates'"></span>
                </div>
            </div>
            <input type="hidden" name="check_in" :value="checkIn">
            <input type="hidden" name="check_out" :value="checkOut">
            @foreach(['category', 'min_price', 'max_price', 'property_type', 'beds'] as $preservedFilter)
                @if(filled($filters[$preservedFilter] ?? null))
                    <input type="hidden" name="{{ $preservedFilter }}" value="{{ $filters[$preservedFilter] }}">
                @endif
            @endforeach

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
            role="dialog"
            aria-label="Choose check-in and checkout dates"
        >
            <p class="date-picker-title">Select your dates</p>
            <div class="date-picker-row">
                <div class="date-picker-field">
                    <label class="date-picker-label">Check In</label>
                    <input type="date" x-model="checkIn" :min="today" class="date-picker-input" />
                </div>
                <div class="date-picker-sep">→</div>
                <div class="date-picker-field">
                    <label class="date-picker-label">Check Out</label>
                    <input type="date" x-model="checkOut" :min="checkoutMin" class="date-picker-input" />
                </div>
            </div>
            <button type="button" @click="applyDates" class="date-picker-apply">Apply dates</button>
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
        mobileExpanded: Boolean(filters.q || filters.check_in || filters.check_out || filters.guests),
        today: new Date().toISOString().slice(0, 10),

        incrementGuests() { if (this.guests < 20) this.guests++; },
        decrementGuests() { if (this.guests > 1) this.guests--; },

        get checkoutMin() {
            if (!this.checkIn) return this.today;

            const next = new Date(this.checkIn + 'T00:00:00');
            next.setDate(next.getDate() + 1);

            return next.toISOString().slice(0, 10);
        },

        get datesDisplay() {
            if (!this.checkIn && !this.checkOut) return '';

            const fmt = d => new Date(d + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });

            if (this.checkIn && this.checkOut) return fmt(this.checkIn) + ' – ' + fmt(this.checkOut);
            if (this.checkIn) return fmt(this.checkIn) + ' – Checkout';

            return 'Check in – ' + fmt(this.checkOut);
        },

        get mobileSummary() {
            const where = this.where || 'Lekki, Ikoyi, V.I…';
            const dates = this.datesDisplay || 'Add dates';
            const guests = this.guests + ' guest' + (this.guests > 1 ? 's' : '');

            return where + ' · ' + dates + ' · ' + guests;
        },

        applyDates() { this.datePickerOpen = false; },

        init() {
            this.$watch('checkIn', value => {
                if (this.checkOut && value && this.checkOut <= value) {
                    this.checkOut = '';
                }
            });
            this.$watch('guests', value => {
                const number = Number(value || 1);
                if (number < 1) this.guests = 1;
                if (number > 20) this.guests = 20;
            });

            if (this.checkOut && this.checkIn && this.checkOut <= this.checkIn) {
                this.checkOut = '';
            }
        },
    }
}
</script>
@endpush
