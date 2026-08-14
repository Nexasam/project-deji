<aside class="w-[240px] bg-white border-r border-gray-200 flex-shrink-0 hidden md:flex flex-col">
    {{-- Logo & User Section --}}
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center gap-2 mb-4">
            <img src="/logo1.png" alt="VS Logo" class="h-8 w-auto" />
            <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
        </div>
        
        {{-- User Profile --}}
        <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-2">
            <div class="w-8 h-8 bg-[#FF5A00] rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-white text-xs font-bold">SM</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-gray-900 text-xs font-semibold truncate">Sonic Movers</p>
                <p class="text-gray-500 text-[10px] truncate">Owino workspace</p>
            </div>
            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-3 overflow-y-auto">
        <div class="mb-6">
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider px-3 mb-2">Overview</p>
            <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 {{ request()->is('dashboard') ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} rounded-lg mb-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
                <span class="text-sm {{ request()->is('dashboard') ? 'font-medium' : '' }}">Dashboard</span>
            </a>
            
            <a href="/properties" class="flex items-center gap-3 px-3 py-2 {{ request()->is('properties') ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} rounded-lg mb-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="text-sm {{ request()->is('properties') ? 'font-medium' : '' }}">Properties</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1 relative">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm">Bookings</span>
                <span class="ml-auto bg-[#FF5A00] text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center">3</span>
            </a>
            
            <a href="/calendar" class="flex items-center gap-3 px-3 py-2 {{ request()->is('calendar') ? 'text-[#FF5A00] bg-orange-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} rounded-lg mb-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                </svg>
                <span class="text-sm {{ request()->is('calendar') ? 'font-medium' : '' }}">Calendar</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-sm">Guests</span>
            </a>
        </div>

        <div class="mb-6">
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider px-3 mb-2">Manage</p>
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-9h10v2H7z"/>
                </svg>
                <span class="text-sm">Finance</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1 relative">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-sm">Operations</span>
                <span class="ml-auto bg-[#FF5A00] text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center">2</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-sm">Reports</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm">Documents</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-sm">Marketplace</span>
            </a>
        </div>

        <div>
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider px-3 mb-2">System</p>
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span class="text-sm">AI Assistant</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm">Settings</span>
            </a>
        </div>
    </nav>
</aside>
