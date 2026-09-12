@props([
    'badge' => 'Verification-first marketplace',
    'title' => 'Find a stay you don\'t have to second-guess.',
    'highlightText' => 'second-guess.',
    'description' => 'Browse shortlets across Lagos that have already passed ID checks and an on-site inspection.',
    'stats' => [
        ['value' => '12,000+', 'label' => 'Verified Listings'],
        ['value' => '28', 'label' => 'Cities Covered'],
        ['value' => '< 48h', 'label' => 'Avg. Verification Time'],
    ],
    'backgroundImage' => '/image.png',
    'heroImages' => ['/hero1.jpg', '/hero2.jpg', '/hero3.jpg'],
])

<section class="landing-hero">
    <div class="landing-hero-backdrop" aria-hidden="true">
        <img src="{{ $backgroundImage }}" alt="" />
        <div class="landing-hero-shade"></div>
    </div>

    <div class="hero-inner">
        <div class="hero-text-col reveal">
            <div class="hero-badge"><span></span>{{ $badge }}</div>
            <h1 class="hero-title">{!! str_replace($highlightText, '<span>'.$highlightText.'</span>', $title) !!}</h1>
            <p class="hero-description">{{ $description }}</p>

            <div class="hero-stats" aria-label="Marketplace statistics">
                @foreach($stats as $stat)
                    <div class="hero-stat"><strong>{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span></div>
                @endforeach
            </div>
        </div>

        <div class="hero-img-col">
            <div class="hero-primary-image">
                <img src="{{ $heroImages[0] }}" alt="Verified serviced apartment in Lagos" />
                <div class="hero-trust-card">
                    <div class="hero-trust-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7.5 12 3 3 6-7" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    <div><strong>Verified stay</strong><span>Identity and property checked</span></div>
                </div>
            </div>
            <div class="hero-supporting-images">
                <div><img src="{{ $heroImages[1] }}" alt="Modern serviced apartment interior" /></div>
                <div><img src="{{ $heroImages[2] }}" alt="Comfortable serviced apartment bedroom" /></div>
            </div>
        </div>
    </div>
</section>
