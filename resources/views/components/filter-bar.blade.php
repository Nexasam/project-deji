<div class="filter-bar" x-data="filterBar()" x-cloak>

    {{-- ── Mobile: single row of chips that open a bottom sheet ── --}}
    <div class="filter-chips-row">

        {{-- Price chip --}}
        <button
            type="button"
            class="filter-chip"
            :class="{ 'filter-chip-active': priceActive }"
            @click="sheet = sheet === 'price' ? null : 'price'"
        >
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span x-text="priceLabel">Price</span>
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="chip-chevron" :class="{ 'rotate-180': sheet === 'price' }"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>

        {{-- Type chip --}}
        <button
            type="button"
            class="filter-chip"
            :class="{ 'filter-chip-active': type !== '' }"
            @click="sheet = sheet === 'type' ? null : 'type'"
        >
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span x-text="type || 'Type'"></span>
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="chip-chevron" :class="{ 'rotate-180': sheet === 'type' }"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>

        {{-- Bedrooms chip --}}
        <button
            type="button"
            class="filter-chip"
            :class="{ 'filter-chip-active': beds > 0 }"
            @click="sheet = sheet === 'beds' ? null : 'beds'"
        >
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12v6m0-6a2 2 0 012-2h14a2 2 0 012 2m-18 0h18m-9-4V4m-4 4V6m8 2V6"/></svg>
            <span x-text="beds > 0 ? beds + (beds === 1 ? ' bed' : ' beds') : 'Beds'"></span>
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="chip-chevron" :class="{ 'rotate-180': sheet === 'beds' }"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>

        {{-- Clear all (shown when any filter active) --}}
        <button
            type="button"
            class="filter-clear"
            x-show="priceActive || type !== '' || beds > 0"
            @click="clearAll"
        >
            Clear
        </button>
    </div>

    {{-- ── Inline panel (slides in below chips, desktop-friendly too) ── --}}

    {{-- Price panel --}}
    <div
        class="filter-panel"
        x-show="sheet === 'price'"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        style="display:none;"
        @click.away="sheet = null"
    >
        <div class="filter-panel-inner">
            <p class="filter-panel-title">Nightly price range</p>
            <div class="price-inputs">
                <div class="price-input-wrap">
                    <span class="price-symbol">₦</span>
                    <input
                        type="number"
                        placeholder="Min"
                        x-model.number="priceMin"
                        min="0"
                        class="price-input"
                    />
                </div>
                <span class="price-sep">–</span>
                <div class="price-input-wrap">
                    <span class="price-symbol">₦</span>
                    <input
                        type="number"
                        placeholder="Max"
                        x-model.number="priceMax"
                        min="0"
                        class="price-input"
                    />
                </div>
            </div>

            {{-- Quick price presets --}}
            <div class="price-presets">
                <template x-for="preset in pricePresets" :key="preset.label">
                    <button
                        type="button"
                        class="price-preset-btn"
                        :class="{ 'active': priceMin === preset.min && priceMax === preset.max }"
                        @click="priceMin = preset.min; priceMax = preset.max"
                        x-text="preset.label"
                    ></button>
                </template>
            </div>

            <div class="filter-panel-actions">
                <button type="button" class="filter-action-reset" @click="priceMin = 0; priceMax = 0">Reset</button>
                <button type="button" class="filter-action-apply" @click="applyPrice">Apply</button>
            </div>
        </div>
    </div>

    {{-- Type panel --}}
    <div
        class="filter-panel"
        x-show="sheet === 'type'"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        style="display:none;"
        @click.away="sheet = null"
    >
        <div class="filter-panel-inner">
            <p class="filter-panel-title">Property type</p>
            <div class="type-grid">
                <template x-for="t in types" :key="t">
                    <button
                        type="button"
                        class="type-btn"
                        :class="{ 'active': type === t }"
                        @click="type = (type === t) ? '' : t"
                        x-text="t"
                    ></button>
                </template>
            </div>
            <div class="filter-panel-actions">
                <button type="button" class="filter-action-reset" @click="type = ''">Reset</button>
                <button type="button" class="filter-action-apply" @click="sheet = null">Apply</button>
            </div>
        </div>
    </div>

    {{-- Beds panel --}}
    <div
        class="filter-panel"
        x-show="sheet === 'beds'"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        style="display:none;"
        @click.away="sheet = null"
    >
        <div class="filter-panel-inner">
            <p class="filter-panel-title">Bedrooms</p>
            <div class="beds-grid">
                <template x-for="n in [0,1,2,3,4,5]" :key="n">
                    <button
                        type="button"
                        class="beds-btn"
                        :class="{ 'active': beds === n }"
                        @click="beds = (beds === n && n !== 0) ? 0 : n"
                        x-text="n === 0 ? 'Any' : (n === 5 ? '5+' : n)"
                    ></button>
                </template>
            </div>
            <div class="filter-panel-actions">
                <button type="button" class="filter-action-reset" @click="beds = 0">Reset</button>
                <button type="button" class="filter-action-apply" @click="sheet = null">Apply</button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function filterBar() {
    return {
        sheet: null,
        priceMin: 0,
        priceMax: 0,
        type: '',
        beds: 0,

        types: ['Apartment', 'Studio', 'Penthouse', 'Villa', 'Duplex', 'Shortlet'],

        pricePresets: [
            { label: 'Under ₦30k',       min: 0,      max: 30000  },
            { label: '₦30k – ₦60k',      min: 30000,  max: 60000  },
            { label: '₦60k – ₦100k',     min: 60000,  max: 100000 },
            { label: '₦100k+',           min: 100000, max: 0      },
        ],

        get priceActive() {
            return this.priceMin > 0 || this.priceMax > 0;
        },

        get priceLabel() {
            if (!this.priceActive) return 'Price';
            const fmt = n => n >= 1000 ? '₦' + (n / 1000).toFixed(0) + 'k' : '₦' + n;
            if (this.priceMin > 0 && this.priceMax > 0) return fmt(this.priceMin) + '–' + fmt(this.priceMax);
            if (this.priceMin > 0) return fmt(this.priceMin) + '+';
            return 'Under ' + fmt(this.priceMax);
        },

        applyPrice() {
            this.sheet = null;
            window.showToast('Price filter applied');
        },

        clearAll() {
            this.priceMin = 0;
            this.priceMax = 0;
            this.type = '';
            this.beds = 0;
            this.sheet = null;
        }
    }
}
</script>
@endpush
