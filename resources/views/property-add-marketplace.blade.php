<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - Add new property (Marketplace)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#ECECEC] font-sans antialiased" x-data="{ sidebarOpen: false, listed: true, success: false }">
<div class="min-h-screen flex">

    @include('partials.sidebar-nav', ['active' => 'properties'])

    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header --}}
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

        <main class="flex-1 overflow-auto bg-[#ECECEC] p-6">

            {{-- Heading --}}
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

                {{-- Tabs --}}
                <div class="flex items-center border-b border-gray-200 px-6">
                    <a href="/property/add/new"         class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Basics</a>
                    <a href="/property/add/amenities"   class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Amenities</a>
                    <a href="/property/add/documents"   class="py-4 mr-8 border-b-2 border-transparent text-gray-500 text-sm hover:text-gray-700">Documents &amp; Assets</a>
                    <a href="/property/add/marketplace" class="py-4 border-b-2 border-[#FF5A00] text-[#FF5A00] text-sm font-semibold">Marketplace</a>
                </div>

                <div class="p-6">

                    {{-- Toggle card --}}
                    <div class="border border-gray-200 rounded-xl px-6 py-6 flex items-center justify-between mb-5">
                        <div>
                            <p class="text-base font-bold text-gray-900 mb-1">List on Verified Shortlet Marketplace</p>
                            <p class="text-sm text-gray-500">Make this property visible to guests once verification is complete.</p>
                        </div>
                        <button
                            @click="listed = !listed"
                            :class="listed ? 'bg-[#FF5A00]' : 'bg-gray-300'"
                            class="relative flex-shrink-0 w-16 h-9 rounded-full transition-colors duration-200 focus:outline-none ml-8"
                        >
                            <span
                                :class="listed ? 'translate-x-8' : 'translate-x-1'"
                                class="inline-block w-7 h-7 bg-white rounded-full shadow transition-transform duration-200"
                            ></span>
                        </button>
                    </div>

                    {{-- Info note --}}
                    <div class="flex items-start gap-2 mb-10">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            New listings go through a quick verification review (photos, address, ID match) before appearing publicly — that review is what earns the property its Verified badge.
                        </p>
                    </div>

                    {{-- Nav --}}
                    <div class="flex items-center justify-between">
                        <a href="/property/add/documents"
                            class="px-8 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                            Back
                        </a>
                        <button @click="success = true"
                            class="px-10 py-2.5 bg-[#FF5A00] hover:bg-[#E64F00] text-white text-sm font-semibold rounded-lg transition-colors">
                            Finish
                        </button>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>

{{-- Success modal --}}
<div
    x-show="success"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50"
>
    <div
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        class="bg-white rounded-2xl shadow-2xl flex flex-col items-center text-center"
        style="width:380px; padding:48px 40px 36px;"
    >
        {{-- Thumbs up icon --}}
        <svg class="w-24 h-24 text-[#FF5A00] mb-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M2 20h2c.55 0 1-.45 1-1v-9c0-.55-.45-1-1-1H2v11zm19.83-7.12c.11-.25.17-.52.17-.8V11c0-1.1-.9-2-2-2h-5.5l.92-4.65c.05-.22.02-.46-.08-.66-.23-.45-.52-.86-.88-1.22L14 2 7.59 8.41C7.21 8.79 7 9.3 7 9.83V19c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.12-.03.22z"/>
        </svg>

        <h2 class="text-2xl font-bold text-gray-900 mb-3">Success!</h2>
        <p class="text-sm text-gray-600 mb-8 leading-relaxed">
            Thank you for adding a new property listing<br>, Under Review.
        </p>

        <a href="/properties"
            class="w-full py-3.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-colors text-center block">
            Close
        </a>
    </div>
</div>

</body>
</html>
