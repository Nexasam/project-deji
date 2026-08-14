<div class="space-y-5">
    {{-- Top Row: Business Health + Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        {{-- Business Health Banner --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-start gap-4">
                    {{-- Score Circle --}}
                    <div class="flex-shrink-0">
                        <div class="relative w-20 h-20">
                            <svg class="w-20 h-20 transform -rotate-90">
                                <circle cx="40" cy="40" r="36" stroke="#FFE5D9" stroke-width="6" fill="none" />
                                <circle 
                                    cx="40" 
                                    cy="40" 
                                    r="36" 
                                    stroke="#FF5A00" 
                                    stroke-width="6" 
                                    fill="none"
                                    stroke-dasharray="226"
                                    stroke-dashoffset="29"
                                    stroke-linecap="round"
                                />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-gray-900 text-2xl font-bold">87</span>
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1">
                        <div class="mb-1">
                            <span class="text-[#FF5A00] text-[10px] font-bold uppercase tracking-wider">BUSINESS HEALTH SCORE · EXCELLENT</span>
                        </div>
                        <h2 class="text-gray-900 text-lg font-bold mb-1">Good morning, Samson</h2>
                        <p class="text-gray-500 text-xs mb-2">Tuesday, 4 August</p>
                        <div class="space-y-0.5 text-xs text-gray-700">
                            <p class="flex items-center gap-1"><span class="text-green-600">↑</span> Revenue and occupancy are both trending up this month.</p>
                            <p class="flex items-center gap-1"><span class="text-red-600">✕</span> 2 properties need attention. 4 check-ins pending. Emerald/suite on track.</p>
                            <p class="flex items-center gap-1"><span>👀</span> 3 guests checking in today, 4 checking out. Luwringsig on track.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-lg p-5 h-full">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Quick actions</h3>
                <div class="grid grid-cols-3 gap-3">
                    {{-- Add Property --}}
                    <a href="/property/add/step1" class="flex flex-col items-center justify-center p-3 rounded-lg hover:bg-gray-50 transition-all">
                        <div class="w-12 h-12 rounded-lg bg-[#FF5A00] flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-900 text-center">+ Add property</span>
                    </a>

                    {{-- New Booking --}}
                    <button class="flex flex-col items-center justify-center p-3 rounded-lg hover:bg-gray-50 transition-all">
                        <div class="w-12 h-12 rounded-lg bg-gray-900 flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-900 text-center">+ New booking</span>
                    </button>

                    {{-- Assign Task --}}
                    <button class="flex flex-col items-center justify-center p-3 rounded-lg hover:bg-gray-50 transition-all">
                        <div class="w-12 h-12 rounded-lg bg-gray-400 flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-900 text-center">+ Assign task</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Total Earnings --}}
        <div class="bg-white rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-600">Total earnings</span>
                <div class="w-6 h-6 bg-orange-50 rounded flex items-center justify-center">
                    <span class="text-[#FF5A00] text-sm font-bold">₦</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">₦2.84M</div>
            <div class="text-xs text-green-600">↑ 12.4% vs last month</div>
        </div>

        {{-- Active Listings --}}
        <div class="bg-white rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-600">Active listings</span>
                <div class="w-6 h-6 bg-orange-50 rounded flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">6</div>
            <div class="text-xs text-gray-600">5 verified, 1 pending</div>
        </div>

        {{-- Occupancy Rate --}}
        <div class="bg-white rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-600">Occupancy rate</span>
                <div class="w-6 h-6 bg-orange-50 rounded flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z" opacity="0.5"/>
                        <path d="M3 3h8v8H3V3z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">71%</div>
            <div class="text-xs text-green-600">↑ 3.1% vs last month</div>
        </div>

        {{-- Outstanding Tasks --}}
        <div class="bg-white rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-600">Outstanding tasks</span>
                <div class="w-6 h-6 bg-orange-50 rounded flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-[#FF5A00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">9</div>
            <div class="text-xs text-gray-600">3 urgent – week-end</div>
        </div>
    </div>

    {{-- Financial Snapshot + AI Alert --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Financial Snapshot --}}
        <div class="lg:col-span-2 bg-white rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Financial snapshot</h3>
                    <p class="text-xs text-gray-600">Revenue vs cost over week-by-week</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded">1M</button>
                    <button class="px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 rounded">3M</button>
                    <button class="px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100 rounded">12M</button>
                </div>
            </div>
            <div class="h-48 flex items-end justify-between gap-1">
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 45%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 60%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 50%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 70%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 65%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 80%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 75%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 85%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 90%"></div>
                <div class="flex-1 bg-gradient-to-t from-orange-200 to-orange-100 rounded-t" style="height: 95%"></div>
            </div>
            <div class="flex items-center justify-center gap-6 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-[#FF5A00] rounded"></div>
                    <span class="text-xs text-gray-600">Revenue</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-gray-400 rounded"></div>
                    <span class="text-xs text-gray-600">Cost-raw</span>
                </div>
            </div>
        </div>

        {{-- AI Host Insight Alert --}}
        <div class="bg-gray-900 rounded-lg p-5 text-white">
            <div class="flex items-start gap-2 mb-3">
                <div class="w-5 h-5 bg-[#FF5A00] rounded flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">⚡</span>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-[#FF5A00] mb-1">AI HOST INSIGHT</h3>
                    <p class="text-sm font-bold">NEEDS YOUR ATTENTION</p>
                </div>
            </div>
            <div class="space-y-2 mb-4 text-xs">
                <p>🔧 <strong>AC maintenance overdue</strong> – Lekki Waterview Suites, flagged 3h ago</p>
                <p>📅 <strong>2 calendar entries unconfirmed</strong> – WhatsApp bookings need review</p>
                <p>💰 <strong>1 payment pending reconciliation</strong> – Lekki Waterview Suites, ₦180,000</p>
            </div>
            <div class="bg-[#2b2b2b] rounded p-3 mb-4 text-xs">
                <p class="font-bold text-[#FF5A00] mb-1">Why did revenue rise this week?</p>
                <p class="text-gray-300">5 of your last 6 bookings came from repeat guests who booked directly after a review pinned...</p>
                <button class="text-[#FF5A00] mt-2 text-xs font-semibold">Toggle contextuals: Turn on AI to Concierge 'Slow up' messages to common...</button>
            </div>
            <button class="w-full bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-3 rounded-lg text-sm transition-colors">
                Ask for more AI insights
            </button>
        </div>
    </div>

    {{-- Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Upcoming Activities --}}
        <div class="bg-white rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Upcoming Activities</h3>
                    <p class="text-xs text-gray-600">Check-ins and check-outs in 2 weeks</p>
                </div>
                <a href="#" class="text-xs text-[#FF5A00] font-semibold">View calendar ></a>
            </div>
            <div class="grid grid-cols-4 gap-3">
                <div class="text-center">
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">FRI</span>
                        <p class="text-lg font-bold">Aug 4</p>
                    </div>
                    <p class="text-xs text-gray-700 mb-2">Lekki ksurso-luwns</p>
                    <button class="w-full bg-[#FF5A00] text-white text-xs font-semibold py-2 rounded">Check-in</button>
                </div>
                <div class="text-center">
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">WED</span>
                        <p class="text-lg font-bold">Aug 9</p>
                    </div>
                    <p class="text-xs text-gray-700 mb-2">Lekki ksurso-luwns</p>
                    <button class="w-full bg-gray-900 text-white text-xs font-semibold py-2 rounded">Check-out</button>
                </div>
                <div class="text-center">
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">SAT</span>
                        <p class="text-lg font-bold">Aug 12</p>
                    </div>
                    <p class="text-xs text-gray-700 mb-2">Lekki ksurso-luwns</p>
                    <button class="w-full bg-gray-900 text-white text-xs font-semibold py-2 rounded">Check-out</button>
                </div>
                <div class="text-center">
                    <div class="mb-2">
                        <span class="text-xs text-gray-500">SAT</span>
                        <p class="text-lg font-bold">Aug 16</p>
                    </div>
                    <p class="text-xs text-gray-700 mb-2">Lekki ksurso-luwns</p>
                    <button class="w-full bg-gray-900 text-white text-xs font-semibold py-2 rounded">Check-out</button>
                </div>
            </div>
            <button class="w-full bg-gray-900 text-white font-semibold py-3 rounded-lg text-sm mt-4">
                View more upcoming activities
            </button>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Recent Activity</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3 p-3 hover:bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">New booking confirmed – Lekki Waterview Suites</p>
                        <p class="text-xs text-gray-500">12 minutes ago</p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                </div>
                <div class="flex items-start gap-3 p-3 hover:bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Payment received – ₦180,000, Emerald Loft</p>
                        <p class="text-xs text-gray-500">28 minutes ago</p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                </div>
                <div class="flex items-start gap-3 p-3 hover:bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Maintenance flagged – Lekki Waterview Suites</p>
                        <p class="text-xs text-gray-500">3 hours ago</p>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <button class="w-full bg-[#FF5A00] text-white font-semibold py-3 rounded-lg text-sm mt-4">
                View more recent activities
            </button>
        </div>
    </div>

    {{-- Your Listings --}}
    <div class="bg-white rounded-lg p-6">
        <div class="mb-4">
            <h3 class="text-base font-bold text-gray-900">Your listings</h3>
            <p class="text-xs text-gray-600">Verification status and performance at a glance</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @for($i = 0; $i < 4; $i++)
            <div class="group cursor-pointer">
                <div class="relative mb-3 rounded-xl overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop" 
                         alt="Property" class="w-full h-48 object-cover"/>
                    <div class="absolute top-3 right-3 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <h4 class="font-bold text-sm text-gray-900 mb-1">Lekki Waterview Suites</h4>
                <p class="text-xs text-gray-600 mb-2">Ikoyi, Lagos • Serviced Apartment</p>
                <p class="text-sm font-bold text-gray-900 mb-3">₦60,000/night</p>
                <button class="w-full bg-orange-50 hover:bg-orange-100 text-[#FF5A00] font-semibold py-2 rounded-lg text-xs transition-colors">
                    View/Edit Details
                </button>
            </div>
            @endfor
        </div>
        <button class="w-full border-2 border-dashed border-gray-300 text-gray-600 font-semibold py-3 rounded-lg text-sm mt-4 hover:border-[#FF5A00] hover:text-[#FF5A00] transition-colors">
            See more listings (4 more)
        </button>
    </div>
</div>
