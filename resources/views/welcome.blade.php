<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verified Shortlet – Find a stay you don't have to second-guess</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Barlow:wght@800;900&display=swap');

        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; }

        /* ── Transitions ── */
        .card-listing { transition: transform .2s ease, box-shadow .2s ease; }
        .card-listing:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.10); }

        .btn-pill { transition: background .15s, color .15s, border-color .15s, transform .1s; }
        .btn-pill:active { transform: scale(.96); }

        /* ── Navbar ── */
        #site-header { transition: box-shadow .25s; }
        #site-header.scrolled { box-shadow: 0 2px 16px rgba(0,0,0,0.07); }

        .nav-link {
            position: relative;
            font-size: 15px;
            font-weight: 400;
            color: #374151;
            text-decoration: none;
            transition: color .18s;
            padding: 4px 0;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 0; height: 2px;
            background: #f97316;
            border-radius: 2px;
            transition: width .22s ease;
        }
        .nav-link:hover         { color: #111; }
        .nav-link:hover::after  { width: 100%; }
        .nav-link.active        { color: #111; font-weight: 500; }
        .nav-link.active::after { width: 100%; }

        /* ── Hamburger ── */
        #nav-toggle { display:flex; flex-direction:column; justify-content:center;
                      gap:5px; width:40px; height:40px; background:none; border:none;
                      cursor:pointer; padding:8px; border-radius:8px;
                      transition: background .15s; }
        #nav-toggle:hover { background: #f3f4f6; }
        #nav-toggle span { display:block; width:22px; height:2px; background:#374151;
                           border-radius:2px; transition: transform .28s cubic-bezier(.4,0,.2,1), opacity .2s, width .2s; }
        #nav-toggle.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        #nav-toggle.open span:nth-child(2) { opacity:0; width:0; }
        #nav-toggle.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Hide hamburger + mobile menu on desktop; hide desktop nav on mobile */
        @media (min-width: 1024px) {
            #nav-toggle   { display: none !important; }
            #mobile-menu  { display: none !important; }
            .nav-desktop  { display: flex !important; }
        }
        @media (max-width: 1023px) {
            .nav-desktop  { display: none !important; }
        }

        /* ── Mobile menu ── */
        .mobile-menu {
            display: grid;
            grid-template-rows: 0fr;
            background: #fff;
            border-top: 1px solid #f3f4f6;
            transition: grid-template-rows .3s ease, box-shadow .3s;
            overflow: hidden;
        }
        .mobile-menu.open {
            grid-template-rows: 1fr;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .mobile-menu-inner { overflow: hidden; }
        .mobile-menu a {
            display: flex; align-items: center;
            padding: 14px 24px;
            font-size: 15px; font-weight: 500; color: #374151;
            text-decoration: none;
            border-bottom: 1px solid #f3f4f6;
            transition: background .15s, color .15s;
        }
        .mobile-menu a:hover { background: #fff7ed; color: #f97316; }
        .mobile-menu a.cta-mobile {
            margin: 16px; border-radius: 10px; border-bottom: none;
            background: #f97316; color: #fff; justify-content: center;
            font-weight: 600;
        }
        .mobile-menu a.cta-mobile:hover { background: #ea6c0a; }

        /* ── Hero ── */
        .hero-inner    { display:flex; align-items:center; gap:20px; }
        .hero-text-col { flex:0 0 36%; max-width:36%; color:#fff; }
        .hero-img-col  { flex:0 0 64%; max-width:64%; height:310px; gap:10px; overflow:hidden; display:flex; }

        /* ── Search bar ── */
        .search-bar-wrap { transform:translateY(-50%); position:relative; z-index:40; }
        .search-bar      { background:#fff; border-radius:999px; box-shadow:0 4px 32px rgba(0,0,0,0.15);
                           display:flex; align-items:center; padding:14px 14px 14px 28px; }
        .search-field       { min-width:0; }
        .search-field-where { flex:1.2; padding-right:20px; border-right:1px solid #e5e7eb; }
        .search-field-dates { flex:1; padding:0 20px; border-right:1px solid #e5e7eb; }
        .search-field-guests{ padding:0 20px; flex-shrink:0; }
        .search-btn { display:flex; align-items:center; gap:8px; background:#f97316; color:#fff;
                      font-size:16px; font-weight:700; padding:14px 28px; border-radius:999px;
                      border:none; cursor:pointer; white-space:nowrap; margin-left:10px; flex-shrink:0;
                      transition: background .15s, transform .1s; }
        .search-btn:hover  { background:#ea6c0a; }
        .search-btn:active { transform: scale(.97); }

        /* ── Date input styling ── */
        .date-input { font-family:'Inter',sans-serif; width:100%; font-size:14px; color:#9ca3af;
                      border:none; outline:none; background:transparent; cursor:pointer; }
        .date-input::-webkit-calendar-picker-indicator { opacity: .5; cursor: pointer; }

        /* ── Category pills ── */
        .cat-pill { padding:8px 18px; border-radius:999px; font-size:14px; font-weight:500;
                    cursor:pointer; white-space:nowrap; display:flex; align-items:center; gap:6px;
                    flex-shrink:0; border:1.5px solid #d1d5db; background:#fff; color:#374151;
                    transition: background .15s, color .15s, border-color .15s, transform .1s; }
        .cat-pill:hover  { border-color:#f97316; color:#f97316; }
        .cat-pill.active { background:#f97316; color:#fff; border-color:#f97316; }
        .cat-pill:active { transform: scale(.95); }

        /* ── Heart / wishlist ── */
        .heart-btn { position:absolute; top:10px; right:10px; width:30px; height:30px;
                     background:rgba(255,255,255,0.92); border-radius:50%; border:none; cursor:pointer;
                     display:flex; align-items:center; justify-content:center;
                     transition: transform .2s, background .15s; }
        .heart-btn:hover { transform: scale(1.15); background:#fff; }
        .heart-btn svg { transition: fill .2s, stroke .2s; }
        .heart-btn.liked svg { fill:#ef4444; stroke:#ef4444; }

        /* ── AI Insights btn ── */
        .insights-btn { background:#111; color:#fff; border:none; border-radius:999px;
                        padding:5px 10px; font-size:11px; font-weight:600;
                        display:flex; align-items:center; gap:5px; cursor:pointer;
                        transition: background .15s, transform .1s; }
        .insights-btn:hover  { background:#333; }
        .insights-btn:active { transform: scale(.95); }

        /* ── AI Insights modal ── */
        #insights-modal { display:none; position:fixed; inset:0; z-index:200; align-items:center; justify-content:center; }
        #insights-modal.open { display:flex; }
        #insights-modal .overlay { position:absolute; inset:0; background:rgba(0,0,0,.55); }
        #insights-modal .box { position:relative; background:#fff; border-radius:20px; padding:32px;
                               max-width:440px; width:90%; z-index:1;
                               animation: popIn .2s ease; }
        @keyframes popIn { from { transform:scale(.92); opacity:0; } to { transform:scale(1); opacity:1; } }

        /* ── Toast notification ── */
        #toast { position:fixed; bottom:28px; left:50%; transform:translateX(-50%) translateY(20px);
                 background:#111; color:#fff; font-size:13px; font-weight:500;
                 padding:10px 22px; border-radius:999px; z-index:300;
                 opacity:0; transition: opacity .3s, transform .3s; pointer-events:none; }
        #toast.show { opacity:1; transform:translateX(-50%) translateY(0); }

        /* ── Chat bubble animation ── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .chat-msg { animation: fadeUp .3s ease; }

        /* ── Scroll reveal — hidden only when JS has initialised ── */
        .reveal-ready { opacity:0; transform:translateY(24px); transition: opacity .55s ease, transform .55s ease; }
        .reveal-ready.visible { opacity:1; transform:translateY(0); }

        /* ── Section spacing ── */
        .section-gap { margin-top: 0; }  /* handled via padding on sections */

        /* ── Footer ── */
        .footer-grid { display:grid; grid-template-columns:1.4fr 1fr 1fr 1fr 1fr; gap:40px; padding-bottom:56px; }

        /* ── Responsive ── */
        @media (max-width:1023px) {
            .hero-text-col { flex:0 0 100%; max-width:100%; }
            .hero-img-col  { display:none !important; }
            .hero-inner    { flex-direction:column; padding-top:32px; padding-bottom:32px; }
        }
        @media (max-width:767px) {
            .search-bar          { flex-direction:column; border-radius:20px; padding:16px; gap:12px; align-items:stretch; }
            .search-field-where  { padding-right:0; border-right:none; border-bottom:1px solid #e5e7eb; padding-bottom:12px; flex:none; width:100%; }
            .search-field-dates  { padding:12px 0; border-right:none; border-bottom:1px solid #e5e7eb; flex:none; width:100%; }
            .search-field-guests { padding:12px 0 0 0; flex:none; width:100%; }
            .search-btn          { margin-left:0; width:100%; justify-content:center; }
            .search-bar-wrap     { transform:none; margin-top:-20px; margin-bottom:20px; }
            .footer-grid         { grid-template-columns:1fr 1fr; gap:32px; }
            .footer-brand        { grid-column:span 2; }
        }
        @media (max-width:480px) {
            .footer-grid { grid-template-columns:1fr 1fr; }
        }
    </style>
</head>
<body class="bg-white font-sans text-gray-900 antialiased">

{{-- ===== TOAST ===== --}}
<div id="toast"></div>

{{-- ===== AI INSIGHTS MODAL ===== --}}
<div id="insights-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="overlay" id="modal-overlay"></div>
    <div class="box">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-orange-500 rounded-full flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                </div>
                <span id="modal-title" class="font-bold text-gray-900 text-base">AI Insights</span>
            </div>
            <button id="modal-close" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors" aria-label="Close">
                <svg width="14" height="14" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="modal-body" class="space-y-3 text-sm text-gray-600 leading-relaxed"></div>
    </div>
</div>

{{-- ===== NAVBAR ===== --}}
<header id="site-header" class="sticky top-0 z-50 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 flex items-center justify-between" style="height:68px;">

        {{-- Logo — image already contains the wordmark, no extra text needed --}}
        <a href="/" class="flex items-center shrink-0">
            <img src="/logo.png" alt="Verified Shortlet" style="height:58px;width:auto;"/>
        </a>

        {{-- Desktop nav — controlled by .nav-desktop CSS class --}}
        <nav class="nav-desktop items-center gap-10">
            <a href="#" class="nav-link active">Explore stays</a>
            <a href="#" class="nav-link">Why verified?</a>
            <a href="#" class="nav-link">Become a host</a>
            <a href="#" class="nav-link">Help</a>
        </nav>

        {{-- Desktop auth --}}
        <div class="nav-desktop items-center gap-6">
            <a href="#" class="text-[15px] font-normal text-gray-700 hover:text-gray-900 transition-colors">Log In</a>
            <a href="#" class="inline-flex items-center bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-semibold px-6 py-2.5 rounded-full text-[14px] transition-all whitespace-nowrap">
                List your property
            </a>
        </div>

        {{-- Hamburger — hidden on desktop via CSS --}}
        <button id="nav-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    {{-- Mobile / tablet menu --}}
    <nav id="mobile-menu" class="mobile-menu" aria-hidden="true">
        <div class="mobile-menu-inner">
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                Explore stays
            </a>
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Why verified?
            </a>
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Become a host
            </a>
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Help
            </a>
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Log In
            </a>
            <a href="#" class="cta-mobile">List your property</a>
        </div>
    </nav>
</header>

{{-- ===== HERO ===== --}}
<section class="relative bg-neutral-800" style="min-height:430px;overflow:hidden;padding-bottom:60px;">
    <div class="absolute inset-0 z-0">
        <img src="/image.png" alt="" class="w-full h-full object-cover object-center"/>
        {{-- Base dim across the whole image --}}
        <div class="absolute inset-0" style="background:rgba(0,0,0,0.45);"></div>
        {{-- Directional gradient on top for left-side text readability --}}
        <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(15,8,2,0.92) 0%,rgba(15,8,2,0.82) 22%,rgba(15,8,2,0.45) 50%,rgba(15,8,2,0.10) 72%,transparent 100%);"></div>
    </div>
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 hero-inner">
        <div class="hero-text-col reveal">
            <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(249,115,22,0.90);border-radius:999px;padding:4px 12px 4px 8px;margin-bottom:16px;">
                <span style="width:7px;height:7px;background:#fff;border-radius:50%;flex-shrink:0;"></span>
                <span style="font-family:'Inter',sans-serif;font-size:10px;font-weight:700;letter-spacing:0.09em;text-transform:uppercase;color:#fff;line-height:1;">Verification-first marketplace</span>
            </div>
            <h1 style="font-family:'Barlow',sans-serif;font-size:clamp(26px,5vw,34px);font-weight:900;line-height:1.10;color:#fff;margin:0 0 14px 0;">
                Find a stay you don't<br>have to <span style="color:#f97316;">second-guess.</span>
            </h1>
            <p style="font-family:'Inter',sans-serif;font-size:13px;line-height:1.65;color:rgba(255,255,255,0.72);margin:0 0 22px 0;">
                Browse shortlets across Lagos that have already passed ID checks and an on-site inspection. Every listing carries an AI-generated insight so you know what a photo alone won't tell you.
            </p>
            <div style="display:flex;align-items:flex-start;gap:22px;flex-wrap:wrap;">
                <div><div style="font-family:'Inter',sans-serif;font-size:26px;font-weight:800;color:#f97316;line-height:1;">12,000+</div><div style="font-family:'Inter',sans-serif;font-size:11px;color:rgba(255,255,255,0.55);margin-top:3px;">Verified Listings</div></div>
                <div><div style="font-family:'Inter',sans-serif;font-size:26px;font-weight:800;color:#f97316;line-height:1;">28</div><div style="font-family:'Inter',sans-serif;font-size:11px;color:rgba(255,255,255,0.55);margin-top:3px;">Cities Covered</div></div>
                <div><div style="font-family:'Inter',sans-serif;font-size:26px;font-weight:800;color:#f97316;line-height:1;">&lt; 48h</div><div style="font-family:'Inter',sans-serif;font-size:11px;color:rgba(255,255,255,0.55);margin-top:3px;">Avg. Verification Time</div></div>
            </div>
        </div>
        <div class="hero-img-col" style="margin-top:40px;align-self:flex-end;">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl" style="flex:0 0 48%;height:310px;">
                <img src="/hero1.jpg" alt="Lagos apartment" class="w-full h-full object-cover"/>
                <div style="position:absolute;top:10px;right:10px;width:52px;height:52px;background:#f97316;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.3);">
                    <svg width="14" height="14" fill="white" viewBox="0 0 20 20" style="margin-bottom:2px;"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span style="font-size:7px;font-weight:700;color:#fff;line-height:1.2;text-align:center;">Verified<br/>Stay</span>
                </div>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;gap:10px;height:310px;">
                <div class="rounded-2xl overflow-hidden shadow-2xl" style="flex:1;"><img src="/hero2.jpg" alt="Lagos balcony" class="w-full h-full object-cover"/></div>
                <div class="rounded-2xl overflow-hidden shadow-2xl" style="flex:1;"><img src="/hero3.jpg" alt="Lagos living room" class="w-full h-full object-cover"/></div>
            </div>
        </div>
    </div>
</section>

{{-- ===== SEARCH BAR ===== --}}
<div style="background:#fff;position:relative;z-index:40;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 search-bar-wrap">
        <form id="search-form" class="search-bar" onsubmit="handleSearch(event)">
            <div class="search-field search-field-where">
                <div style="font-family:'Inter',sans-serif;font-size:11px;font-weight:800;letter-spacing:0.07em;text-transform:uppercase;color:#111;margin-bottom:3px;">WHERE</div>
                <input id="search-where" type="text" placeholder="Lekki, Ikoyi, Victoria Island..."
                       style="font-family:'Inter',sans-serif;width:100%;font-size:14px;color:#374151;border:none;outline:none;background:transparent;"
                       autocomplete="off"/>
            </div>
            <div class="search-field search-field-dates">
                <div style="font-family:'Inter',sans-serif;font-size:11px;font-weight:800;letter-spacing:0.07em;text-transform:uppercase;color:#111;margin-bottom:3px;">CHECK IN / OUT</div>
                <input id="search-dates" type="text" placeholder="Add dates"
                       class="date-input" readonly
                       style="font-size:14px;"/>
            </div>
            <div class="search-field search-field-guests">
                <div style="font-family:'Inter',sans-serif;font-size:11px;font-weight:800;letter-spacing:0.07em;text-transform:uppercase;color:#111;margin-bottom:3px;">GUESTS</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <button type="button" id="guest-minus" aria-label="Remove guest" style="width:24px;height:24px;border-radius:50%;border:1.5px solid #d1d5db;display:flex;align-items:center;justify-content:center;font-size:16px;color:#6b7280;cursor:pointer;background:none;transition:border-color .15s,color .15s;">−</button>
                    <span id="guest-count" style="font-family:'Inter',sans-serif;font-size:14px;font-weight:600;color:#111;min-width:14px;text-align:center;">2</span>
                    <button type="button" id="guest-plus"  aria-label="Add guest"    style="width:24px;height:24px;border-radius:50%;border:1.5px solid #d1d5db;display:flex;align-items:center;justify-content:center;font-size:16px;color:#6b7280;cursor:pointer;background:none;transition:border-color .15s,color .15s;">+</button>
                </div>
            </div>
            <button type="submit" class="search-btn">
                <svg width="17" height="17" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                Search
            </button>
        </form>

        {{-- Date picker dropdown --}}
        <div id="date-picker" style="display:none;position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%);background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,0.15);padding:20px;z-index:50;width:320px;">
            <div class="flex gap-3 mb-3">
                <div style="flex:1;">
                    <label style="font-size:11px;font-weight:700;color:#111;letter-spacing:.06em;text-transform:uppercase;display:block;margin-bottom:6px;">Check In</label>
                    <input type="date" id="check-in"  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-400 transition-colors"/>
                </div>
                <div style="flex:1;">
                    <label style="font-size:11px;font-weight:700;color:#111;letter-spacing:.06em;text-transform:uppercase;display:block;margin-bottom:6px;">Check Out</label>
                    <input type="date" id="check-out" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-400 transition-colors"/>
                </div>
            </div>
            <button onclick="applyDates()" class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">Apply dates</button>
        </div>
    </div>
</div>

{{-- ===== CATEGORY PILLS ===== --}}
<div class="bg-white border-b border-gray-100" style="padding-top:28px;padding-bottom:20px;">
    <div id="cat-pills" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 flex gap-3 overflow-x-auto pb-1"
         style="-webkit-overflow-scrolling:touch;scrollbar-width:none;">
        @foreach ([
            ['label'=>'All stays','active'=>true],
            ['label'=>'Lekki','active'=>false],
            ['label'=>'Ikoyi','active'=>false],
            ['label'=>'Victoria Island','active'=>false],
            ['label'=>'Beachfront','active'=>false],
            ['label'=>'Family stays','active'=>false],
            ['label'=>'Business stays','active'=>false],
        ] as $cat)
            <button class="cat-pill {{ $cat['active'] ? 'active' : '' }}"
                    data-cat="{{ $cat['label'] }}"
                    onclick="handleCatPill(this)">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="pointer-events:none;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ $cat['label'] }}
            </button>
        @endforeach
    </div>
</div>

{{-- ===== MAIN CONTENT ===== --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-20">

    {{-- Recently Viewed --}}
    <section class="reveal">
        <div class="flex items-center justify-between mb-6 gap-4">
            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Recently Viewed <span class="text-orange-500">Apartments</span></h2>
            <a href="#" class="shrink-0 text-[13px] text-gray-700 border border-gray-300 rounded-full px-4 py-1.5 font-medium hover:border-orange-400 hover:text-orange-500 transition-colors">View All</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php
                $recentCards = [
                    ['img'=>'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=500&h=380&fit=crop','name'=>'Sunset Loft, Lekki Phase 1','loc'=>'Lekki, Lagos','guests'=>2,'price'=>45000,'rating'=>4.5],
                    ['img'=>'/hero2.jpg','name'=>'Skyline Studio, Ikoyi','loc'=>'Ikoyi, Lagos','guests'=>2,'price'=>52000,'rating'=>4.7],
                    ['img'=>'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=500&h=380&fit=crop','name'=>'Garden View, VI','loc'=>'Victoria Island','guests'=>4,'price'=>60000,'rating'=>4.6],
                    ['img'=>'/hero3.jpg','name'=>'Cosy Nest, GRA Ikeja','loc'=>'GRA, Ikeja','guests'=>3,'price'=>38000,'rating'=>4.4],
                ];
            @endphp
            @foreach ($recentCards as $idx => $c)
            <div class="card-listing" style="background:#fff;border-radius:16px;overflow:hidden;cursor:pointer;" tabindex="0" role="article">
                <div style="position:relative;">
                    <img src="{{ $c['img'] }}" alt="{{ $c['name'] }}" style="width:100%;height:200px;object-fit:cover;border-radius:16px;display:block;" loading="lazy"/>
                    <div style="position:absolute;top:10px;left:10px;background:rgba(255,255,255,0.92);border-radius:999px;padding:4px 10px;display:flex;align-items:center;gap:5px;">
                        <svg width="11" height="11" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span style="font-size:11px;font-weight:600;color:#111;">Verified</span>
                    </div>
                    <button class="heart-btn" onclick="toggleHeart(this)" aria-label="Save to wishlist">
                        <svg width="14" height="14" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                </div>
                <div style="padding:10px 4px 4px 4px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2px;">
                        <span style="font-size:14px;font-weight:700;color:#111;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:72%;">{{ $c['name'] }}</span>
                        <div style="display:flex;align-items:center;gap:3px;flex-shrink:0;margin-left:6px;">
                            <svg width="13" height="13" fill="#f97316" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span style="font-size:12px;font-weight:600;color:#111;">{{ $c['rating'] }}</span>
                        </div>
                    </div>
                    <p style="font-size:11px;color:#9ca3af;margin:0 0 6px 0;">{{ $c['loc'] }} · {{ $c['guests'] }} guests</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:13px;font-weight:700;color:#111;">₦{{ number_format($c['price']) }} <span style="font-weight:400;color:#9ca3af;font-size:12px;">/ night</span></span>
                        <button class="insights-btn" onclick="openInsights('{{ $c['name'] }}')">
                            <svg width="10" height="10" fill="#f97316" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                            AI Insights
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Popular Verified Stays --}}
    <section class="reveal">
        <div class="flex items-start justify-between mb-1 gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Popular verified <span class="text-orange-500">stays</span></h2>
                <p style="font-size:13px;color:#9ca3af;margin-top:4px;">Hand-picked listings across Lagos, Abuja and Port Harcourt.</p>
            </div>
            <a href="#" class="hidden sm:block shrink-0 text-[13px] text-gray-700 border border-gray-300 rounded-full px-4 py-1.5 font-medium hover:border-orange-400 hover:text-orange-500 transition-colors whitespace-nowrap mt-1">View all stays</a>
        </div>
        <div class="flex gap-2 mt-4 mb-6 flex-wrap">
            <select id="sort-select" class="text-[13px] border border-gray-300 rounded-full px-4 py-2 outline-none bg-white text-gray-700 cursor-pointer hover:border-orange-400 transition-colors focus:border-orange-400">
                <option value="relevance">Sort: Relevance</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
                <option value="rating">Top Rated</option>
            </select>
            <select id="guest-filter" class="text-[13px] border border-gray-300 rounded-full px-4 py-2 outline-none bg-white text-gray-700 cursor-pointer hover:border-orange-400 transition-colors focus:border-orange-400">
                <option value="0">Any guests</option>
                <option value="2">1–2 guests</option>
                <option value="4">3–4 guests</option>
                <option value="5">5+ guests</option>
            </select>
        </div>
        <div id="popular-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php
                $popularCards = [
                    ['img'=>'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=500&h=380&fit=crop','name'=>'Sunset Loft, Lekki Phase 1','loc'=>'Lekki, Lagos','guests'=>2,'price'=>38000,'rating'=>4.8,'cats'=>'lekki'],
                    ['img'=>'/hero2.jpg','name'=>'The Penthouse, Ikoyi','loc'=>'Ikoyi, Lagos','guests'=>4,'price'=>55000,'rating'=>4.6,'cats'=>'ikoyi'],
                    ['img'=>'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=500&h=380&fit=crop','name'=>'Marina View, VI','loc'=>'VI, Lagos','guests'=>3,'price'=>42000,'rating'=>4.9,'cats'=>'victoria island'],
                    ['img'=>'/hero3.jpg','name'=>'Beachfront Villa, Lekki','loc'=>'Lekki Phase 1','guests'=>6,'price'=>65000,'rating'=>4.7,'cats'=>'lekki beachfront'],
                    ['img'=>'/hero1.jpg','name'=>'Family Suite, Ikoyi','loc'=>'Ikoyi, Lagos','guests'=>5,'price'=>48000,'rating'=>4.5,'cats'=>'ikoyi family stays'],
                    ['img'=>'/hero2.jpg','name'=>'Business Flat, GRA','loc'=>'GRA, Ikeja','guests'=>2,'price'=>52000,'rating'=>4.8,'cats'=>'business stays'],
                    ['img'=>'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=500&h=380&fit=crop','name'=>'Ocean Breeze, VI','loc'=>'Victoria Island','guests'=>4,'price'=>35000,'rating'=>4.6,'cats'=>'victoria island beachfront'],
                    ['img'=>'/hero3.jpg','name'=>'Family Haven, Lekki','loc'=>'Lekki, Lagos','guests'=>6,'price'=>70000,'rating'=>4.9,'cats'=>'lekki family stays'],
                ];
            @endphp
            @foreach ($popularCards as $idx => $c)
            <div class="card-listing popular-card" style="background:#fff;border-radius:16px;overflow:hidden;cursor:pointer;"
                 data-price="{{ $c['price'] }}" data-rating="{{ $c['rating'] }}" data-guests="{{ $c['guests'] }}"
                 data-cats="{{ $c['cats'] }}"
                 tabindex="0" role="article">
                <div style="position:relative;">
                    <img src="{{ $c['img'] }}" alt="{{ $c['name'] }}" style="width:100%;height:200px;object-fit:cover;border-radius:16px;display:block;" loading="lazy"/>
                    <div style="position:absolute;top:10px;left:10px;background:rgba(255,255,255,0.92);border-radius:999px;padding:4px 10px;display:flex;align-items:center;gap:5px;">
                        <svg width="11" height="11" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span style="font-size:11px;font-weight:600;color:#111;">Verified</span>
                    </div>
                    <button class="heart-btn" onclick="toggleHeart(this)" aria-label="Save to wishlist">
                        <svg width="14" height="14" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                </div>
                <div style="padding:10px 4px 4px 4px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2px;">
                        <span style="font-size:14px;font-weight:700;color:#111;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:70%;">{{ $c['name'] }}</span>
                        <div style="display:flex;align-items:center;gap:3px;flex-shrink:0;">
                            <svg width="13" height="13" fill="#f97316" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span style="font-size:12px;font-weight:600;color:#111;">{{ $c['rating'] }}</span>
                        </div>
                    </div>
                    <p style="font-size:11px;color:#9ca3af;margin:0 0 6px 0;">{{ $c['loc'] }} · {{ $c['guests'] }} guests</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:13px;font-weight:700;color:#111;">₦{{ number_format($c['price']) }} <span style="font-weight:400;color:#9ca3af;font-size:12px;">/ night</span></span>
                        <button class="insights-btn" onclick="openInsights('{{ $c['name'] }}')">
                            <svg width="10" height="10" fill="#f97316" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                            AI Insights
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

</main>

{{-- ===== AI CONCIERGE ===== --}}
<section class="bg-gray-900 reveal" style="margin-top:80px;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12 items-start">
            <div class="text-white">
                <div class="text-xs font-semibold text-orange-400 uppercase tracking-widest mb-3">AI Trip Concierge</div>
                <h2 class="text-2xl sm:text-3xl font-bold mb-3">Ask before you book, not after.</h2>
                <p class="text-gray-400 text-sm leading-relaxed mb-7">Type a question about any neighbourhood, budget or house rule and get an answer grounded in verified listing data.</p>
                <div class="bg-gray-800 rounded-2xl overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-700">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-white font-medium">AI Trip Concierge</span>
                    </div>
                    <div id="chat-messages" class="px-4 py-5 space-y-4" style="min-height:140px;max-height:280px;overflow-y:auto;">
                        <div class="flex gap-3 chat-msg">
                            <div class="w-7 h-7 bg-orange-500 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="bg-gray-700 rounded-xl rounded-tl-none px-4 py-3 text-sm text-gray-200 max-w-xs">
                                Hi! I'm the Verified Shortlet concierge. Ask me about neighbourhoods, pricing or house rules for any verified stay.
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-3 flex gap-2 flex-wrap">
                        <button class="text-xs bg-gray-700 hover:bg-gray-600 active:scale-95 text-gray-300 px-3 py-1.5 rounded-full transition-all"
                                onclick="sendQuickMsg(this, 'Beachfront under ₦60k?')">Beachfront under ₦60k?</button>
                        <button class="text-xs bg-gray-700 hover:bg-gray-600 active:scale-95 text-gray-300 px-3 py-1.5 rounded-full transition-all"
                                onclick="sendQuickMsg(this, 'Stay for business?')">Stay for business?</button>
                        <button class="text-xs bg-gray-700 hover:bg-gray-600 active:scale-95 text-gray-300 px-3 py-1.5 rounded-full transition-all"
                                onclick="sendQuickMsg(this, 'Family-friendly in Lekki?')">Family-friendly in Lekki?</button>
                    </div>
                    <div class="flex items-center gap-3 border-t border-gray-700 px-4 py-3">
                        <input id="chat-input" type="text" placeholder="Ask about any verified stay…"
                               class="bg-transparent text-sm text-gray-300 placeholder-gray-600 outline-none flex-1 min-w-0"
                               onkeydown="if(event.key==='Enter') sendChat()"/>
                        <button onclick="sendChat()" aria-label="Send message"
                                class="w-8 h-8 bg-orange-500 hover:bg-orange-600 active:scale-90 rounded-full flex items-center justify-center transition-all shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="space-y-4 lg:pt-16">
                @foreach ([
                    ['tag'=>'Guest Matching','title'=>'Suggests stays based on what you ask','desc'=>'Mention "remote work" or "family trip" and the concierge narrows the list to stays that fit, not just what\'s available.','icon'=>'M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z'],
                    ['tag'=>'Plain-language Q&A','title'=>'Answers pulled from verified listing data','desc'=>'House rules, wifi speed, nearest supermarket — the concierge only speaks from what\'s actually been checked.','icon'=>'M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7z'],
                    ['tag'=>'For Hosts','title'=>'Pricing suggestions while you list','desc'=>'When you list a property, the same AI layer suggests a nightly rate based on comparable verified stays nearby.','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ] as $feat)
                <div class="bg-gray-800/60 hover:bg-gray-800 rounded-2xl p-5 border border-gray-700/50 transition-colors cursor-default">
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 bg-orange-500/20 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-orange-400 uppercase tracking-wide mb-1">{{ $feat['tag'] }}</div>
                            <div class="text-white font-semibold text-sm mb-1">{{ $feat['title'] }}</div>
                            <p class="text-gray-400 text-xs leading-relaxed">{{ $feat['desc'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ===== FROM LISTING TO BADGE ===== --}}
<div style="height:80px;background:#0a0a0a;"></div>
<section class="reveal" style="background:#f9fafb;padding:100px 0;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-16">
            <span style="display:inline-block;background:#fff3e8;color:#f97316;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:6px 16px;border-radius:999px;margin-bottom:16px;">Become a host</span>
            <h2 style="font-size:clamp(32px,4.5vw,52px);font-weight:800;color:#111;margin:0 0 14px 0;line-height:1.15;">From listing to badge, in <span style="color:#f97316;">four steps</span></h2>
            <p style="font-size:15px;color:#6b7280;max-width:460px;margin:0 auto;line-height:1.7;">The sequence every property moves through before it can appear in search results.</p>
        </div>

        {{-- Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['step'=>'01','title'=>'Register','desc'=>'The host creates a profile and adds the property details.','icon'=>'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
                ['step'=>'02','title'=>'Verify','desc'=>'ID checks and on-site property details are reviewed by our team.','icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['step'=>'03','title'=>'Approve','desc'=>'The listing is reviewed and admitted to the verified directory.','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['step'=>'04','title'=>'Go Live','desc'=>'Your stay goes live in search with the verified badge visible to all guests.','icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
            ] as $idx => $step)
            <div style="background:#fff;border-radius:20px;padding:32px 28px;border:1px solid #f0f0f0;position:relative;overflow:hidden;transition:box-shadow .2s,transform .2s;"
                 onmouseover="this.style.boxShadow='0 12px 40px rgba(0,0,0,0.08)';this.style.transform='translateY(-4px)'"
                 onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)'">

                {{-- Step number watermark --}}
                <div style="position:absolute;top:-8px;right:16px;font-size:72px;font-weight:900;color:#f97316;opacity:.06;line-height:1;user-select:none;pointer-events:none;">{{ $step['step'] }}</div>

                {{-- Icon --}}
                <div style="width:48px;height:48px;background:#fff3e8;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                    <svg width="22" height="22" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="{{ $step['icon'] }}"/>
                    </svg>
                </div>

                {{-- Step label --}}
                <div style="font-size:11px;font-weight:700;color:#f97316;letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px;">Step {{ $step['step'] }}</div>

                {{-- Title --}}
                <div style="font-size:17px;font-weight:700;color:#111;margin-bottom:10px;">{{ $step['title'] }}</div>

                {{-- Description --}}
                <p style="font-size:13px;color:#6b7280;line-height:1.7;margin:0;">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- CTA --}}
        <div style="text-align:center;margin-top:52px;">
            <a href="#" style="display:inline-flex;align-items:center;gap:8px;background:#f97316;color:#fff;font-size:15px;font-weight:600;padding:14px 36px;border-radius:999px;text-decoration:none;transition:background .15s,transform .1s;"
               onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'"
               onmousedown="this.style.transform='scale(.97)'" onmouseup="this.style.transform='scale(1)'">
                Start listing your property
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

    </div>
</section>

<div style="height:80px;background:#0a0a0a;"></div>

{{-- ===== BOOKING WORKS DIFFERENTLY ===== --}}
<section style="background:#0a0a0a;padding:80px 0;position:relative;overflow:hidden;" class="reveal">
    <div style="position:absolute;top:-60px;right:-60px;width:260px;height:260px;background:#2a1a0e;border-radius:50%;pointer-events:none;"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="mb-14">
            <div style="font-size:11px;font-weight:700;color:#f97316;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px;">Why guests choose us</div>
            <h2 style="font-size:clamp(24px,4vw,32px);font-weight:800;color:#fff;margin:0 0 14px 0;">Booking here works differently</h2>
            <p style="font-size:14px;color:rgba(255,255,255,0.55);max-width:480px;line-height:1.7;margin:0;">
                Search, compare and book stays that have already passed verification —
                with an AI concierge on hand for anything a listing page can't answer.
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach([
                ['icon_html'=>'<circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>','fill'=>false,'title'=>'Search by neighbourhood','desc'=>'Filter by area, dates and guest count, and browse a grid built for comparing stays quickly.'],
                ['icon_html'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>','fill'=>false,'title'=>'Verified property pages','desc'=>'ID checks, an on-site inspection and photo verification — every badge you see means something.'],
                ['icon_html'=>'star','fill'=>true,'title'=>'AI insight on every listing','desc'=>'A short, specific read on wifi, noise, safety or host responsiveness — pulled from verified data and guest notes.'],
            ] as $card)
            <div style="background:#fff;border-radius:16px;padding:28px 24px;" class="hover:-translate-y-1 hover:shadow-xl transition-all duration-200 cursor-default">
                <div style="width:44px;height:44px;background:#fff3ec;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
                    @if($card['icon_html']==='star')
                        <svg width="20" height="20" fill="#f97316" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @else
                        <svg width="20" height="20" fill="none" stroke="#f97316" stroke-width="2.5" viewBox="0 0 24 24">{!! $card['icon_html'] !!}</svg>
                    @endif
                </div>
                <div style="font-size:15px;font-weight:700;color:#111;margin-bottom:8px;">{{ $card['title'] }}</div>
                <p style="font-size:13px;color:#6b7280;line-height:1.65;margin:0;">{{ $card['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== HOST CTA ===== --}}
<section style="background:#f5f5f5;padding:80px 0;" class="reveal">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 style="font-size:clamp(40px,6vw,72px);font-weight:900;color:#111;line-height:1.08;margin:0 0 20px 0;letter-spacing:-0.03em;">
            List a shortlet, earn from <span style="color:#f97316;">verified guests</span>
        </h2>
        <p class="text-sm text-gray-500 mb-8">No dashboards to learn first. Add your property, get verified, and go live.</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            @foreach ([
                ['stat'=>'< 48h',       'desc'=>'Average time from submitting documents to going live.'],
                ['stat'=>'AI-suggested','desc'=>'Nightly pricing based on comparable verified stays nearby.'],
                ['stat'=>'1 badge',     'desc'=>'One verification badge, shown on your listing for as long as it\'s active.'],
            ] as $item)
            <div class="bg-white border border-gray-200 rounded-xl p-5 text-left hover:border-orange-300 hover:shadow-sm transition-all duration-200">
                <div class="text-[17px] font-extrabold text-orange-500 mb-1.5">{{ $item['stat'] }}</div>
                <p class="text-xs text-gray-400 leading-relaxed">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
        <a href="#" class="block w-full bg-orange-500 hover:bg-orange-600 active:scale-[.98] text-white text-[15px] font-bold py-4 rounded-full text-center transition-all no-underline">
            Register your property
        </a>
    </div>
</section>

{{-- ===== GET STARTED CARD ===== --}}
<section style="background:#f5f5f5;padding:0 0 80px 0;" class="reveal">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl text-center" style="background:#fef0e6;padding:clamp(28px,6vw,48px) clamp(20px,6vw,40px);">
            <div style="font-size:11px;font-weight:700;color:#f97316;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:14px;">Get Started</div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-3">
                Ready to book a stay you don't have to second-guess?
            </h2>
            <p class="text-sm text-gray-500 mb-7 max-w-xs mx-auto leading-relaxed">
                Browse verified stays across Lagos, or list your own property in under 48 hours.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#"
                   class="inline-flex items-center justify-center gap-2.5 bg-orange-500 hover:bg-orange-600 active:scale-95 text-white text-[15px] font-semibold px-8 py-3.5 rounded-full transition-all text-center no-underline shadow-lg shadow-orange-200"
                   style="letter-spacing:0.01em;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    Browse verified stays
                </a>
                <a href="#"
                   class="inline-flex items-center justify-center gap-2.5 bg-white hover:bg-orange-50 active:scale-95 text-gray-900 text-[15px] font-semibold px-8 py-3.5 rounded-full border-2 border-gray-200 hover:border-orange-400 hover:text-orange-500 transition-all text-center no-underline"
                   style="letter-spacing:0.01em;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    List your property
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ===== FOOTER ===== --}}
<footer style="background:#1e1e1e;padding:72px 0 0 0;" class="reveal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
        <div class="footer-grid">
            <div class="footer-brand">
                <div style="margin-bottom:16px;">
                    <img src="/logo.png" alt="Verified Shortlet" style="height:44px;width:auto;filter:brightness(0) invert(1);"/>
                </div>
                <p style="font-size:13px;color:#9ca3af;line-height:1.7;max-width:260px;margin:0;">
                    A verified shortlet marketplace across Lagos's most trusted neighbourhoods, with an AI concierge to help you choose.
                </p>
            </div>
            @foreach([
                ['heading'=>'Company','links'=>['About us','Careers','Blog','Contact']],
                ['heading'=>'Explore','links'=>['Featured stays','Categories','AI concierge','Become a host']],
                ['heading'=>'Support','links'=>['Help centre','FAQ','Trust & safety']],
                ['heading'=>'Legal','links'=>['Privacy policy','Terms & conditions']],
            ] as $col)
            <div>
                <div style="font-size:12px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:18px;">{{ $col['heading'] }}</div>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:12px;">
                    @foreach($col['links'] as $link)
                    <li>
                        <a href="#" class="footer-link" style="font-size:14px;color:#9ca3af;text-decoration:none;transition:color .15s;"
                           onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#9ca3af'">{{ $link }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
        <div style="border-top:1px solid #2e2e2e;padding:20px 0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <span style="font-size:13px;color:#6b7280;">© {{ date('Y') }} Verified Shortlet. All rights reserved.</span>
            <div style="display:flex;gap:16px;">
                {{-- Social icons --}}
                @foreach([
                    ['label'=>'Twitter/X','path'=>'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.741l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
                    ['label'=>'Instagram','path'=>'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z'],
                    ['label'=>'LinkedIn','path'=>'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
                ] as $social)
                <a href="#" aria-label="{{ $social['label'] }}"
                   style="color:#6b7280;transition:color .15s;"
                   onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#6b7280'">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $social['path'] }}"/></svg>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

<script>
/* ─── Helpers ─── */
function showToast(msg, duration = 2800) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), duration);
}

/* ─── Navbar ─── */
const navToggle = document.getElementById('nav-toggle');
const mobileMenu = document.getElementById('mobile-menu');
const siteHeader = document.getElementById('site-header');

// Scroll shadow
window.addEventListener('scroll', () => {
    siteHeader.classList.toggle('scrolled', window.scrollY > 10);
}, { passive: true });

// Active nav link based on scroll position
const sections = [
    { id: 'explore',   label: 'Explore stays' },
    { id: 'concierge', label: 'Why verified?' },
    { id: 'host',      label: 'Become a host' },
];
const navLinks = document.querySelectorAll('.nav-link');
navLinks.forEach(link => {
    link.addEventListener('click', function () {
        navLinks.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

// Hamburger toggle — smooth slide via grid-template-rows
navToggle.addEventListener('click', () => {
    const isOpen = mobileMenu.classList.toggle('open');
    navToggle.classList.toggle('open', isOpen);
    navToggle.setAttribute('aria-expanded', isOpen);
    mobileMenu.setAttribute('aria-hidden', !isOpen);
});

// Close mobile menu on outside click
document.addEventListener('click', e => {
    if (!navToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
        mobileMenu.classList.remove('open');
        navToggle.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
        mobileMenu.setAttribute('aria-hidden', 'true');
    }
});

// Close on Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
        mobileMenu.classList.remove('open');
        navToggle.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.focus();
    }
});

// Close mobile menu when resized to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        mobileMenu.classList.remove('open');
        navToggle.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
        mobileMenu.setAttribute('aria-hidden', 'true');
    }
});

/* ─── Guest counter ─── */
let guestCount = 2;
document.getElementById('guest-minus').addEventListener('click', () => {
    if (guestCount > 1) { guestCount--; document.getElementById('guest-count').textContent = guestCount; }
});
document.getElementById('guest-plus').addEventListener('click', () => {
    if (guestCount < 20) { guestCount++; document.getElementById('guest-count').textContent = guestCount; }
});

/* ─── Date picker dropdown ─── */
const dateInput = document.getElementById('search-dates');
const datePicker = document.getElementById('date-picker');
dateInput.addEventListener('click', e => {
    e.stopPropagation();
    datePicker.style.display = datePicker.style.display === 'none' ? 'block' : 'none';
});
document.addEventListener('click', e => {
    if (!datePicker.contains(e.target) && e.target !== dateInput) {
        datePicker.style.display = 'none';
    }
});
// Set min dates to today
const today = new Date().toISOString().split('T')[0];
document.getElementById('check-in').min  = today;
document.getElementById('check-out').min = today;
document.getElementById('check-in').addEventListener('change', function () {
    document.getElementById('check-out').min = this.value;
});

function applyDates() {
    const ci = document.getElementById('check-in').value;
    const co = document.getElementById('check-out').value;
    if (!ci || !co) { showToast('Please select both check-in and check-out dates.'); return; }
    if (ci >= co) { showToast('Check-out must be after check-in.'); return; }
    const fmt = d => new Date(d).toLocaleDateString('en-GB',{day:'numeric',month:'short'});
    dateInput.value = `${fmt(ci)} → ${fmt(co)}`;
    datePicker.style.display = 'none';
}

/* ─── Search form ─── */
function handleSearch(e) {
    e.preventDefault();
    const where = document.getElementById('search-where').value.trim();
    const dates = document.getElementById('search-dates').value.trim();
    if (!where) { showToast('Please enter a location to search.'); document.getElementById('search-where').focus(); return; }
    if (!dates) { showToast('Please add your travel dates.'); dateInput.click(); return; }
    showToast(`Searching ${guestCount} guest${guestCount > 1 ? 's' : ''} in ${where}…`);
}

/* ─── Category pills ─── */
let activeCat = 'all stays';

function handleCatPill(btn) {
    document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    activeCat = btn.dataset.cat.toLowerCase();
    filterCards();
}

// Also handle via event delegation as fallback
document.getElementById('cat-pills').addEventListener('click', e => {
    const pill = e.target.closest('.cat-pill');
    if (!pill) return;
    handleCatPill(pill);
});

function filterCards() {
    const grid     = document.getElementById('popular-grid');
    const cards    = Array.from(grid.querySelectorAll('.popular-card'));
    const guestVal = parseInt(document.getElementById('guest-filter').value);
    let anyVisible = false;

    cards.forEach(card => {
        const cats   = card.dataset.cats.toLowerCase();
        const guests = parseInt(card.dataset.guests);

        const catMatch   = activeCat === 'all stays' || cats.includes(activeCat);
        let   guestMatch = true;
        if (guestVal === 2 && guests > 2)                  guestMatch = false;
        if (guestVal === 4 && (guests < 3 || guests > 4))  guestMatch = false;
        if (guestVal === 5 && guests < 5)                  guestMatch = false;

        const show = catMatch && guestMatch;
        card.style.display = show ? '' : 'none';
        if (show) anyVisible = true;
    });

    // Empty state
    let emptyEl = grid.querySelector('.cat-empty');
    if (!anyVisible) {
        if (!emptyEl) {
            emptyEl = document.createElement('div');
            emptyEl.className = 'cat-empty';
            emptyEl.style.cssText = 'grid-column:1/-1;text-align:center;padding:48px 0;color:#9ca3af;font-size:14px;';
            emptyEl.innerHTML = '<div style="font-size:32px;margin-bottom:12px;">🏠</div>No stays found for this filter.<br><span style="font-size:12px;">Try a different category or guest count.</span>';
        }
        grid.appendChild(emptyEl);
    } else {
        emptyEl?.remove();
    }

    sortCards();
}

/* ─── Wishlist / Heart toggle ─── */
function toggleHeart(btn) {
    const liked = btn.classList.toggle('liked');
    btn.querySelector('svg').setAttribute('stroke', liked ? '#ef4444' : '#6b7280');
    showToast(liked ? 'Added to wishlist ♥' : 'Removed from wishlist');
}

/* ─── AI Insights modal ─── */
const insightsData = {
    default: [
        { label: '📶 WiFi', value: 'Fast fibre connection (tested at 85 Mbps). Great for remote work.' },
        { label: '🔇 Noise',  value: 'Quiet residential street. Minimal disturbance after 10 PM.' },
        { label: '🛡 Safety', value: 'Gated compound, 24/7 security, CCTV on site.' },
        { label: '📍 Location', value: '5 min walk to nearest supermarket. 12 min drive to the beach.' },
        { label: '🏠 Host',   value: 'Average response time under 1 hour. Consistently positive reviews.' },
    ]
};

function openInsights(name) {
    const modal = document.getElementById('insights-modal');
    document.getElementById('modal-title').textContent = `AI Insights – ${name}`;
    const body = document.getElementById('modal-body');
    const items = insightsData.default;
    body.innerHTML = items.map(i =>
        `<div style="display:flex;gap:10px;align-items:flex-start;padding:10px 0;border-bottom:1px solid #f3f4f6;">
            <span style="font-size:13px;font-weight:600;color:#111;min-width:110px;">${i.label}</span>
            <span style="font-size:13px;color:#6b7280;">${i.value}</span>
        </div>`
    ).join('');
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeInsights() {
    document.getElementById('insights-modal').classList.remove('open');
    document.body.style.overflow = '';
}

document.getElementById('modal-close').addEventListener('click', closeInsights);
document.getElementById('modal-overlay').addEventListener('click', closeInsights);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeInsights(); });

/* ─── Sort & filter popular stays ─── */
function sortCards() {
    const sortVal = document.getElementById('sort-select').value;
    const grid    = document.getElementById('popular-grid');
    const visible = Array.from(grid.querySelectorAll('.popular-card'))
                         .filter(c => c.style.display !== 'none');

    visible.sort((a, b) => {
        if (sortVal === 'price-asc')  return parseFloat(a.dataset.price)  - parseFloat(b.dataset.price);
        if (sortVal === 'price-desc') return parseFloat(b.dataset.price)  - parseFloat(a.dataset.price);
        if (sortVal === 'rating')     return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
        return 0;
    });
    visible.forEach(c => grid.appendChild(c));
}

// Both dropdowns now go through filterCards so category + guest + sort all work together
document.getElementById('sort-select').addEventListener('change', filterCards);
document.getElementById('guest-filter').addEventListener('change', filterCards);

/* ─── AI Chat ─── */
const chatResponses = {
    'beachfront': 'We have 14 verified beachfront stays under ₦60k/night. Top picks are in Lekki Phase 1 and Ilashe. Want me to narrow by dates?',
    'business':   'For business trips we recommend stays in VI, Ikoyi, or Abuja CBD — all have high-speed wifi verified on site and quiet work environments.',
    'family':     'Family-friendly picks in Lekki include 3–5 bedroom flats with secure compounds, dedicated parking, and verified baby-proofed units.',
    'lekki':      'Lekki has 340+ verified stays ranging from ₦30k to ₦150k/night. Most popular: Lekki Phase 1 and Ikate. Shall I filter by dates?',
    'ikoyi':      'Ikoyi has some of Lagos's most premium verified stays. Expect high-end furnishing and concierge-level hosting. Average ₦60k–₦200k/night.',
    'wifi':       'Every verified listing has its wifi speed tested during inspection. We only certify speeds above 20 Mbps for business and 10 Mbps for standard.',
    'default':    'Great question! Our verified listings cover Lagos, Abuja and Port Harcourt. Want me to search by location or budget for you?',
};

function getBotReply(msg) {
    const m = msg.toLowerCase();
    if (m.includes('beach')) return chatResponses.beachfront;
    if (m.includes('business') || m.includes('work')) return chatResponses.business;
    if (m.includes('family') || m.includes('kid') || m.includes('child')) return chatResponses.family;
    if (m.includes('lekki')) return chatResponses.lekki;
    if (m.includes('ikoyi')) return chatResponses.ikoyi;
    if (m.includes('wifi') || m.includes('internet') || m.includes('speed')) return chatResponses.wifi;
    return chatResponses.default;
}

function appendChatMsg(text, isUser = false) {
    const box = document.getElementById('chat-messages');
    const wrap = document.createElement('div');
    wrap.className = 'flex gap-3 chat-msg' + (isUser ? ' justify-end' : '');
    if (isUser) {
        wrap.innerHTML = `<div style="background:#f97316;border-radius:12px;border-bottom-right-radius:4px;padding:10px 14px;font-size:13px;color:#fff;max-width:75%;">${text}</div>`;
    } else {
        wrap.innerHTML = `
            <div class="w-7 h-7 bg-orange-500 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
            </div>
            <div style="background:#374151;border-radius:12px;border-top-left-radius:4px;padding:10px 14px;font-size:13px;color:#e5e7eb;max-width:75%;">${text}</div>`;
    }
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
}

function sendChat() {
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg) return;
    input.value = '';
    appendChatMsg(msg, true);

    // Typing indicator
    const typing = document.createElement('div');
    typing.className = 'flex gap-3 chat-msg';
    typing.id = 'typing-indicator';
    typing.innerHTML = `
        <div class="w-7 h-7 bg-orange-500 rounded-full flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
        </div>
        <div style="background:#374151;border-radius:12px;border-top-left-radius:4px;padding:10px 14px;font-size:13px;color:#9ca3af;">
            <span style="letter-spacing:.1em;">●●●</span>
        </div>`;
    document.getElementById('chat-messages').appendChild(typing);
    document.getElementById('chat-messages').scrollTop = 9999;

    setTimeout(() => {
        document.getElementById('typing-indicator')?.remove();
        appendChatMsg(getBotReply(msg));
    }, 900 + Math.random() * 400);
}

function sendQuickMsg(btn, text) {
    btn.disabled = true;
    btn.style.opacity = '.5';
    document.getElementById('chat-input').value = text;
    sendChat();
}

/* ─── Scroll reveal ─── */
// Add the hidden class via JS only — so content is visible if JS fails or is slow
document.querySelectorAll('.reveal').forEach(el => el.classList.add('reveal-ready'));

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal-ready').forEach(el => {
    // Elements already in viewport (above fold) get shown immediately
    const rect = el.getBoundingClientRect();
    if (rect.top < window.innerHeight) {
        el.classList.add('visible');
    } else {
        revealObserver.observe(el);
    }
});
</script>

</body>
</html>
