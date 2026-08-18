@props([
    'image',
    'name',
    'location',
    'guests',
    'price',
    'rating',
    'verified' => true
])

<div 
    class="card-listing" 
    style="background:#fff;border-radius:16px;overflow:hidden;cursor:pointer;" 
    tabindex="0" 
    role="article"
    x-data="propertyCard()"
>
    <div style="position:relative;">
        <img 
            src="{{ $image }}" 
            alt="{{ $name }}" 
            style="width:100%;height:200px;object-fit:cover;border-radius:16px;display:block;" 
            loading="lazy"
        />
        
        @if($verified)
            <div style="position:absolute;top:10px;left:10px;background:rgba(255,255,255,0.92);border-radius:999px;padding:4px 10px;display:flex;align-items:center;gap:5px;">
                <svg width="11" height="11" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span style="font-size:11px;font-weight:600;color:#111;">Verified</span>
            </div>
        @endif

        <button 
            class="heart-btn" 
            :class="{ 'liked': isLiked }"
            @click="toggleWishlist"
            aria-label="Save to wishlist"
        >
            <svg width="14" height="14" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>

    <div style="padding:12px 12px 10px 12px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
            <span style="font-size:14px;font-weight:700;color:#111;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:72%;">
                {{ $name }}
            </span>
            <div style="display:flex;align-items:center;gap:3px;flex-shrink:0;margin-left:6px;">
                <svg width="13" height="13" fill="#f97316" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span style="font-size:12px;font-weight:600;color:#111;">{{ $rating }}</span>
            </div>
        </div>

        <div style="font-size:12px;color:#6b7280;margin-bottom:10px;">{{ $location }}</div>

        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
            <div style="display:flex;align-items:center;gap:4px;font-size:11px;color:#6b7280;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>{{ $guests }} guests</span>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <span style="font-size:16px;font-weight:800;color:#111;">₦{{ number_format($price) }}</span>
                <span style="font-size:12px;color:#6b7280;"> / night</span>
            </div>
            <button
                @click="openInsights"
                class="insights-btn"
            >
                <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                AI Insights
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function propertyCard() {
    return {
        isLiked: false,

        toggleWishlist() {
            this.isLiked = !this.isLiked;
            const message = this.isLiked ? 'Added to wishlist!' : 'Removed from wishlist';
            window.showToast(message);
        },

        openInsights() {
            // Dispatch custom event to open modal
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { id: 'insights-modal' } 
            }));
        }
    }
}
</script>
@endpush
