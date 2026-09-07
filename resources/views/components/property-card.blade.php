@props([
    'image',
    'name',
    'location',
    'guests',
    'price',
    'rating',
    'verified' => true
])

<article
    class="card-listing"
    tabindex="0"
    role="article"
    x-data="propertyCard()"
>
    {{-- Image --}}
    <div class="card-img-wrap">
        <img
            src="{{ $image }}"
            alt="{{ $name }}"
            class="card-img"
            loading="lazy"
        />

        @if($verified)
            <div class="card-verified-badge">
                <svg width="10" height="10" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Verified</span>
            </div>
        @endif

        <button
            class="heart-btn"
            :class="{ 'liked': isLiked }"
            @click.stop="toggleWishlist"
            aria-label="Save to wishlist"
        >
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>

    {{-- Body --}}
    <div class="card-body">
        {{-- Name + rating --}}
        <div class="card-row">
            <span class="card-name">{{ $name }}</span>
            <div class="card-rating">
                <svg width="11" height="11" fill="#f97316" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>{{ $rating }}</span>
            </div>
        </div>

        {{-- Location + guests --}}
        <div class="card-meta">
            <span class="card-location">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $location }}
            </span>
            <span class="card-dot" aria-hidden="true">·</span>
            <span class="card-guests">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $guests }} guests
            </span>
        </div>

        {{-- Price + AI insights --}}
        <div class="card-footer">
            <div class="card-price">
                <span class="card-price-amount">₦{{ number_format($price) }}</span>
                <span class="card-price-unit">/ night</span>
            </div>
            <button @click.stop="openInsights" class="insights-btn" aria-label="View AI Insights">
                <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                AI Insights
            </button>
        </div>
    </div>
</article>

@push('scripts')
<script>
function propertyCard() {
    return {
        isLiked: false,
        toggleWishlist() {
            this.isLiked = !this.isLiked;
            window.showToast(this.isLiked ? 'Saved to wishlist' : 'Removed from wishlist');
        },
        openInsights() {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'insights-modal' } }));
        }
    }
}
</script>
@endpush
