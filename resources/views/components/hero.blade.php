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
    'heroImages' => [
        '/hero1.jpg',
        '/hero2.jpg',
        '/hero3.jpg',
    ]
])

<section class="relative bg-neutral-800" style="min-height:430px;overflow:hidden;padding-bottom:60px;">
    {{-- Background with overlay --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ $backgroundImage }}" alt="" class="w-full h-full object-cover object-center"/>
        <div class="absolute inset-0" style="background:rgba(0,0,0,0.45);"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(15,8,2,0.92) 0%,rgba(15,8,2,0.82) 22%,rgba(15,8,2,0.45) 50%,rgba(15,8,2,0.10) 72%,transparent 100%);"></div>
    </div>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 hero-inner">
        {{-- Text Column --}}
        <div class="hero-text-col reveal">
            {{-- Badge --}}
            <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(249,115,22,0.90);border-radius:999px;padding:4px 12px 4px 8px;margin-bottom:16px;">
                <span style="width:7px;height:7px;background:#fff;border-radius:50%;flex-shrink:0;"></span>
                <span style="font-family:'Inter',sans-serif;font-size:10px;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:#fff;line-height:1;">{{ $badge }}</span>
            </div>

            {{-- Title --}}
            <h1 style="font-family:'Barlow',sans-serif;font-size:clamp(26px,5vw,34px);font-weight:900;line-height:1.10;color:#fff;margin:0 0 14px 0;">
                {!! str_replace($highlightText, '<span style="color:#f97316;">' . $highlightText . '</span>', $title) !!}
            </h1>

            {{-- Description --}}
            <p style="font-family:'Inter',sans-serif;font-size:13px;line-height:1.65;color:rgba(255,255,255,0.72);margin:0 0 22px 0;">
                {{ $description }}
            </p>

            {{-- Stats --}}
            <div style="display:flex;align-items:flex-start;gap:22px;flex-wrap:wrap;">
                @foreach($stats as $stat)
                    <div>
                        <div style="font-family:'Inter',sans-serif;font-size:26px;font-weight:800;color:#f97316;line-height:1;">{{ $stat['value'] }}</div>
                        <div style="font-family:'Inter',sans-serif;font-size:11px;color:rgba(255,255,255,0.55);margin-top:3px;">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Image Column --}}
        <div class="hero-img-col" style="margin-top:40px;align-self:flex-end;">
            {{-- Main Image --}}
            <div class="relative rounded-2xl overflow-hidden shadow-2xl" style="flex:0 0 48%;height:310px;">
                <img src="{{ $heroImages[0] }}" alt="Featured property" class="w-full h-full object-cover"/>
                <div style="position:absolute;top:10px;right:10px;width:52px;height:52px;background:#f97316;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.3);">
                    <svg width="14" height="14" fill="white" viewBox="0 0 20 20" style="margin-bottom:2px;">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span style="font-size:7px;font-weight:700;color:#fff;line-height:1.2;text-align:center;">Verified<br/>Stay</span>
                </div>
            </div>

            {{-- Side Images --}}
            <div style="flex:1;display:flex;flex-direction:column;gap:10px;height:310px;">
                <div class="rounded-2xl overflow-hidden shadow-2xl" style="flex:1;">
                    <img src="{{ $heroImages[1] }}" alt="Property view" class="w-full h-full object-cover"/>
                </div>
                <div class="rounded-2xl overflow-hidden shadow-2xl" style="flex:1;">
                    <img src="{{ $heroImages[2] }}" alt="Property interior" class="w-full h-full object-cover"/>
                </div>
            </div>
        </div>
    </div>
</section>
