@php
    $isExploreActive = request()->routeIs('home') || request()->routeIs('home.index2') || request()->routeIs('marketplace.show');
    $isDashboardActive = request()->routeIs('guest.dashboard');
    $isBookingsActive = request()->routeIs('guest.bookings.*') && !request()->routeIs('guest.bookings.messages.*');
    $isCalendarActive = request()->routeIs('guest.calendar');
    $isMessagesActive = request()->routeIs('guest.messages.*') || request()->routeIs('guest.bookings.messages.*');
    $isSettingsActive = request()->routeIs('guest.settings.*') || request()->routeIs('profile.*');
    $isFavouritesActive = request()->routeIs('guest.favourites.*');
    $guestLink = fn (bool $active) => 'inline-flex h-[68px] items-center border-b-2 px-1 text-[13px] font-semibold transition '.($active ? 'border-orange-600 text-orange-600' : 'border-transparent text-slate-800 hover:text-orange-600');
    $favouritesCount = auth()->check() ? auth()->user()->favourites()->count() : 0;
    $hasBusinessAccess = auth()->check() && auth()->user()->businessMemberships()->where('status', 'active')->exists();
@endphp

<header id="site-header" class="sticky top-0 z-50 border-b border-slate-200 bg-white" x-data="{ mobileMenuOpen: false, accountOpen: false }">
    <div class="mx-auto flex h-[68px] max-w-[1440px] items-center px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
            <img src="/logo.png" alt="Verified Shortlet" class="h-11 w-auto sm:h-12">
            <span class="hidden text-[15px] font-bold text-slate-500 sm:inline">Verified Shortlet</span>
        </a>

        @auth
            <nav class="nav-desktop ml-10 items-center gap-8 xl:ml-14 xl:gap-10" aria-label="Guest navigation">
                <a href="{{ route('guest.dashboard') }}" class="{{ $guestLink($isDashboardActive) }}" @if($isDashboardActive) aria-current="page" @endif>Dashboard</a>
                <a href="{{ route('home') }}" class="{{ $guestLink($isExploreActive) }}" @if($isExploreActive) aria-current="page" @endif>Explore stays</a>
                <a href="{{ route('guest.bookings.index') }}" class="{{ $guestLink($isBookingsActive) }}" @if($isBookingsActive) aria-current="page" @endif>Bookings</a>
                <a href="{{ route('guest.calendar') }}" class="{{ $guestLink($isCalendarActive) }}" @if($isCalendarActive) aria-current="page" @endif>Calendar</a>
                <a href="{{ route('guest.messages.index') }}" class="{{ $guestLink($isMessagesActive) }}" @if($isMessagesActive) aria-current="page" @endif>Messages</a>
                <a href="{{ route('guest.settings.edit') }}" class="{{ $guestLink($isSettingsActive) }}" @if($isSettingsActive) aria-current="page" @endif>Settings</a>
            </nav>

            <div class="nav-desktop ml-auto items-center gap-4">
                <div class="flex items-center rounded-full border border-slate-950 p-1">
                    <a href="{{ route('home') }}" aria-label="Explore stays" class="grid size-9 place-items-center rounded-full {{ $isExploreActive ? 'bg-orange-600 text-white' : 'text-slate-950' }}"><svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 11.5 12 4l9 7.5v8a1.5 1.5 0 0 1-1.5 1.5H15v-6H9v6H4.5A1.5 1.5 0 0 1 3 19.5v-8Z"/></svg></a>
                    <a href="{{ route('guest.dashboard') }}" aria-label="Guest dashboard" class="grid size-9 place-items-center rounded-full {{ !$isExploreActive ? 'bg-orange-600 text-white' : 'text-slate-950' }}"><svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm-9 9a9 9 0 0 1 18 0H3Z"/></svg></a>
                </div>
                <a href="{{ route('notifications.index') }}" aria-label="Notifications" class="relative grid size-11 place-items-center rounded-lg border border-slate-200 text-orange-600 hover:bg-orange-50"><svg class="size-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Zm-8 11a2 2 0 0 0 4 0h-4Z"/></svg><span class="absolute right-2 top-2 size-1.5 rounded-full bg-orange-600"></span></a>
                <div class="relative">
                    <button type="button" @click="accountOpen=!accountOpen" @click.outside="accountOpen=false" class="grid size-11 place-items-center rounded-full bg-orange-400 text-xs font-black text-white" aria-label="Open account menu">{{ str(auth()->user()->name)->squish()->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->join('') }}</button>
                    <div x-show="accountOpen" x-cloak x-transition class="absolute right-0 mt-3 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl">
                        <div class="border-b border-slate-100 px-3 py-3"><p class="truncate text-sm font-bold text-slate-950">{{ auth()->user()->name }}</p><p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p></div>
                        <a href="{{ route('guest.favourites.index') }}" class="mt-2 flex items-center rounded-xl px-3 py-2.5 text-sm font-semibold {{ $isFavouritesActive ? 'bg-orange-50 text-orange-700' : 'text-slate-700 hover:bg-slate-50' }}">Favourites <span x-show="$store.favourites.count > 0" x-cloak x-text="$store.favourites.count" class="ml-auto rounded-full bg-orange-600 px-2 py-0.5 text-[10px] text-white"></span></a>
                        @if($hasBusinessAccess)
                            <a href="{{ route('owner.entry') }}" class="flex items-center rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Business</a>
                        @else
                            <span class="flex cursor-not-allowed items-center rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-400" title="Sorry, you cannot access the business workspace with this account.">Business unavailable</span>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="flex w-full rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50">Log out</button></form>
                    </div>
                </div>
            </div>
        @else
            <nav class="nav-desktop ml-auto items-center gap-8">
                <a href="{{ route('home') }}" class="nav-link {{ $isExploreActive ? 'active' : '' }}">Explore stays</a>
                <a href="#" class="nav-link">Why verified?</a>
                <a href="#" class="nav-link">Become a host</a>
            @if(request()->routeIs('home'))<button @click="$store.modals.openLogin()" class="inline-flex items-center justify-center text-sm font-semibold text-slate-700">Log In</button>@else<a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="text-sm font-semibold text-slate-700">Log In</a>@endif
                <a href="{{ route('register') }}" class="rounded-full bg-orange-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-orange-700">Get started</a>
            </nav>
        @endauth

        <button id="nav-toggle" @click="mobileMenuOpen=!mobileMenuOpen" :class="{ 'open': mobileMenuOpen }" aria-label="Toggle menu" :aria-expanded="mobileMenuOpen" aria-controls="mobile-menu" class="ml-auto"><span></span><span></span><span></span></button>
    </div>

    <nav id="mobile-menu" class="mobile-menu" :class="{ 'open': mobileMenuOpen }" :aria-hidden="!mobileMenuOpen">
        <div class="mobile-menu-inner">
            @auth
                <div class="border-b border-slate-100 px-6 py-4"><p class="font-bold text-slate-950">{{ auth()->user()->name }}</p><p class="text-xs text-slate-500">{{ auth()->user()->email }}</p></div>
                <a href="{{ route('guest.dashboard') }}" class="{{ $isDashboardActive ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('home') }}" class="{{ $isExploreActive ? 'active' : '' }}">Explore stays</a>
                <a href="{{ route('guest.bookings.index') }}" class="{{ $isBookingsActive ? 'active' : '' }}">Bookings</a>
                <a href="{{ route('guest.calendar') }}" class="{{ $isCalendarActive ? 'active' : '' }}">Calendar</a>
                <a href="{{ route('guest.messages.index') }}" class="{{ $isMessagesActive ? 'active' : '' }}">Messages</a>
                <a href="{{ route('guest.settings.edit') }}" class="{{ $isSettingsActive ? 'active' : '' }}">Settings</a>
                <a href="{{ route('guest.favourites.index') }}" class="{{ $isFavouritesActive ? 'active' : '' }}">Favourites <span x-show="$store.favourites.count > 0" x-cloak x-text="$store.favourites.count" class="ml-auto rounded-full bg-orange-600 px-2 py-0.5 text-[10px] text-white"></span></a>
                @if($hasBusinessAccess)<a href="{{ route('owner.entry') }}">Business</a>@else<span class="flex cursor-not-allowed text-slate-400">Business unavailable · Sorry, you cannot access this page.</span>@endif
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="flex w-full">Log out</button></form>
            @else
                <a href="{{ route('home') }}" class="{{ $isExploreActive ? 'active' : '' }}">Explore stays</a><a href="#">Why verified?</a><a href="#">Become a host</a>
                @if(request()->routeIs('home'))<button @click="mobileMenuOpen=false; $store.modals.openLogin()" class="flex w-full">Log In</button>@else<a href="{{ route('login') }}">Log In</a>@endif
                <a href="{{ route('register') }}" class="cta-mobile">Get started</a>
            @endauth
        </div>
    </nav>
</header>
