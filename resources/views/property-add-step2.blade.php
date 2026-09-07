<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 2 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ showNewLocation: false }">
    <div class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-3xl mx-auto px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-7 w-auto" />
                    <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">Step 2 of 10</span>
                    <a href="/owner/properties" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Save & exit</a>
                </div>
            </div>
            <div class="h-0.5 bg-gray-200"><div class="h-0.5 bg-[#FF5A00]" style="width: 22.22%"></div></div>
        </header>

        <main class="flex-1 px-4 py-8">
            <div class="w-full max-w-3xl mx-auto">
                {{-- Title --}}
                <div class="mb-5">
                    <p class="text-[#FF5A00] text-xs font-bold uppercase tracking-wider mb-1.5">Location</p>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1.5">Where is it?</h1>
                    <p class="text-gray-500 text-sm">Guests find your place by state and area — let's get that locked in first.</p>
                </div>

                <form method="POST" action="{{ route('owner.properties.wizard.store', ['property' => $property, 'step' => 2]) }}">
                @csrf
                @if($errors->any())<div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
                {{-- Company/Location --}}
                <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Company / Location (business)</label>
                    <button @click="showNewLocation = !showNewLocation"
                        class="w-full px-4 py-2.5 text-left bg-white border border-gray-300 rounded-lg hover:border-gray-400 focus:outline-none focus:ring-1 focus:ring-[#FF5A00] transition-colors flex items-center justify-between text-sm">
                        <span class="text-gray-700">+ Create new location...</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="showNewLocation ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="showNewLocation" x-cloak class="mt-4 pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">New location name</label>
                                <input type="text" placeholder="e.g Lekki"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Assign a manager</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] transition-colors appearance-none bg-white">
                                    <option>Unassigned</option>
                                    <option>Manager 1</option>
                                    <option>Manager 2</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map --}}
                <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Find it on the map</h3>
                    <div class="relative mb-3">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="address_line" value="{{ old('address_line', data_get($property->address, 'line_1')) }}" required placeholder="Street, estate or landmark"
                            class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] transition-colors" />
                        <button class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-4 h-4 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="relative w-full h-48 bg-gray-200 rounded-lg overflow-hidden">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-10 h-10 text-[#FF5A00]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                        <div class="absolute inset-0 flex items-end justify-center pb-4 pointer-events-none">
                            <div class="bg-black/60 text-white px-4 py-2 rounded-lg text-center">
                                <p class="text-xs font-medium">Exact map verification will be added later</p>
                            </div>
                        </div>
                        <div class="absolute bottom-2 right-2 text-[10px] text-gray-500">Map verification pending</div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="bg-white rounded-xl shadow-sm p-4 mb-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Property address</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <select name="state" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] appearance-none bg-white text-gray-500">
                            <option value="">Select state</option>
                            @foreach(['Lagos','Abuja','Rivers'] as $state)<option value="{{ $state }}" @selected(old('state', data_get($property->address, 'state')) === $state)>{{ $state }}</option>@endforeach
                        </select>
                        <input name="city" value="{{ old('city', data_get($property->address, 'city')) }}" required placeholder="Area / city, e.g. Lekki" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <input type="text" placeholder="Street / estate name (optional)"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#FF5A00] mb-3" />
                    <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <p class="text-xs text-gray-600">Guests see this for context, but marketplace search relies on the State and Area selected above.</p>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('owner.properties.create.step1') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors underline">Back</a>
                    <button type="submit" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-2.5 px-8 rounded-lg text-sm transition-colors shadow-md inline-block">Next Step</button>
                </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
