{{-- Signup Modal --}}
<div 
    x-show="$store.modals.showSignupModal" 
    x-cloak
    @keydown.escape.window="$store.modals.closeAll()"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
    x-data="{ showPassword: false }"
>
    <div 
        @click.away="$store.modals.closeAll()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="bg-white shadow-2xl w-full max-w-lg relative"
        style="border-radius: 32px;"
    >
        {{-- Close button --}}
        <button 
            @click="$store.modals.closeAll()"
            class="absolute top-4 right-4 bg-[#FF5A00] text-white rounded-lg p-2 hover:bg-[#E65100] transition-colors z-10"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Modal content --}}
        <div class="px-6 py-6">
            {{-- Title --}}
            <h2 class="text-[26px] font-bold text-[#2D2D2D] mb-1">Sign Up</h2>
            <p class="text-[14px] text-[#6B6B6B] mb-5">Let us get you onboard.</p>

            {{-- Form --}}
            <form action="/register" method="POST" class="space-y-3.5">
                @csrf
                
                {{-- Full Name --}}
                <div>
                    <label for="signup-name" class="block text-[12px] font-semibold text-[#2D2D2D] mb-1.5">
                        Full name
                    </label>
                    <input 
                        type="text" 
                        id="signup-name" 
                        name="name" 
                        required
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-[13px] text-gray-900 placeholder:text-[#9CA3AF] focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                        placeholder="e.g. Sarah Jenkins"
                    />
                </div>

                {{-- Email --}}
                <div>
                    <label for="signup-email" class="block text-[12px] font-semibold text-[#2D2D2D] mb-1.5">
                        Email address
                    </label>
                    <input 
                        type="email" 
                        id="signup-email" 
                        name="email" 
                        required
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-[13px] text-gray-900 placeholder:text-[#9CA3AF] focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                        placeholder="you@example.com"
                    />
                </div>

                {{-- Phone --}}
                <div>
                    <label for="signup-phone" class="block text-[12px] font-semibold text-[#2D2D2D] mb-1.5">
                        Phone number
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-shrink-0">
                            <select class="appearance-none h-full px-2.5 py-2.5 pr-7 bg-gray-50 border border-gray-200 rounded-xl text-[13px] text-gray-900 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all cursor-pointer">
                                <option value="+234">🇳🇬 +234</option>
                                <option value="+1">🇺🇸 +1</option>
                                <option value="+44">🇬🇧 +44</option>
                                <option value="+233">🇬🇭 +233</option>
                                <option value="+254">🇰🇪 +254</option>
                            </select>
                            <svg class="absolute right-1.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <input 
                            type="tel" 
                            id="signup-phone" 
                            name="phone" 
                            required
                            class="flex-1 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-[13px] text-gray-900 placeholder:text-[#9CA3AF] focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            placeholder="Phone number"
                        />
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="signup-password" class="block text-[12px] font-semibold text-[#2D2D2D] mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            id="signup-password" 
                            name="password" 
                            required
                            minlength="8"
                            class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-[13px] text-gray-900 placeholder:text-[#9CA3AF] focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all pr-14"
                            placeholder="Min. 8 characters"
                        />
                        <button 
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#FF5A00] text-[12px] font-semibold hover:text-[#E65100] transition-colors"
                        >
                            <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                </div>

                {{-- Terms and conditions --}}
                <div class="flex items-start pt-1">
                    <input 
                        type="checkbox" 
                        id="signup-terms" 
                        name="terms" 
                        required
                        class="w-4 h-4 mt-0.5 text-orange-500 border-gray-300 rounded focus:ring-orange-500 cursor-pointer"
                    />
                    <label for="signup-terms" class="ml-2.5 text-[12px] text-[#6B6B6B] leading-snug cursor-pointer">
                        I agree to the <a href="#" class="text-[#FF5A00] hover:text-[#E65100] underline font-medium">Terms of Service</a> and <a href="#" class="text-[#FF5A00] hover:text-[#E65100] underline font-medium">Privacy Policy</a>
                    </label>
                </div>

                {{-- Submit button --}}
                <button 
                    type="submit"
                    class="w-full bg-[#FF5A00] hover:bg-[#E65100] text-white font-bold text-[15px] py-3 rounded-xl transition-all active:scale-[0.98] shadow-sm mt-2"
                >
                    Sign Up
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center my-4">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-3 text-[11px] text-[#9CA3AF] uppercase tracking-wide">or continue with</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            {{-- Social signup --}}
            <div class="grid grid-cols-3 gap-2.5">
                <button type="button" class="flex items-center justify-center px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="sr-only">Google</span>
                </button>
                <button type="button" class="flex items-center justify-center px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                    </svg>
                    <span class="sr-only">Apple</span>
                </button>
                <button type="button" class="flex items-center justify-center px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="sr-only">Facebook</span>
                </button>
            </div>

            {{-- Login link --}}
            <p class="text-center text-[13px] text-[#6B6B6B] mt-6">
                Already have an account? 
                <button 
                    type="button"
                    @click="$store.modals.openLogin()"
                    class="text-[#FF5A00] hover:text-[#E65100] font-semibold transition-colors"
                >
                    Log in
                </button>
            </p>
        </div>
    </div>
</div>
