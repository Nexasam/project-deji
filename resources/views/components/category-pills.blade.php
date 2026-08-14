@props([
    'categories' => [
        ['label' => 'All stays', 'active' => true],
        ['label' => 'Lekki', 'active' => false],
        ['label' => 'Ikoyi', 'active' => false],
        ['label' => 'Victoria Island', 'active' => false],
        ['label' => 'Beachfront', 'active' => false],
        ['label' => 'Family stays', 'active' => false],
        ['label' => 'Business stays', 'active' => false],
    ]
])

<div class="bg-white border-b border-gray-100" style="padding-top:28px;padding-bottom:20px;">
    <div 
        id="cat-pills" 
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 flex gap-3 overflow-x-auto pb-1"
        style="-webkit-overflow-scrolling:touch;scrollbar-width:none;"
        x-data="categoryPills()"
    >
        @foreach($categories as $category)
            <button 
                class="cat-pill {{ $category['active'] ? 'active' : '' }}"
                data-cat="{{ $category['label'] }}"
                @click="handleCategoryClick($event.target)"
            >
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="pointer-events:none;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ $category['label'] }}
            </button>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
function categoryPills() {
    return {
        handleCategoryClick(btn) {
            const cat = btn.dataset.cat;
            
            // Remove active class from all pills
            document.querySelectorAll('.cat-pill').forEach(pill => {
                pill.classList.remove('active');
            });
            
            // Add active class to clicked pill
            btn.classList.add('active');
            
            console.log('Category selected:', cat);
            // Add your filter logic here
        }
    }
}
</script>
@endpush
