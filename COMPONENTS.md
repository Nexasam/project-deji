# Component Architecture Documentation

## Overview
This project has been refactored from a monolithic Blade template into a **component-based architecture** using **Laravel Blade Components + Alpine.js**.

## Benefits
- ✅ **Reusable components** - Build once, use everywhere
- ✅ **Maintainable code** - Each component is self-contained
- ✅ **Better organization** - Clear separation of concerns
- ✅ **Alpine.js integration** - Reactive interactivity without heavy frameworks
- ✅ **Type-safe props** - Component props are documented and validated

## Project Structure

```
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php              # Main layout wrapper
│   ├── components/
│   │   ├── navbar.blade.php           # Site header with mobile menu
│   │   ├── hero.blade.php             # Hero section with stats
│   │   ├── search-bar.blade.php       # Search form with date picker
│   │   ├── category-pills.blade.php   # Category filter pills
│   │   ├── property-card.blade.php    # Property listing card
│   │   ├── modal.blade.php            # Reusable modal component
│   │   └── toast.blade.php            # Toast notification
│   └── welcome.blade.php              # Main page (orchestrates components)
├── css/
│   ├── app.css                        # Main CSS (imports components.css)
│   └── components.css                 # All component styles
└── js/
    └── app.js                         # Alpine.js initialization
```

## Components Reference

### 1. Layout (`layouts/app.blade.php`)
Base HTML structure with head, body, and scripts.

**Usage:**
```blade
@extends('layouts.app')

@section('content')
    <!-- Your page content -->
@endsection
```

---

### 2. Navbar (`<x-navbar />`)
Sticky header with desktop/mobile navigation and hamburger menu.

**Features:**
- Responsive design (desktop + mobile)
- Alpine.js powered mobile menu toggle
- Active link states
- CTA buttons

**Usage:**
```blade
<x-navbar />
```

---

### 3. Hero (`<x-hero />`)
Hero section with background image, stats, and image grid.

**Props:**
- `badge` - Badge text (default: "Verification-first marketplace")
- `title` - Main heading
- `highlightText` - Text to highlight in orange
- `description` - Description paragraph
- `stats` - Array of stats `[['value' => '12,000+', 'label' => 'Verified Listings'], ...]`
- `backgroundImage` - Hero background image
- `heroImages` - Array of 3 images for the image grid

**Usage:**
```blade
<x-hero 
    title="Your custom title"
    highlightText="custom"
    :stats="[
        ['value' => '10K+', 'label' => 'Users'],
        ['value' => '50', 'label' => 'Cities'],
    ]"
/>
```

---

### 4. Search Bar (`<x-search-bar />`)
Search form with location, dates, and guest count inputs.

**Features:**
- Alpine.js reactive state
- Date picker dropdown
- Guest counter
- Form submission handling

**Usage:**
```blade
<x-search-bar />
```

**Alpine.js API:**
- `where` - Location string
- `guests` - Number of guests (min: 1, max: 20)
- `checkIn` / `checkOut` - Date values
- `handleSearch()` - Form submission method

---

### 5. Category Pills (`<x-category-pills />`)
Horizontal scrollable category filter.

**Props:**
- `categories` - Array of categories `[['label' => 'All stays', 'active' => true], ...]`

**Usage:**
```blade
<x-category-pills 
    :categories="[
        ['label' => 'Lekki', 'active' => true],
        ['label' => 'Ikoyi', 'active' => false],
    ]"
/>
```

---

### 6. Property Card (`<x-property-card />`)
Individual property listing card with image, details, and actions.

**Props:**
- `image` - Property image URL (required)
- `name` - Property name (required)
- `location` - Location string (required)
- `guests` - Number of guests (required)
- `price` - Price per night (required)
- `rating` - Star rating (required)
- `verified` - Show verified badge (default: true)

**Features:**
- Wishlist toggle with heart icon
- AI Insights button
- Hover animations
- Alpine.js reactive state

**Usage:**
```blade
<x-property-card
    image="https://example.com/image.jpg"
    name="Luxury Apartment"
    location="Lekki, Lagos"
    :guests="4"
    :price="75000"
    :rating="4.8"
/>
```

---

### 7. Modal (`<x-modal />`)
Reusable modal dialog with Alpine.js events.

**Props:**
- `id` - Unique modal ID (default: "insights-modal")
- `title` - Modal title (default: "AI Insights")

**Usage:**
```blade
<x-modal id="my-modal" title="Custom Title">
    <p>Modal content goes here</p>
</x-modal>
```

**Opening the modal:**
```javascript
window.dispatchEvent(new CustomEvent('open-modal', { 
    detail: { id: 'my-modal' } 
}));
```

**Closing the modal:**
```javascript
window.dispatchEvent(new CustomEvent('close-modal', { 
    detail: { id: 'my-modal' } 
}));
```

---

### 8. Toast (`<x-toast />`)
Global toast notification system.

**Usage:**
```blade
<x-toast />
```

**Showing a toast:**
```javascript
window.showToast('Success message!', 3000); // duration in ms
```

---

## Alpine.js Integration

All interactive components use **Alpine.js** for reactivity:

### Component Data Functions
Each interactive component defines an Alpine.js data function:

