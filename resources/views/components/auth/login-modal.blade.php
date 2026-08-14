{{-- Login Modal --}}
<div 
    x-show="showLoginModal" 
    x-cloak
    @keydown.escape.window="showLoginModal = false"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 px-4"
    style="display: none;"
>
    <div 
        @click.away="showLoginModal = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative"
    >
        {{-- Close button --}}
        <button 
            @click="showLoginModal = false"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Modal content --}}
        <div class="p-8">
            {{-- Logo --}}
            <div class="flex justify-center mb-6">
                <img src="/logo1.png" alt="Verified Shortlet" class="h-12 w-auto" />
            </div>

            {{-- Title --}}
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Welcome back</h2>
            <p class="text-sm text-gray-600 text-center mb-6">Log in to access your account</p>

            {{-- Form --}}
            <form action="/login" method="POST" class="space-y-4">
                @csrf
                
                {{-- Email --}}
                <div>
                    <label for="login-email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email address
                    </label>
                    <input 
                        type="email" 
                        id="login-email" 
                        name="email" 
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                        placeholder="you@example.com"
                    />
                </div>

                {{-- Password --}}
                <div>
                    <label for="login-password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="login-password" 
                        name="password" 
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                        placeholder="••••••••"
                    />
                </div>

                {{-- Remember me & Forgot password --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-orange-500 hover:text-orange-600 font-medium">
                        Forgot password?
                    </a>
                </div>

                {{-- Submit button --}}
                <button 
                    type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-lg transition-colors active:scale-[0.98]"
                >
                    Log In
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-4 text-sm text-gray-500">or</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>

            {{-- Social login --}}
            <div class="space-y-3">
                <button class="w-full flex items-center justify-center gap-3 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Continue with Google</span>
                </button>
            </div>

            {{-- Sign up link --}}
            <p class="text-center text-sm text-gray-600 mt-6">
                Don't have an account? 
                <button 
                    @click="showLoginModal = false; showSignupModal = true"
                    class="text-orange-500 hover:text-orange-600 font-semibold"
                >
                    Sign up
                </button>
            </p>
        </div>
    </div>
</div>
