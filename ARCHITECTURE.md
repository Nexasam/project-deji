# Architecture Overview

## Visual Component Tree

```
┌─────────────────────────────────────────────┐
│           layouts/app.blade.php             │
│         (Base HTML Structure)               │
└─────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────┐
│          welcome.blade.php                  │
│        (Page Orchestration)                 │
└─────────────────────────────────────────────┘
                    │
        ┌───────────┴───────────┐
        │                       │
        ▼                       ▼
┌──────────────┐        ┌──────────────┐
│  <x-toast />  │        │ <x-modal />   │
│  (Global)     │        │ (Global)      │
└──────────────┘        └──────────────┘
        │
        ▼
┌──────────────────────────────────────────┐
│           <x-navbar />                    │
│  ┌────────────────────────────────────┐  │
│  │  Desktop Menu  |  Mobile Menu      │  │
│  │  Auth Buttons  |  Hamburger        │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
        │
        ▼
┌──────────────────────────────────────────┐
│           <x-hero />                      │
│  ┌────────────────────────────────────┐  │
│  │  Badge | Title | Description       │  │
│  │  Stats | Hero Images               │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
        │
        ▼
┌──────────────────────────────────────────┐
│        <x-search-bar />                   │
│  ┌────────────────────────────────────┐  │
│  │  Where | Dates | Guests | Search   │  │
│  │  Date Picker Dropdown              │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
        │
        ▼
┌──────────────────────────────────────────┐
│      <x-category-pills />                 │
│  ┌────────────────────────────────────┐  │
│  │  [All] [Lekki] [Ikoyi] [VI] ...   │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
        │
        ▼
┌──────────────────────────────────────────┐
│            <main>                         │
│  ┌────────────────────────────────────┐  │
│  │  Section: Recently Viewed          │  │
│  │  ┌─────────────────────────────┐   │  │
│  │  │  <x-property-card />        │   │  │
│  │  │  <x-property-card />        │   │  │
│  │  │  <x-property-card />        │   │  │
│  │  │  <x-property-card />        │   │  │
│  │  └─────────────────────────────┘   │  │
│  │                                    │  │
│  │  Section: Featured Properties      │  │
│  │  ┌─────────────────────────────┐   │  │
│  │  │  <x-property-card />        │   │  │
│  │  │  <x-property-card />        │   │  │
│  │  │  <x-property-card />        │   │  │
│  │  │  <x-property-card />        │   │  │
│  │  └─────────────────────────────┘   │  │
│  └────────────────────────────────────┘  │
└──────────────────────────────────────────┘
        │
        ▼
┌──────────────────────────────────────────┐
│           <footer>                        │
│  (Inline HTML for now)                    │
└──────────────────────────────────────────┘
```

---

## Component Interaction Flow

```
┌──────────────┐
│     User     │
└──────┬───────┘
       │
       ├─ Clicks Search Button
       │  └─▶ <x-search-bar /> (Alpine.js)
       │      └─▶ handleSearch() method
       │          └─▶ console.log / API call
       │
       ├─ Clicks Category Pill
       │  └─▶ <x-category-pills /> (Alpine.js)
       │      └─▶ handleCategoryClick()
       │          └─▶ Filter properties
       │
       ├─ Clicks Heart Icon
       │  └─▶ <x-property-card /> (Alpine.js)
       │      └─▶ toggleWishlist()
       │          └─▶ window.showToast()
       │              └─▶ <x-toast /> displays
       │
       ├─ Clicks AI Insights
       │  └─▶ <x-property-card /> (Alpine.js)
       │      └─▶ openInsights()
       │          └─▶ dispatch 'open-modal' event
       │              └─▶ <x-modal /> opens
       │
       └─ Toggles Mobile Menu
          └─▶ <x-navbar /> (Alpine.js)
              └─▶ mobileMenuOpen toggle
                  └─▶ Menu slides in/out
```

---

## Data Flow Architecture

