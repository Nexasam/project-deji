@props(['eyebrow' => null, 'title', 'description' => null])

<header {{ $attributes->class(['owner-page-header']) }}>
    <div class="min-w-0">
        @if ($eyebrow)
            <p class="owner-page-eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="owner-page-title">{{ $title }}</h1>
        @if ($description)
            <p class="owner-page-description">{{ $description }}</p>
        @endif
        @isset($meta)
            <div class="owner-page-meta">{{ $meta }}</div>
        @endisset
    </div>

    @isset($actions)
        <div class="owner-page-actions">{{ $actions }}</div>
    @endisset
</header>
