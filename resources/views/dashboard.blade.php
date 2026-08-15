<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F4F4] font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        @include('partials.sidebar-nav', ['active' => 'dashboard'])

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top Header --}}
            <header class="bg-white border-b border-gray-200 h-16 flex items-center px-4 md:px-6">
                <div class="flex items-center justify-between w-full gap-3">
                    <div class="flex items-center gap-2 md:gap-4">
                        {{-- Hamburger – mobile only --}}
                        <button @click="sidebarOpen = true"
                                class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <button class="hidden sm:flex items-center gap-2 px-3 py-2 bg-orange-50 border border-orange-200 rounded-lg">
                            <svg class="w-4 h-4 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">System</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Property selector dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                class="flex items-center gap-2 md:gap-3 px-2 md:px-3 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors min-w-0 md:min-w-[220px] max-w-[180px] md:max-w-none">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                    </svg>
                                </div>
                                <div class="flex items-center gap-1 flex-1 min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Viewing</span>
                                    <span class="text-sm font-semibold text-gray-900 truncate ml-1">Lekki Waterview Suites</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown panel --}}
                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-2xl shadow-xl z-50 overflow-hidden w-[calc(100vw-2rem)] max-w-[420px]">

                                {{-- Current viewing header --}}
                                <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
                                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Viewing</div>
                                        <div class="text-sm font-bold text-gray-900">Lekki Waterview Suites</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>

                                {{-- Search --}}
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-xl">
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input type="text" placeholder="Find a property..."
                                            class="flex-1 bg-transparent text-sm text-gray-700 outline-none placeholder:text-gray-400"/>
                                    </div>
                                </div>

                                {{-- Property list --}}
                                <div class="max-h-72 overflow-y-auto">
                                    <div class="px-4 pt-3 pb-1">
                                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Your Properties (5)</p>
                                    </div>

                                    {{-- Egbeda group --}}
                                    <div class="px-4 pb-1">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Egbeda</p>
                                    </div>
                                    @foreach([
                                        ['initials'=>'BS','name'=>'Bluewater Suite 4B',    'loc'=>'Egbeda, Lagos','bg'=>'#1F2937','active'=>false],
                                        ['initials'=>'SA','name'=>'Sunset Apartment',       'loc'=>'Egbeda, Lagos','bg'=>'#FF5A00','active'=>true],
                                        ['initials'=>'BS','name'=>'Bluewater Suite 4B',    'loc'=>'Egbeda, Lagos','bg'=>'#1F2937','active'=>false],
                                    ] as $p)
                                    <button @click="open = false"
                                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors text-left {{ $p['active'] ? 'bg-orange-50' : '' }}">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-xs font-bold"
                                             style="background:{{ $p['bg'] }}">{{ $p['initials'] }}</div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-900">{{ $p['name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $p['loc'] }}</div>
                                        </div>
                                        <span class="flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full flex-shrink-0">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span>
                                            Verified
                                        </span>
                                    </button>
                                    @endforeach

                                    {{-- Lekki group --}}
                                    <div class="px-4 pt-2 pb-1">
                                        <p class="text-xs font-semibold text-gray-500 mb-1">Lekki</p>
                                    </div>
                                    @foreach([
                                        ['initials'=>'BS','name'=>'Lekki Waterview Suites','loc'=>'Lekki, Lagos', 'bg'=>'#1F2937','active'=>true],
                                    ] as $p)
                                    <button @click="open = false"
                                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors text-left {{ $p['active'] ? 'bg-orange-50' : '' }}">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-xs font-bold"
                                             style="background:{{ $p['bg'] }}">{{ $p['initials'] }}</div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-900">{{ $p['name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $p['loc'] }}</div>
                                        </div>
                                        <span class="flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full flex-shrink-0">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span>
                                            Verified
                                        </span>
                                    </button>
                                    @endforeach
                                </div>

                                {{-- Footer --}}
                                <div class="border-t border-gray-100 px-4 py-3">
                                    <a href="/properties" class="flex items-center justify-center gap-1.5 text-sm font-semibold text-[#FF5A00] hover:text-[#E64F00] transition-colors">
                                        See all properties
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 md:gap-4">
                        <div class="relative hidden lg:block">
                            <input type="text" placeholder="Search your listings, bookings..." 
                                   class="w-64 xl:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm"/>
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <button class="w-9 h-9 bg-gray-900 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>

                        <button class="hidden sm:flex w-9 h-9 items-center justify-center">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </button>

                        <button class="relative w-9 h-9 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </button>

                        <button class="flex items-center gap-2 flex-shrink-0">
                            <div class="w-8 h-8 bg-[#FF5A00] rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-semibold">SM</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-auto p-4 md:p-6">
                @include('partials.dashboard-content')
            </main>
        </div>
    </div>
</body>
</html>