```
┌─────────────────────────────────────────┐
│          Controller / Route              │
│  Route::get('/', function() {           │
│      return view('welcome', [           │
│          'properties' => [...],          │
│      ]);                                 │
│  });                                     │
└───────────────┬─────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────┐
│         welcome.blade.php                │
│  @php                                    │
│      $recentProperties = [...];          │
│  @endphp                                 │
└───────────────┬─────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────┐
│     Component with Props                 │
│  <x-property-card                        │
│      :image="$property['image']"         │
│      :name="$property['name']"           │
│      :price="$property['price']"         │
│  />                                      │
└───────────────┬─────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────┐
│    property-card.blade.php               │
│  @props(['image', 'name', 'price'])      │
│  <div x-data="propertyCard()">           │
│      <img src="{{ $image }}" />          │
│      <h3>{{ $name }}</h3>                │
│      <span>₦{{ number_format($price) }}</span>
│  </div>                                  │
└─────────────────────────────────────────┘
```

---

## Alpine.js State Management

```
┌────────────────────────────────────────┐
│       Component-Level State             │
├────────────────────────────────────────┤
│                                         │
│  <x-navbar />                           │
│  ├─ mobileMenuOpen: false               │
│  └─ toggleMenu()                        │
│                                         │
│  <x-search-bar />                       │
│  ├─ where: ''                           │
│  ├─ guests: 2                           │
│  ├─ checkIn: ''                         │
│  ├─ checkOut: ''                        │
│  ├─ datePickerOpen: false               │
│  ├─ incrementGuests()                   │
│  ├─ decrementGuests()                   │
│  ├─ applyDates()                        │
│  └─ handleSearch()                      │
│                                         │
│  <x-property-card />                    │
│  ├─ isLiked: false                      │
│  ├─ toggleWishlist()                    │
│  └─ openInsights()                      │
│                                         │
│  <x-modal />                            │
│  ├─ open: false                         │
│  └─ @open-modal.window listener         │
│                                         │
└────────────────────────────────────────┘

┌────────────────────────────────────────┐
│        Global State (Window)            │
├────────────────────────────────────────┤
│  window.showToast(msg, duration)        │
│  window.Alpine (Alpine.js instance)     │
│  Custom Events:                         │
│    ├─ 'open-modal'                      │
│    └─ 'close-modal'                     │
└────────────────────────────────────────┘
```

---

## CSS Architecture

```
┌─────────────────────────────────────────┐
│          resources/css/app.css           │
│  @import 'tailwindcss';                 │
│  @import './components.css';            │
└───────────────┬─────────────────────────┘
                │
                ├─▶ Tailwind Utilities
                │   (Generated at build time)
                │
                └─▶ resources/css/components.css
                    ├─ Navbar styles
                    ├─ Hero styles
                    ├─ Search bar styles
                    ├─ Category pills
                    ├─ Property card
                    ├─ Modal animations
                    ├─ Toast styles
                    └─ Responsive breakpoints
```

---

## Build Process

```
┌──────────────────────────────────────────┐
│            Source Files                   │
├──────────────────────────────────────────┤
│  resources/css/app.css                   │
│  resources/css/components.css            │
│  resources/js/app.js                     │
└───────────────┬──────────────────────────┘
                │
                ▼
        ┌───────────────┐
        │  Vite Build   │
        │  - Bundling   │
        │  - Minifying  │
        │  - Hashing    │
        └───────┬───────┘
                │
                ▼
┌──────────────────────────────────────────┐
│          public/build/                    │
├──────────────────────────────────────────┤
│  assets/app-[hash].css                   │
│  assets/app-[hash].js                    │
│  manifest.json                           │
└──────────────────────────────────────────┘
                │
                ▼
┌──────────────────────────────────────────┐
│     Loaded via @vite() directive         │
│  @vite(['resources/css/app.css',         │
│         'resources/js/app.js'])          │
└──────────────────────────────────────────┘
```

---

## Request Lifecycle

