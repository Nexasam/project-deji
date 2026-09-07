<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dashboard - Verified Shortlet')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#F4F4F4] font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        @php($activeNav = trim($__env->yieldContent('active', '')))
        @include('partials.sidebar-nav', ['active' => $activeNav])
        
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-[60px] bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6 flex-shrink-0">
                <button type="button" @click="sidebarOpen = true" class="md:hidden text-gray-600 hover:text-gray-900" aria-label="Open navigation">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <p class="ml-auto text-sm font-semibold text-gray-700">{{ $activeBusiness->name ?? 'Verified Shortlet' }}</p>
            </header>
            
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
