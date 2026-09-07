<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 8 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ 
    propertyName: @js(old('name', $property->name === 'Untitled property' ? '' : $property->name)),
    description: @js(old('description', $property->description)),
    nightlyRate: @js(old('default_nightly_price', $property->default_nightly_price)),
    discount: @js(old('discount_percentage', optional($property->promotions->firstWhere('name', 'Longer stay discount'))->discount_value)),
    minimumStay: @js(old('minimum_stay_nights', optional($property->promotions->firstWhere('name', 'Longer stay discount'))->minimum_stay_nights ?? 7)),
    multipleRooms: false
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
                    <span class="text-xs text-gray-500">Step 8 of 10</span>
                    <a href="/owner/properties" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Save & exit</a>
                </div>
            </div>
            <div class="h-0.5 bg-gray-200"><div class="h-0.5 bg-[#FF5A00]" style="width: 88.88%"></div></div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-7">
            <form method="POST" action="{{ route('owner.properties.wizard.store', ['property'=>$property,'step'=>8]) }}" class="w-full max-w-4xl mx-auto">
                @csrf
                @if($errors->any())<div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
                {{-- Title Section --}}
                <div class="mb-5">
                    <p class="text-[#FF5A00] text-xs font-bold uppercase tracking-wider mb-1.5">ALMOST THERE</p>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1.5">Give it a name and a price</h1>
                    <p class="text-gray-500 text-sm">
                        The name is how you'll find this flat everywhere in Verified Shortlet — from your dashboard to guest bookings
                    </p>
                </div>

                {{-- Form --}}
                <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
                    {{-- Property Name --}}
                    <div class="mb-6">
                        <label class="block text-gray-900 font-bold text-base mb-3">Property name</label>
                        <input 
                            type="text"
                            name="name"
                            x-model="propertyName"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors text-gray-900"
                        />
                        <div class="flex items-start gap-2 mt-3 text-gray-600 text-sm">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p>Names must be unique within your portfolio — pair the property with a unit, e.g. "Bluewater 2A", so it never clashes with another flat you manage.</p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <label class="block text-gray-900 font-bold text-base mb-3">Description</label>
                        <textarea name="description"
                            x-model="description"
                            placeholder="Describe the layout, what makes it a great stay, nearby landmarks..."
                            rows="5"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors text-gray-900 resize-none"
                        ></textarea>
                    </div>

                    {{-- Pricing Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Nightly Rate --}}
                        <div>
                            <label class="block text-gray-900 font-bold text-base mb-3">Nightly rate (₦)</label>
                            <input 
                            type="number"
                                name="default_nightly_price"
                                x-model="nightlyRate"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors text-gray-900"
                            />
                        </div>

                        {{-- Weekly/Monthly Discount --}}
                        <div class="grid grid-cols-2 gap-3">
                            <label class="block text-gray-900 font-bold text-base mb-3">
                                Longer-stay discount <span class="font-normal text-gray-600">(optional)</span>
                            </label>
                            <span></span>
                            <input 
                                type="number" name="discount_percentage" min="1" max="100" step="0.01"
                                x-model="discount"
                                placeholder="10% off"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors text-gray-900"
                            />
                            <input type="number" name="minimum_stay_nights" min="2" max="365" x-model="minimumStay" placeholder="7 nights" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors text-gray-900" />
                        </div>
                    </div>

                    {{-- Multiple Bookable Rooms Checkbox --}}
                    <div class="flex items-start gap-3">
                        <label class="flex items-center gap-3 cursor-not-allowed opacity-60">
                            <input 
                                type="checkbox" 
                                disabled
                                class="sr-only"
                            />
                            <div class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                                 :class="multipleRooms ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-400 bg-white'">
                                <svg x-show="multipleRooms" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-gray-900 font-bold text-base">Multiple bookable rooms — coming soon</span>
                        </label>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('owner.properties.wizard.step', ['property'=>$property,'step'=>7]) }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors underline">Back</a>
                    <button type="submit" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-2.5 px-8 rounded-lg text-sm transition-colors shadow-md inline-block">Next Step</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
