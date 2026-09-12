<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Property - Step 6 - Verified Shortlet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ 
    items: @js($property->assets->where('status','active')->pluck('name')->values()->all()),
    newItem: '',
    addItem() { const value=this.newItem.trim(); if(value && !this.items.includes(value)) this.items.push(value); this.newItem=''; }
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
                    <span class="text-xs text-gray-500">Step 6 of 10</span>
                    <a href="/owner/properties" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Save & exit</a>
                </div>
            </div>
            <div class="h-0.5 bg-gray-200"><div class="h-0.5 bg-[#FF5A00]" style="width: 66.66%"></div></div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 px-4 py-7">
            <form method="POST" action="{{ route('owner.properties.wizard.store', ['property'=>$property,'step'=>6]) }}" class="w-full max-w-4xl mx-auto">
                @csrf
                <input type="hidden" name="assets" :value="JSON.stringify(items)">
                {{-- Title Section --}}
                <div class="mb-5">
                    <p class="text-[#FF5A00] text-xs font-bold uppercase tracking-wider mb-1.5">INSIDE THE FLAT</p>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1.5">What's already there?</h1>
                    <p class="text-gray-600 text-sm text-gray-500">
                        Select what's in the flat itself — not shared building items like a generator for the whole block. We'll mark these as Good condition to start; you can update any of them later.
                    </p>
                </div>

                {{-- Items Selection --}}
                <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        {{-- Smart TV --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('smart-tv') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('smart-tv') ? items = items.filter(i => i !== 'smart-tv') : items.push('smart-tv')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('smart-tv') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('smart-tv')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Smart TV</span>
                        </label>

                        {{-- Air conditioner --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('air-conditioner') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('air-conditioner') ? items = items.filter(i => i !== 'air-conditioner') : items.push('air-conditioner')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('air-conditioner') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('air-conditioner')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Air conditioner</span>
                        </label>

                        {{-- Refrigerator --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('refrigerator') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('refrigerator') ? items = items.filter(i => i !== 'refrigerator') : items.push('refrigerator')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('refrigerator') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('refrigerator')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Refrigerator</span>
                        </label>

                        {{-- Microwave --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('microwave') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('microwave') ? items = items.filter(i => i !== 'microwave') : items.push('microwave')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('microwave') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('microwave')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Microwave</span>
                        </label>

                        {{-- Washing machine --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('washing-machine') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('washing-machine') ? items = items.filter(i => i !== 'washing-machine') : items.push('washing-machine')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('washing-machine') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('washing-machine')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Washing machine</span>
                        </label>

                        {{-- Gas cooker --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('gas-cooker') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('gas-cooker') ? items = items.filter(i => i !== 'gas-cooker') : items.push('gas-cooker')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('gas-cooker') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('gas-cooker')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Gas cooker</span>
                        </label>

                        {{-- Dining set --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('dining-set') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('dining-set') ? items = items.filter(i => i !== 'dining-set') : items.push('dining-set')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('dining-set') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('dining-set')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Dining set</span>
                        </label>

                        {{-- Sofa set --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('sofa-set') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('sofa-set') ? items = items.filter(i => i !== 'sofa-set') : items.push('sofa-set')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('sofa-set') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('sofa-set')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Sofa set</span>
                        </label>

                        {{-- Wardrobe --}}
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="items.includes('wardrobe') ? 'border-[#FF5A00] bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="checkbox" class="sr-only" 
                                   @change="items.includes('wardrobe') ? items = items.filter(i => i !== 'wardrobe') : items.push('wardrobe')" />
                            <div class="w-6 h-6 rounded border-2 flex items-center justify-center flex-shrink-0"
                                 :class="items.includes('wardrobe') ? 'border-[#FF5A00] bg-[#FF5A00]' : 'border-gray-300'">
                                <svg x-show="items.includes('wardrobe')" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">Wardrobe</span>
                        </label>
                    </div>

                    {{-- Custom Items Section --}}
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-base font-bold text-gray-900 mb-4">Anything else? <span class="text-sm font-normal text-gray-600">(add your own)</span></h3>
                        
                        {{-- Custom Item Input --}}
                        <div class="flex gap-2 mb-4">
                            <input 
                                type="text" 
                                x-model="newItem"
                                placeholder="e.g Standing fan, rug, blender..."
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF5A00] focus:border-[#FF5A00] transition-colors"
                                @keydown.enter.prevent="addItem()"
                            />
                            <button type="button" @click="addItem()" aria-label="Add extra item"
                                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4 flex flex-wrap gap-2" x-show="items.length"><template x-for="item in items" :key="item"><span class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1.5 text-sm font-semibold text-orange-800"><span x-text="item.replaceAll('-', ' ')"></span><button type="button" @click="items=items.filter(value=>value!==item)" class="font-bold text-orange-500" :aria-label="`Remove ${item}`">×</button></span></template></div>

                        {{-- Add Another Item Button --}}
                        <button type="button" @click="$el.parentElement.querySelector('input[type=text]').focus()" class="w-full p-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 font-medium hover:border-[#FF5A00] hover:text-[#FF5A00] transition-colors">
                            + Add another item
                        </button>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('owner.properties.wizard.step', ['property'=>$property,'step'=>5]) }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors underline">Back</a>
                    <div class="flex gap-3"><button type="submit" formaction="{{ route('owner.properties.wizard.skip', ['property'=>$property,'step'=>6]) }}" class="text-sm underline">Skip</button><button type="submit" class="bg-[#FF5A00] hover:bg-[#E55000] text-white font-bold py-2.5 px-8 rounded-lg text-sm transition-colors shadow-md">Next Step</button></div>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
