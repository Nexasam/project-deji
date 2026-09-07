<header 
    id="site-header" 
    class="sticky top-0 z-50 bg-white border-b border-gray-100"
    x-data="{ mobileMenuOpen: false }"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 flex items-center justify-between" style="height:68px;">
        {{-- Logo --}}
        <a href="/" class="flex items-center shrink-0">
            <img src="/logo.png" alt="Verified Shortlet" style="height:58px;width:auto;"/>
        </a>

        {{-- Desktop nav --}}
        <nav class="nav-desktop items-center gap-10">
            <a href="#" class="nav-link active">Explore stays</a>
            <a href="#" class="nav-link">Why verified?</a>
            <a href="#" class="nav-link">Become a host</a>
            <a href="#" class="nav-link">Help</a>
        </nav>

        {{-- Desktop auth --}}
        <div class="nav-desktop items-center gap-6">
            <button 
                @click="$store.modals.openLogin()" 
                class="inline-flex h-10 items-center justify-center text-[15px] font-normal text-gray-700 hover:text-gray-900 transition-colors"
            >
                Log In
            </button>
            <a 
                href="{{ route('register') }}"
                class="inline-flex items-center bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-semibold px-6 py-2.5 rounded-full text-[14px] transition-all whitespace-nowrap"
            >
                Get started
            </a>
        </div>

        {{-- Hamburger --}}
        <button 
            id="nav-toggle"
            @click="mobileMenuOpen = !mobileMenuOpen"
            :class="{ 'open': mobileMenuOpen }"
            aria-label="Toggle menu" 
            :aria-expanded="mobileMenuOpen"
            aria-controls="mobile-menu"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    {{-- Mobile menu --}}
    <nav 
        id="mobile-menu" 
        class="mobile-menu" 
        :class="{ 'open': mobileMenuOpen }"
        :aria-hidden="!mobileMenuOpen"
    >
        <div class="mobile-menu-inner">
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                Explore stays
            </a>
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Why verified?
            </a>
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Become a host
            </a>
            <a href="#">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Help
            </a>
            <button 
                @click="mobileMenuOpen = false; $store.modals.openLogin()"
                class="flex items-center w-full"
            >
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:10px;opacity:.5;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Log In
            </button>
            <a 
                href="{{ route('register') }}"
                @click="mobileMenuOpen = false" 
                class="cta-mobile"
            >
                Get started
            </a>
        </div>
    </nav>
</header>
