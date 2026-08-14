# Troubleshooting Guide

## Common Issues & Solutions

### ✅ FIXED: Vite Manifest Error - Unable to locate components.css

**Error Message:**
```
Unable to locate file in Vite manifest: resources/css/components.css
```

**Cause:**
Vite cannot load CSS files directly through the `@vite()` directive unless they're entry points. Component styles should be imported via the main CSS file.

**Solution:**
The fix has been applied. The correct setup is:

**1. Import in `app.css`:**
```css
/* resources/css/app.css */
@import 'tailwindcss';
@import './components.css';  ← Imports components.css
```

**2. Load only app.css in layout:**
```blade
{{-- resources/views/layouts/app.blade.php --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
{{-- NOT: @vite(['resources/css/app.css', 'resources/css/components.css', ...]) --}}
```

**3. Rebuild:**
```bash
npm run build
```

**Status:** ✅ Fixed and documented

---

### Components Not Rendering

**Symptoms:**
- Blank page
- Components show as plain text
- Layout issues

**Solution:**
```bash
# Clear compiled views
php artisan view:clear

# Clear config cache
php artisan config:clear

# Refresh browser
Ctrl + F5 (hard refresh)
```

---

### Styles Not Applying

**Symptoms:**
- No styling
- Looks like plain HTML
- CSS not loaded

**Checklist:**
1. **Verify CSS import in app.css:**
   ```css
   @import './components.css';
   ```

2. **Rebuild assets:**
   ```bash
   npm run build
   ```

3. **Check browser console:**
   - Look for 404 errors on CSS files
   - Check Network tab for failed requests

4. **Hard refresh browser:**
   ```
   Ctrl + F5 (Windows/Linux)
   Cmd + Shift + R (Mac)
   ```

5. **Verify Vite manifest:**
   ```bash
   # Should exist after build
   dir public\build\manifest.json
   ```

---

### Alpine.js Not Working

**Symptoms:**
- Clicks don't work
- Menus don't toggle
- No interactivity

**Solution:**

1. **Check browser console for errors**

2. **Verify Alpine.js is loaded:**
   Open browser console and type:
   ```javascript
   window.Alpine
   ```
   Should return an object, not undefined.

3. **Check app.js has Alpine:**
   ```javascript
   // resources/js/app.js
   import Alpine from 'alpinejs';
   window.Alpine = Alpine;
   Alpine.start();
   ```

4. **Rebuild assets:**
   ```bash
   npm run build
   ```

5. **Clear cache:**
   ```bash
   php artisan view:clear
   ```

---

### Mobile Menu Not Working

**Symptoms:**
- Hamburger icon doesn't toggle menu
- Menu stays open/closed

**Solution:**

1. **Check Alpine.js is working** (see above)

2. **Verify component syntax:**
   ```blade
   <header x-data="{ mobileMenuOpen: false }">
       <button @click="mobileMenuOpen = !mobileMenuOpen">
       </button>
       <nav :class="{ 'open': mobileMenuOpen }">
       </nav>
   </header>
   ```

3. **Check CSS classes exist:**
   - `.mobile-menu`
   - `.mobile-menu.open`
   
   These are in `resources/css/components.css`

---

### Modal Not Opening

**Symptoms:**
- Clicking "AI Insights" does nothing
- Modal doesn't appear

**Solution:**

1. **Check event dispatch:**
   ```javascript
   // Correct way to open modal
   window.dispatchEvent(new CustomEvent('open-modal', { 
       detail: { id: 'insights-modal' } 
   }));
   ```

2. **Verify modal component:**
   - Modal should have `x-data` with Alpine.js
   - Modal should listen to `@open-modal.window`

3. **Check modal ID matches:**
   ```blade
   <x-modal id="insights-modal" />
   
   <!-- ID must match in dispatch -->
   detail: { id: 'insights-modal' }
   ```

---

### Toast Not Showing

**Symptoms:**
- No notification appears
- `showToast()` does nothing

**Solution:**

1. **Verify toast component is included:**
   ```blade
   <x-toast />
   ```

2. **Check the function exists:**
   Open browser console:
   ```javascript
   typeof window.showToast
   ```
   Should return "function"

3. **Verify function is defined:**
   Check in `welcome.blade.php` `@push('scripts')` section:
   ```javascript
   window.showToast = function(message, duration = 3000) {
       // ...
   }
   ```

4. **Call correctly:**
   ```javascript
   window.showToast('Your message here!', 3000);
   ```

---

### Search Bar Not Working

