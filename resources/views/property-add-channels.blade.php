<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 9 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{
    channels: {
        airbnb:     { connected: {{ $property->channelConnections->contains('provider', 'airbnb') ? 'true' : 'false' }}, username: @js(optional($property->channelConnections->firstWhere('provider', 'airbnb'))->external_reference ?? ''), syncing: false },
        bookingcom: { connected: {{ $property->channelConnections->contains('provider', 'bookingcom') ? 'true' : 'false' }}, propertyId: @js(optional($property->channelConnections->firstWhere('provider', 'bookingcom'))->external_reference ?? ''), syncing: false },
        whatsapp:   { connected: {{ $property->channelConnections->contains('provider', 'whatsapp') ? 'true' : 'false' }}, phone: @js(optional($property->channelConnections->firstWhere('provider', 'whatsapp'))->external_reference ?? ''), syncing: false },
        direct:     { connected: true,  note: 'Always on — guests book directly through Verified Shortlet.' },
    },
    connectChannel(key) {
        this.channels[key].connected = true;
        this.channels[key].syncing = false;
    },
    disconnectChannel(key) {
        this.channels[key].connected = false;
    }
}">
    <div class="min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo1.png" alt="VS Logo" class="h-7 w-auto" />
                    <span class="text-gray-900 text-sm font-semibold">Verified Shortlet</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">Step 9 of 10</span>
                    <a href="/owner/properties" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Save & exit</a>
                </div>
            </div>
            <div class="h-0.5 bg-gray-200"><div class="h-0.5 bg-[#FF5A00]" style="width: 90%"></div></div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-7">
            <div class="w-full max-w-4xl mx-auto">

                {{-- Title --}}
                <div class="mb-6">
                    <p class="text-[#FF5A00] text-xs font-bold uppercase tracking-wider mb-1.5">DISTRIBUTION CHANNELS</p>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1.5">Where do you want to take bookings?</h1>
                    <p class="text-gray-500 text-sm">
                        Add the platforms you use. We’ll save each request as connection pending; live synchronization will be enabled later.
                    </p>
                </div>

                {{-- Channel cards --}}
                <div class="space-y-3 mb-6">

                    {{-- Verified Shortlet Direct (always on) --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#FF5A00] flex items-center justify-center flex-shrink-0">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <p class="text-sm font-bold text-gray-900">Verified Shortlet — Direct</p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                    <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    ACTIVE
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">Guests book directly through your Verified Shortlet profile page and widget.</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="text-xs text-gray-400 font-medium">Always on</span>
                        </div>
                    </div>

                    {{-- Airbnb --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-5" x-data>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-[#FF385C] flex items-center justify-center flex-shrink-0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.87-3.13-7-7-7zm-1 14v-1h2v1h-2zm3-3.27V15h-4v-2.27C8.48 11.97 7 10.63 7 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.63-1.48 2.97-3 3.73z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-sm font-bold text-gray-900">Airbnb</p>
                                    <span x-show="channels.airbnb.connected"
                                          class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                        CONNECTION PENDING
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">Sync via iCal — bookings confirmed on Airbnb appear on your calendar as locked entries.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <button
                                    x-show="!channels.airbnb.connected"
                                    @click="connectChannel('airbnb')"
                                    :disabled="channels.airbnb.syncing"
                                    class="px-4 py-2 bg-[#FF385C] hover:bg-[#E0304F] text-white text-xs font-bold rounded-lg transition-colors disabled:opacity-60 flex items-center gap-2">
                                    <span x-show="channels.airbnb.syncing">
                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                        </svg>
                                    </span>
                                    <span x-text="channels.airbnb.syncing ? 'Connecting…' : 'Connect'"></span>
                                </button>
                                <button
                                    x-show="channels.airbnb.connected"
                                    @click="disconnectChannel('airbnb')"
                                    class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 text-xs font-bold rounded-lg transition-colors">
                                    Disconnect
                                </button>
                            </div>
                        </div>
                        {{-- Connected state: show iCal URL --}}
                        <div x-show="channels.airbnb.connected" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-4 pt-4 border-t border-gray-100">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Airbnb iCal URL</label>
                            <div class="flex items-center gap-2">
                                <input form="channels-form" name="channels[airbnb]" type="url" x-model="channels.airbnb.username"
                                       placeholder="Paste your Airbnb iCal export link here"
                                       class="flex-1 px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#FF385C] text-gray-800" />
                                <button class="px-3 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-black transition-colors flex-shrink-0">Save</button>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1.5">Find this in Airbnb → Your listings → Availability → Export calendar</p>
                        </div>
                    </div>

                    {{-- Booking.com --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-5" x-data>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-[#003580] flex items-center justify-center flex-shrink-0">
                                <svg width="20" height="14" viewBox="0 0 40 28" fill="none">
                                    <rect width="40" height="28" rx="4" fill="#003580"/>
                                    <text x="5" y="21" font-family="Arial" font-size="16" font-weight="bold" fill="white">B.</text>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-sm font-bold text-gray-900">Booking.com</p>
                                    <span x-show="channels.bookingcom.connected"
                                          class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                        CONNECTION PENDING
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">Sync via iCal or channel manager API — confirmed bookings lock your calendar automatically.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <button
                                    x-show="!channels.bookingcom.connected"
                                    @click="connectChannel('bookingcom')"
                                    :disabled="channels.bookingcom.syncing"
                                    class="px-4 py-2 bg-[#003580] hover:bg-[#002d6b] text-white text-xs font-bold rounded-lg transition-colors disabled:opacity-60 flex items-center gap-2">
                                    <span x-show="channels.bookingcom.syncing">
                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                        </svg>
                                    </span>
                                    <span x-text="channels.bookingcom.syncing ? 'Connecting…' : 'Connect'"></span>
                                </button>
                                <button
                                    x-show="channels.bookingcom.connected"
                                    @click="disconnectChannel('bookingcom')"
                                    class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 text-xs font-bold rounded-lg transition-colors">
                                    Disconnect
                                </button>
                            </div>
                        </div>
                        <div x-show="channels.bookingcom.connected" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-4 pt-4 border-t border-gray-100">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Booking.com iCal URL</label>
                            <div class="flex items-center gap-2">
                                <input form="channels-form" name="channels[bookingcom]" type="url" x-model="channels.bookingcom.propertyId"
                                       placeholder="Paste your Booking.com iCal export link here"
                                       class="flex-1 px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#003580] text-gray-800" />
                                <button class="px-3 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-black transition-colors flex-shrink-0">Save</button>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1.5">Find this in Booking.com Extranet → Rates & Availability → Sync calendars</p>
                        </div>
                    </div>

                    {{-- WhatsApp --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-5" x-data>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-[#25D366] flex items-center justify-center flex-shrink-0">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.528 5.845L.057 23.998l6.3-1.652A11.953 11.953 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.886 0-3.655-.513-5.17-1.408l-.37-.22-3.841 1.007 1.025-3.743-.242-.386A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-sm font-bold text-gray-900">WhatsApp</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold">MANUAL</span>
                                    <span x-show="channels.whatsapp.connected"
                                          class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                        SAVED
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">No API sync — you log WhatsApp bookings manually. We'll pre-fill your number so the form is one tap.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <button
                                    x-show="!channels.whatsapp.connected"
                                    @click="channels.whatsapp.connected = true"
                                    class="px-4 py-2 bg-[#25D366] hover:bg-[#1ebe5a] text-white text-xs font-bold rounded-lg transition-colors">
                                    Enable
                                </button>
                                <button
                                    x-show="channels.whatsapp.connected"
                                    @click="channels.whatsapp.connected = false"
                                    class="px-4 py-2 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 text-xs font-bold rounded-lg transition-colors">
                                    Disable
                                </button>
                            </div>
                        </div>
                        <div x-show="channels.whatsapp.connected" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-4 pt-4 border-t border-gray-100">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">WhatsApp booking number</label>
                            <div class="flex items-center gap-2">
                                <input form="channels-form" name="channels[whatsapp]" type="tel" x-model="channels.whatsapp.phone"
                                       placeholder="+2348105550555"
                                       class="flex-1 px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#25D366] text-gray-800" />
                                <button class="px-3 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-black transition-colors flex-shrink-0">Save</button>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1.5">When you log a booking from WhatsApp, this number will be pre-filled.</p>
                        </div>
                    </div>

                    {{-- Walk-in / Direct --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gray-800 flex items-center justify-center flex-shrink-0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-sm font-bold text-gray-900">Walk-in / Phone call</p>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                        ACTIVE
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">Log bookings from guests who arrive in person or call — always available, no setup needed.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="text-xs text-gray-400 font-medium">Always on</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info note --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-gray-700 text-sm">
                        You can skip this and add channel details later. Saving here does not connect to an external provider yet.
                    </p>
                </div>

                {{-- Navigation --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('owner.properties.wizard.step', ['property'=>$property,'step'=>8]) }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors underline">Back</a>
                    <form id="channels-form" method="POST" action="{{ route('owner.properties.wizard.store', ['property'=>$property,'step'=>9]) }}" class="flex items-center gap-3">@csrf
                        <button type="submit" formaction="{{ route('owner.properties.wizard.skip', ['property'=>$property,'step'=>9]) }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Skip for now</button>
                        <button type="submit" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-2.5 px-8 rounded-lg text-sm shadow-md">Next Step</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