```
1. User visits /
        ↓
2. Route resolves to welcome view
        ↓
3. Laravel loads layouts/app.blade.php
        ↓
4. Renders welcome.blade.php content
        ↓
5. Blade compiles <x-component /> tags
        ↓
6. Component files are included and rendered
        ↓
7. @stack('scripts') collects Alpine.js code
        ↓
8. HTML sent to browser
        ↓
9. Browser loads CSS and JS via @vite
        ↓
10. Alpine.js initializes x-data components
        ↓
11. User interacts → Alpine.js handles reactivity
```

---

## File Size Comparison

### Before Refactoring
```
welcome.blade.php: ~1200 lines
├─ HTML: ~600 lines
├─ CSS: ~500 lines
└─ JavaScript: ~100 lines
```

### After Refactoring
```
welcome.blade.php: ~150 lines
layouts/app.blade.php: ~20 lines
components/navbar.blade.php: ~60 lines
components/hero.blade.php: ~80 lines
components/search-bar.blade.php: ~100 lines
components/category-pills.blade.php: ~40 lines
components/property-card.blade.php: ~80 lines
components/modal.blade.php: ~40 lines
components/toast.blade.php: ~1 line
resources/css/components.css: ~400 lines
```

**Result:** Better organization, same functionality!

---

## Technology Stack

```
┌─────────────────────────────────────────┐
│            Frontend Stack                │
├─────────────────────────────────────────┤
│  ├─ HTML: Blade Templates                │
│  ├─ CSS: Tailwind CSS 4.0 + Custom      │
│  ├─ JavaScript: Alpine.js 3.15           │
│  └─ Build Tool: Vite 8.0                 │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│            Backend Stack                 │
├─────────────────────────────────────────┤
│  ├─ Framework: Laravel 11                │
│  ├─ PHP: 8.3+                            │
│  └─ Template Engine: Blade               │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│          Development Tools               │
├─────────────────────────────────────────┤
│  ├─ Package Manager: npm                 │
│  ├─ Module Bundler: Vite                 │
│  └─ Hot Reload: Vite HMR                 │
└─────────────────────────────────────────┘
```

---

## Performance Metrics

### Bundle Sizes (After Build)
```
app.css:  ~46 KB (9.3 KB gzipped)
app.js:   ~93 KB (33.6 KB gzipped)
Alpine.js: Included in app.js (~15 KB contribution)
```

### Page Load
```
HTML: Minimal (components compile to plain HTML)
CSS: Single file, cached
JS: Single file, cached
Fonts: Preconnected, cached
```

---

## Component Reusability Matrix

| Component | Reusable? | Use Cases |
|-----------|-----------|-----------|
| `<x-navbar />` | ✅ Yes | All pages |
| `<x-hero />` | ✅ Yes | Landing pages, about pages |
| `<x-search-bar />` | ✅ Yes | Search pages, listings |
| `<x-category-pills />` | ✅ Yes | Any filterable content |
| `<x-property-card />` | ✅ Yes | Property listings, search results, favorites |
| `<x-modal />` | ✅ Yes | Any dialog/popup needs |
| `<x-toast />` | ✅ Yes | Global notifications |

---

## Scalability Plan

### Phase 1 (Current) ✅
- Component-based architecture
- Alpine.js for interactivity
- Clean separation of concerns

### Phase 2 (Recommended)
- Connect to database/API
- Add authentication
- Implement search functionality
- Build wishlist feature

### Phase 3 (Future)
- Add more page types (property detail, user profile)
- Implement booking system
- Add admin dashboard
- Performance optimizations

### Phase 4 (Advanced)
- PWA capabilities
- Real-time notifications
- Advanced filtering/search
- Analytics integration

---

## Summary

Your application now follows **modern best practices**:

✅ **Component-Based** - Modular, reusable components  
✅ **Reactive** - Alpine.js for interactivity  
✅ **Maintainable** - Clear structure, easy to update  
✅ **Scalable** - Ready to grow with your needs  
✅ **Performant** - Optimized bundles, lazy loading ready  
✅ **Well-Documented** - Comprehensive guides included  

You're ready to build! 🚀
