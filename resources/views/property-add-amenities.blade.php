<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - Add new property (Amenities)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECECEC] font-sans antialiased" x-data="{
    sidebarOpen: false,
    selected: ['wifi', 'power-backup', 'smart-tv', 'swimming-pool', 'security', 'free-parking'],
    toggle(val) {
        this.selected.includes(val)
            ? this.selected = this.selected.filter(v => v !== val)
            : this.selected.push(val)
    },
    has(val) { return this.selected.includes(val) }
}">
<div class="min-h-screen flex">

    {{-- Sidebar --}}
    @include('partials.sidebar-nav', ['active' => 'properties'])

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top Header --}}
        <header class="bg-white border-b border-gray-200 h-14 flex items-center px-6">
            <div class="flex items-center justify-between w-full">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" placeholder="Search your listings, bookings..."
                        class="w-72 pl-10 pr-4 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00]"/>
                </div>
                <div class="flex items-center gap-3">
                    <button class="relative w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-0.5 right-0.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <button class="flex items-center gap-2 hover:bg-gray-50 rounded-lg px-2 py-1">
                        <div class="w-7 h-7 bg-[#FF5A00] rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-semibold">SM</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-auto bg-[#ECECEC] p-6">

            {{-- Page heading --}}
            <div class="mb-5">
                <h1 class="text-xl font-bold text-gray-900 mb-1">Add a property</h1>
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <a href="/dashboard" class="hover:text-[#FF5A00]">Sonic Movers</a>
                    <span>›</span>
                    <a href="/properties" class="hover:text-[#FF5A00]">Properties</a>
                    <span>›</span>
                    <span class="text-gray-900">Add property</span>
                </div>
            </div>

            {{-- Form card --}}
            <div class="bg-white rounded-xl border border-gray-200">

                {{-- Step tabs --}}
                <div class="flex items-center border-b border-gray-200 px-6">
                    <a href="/property/add/new"         class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Basics</a>
                    <a href="/property/add/amenities"   class="py-4 mr-8 border-b-2 border-[#FF5A00] text-[#FF5A00] text-sm font-semibold">Amenities</a>
                    <a href="/property/add/documents"   class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Documents &amp; Assets</a>
                    <a href="/property/add/marketplace" class="py-4 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Marketplace</a>
                </div>

                <div class="p-6">

                    @php
                    $sections = [
                        'ESSENTIALS' => [
                            ['id'=>'wifi',          'label'=>'WiFi'],
                            ['id'=>'air-con',       'label'=>'Air conditioning'],
                            ['id'=>'kitchen',       'label'=>'Kitchen'],
                            ['id'=>'washing',       'label'=>'Washing Machine'],
                            ['id'=>'water-heater',  'label'=>'Water heater'],
                            ['id'=>'power-backup',  'label'=>'Power backup / Generator'],
                        ],
                        'ENTERTAINMENT' => [
                            ['id'=>'smart-tv',      'label'=>'Smart TV'],
                            ['id'=>'ps5',           'label'=>'PS5 / Gaming console'],
                            ['id'=>'sound-system',  'label'=>'Sound system'],
                            ['id'=>'netflix',       'label'=>'Netflix / Streaming'],
                        ],
                        'LEISURE' => [
                            ['id'=>'swimming-pool', 'label'=>'Swimming pool'],
                            ['id'=>'gym',           'label'=>'Gym'],
                            ['id'=>'bbq',           'label'=>'BBQ/ Garden'],
                            ['id'=>'balcony',       'label'=>'Balcony / Terrace'],
                        ],
                        'SAFETY & SECURITY' => [
                            ['id'=>'security',      'label'=>'24/7 Security'],
                            ['id'=>'cctv',          'label'=>'CCTV'],
                            ['id'=>'smoke-detector','label'=>'Smoke Detector'],
                            ['id'=>'free-parking',  'label'=>'Free Parking'],
                        ],
                    ];
                    @endphp

                    <div class="space-y-7">
                        @foreach($sections as $title => $items)
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">{{ $title }}</p>
                            <div class="grid grid-cols-4 gap-3">
                                @foreach($items as $item)
                                <label
                                    @click="toggle('{{ $item['id'] }}')"
                                    :class="has('{{ $item['id'] }}') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 bg-[#F0F4F8] hover:border-gray-300'"
                                    class="flex items-center gap-2.5 px-3 py-2.5 border-2 rounded-lg cursor-pointer transition-all select-none"
                                >
                                    {{-- Checkbox --}}
                                    <div
                                        :class="has('{{ $item['id'] }}') ? 'bg-[#FF5A00] border-[#FF5A00]' : 'bg-white border-gray-300'"
                                        class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                    >
                                        <svg x-show="has('{{ $item['id'] }}')" width="10" height="10" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-800">{{ $item['label'] }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Helper note --}}
                    <div class="flex items-start gap-2 mt-6 mb-6">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-gray-500">
                            These become filters on the marketplace — a guest searching "pool + PS5" only sees listings where both are checked, so accuracy here directly affects discoverability.
                        </p>
                    </div>

                    {{-- Nav buttons --}}
                    <div class="flex items-center justify-between">
                        <a href="/property/add/new"
                            class="px-8 py-2.5 border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            Back
                        </a>
                        <a href="/property/add/documents"
                            class="px-8 py-2.5 bg-[#FF5A00] hover:bg-[#E64F00] text-white text-sm font-semibold rounded-lg transition-colors">
                            Next
                        </a>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