**Symptoms:**
- Date picker doesn't open
- Guest counter doesn't work
- Search doesn't submit

**Solution:**

1. **Check Alpine.js component:**
   ```blade
   <form x-data="searchBar()" @submit.prevent="handleSearch">
   ```

2. **Verify function exists:**
   Check `@push('scripts')` has `searchBar()` function

3. **Check for JavaScript errors:**
   Open browser console (F12)

---

### Images Not Loading

**Symptoms:**
- Broken image icons
- 404 errors for images

**Solution:**

1. **Verify image paths:**
   ```blade
   {{-- Correct: relative to public directory --}}
   <img src="/hero1.jpg" />
   
   {{-- Or use asset() helper --}}
   <img src="{{ asset('hero1.jpg') }}" />
   ```

2. **Check images exist:**
   ```bash
   dir public\hero1.jpg
   dir public\logo.png
   ```

3. **Add placeholder images if needed:**
   ```bash
   # Download placeholder or create image
   # Place in public directory
   ```

---

### Build Errors

**Symptoms:**
- `npm run build` fails
- Vite errors

**Solution:**

1. **Clean install:**
   ```bash
   rmdir /s /q node_modules
   del package-lock.json
   npm install
   ```

2. **Check Node version:**
   ```bash
   node --version
   # Should be 18+ for Vite 8
   ```

3. **Verify package.json:**
   ```json
   {
     "devDependencies": {
       "@tailwindcss/vite": "^4.0.0",
       "vite": "^8.0.0",
       "laravel-vite-plugin": "^3.1"
     }
   }
   ```

---

### Development Server Issues

**Symptoms:**
- `npm run dev` fails
- Port already in use

**Solution:**

1. **Kill existing process:**
   ```bash
   # Find process on port 5173
   netstat -ano | findstr :5173
   
   # Kill process (use PID from above)
   taskkill /PID <PID> /F
   ```

2. **Try different port:**
   ```bash
   # Edit vite.config.js if needed
   server: {
       port: 3000
   }
   ```

---

### Laravel Server Issues

**Symptoms:**
- `php artisan serve` fails
- Port 8000 already in use

**Solution:**

1. **Use different port:**
   ```bash
   php artisan serve --port=8080
   ```

2. **Kill existing process:**
   ```bash
   netstat -ano | findstr :8000
   taskkill /PID <PID> /F
   ```

---

## Debug Checklist

When something isn't working:

1. **[ ] Check browser console** (F12) for JavaScript errors
2. **[ ] Check Network tab** for failed requests (404s, 500s)
3. **[ ] Clear Laravel caches**
   ```bash
   php artisan view:clear
   php artisan config:clear
   ```
4. **[ ] Rebuild assets**
   ```bash
   npm run build
   ```
5. **[ ] Hard refresh browser** (Ctrl+F5)
6. **[ ] Check file exists** where you expect it
7. **[ ] Verify syntax** (missing quotes, brackets, etc.)
8. **[ ] Check Laravel logs** (`storage/logs/laravel.log`)

---

## Quick Fixes Summary

| Issue | Quick Fix |
|-------|-----------|
| Components not rendering | `php artisan view:clear` |
| Styles not applying | `npm run build` + Ctrl+F5 |
| Alpine.js not working | Check console, rebuild assets |
| Vite manifest error | Use `@import` in app.css, not `@vite` |
| Modal not opening | Check event dispatch syntax |
| Build failing | `rm -rf node_modules && npm install` |

---

## Getting Help

If you're still stuck:

1. **Check documentation:**
   - `COMPONENTS.md` - Component API
   - `QUICK-REFERENCE.md` - Syntax guide
   - `ARCHITECTURE.md` - System design

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Enable debug mode:**
   ```env
   APP_DEBUG=true
   ```

4. **Check versions:**
   ```bash
   php --version
   node --version
   npm --version
   php artisan --version
   ```

---

## Prevention Tips

- **Always rebuild after CSS changes:** `npm run build`
- **Use `npm run dev`** for development (hot reload)
- **Clear caches** after updating views
- **Check browser console** before asking for help
- **Use version control** to track changes
- **Test in incognito** to rule out cache issues

---

## Still Having Issues?

1. Check if original file works:
   ```bash
   cp welcome.blade.php.backup welcome.blade.php
   php artisan view:clear
   ```

2. Gradually migrate back:
   - Start with just the layout
   - Add one component at a time
   - Test after each addition

3. Compare with backup:
   - What's different?
   - What changed between working and broken?

---

**Last Updated:** August 14, 2026

**Status:** All known issues documented ✅
