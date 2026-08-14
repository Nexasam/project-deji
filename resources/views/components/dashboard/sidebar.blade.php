<aside class="w-[220px] bg-[#1D1D1F] text-gray-400 flex-shrink-0 flex flex-col border-r-2 border-[#FF5A00] hidden md:flex">
    <div class="flex-1 flex flex-col overflow-y-auto">
        {{-- Brand --}}
        <div class="px-5 py-4 border-b border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-[#FF5A00] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-400">Verified Shortlet</span>
            </div>
        </div>

        {{-- Workspace Selector --}}
        <div class="px-4 py-4 flex-shrink-0">
            <div class="bg-[#2b2b2b] rounded-lg px-3 py-2.5 flex items-center gap-3 cursor-pointer hover:bg-[#333333]">
                <div class="w-8 h-8 bg-[#FF5A00] rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-semibold">SM</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-sm font-medium truncate">Sonic Movers</div>
                    <div class="text-gray-500 text-xs truncate">Owner workspace</div>
                </div>
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                </svg>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="px-4 py-2 space-y-5 flex-1">
            {{-- Overview Section --}}
            <div>
                <div class="text-[10px] font-semibold text-gray-600 uppercase tracking-wider px-2 mb-2">Overview</div>
                <div class="space-y-1">
                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded bg-[#2b2b2b] text-[#FF5A00] text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Properties</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Bookings</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Calendar</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Guests</span>
                    </a>
                </div>
            </div>

            {{-- Manage Section --}}
            <div>
                <div class="text-[10px] font-semibold text-gray-600 uppercase tracking-wider px-2 mb-2">Manage</div>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Finance</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Operations</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Reports</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>Documents</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>Marketplace</span>
                    </a>
                </div>
            </div>

            {{-- System Section --}}
            <div>
                <div class="text-[10px] font-semibold text-gray-600 uppercase tracking-wider px-2 mb-2">System</div>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>AI Assistant</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</aside>

{{-- Mobile Sidebar Overlay --}}
<div x-show="sidebarOpen" 
     x-cloak
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

{{-- Mobile Sidebar --}}
<aside x-show="sidebarOpen"
       x-cloak
       @click.away="sidebarOpen = false"
       class="fixed inset-y-0 left-0 w-[280px] bg-[#1D1D1F] text-gray-400 flex flex-col border-r-2 border-[#FF5A00] z-50 md:hidden transform transition-transform duration-300 ease-in-out"
       x-transition:enter="transition-transform ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition-transform ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full">
    <div class="flex-1 flex flex-col overflow-y-auto">
        {{-- Mobile Brand --}}
        <div class="px-5 py-4 border-b border-gray-800 flex-shrink-0 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-[#FF5A00] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm text-gray-400">Verified Shortlet</span>
            </div>
            <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile Workspace Selector --}}
        <div class="px-4 py-4 flex-shrink-0">
            <div class="bg-[#2b2b2b] rounded-lg px-3 py-2.5 flex items-center gap-3 cursor-pointer hover:bg-[#333333]">
                <div class="w-8 h-8 bg-[#FF5A00] rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-semibold">SM</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-sm font-medium truncate">Sonic Movers</div>
                    <div class="text-gray-500 text-xs truncate">Owner workspace</div>
                </div>
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                </svg>
            </div>
        </div>

        {{-- Mobile Navigation (same as desktop) --}}
        <nav class="px-4 py-2 space-y-5 flex-1 pb-8">
            <div>
                <div class="text-[10px] font-semibold text-gray-600 uppercase tracking-wider px-2 mb-2">Overview</div>
                <div class="space-y-1">
                    <a href="/dashboard" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded bg-[#2b2b2b] text-[#FF5A00] text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Properties</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Bookings</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Calendar</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Guests</span>
                    </a>
                </div>
            </div>
            <div>
                <div class="text-[10px] font-semibold text-gray-600 uppercase tracking-wider px-2 mb-2">Manage</div>
                <div class="space-y-1">
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Finance</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Operations</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Reports</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Documents</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Marketplace</span>
                    </a>
                </div>
            </div>
            <div>
                <div class="text-[10px] font-semibold text-gray-600 uppercase tracking-wider px-2 mb-2">System</div>
                <div class="space-y-1">
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>AI Assistant</span>
                    </a>
                    <a href="#" @click="sidebarOpen = false" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-[#2b2b2b] text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</aside>
