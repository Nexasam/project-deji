@props([
    'filters' => [],
    'categories' => [
        ['label' => 'All stays',       'icon' => 'all'],
        ['label' => 'Lekki',           'icon' => 'location'],
        ['label' => 'Ikoyi',           'icon' => 'location'],
        ['label' => 'Victoria Island', 'icon' => 'location'],
        ['label' => 'Beachfront',      'icon' => 'beach'],
        ['label' => 'Family stays',    'icon' => 'family'],
        ['label' => 'Business stays',  'icon' => 'business'],
    ]
])

<div class="cat-strip">
    {{-- Left fade mask --}}
    <div class="cat-fade cat-fade-left" aria-hidden="true"></div>

    <div id="cat-pills" class="cat-scroll">
        @foreach($categories as $i => $category)
            @php($categoryValue = ['All stays'=>'all','Lekki'=>'lekki','Ikoyi'=>'ikoyi','Victoria Island'=>'victoria-island','Beachfront'=>'beachfront','Family stays'=>'family','Business stays'=>'business'][$category['label']])
            <a
                href="{{ route('home', array_filter([...$filters, 'category' => $categoryValue], fn ($value) => filled($value))) }}"
                class="cat-pill {{ ($filters['category'] ?? 'all') === $categoryValue ? 'active' : '' }}"
                data-cat="{{ $category['label'] }}"
            >
                @if($category['icon'] === 'all')
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                @elseif($category['icon'] === 'beach')
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3C8 3 4 7 4 11c0 3 2 5 4 6m4-14c4 0 8 4 8 8 0 3-2 5-4 6m-4-14v18M8 21h8"/></svg>
                @elseif($category['icon'] === 'family')
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="7" r="2"/><circle cx="15" cy="7" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-2a4 4 0 014-4h2m4 0h2a4 4 0 014 4v2"/></svg>
                @elseif($category['icon'] === 'business')
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                @else
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                @endif
                {{ $category['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Right fade mask --}}
    <div class="cat-fade cat-fade-right" aria-hidden="true"></div>
</div>