**Example: Search Bar**
```javascript
function searchBar() {
    return {
        where: '',
        guests: 2,
        incrementGuests() { ... },
        handleSearch() { ... }
    }
}
```

### Global Helpers

**Toast Notification:**
```javascript
window.showToast(message, duration = 3000)
```

**Modal Events:**
```javascript
// Open
window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'modal-id' } }));

// Close
window.dispatchEvent(new CustomEvent('close-modal', { detail: { id: 'modal-id' } }));
```

---

## Styling Architecture

### Component Styles (`resources/css/components.css`)
All component-specific CSS is centralized in one file:
- Navbar styles
- Card animations
- Modal transitions
- Search bar layout
- Responsive breakpoints

### Tailwind Integration
Components use a mix of:
- **Utility classes** for spacing, colors, typography
- **Custom CSS** for complex animations and interactions

---

## Development Workflow

### Adding a New Component

1. **Create the component file:**
   ```bash
   # In resources/views/components/
   touch my-component.blade.php
   ```

2. **Define props:**
   ```blade
   @props(['title', 'description' => 'Default value'])
   ```

3. **Add Alpine.js interactivity (if needed):**
   ```blade
   <div x-data="myComponent()">
       <!-- Component markup -->
   </div>

   @push('scripts')
   <script>
   function myComponent() {
       return {
           // Component state and methods
       }
   }
   </script>
   @endpush
   ```

4. **Add styles to `components.css`:**
   ```css
   .my-component {
       /* Component styles */
   }
   ```

5. **Use the component:**
   ```blade
   <x-my-component title="Hello" />
   ```

---

## Best Practices

### ✅ DO
- Keep components small and focused
- Use Alpine.js for simple interactivity
- Extract reusable logic into functions
- Document component props
- Use semantic HTML

### ❌ DON'T
- Mix server-side and client-side state
- Create deeply nested component hierarchies
- Inline large blocks of CSS
- Ignore accessibility attributes
- Duplicate styles across components

---

## Testing Components

### Visual Testing
```bash
npm run dev
# Visit http://localhost:8000
```

### Component Isolation
To test components individually, create dedicated routes:

```php
// routes/web.php
Route::get('/components/card', function () {
    return view('components.property-card', [
        'image' => '...',
        'name' => 'Test Property',
        // ... other props
    ]);
});
```

---

## Migration Notes

### Before (Monolithic)
- ❌ 1000+ lines in single file
- ❌ Inline styles and scripts
- ❌ Hard to maintain
- ❌ No reusability

### After (Component-Based)
- ✅ ~100 lines per component
- ✅ Separated concerns
- ✅ Easy to maintain
- ✅ Fully reusable

---

## Future Enhancements

Consider these improvements as the project grows:

1. **Component Library** - Create a Storybook-like documentation site
2. **Form Components** - Build reusable form inputs, selects, checkboxes
3. **Data Fetching** - Integrate with APIs for dynamic property data
4. **State Management** - Use Alpine.js stores for global state
5. **Testing** - Add unit tests for Alpine.js components
6. **Performance** - Lazy load images and components

---

## Resources

- [Laravel Blade Components](https://laravel.com/docs/blade#components)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Tailwind CSS](https://tailwindcss.com/)

---

## Support

For questions or issues with the component architecture, refer to:
- This documentation
- Component source code comments
- Laravel Blade documentation
- Alpine.js documentation


---

### 9. Feature Grid (`<x-feature-grid />`)
Showcase features with icons and descriptions.

**Props:**
- `title` - Section title (default: "Why Choose Us")
- `subtitle` - Section subtitle (default: "What makes us different")
- `features` - Array of features with icon, title, description

**Usage:**
```blade
<x-feature-grid 
    title="Why Choose Us"
    :features="[
        [
            'icon' => '<svg>...</svg>',
            'title' => 'Feature Title',
            'description' => 'Feature description'
        ],
    ]"
/>
```

---

### 10. How It Works (`<x-how-it-works />`)
Display step-by-step process with numbered circles.

**Props:**
- `title` - Section title (default: "How It Works")
- `subtitle` - Section subtitle
- `steps` - Array of steps with title and description

**Usage:**
```blade
<x-how-it-works 
    :steps="[
        [
            'title' => 'Step Title',
            'description' => 'Step description'
        ],
    ]"
/>
```

---

### 11. Testimonials (`<x-testimonials />`)
Customer reviews with ratings and profile info.

**Props:**
- `title` - Section title (default: "What Our Guests Say")
- `subtitle` - Section subtitle
- `testimonials` - Array with name, location, rating, text

**Usage:**
```blade
<x-testimonials 
    :testimonials="[
        [
            'name' => 'John Doe',
            'location' => 'Lagos, Nigeria',
            'rating' => 5,
            'text' => 'Great experience!'
        ],
    ]"
/>
```

---

### 12. CTA Section (`<x-cta-section />`)
Call-to-action banner with buttons.

**Props:**
- `title` - CTA headline
- `description` - Supporting text
- `primaryButton` - Primary button text
- `primaryUrl` - Primary button URL
- `secondaryButton` - Secondary button text
- `secondaryUrl` - Secondary button URL

**Usage:**
```blade
<x-cta-section 
    title="Ready to get started?"
    primaryButton="Sign Up Now"
    primaryUrl="/register"
/>
```
