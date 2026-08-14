{{-- Signup Modal --}}
<div 
    x-show="$store.modals.showSignupModal" 
    x-cloak
    @keydown.escape.window="$store.modals.closeAll()"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm px-4"
    x-data="{ showPassword: false }"
>
    <div 
        @click.away="$store.modals.closeAll()"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full bg-white overflow-hidden shadow-2xl"
        style="max-width:520px; border-radius:24px; max-height:92vh;"
    >
        {{-- ── FORM PANEL ── --}}
        <div class="overflow-y-auto" style="padding: 36px 40px;">
            {{-- Close button --}}
            <button 
                @click="$store.modals.closeAll()"
                class="absolute top-4 right-4 z-10 flex items-center justify-center rounded-lg transition-colors"
                style="width:34px;height:34px;background:#FF5A00;"
                onmouseover="this.style.background='#E64F00'"
                onmouseout="this.style.background='#FF5A00'"
            >
                <svg width="13" height="13" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Heading --}}
            <div style="margin-bottom:24px;">
                <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:700;color:#111827;margin:0 0 6px 0;line-height:1.2;">Create your account</h2>
                <p style="font-family:'Inter',sans-serif;font-size:13px;color:#6B7280;margin:0;">It's free and takes less than 2 minutes</p>
            </div>

            {{-- Social buttons --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:20px;">
                <button type="button" style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;cursor:pointer;transition:background .15s;"
                    onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='#F9FAFB'">
                    <svg width="17" height="17" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span style="font-family:'Inter',sans-serif;font-size:12px;font-weight:500;color:#374151;">Google</span>
                </button>
                <button type="button" style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;cursor:pointer;transition:background .15s;"
                    onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='#F9FAFB'">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                    </svg>
                    <span style="font-family:'Inter',sans-serif;font-size:12px;font-weight:500;color:#374151;">Apple</span>
                </button>
                <button type="button" style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;cursor:pointer;transition:background .15s;"
                    onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='#F9FAFB'">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span style="font-family:'Inter',sans-serif;font-size:12px;font-weight:500;color:#374151;">Facebook</span>
                </button>
            </div>

            {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                <div style="flex:1;height:1px;background:#E5E7EB;"></div>
                <span style="font-size:11px;color:#9CA3AF;font-family:'Inter',sans-serif;font-weight:500;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">or sign up with email</span>
                <div style="flex:1;height:1px;background:#E5E7EB;"></div>
            </div>

            {{-- Form --}}
            <form style="display:flex;flex-direction:column;gap:14px;" @submit.prevent>

                {{-- Name + Email row --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;font-family:'Inter',sans-serif;">Full name</label>
                        <input 
                            type="text" 
                            name="name" 
                            required
                            placeholder="Sarah Jenkins"
                            style="width:100%;padding:10px 12px;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:13px;color:#111827;font-family:'Inter',sans-serif;outline:none;box-sizing:border-box;transition:border-color .15s;"
                            onfocus="this.style.borderColor='#FF5A00';this.style.background='#fff'"
                            onblur="this.style.borderColor='#E5E7EB';this.style.background='#F9FAFB'"
                        />
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;font-family:'Inter',sans-serif;">Email address</label>
                        <input 
                            type="email" 
                            name="email" 
                            required
                            placeholder="you@example.com"
                            style="width:100%;padding:10px 12px;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:13px;color:#111827;font-family:'Inter',sans-serif;outline:none;box-sizing:border-box;transition:border-color .15s;"
                            onfocus="this.style.borderColor='#FF5A00';this.style.background='#fff'"
                            onblur="this.style.borderColor='#E5E7EB';this.style.background='#F9FAFB'"
                        />
                    </div>
                </div>

                {{-- Phone --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;font-family:'Inter',sans-serif;">Phone number</label>
                    <div style="display:flex;gap:8px;">
                        <div style="position:relative;flex-shrink:0;">
                            <select style="appearance:none;height:100%;padding:10px 28px 10px 10px;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:13px;color:#111827;font-family:'Inter',sans-serif;outline:none;cursor:pointer;"
                                onfocus="this.style.borderColor='#FF5A00'" onblur="this.style.borderColor='#E5E7EB'">
                                <option>🇳🇬 +234</option>
                                <option>🇺🇸 +1</option>
                                <option>🇬🇧 +44</option>
                                <option>🇬🇭 +233</option>
                                <option>🇰🇪 +254</option>
                            </select>
                            <svg style="position:absolute;right:8px;top:50%;transform:translateY(-50%);pointer-events:none;" width="12" height="12" fill="none" stroke="#6B7280" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <input 
                            type="tel" 
                            name="phone" 
                            required
                            placeholder="080 0000 0000"
                            style="flex:1;padding:10px 12px;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:13px;color:#111827;font-family:'Inter',sans-serif;outline:none;box-sizing:border-box;transition:border-color .15s;"
                            onfocus="this.style.borderColor='#FF5A00';this.style.background='#fff'"
                            onblur="this.style.borderColor='#E5E7EB';this.style.background='#F9FAFB'"
                        />
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;font-family:'Inter',sans-serif;">Password</label>
                    <div style="position:relative;">
                        <input 
                            :type="showPassword ? 'text' : 'password'"
                            name="password" 
                            required
                            minlength="8"
                            placeholder="Min. 8 characters"
                            style="width:100%;padding:10px 44px 10px 12px;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:13px;color:#111827;font-family:'Inter',sans-serif;outline:none;box-sizing:border-box;transition:border-color .15s;"
                            onfocus="this.style.borderColor='#FF5A00';this.style.background='#fff'"
                            onblur="this.style.borderColor='#E5E7EB';this.style.background='#F9FAFB'"
                        />
                        <button 
                            type="button"
                            @click="showPassword = !showPassword"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;"
                        >
                            <svg x-show="!showPassword" width="16" height="16" fill="none" stroke="#9CA3AF" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" width="16" height="16" fill="none" stroke="#9CA3AF" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Terms --}}
                <div style="display:flex;align-items:flex-start;gap:10px;padding-top:2px;">
                    <input 
                        type="checkbox" 
                        id="signup-terms" 
                        name="terms" 
                        required
                        style="width:15px;height:15px;margin-top:1px;accent-color:#FF5A00;cursor:pointer;flex-shrink:0;"
                    />
                    <label for="signup-terms" style="font-size:12px;color:#6B7280;line-height:1.5;cursor:pointer;font-family:'Inter',sans-serif;">
                        I agree to the 
                        <a href="#" style="color:#FF5A00;font-weight:500;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Terms of Service</a>
                        and 
                        <a href="#" style="color:#FF5A00;font-weight:500;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Privacy Policy</a>
                    </label>
                </div>

                {{-- Submit --}}
                <button 
                    type="button"
                    onclick="window.location.href='/dashboard'"
                    style="width:100%;padding:12px;background:#FF5A00;border:none;border-radius:10px;font-size:14px;font-weight:600;color:#fff;font-family:'Inter',sans-serif;cursor:pointer;transition:background .15s;margin-top:2px;"
                    onmouseover="this.style.background='#E64F00'"
                    onmouseout="this.style.background='#FF5A00'"
                >
                    Create free account
                </button>
            </form>

            {{-- Footer --}}
            
        </div>
    </div>
</div>
