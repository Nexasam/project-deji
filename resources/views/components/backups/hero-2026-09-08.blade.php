{{-- Backup of the landing-page hero before the bold redesign on 2026-09-08. --}}
@props([
    'badge' => 'Verification-first marketplace',
    'title' => 'Find a stay you don\'t have to second-guess.',
    'highlightText' => 'second-guess.',
    'description' => 'Browse shortlets across Lagos that have already passed ID checks and an on-site inspection.',
    'stats' => [],
    'backgroundImage' => '/image.png',
    'heroImages' => ['/hero1.jpg', '/hero2.jpg', '/hero3.jpg'],
])
<section class="relative bg-neutral-800" style="min-height:430px;overflow:hidden;padding-bottom:60px;padding-top:16px;">
    <div class="absolute inset-0 z-0"><img src="{{ $backgroundImage }}" alt="" class="h-full w-full object-cover object-center"><div class="absolute inset-0" style="background:rgba(0,0,0,.45)"></div><div class="absolute inset-0" style="background:linear-gradient(to right,rgba(15,8,2,.92),rgba(15,8,2,.45) 50%,transparent)"></div></div>
    <div class="hero-inner relative z-10 mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-10">
        <div class="hero-text-col reveal"><div style="display:inline-flex;align-items:center;gap:7px;background:rgba(249,115,22,.9);border-radius:999px;padding:4px 12px 4px 8px;margin-bottom:16px"><span style="width:7px;height:7px;background:#fff;border-radius:50%"></span><span style="font-size:10px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#fff">{{ $badge }}</span></div><h1 style="font-family:'Barlow',sans-serif;font-size:clamp(26px,5vw,34px);font-weight:900;line-height:1.1;color:#fff;margin:0 0 14px">{!! str_replace($highlightText, '<span style="color:#f97316">'.$highlightText.'</span>', $title) !!}</h1><p style="font-size:13px;line-height:1.65;color:rgba(255,255,255,.72);margin:0 0 22px">{{ $description }}</p><div style="display:flex;gap:22px;flex-wrap:wrap">@foreach($stats as $stat)<div><div style="font-size:26px;font-weight:800;color:#f97316">{{ $stat['value'] }}</div><div style="font-size:11px;color:rgba(255,255,255,.55)">{{ $stat['label'] }}</div></div>@endforeach</div></div>
        <div class="hero-img-col" style="margin-top:40px;align-self:flex-end"><div class="relative overflow-hidden rounded-2xl shadow-2xl" style="flex:0 0 48%;height:310px"><img src="{{ $heroImages[0] }}" alt="Featured property" class="h-full w-full object-cover"></div><div style="flex:1;display:flex;flex-direction:column;gap:10px;height:310px"><div class="flex-1 overflow-hidden rounded-2xl"><img src="{{ $heroImages[1] }}" alt="Property view" class="h-full w-full object-cover"></div><div class="flex-1 overflow-hidden rounded-2xl"><img src="{{ $heroImages[2] }}" alt="Property interior" class="h-full w-full object-cover"></div></div></div>
    </div>
</section>
