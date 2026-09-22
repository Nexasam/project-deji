@props([
    'image',
    'name',
    'location',
    'guests',
    'price',
    'rating',
    'priceLabel' => null,
    'priceUnit' => '/ night',
    'pricingNote' => null,
    'basePriceLabel' => null,
    'savingsLabel' => null,
    'valueBadge' => null,
    'insights' => [],
    'propertyId' => null,
    'favouriteUrl' => null,
    'unfavouriteUrl' => null,
    'isFavourite' => false,
    'href' => null,
    'verified' => true
])

<article
    class="card-listing"
    tabindex="0"
    role="{{ $href ? 'link' : 'article' }}"
    @if($href)
        @click="if (!$event.target.closest('button,a,form,input,select,textarea')) window.location.href = @js($href)"
        @keydown.enter.prevent="if (!$event.target.closest('button,a,form,input,select,textarea')) window.location.href = @js($href)"
    @endif
    x-data="propertyCard({
        isLiked: @js($isFavourite),
        favouriteUrl: @js($favouriteUrl),
        unfavouriteUrl: @js($unfavouriteUrl),
        loginUrl: @js(route('login', ['redirect' => request()->getRequestUri()]))
    })"
>
    {{-- Image --}}
    <div class="card-img-wrap">
        <img
            src="{{ $image }}"
            onerror="this.onerror=null;this.src='/image.png'"
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
            type="button"
            class="heart-btn"
            :class="{ 'liked': isLiked }"
            @click.prevent.stop="toggleWishlist"
            :aria-label="isLiked ? 'Remove from favourites' : 'Save to favourites'"
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
                <svg width="14" height="14" fill="#f97316" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>{{ $rating }}</span>
            </div>
        </div>

        {{-- Location + guests --}}
        <div class="card-meta">
            <span class="card-location">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $location }}
            </span>
            <span class="card-guests">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $guests }} guests
            </span>
        </div>

        {{-- Price + AI insights --}}
        @if($valueBadge)
            <div class="pricing-value-badge">{{ $valueBadge }}</div>
        @endif
        <div class="card-footer">
            <div class="card-price">
                <span class="card-price-amount">{{ $priceLabel ?: '₦'.number_format($price) }}</span>
                <span class="card-price-unit">{{ $priceUnit }}</span>
                @if($basePriceLabel)
                    <span class="card-base-price">{{ $basePriceLabel }}</span>
                @endif
                @if($pricingNote)
                    <span class="card-pricing-note">{{ $pricingNote }}</span>
                @endif
                @if($savingsLabel)
                    <span class="card-savings-note">{{ $savingsLabel }}</span>
                @endif
            </div>
            <button type="button" class="insights-btn" @click.prevent.stop="openInsights" data-insights='@json($insights)' title="Preview AI Insights">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2a7 7 0 0 0-4.17 12.62c.67.5 1.17 1.2 1.17 2.05V17h6v-.33c0-.85.5-1.55 1.17-2.05A7 7 0 0 0 12 2Zm-3 17a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-.5H9V19Zm2 3h2a1 1 0 0 0 .92-.62H10.08A1 1 0 0 0 11 22Z"/>
                </svg>
                <span>AI Insights</span>
            </button>
        </div>
    </div>
</article>

@push('scripts')
<script>
function propertyCard(config = {}) {
    return {
        isLiked: Boolean(config.isLiked),
        async toggleWishlist() {
            if (!config.favouriteUrl || !config.unfavouriteUrl) {
                window.location.href = config.loginUrl || '/login';
                return;
            }

            const nextLiked = !this.isLiked;
            const url = nextLiked ? config.favouriteUrl : config.unfavouriteUrl;
            const method = nextLiked ? 'POST' : 'DELETE';

            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({}),
                });

                if (response.status === 401 || response.redirected) {
                    window.location.href = config.loginUrl || '/login';
                    return;
                }

                if (!response.ok) {
                    throw new Error('Favourite request failed');
                }

                this.isLiked = nextLiked;
                if (window.Alpine?.store('favourites')) {
                    nextLiked
                        ? window.Alpine.store('favourites').increment()
                        : window.Alpine.store('favourites').decrement();
                }
                window.showToast(this.isLiked ? 'Saved to favourites' : 'Removed from favourites');
            } catch (error) {
                window.showToast('Could not update favourites. Please try again.');
            }
        },
        openInsights(event) {
            let insights = {};
            try {
                insights = JSON.parse(event.currentTarget.dataset.insights || '{}');
            } catch (error) {
                insights = {};
            }
            window.dispatchEvent(new CustomEvent('open-property-insights', { detail: insights }));
        }
    }
}
</script>
@endpush
