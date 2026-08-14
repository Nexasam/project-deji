# Component Quick Reference

## 🚀 Quick Start

```bash
# Start dev server
npm run dev

# In another terminal
php artisan serve
```

Visit: http://localhost:8000

---

## 📦 Component Syntax

### Basic Component
```blade
<x-navbar />
<x-toast />
```

### With Props
```blade
<x-hero 
    title="My Title"
    description="My description"
/>
```

### With Dynamic Data
```blade
<x-property-card
    :image="$property->image"
    :name="$property->name"
    :price="$property->price"
/>
```

---

## 🎨 Available Components

| Component | Usage | Key Props |
|-----------|-------|-----------|
| `<x-navbar />` | Site header | None |
| `<x-hero />` | Hero section | `title`, `stats`, `heroImages` |
| `<x-search-bar />` | Search form | None (self-contained) |
| `<x-category-pills />` | Category filters | `categories` (array) |
| `<x-property-card />` | Property listing | `image`, `name`, `price`, `rating` |
| `<x-modal />` | Modal dialog | `id`, `title` |
| `<x-toast />` | Notifications | None |

---

## ⚡ Alpine.js Helpers

### Show Toast
```javascript
window.showToast('Your message here!', 3000);
```

### Open Modal
```javascript
window.dispatchEvent(new CustomEvent('open-modal', { 
    detail: { id: 'modal-id' } 
}));
```

### Close Modal
```javascript
window.dispatchEvent(new CustomEvent('close-modal', { 
    detail: { id: 'modal-id' } 
}));
```

---

## 🔧 Common Tasks

### Add New Component
```bash
# Create file
touch resources/views/components/my-component.blade.php
```

```blade
@props(['title', 'content'])

<div class="my-component">
    <h3>{{ $title }}</h3>
    <p>{{ $content }}</p>
</div>
```

```blade
<!-- Use it -->
<x-my-component title="Hello" content="World" />
```

### Add Alpine.js to Component
```blade
<div x-data="myComponent()">
    <button @click="handleClick">Click</button>
</div>

@push('scripts')
<script>
function myComponent() {
    return {
        handleClick() {
            console.log('Clicked!');
        }
    }
}
</script>
@endpush
```

### Add Component Styles
Edit `resources/css/components.css`:

```css
.my-component {
    padding: 1rem;
    border-radius: 8px;
}
```

Then rebuild:
```bash
npm run build
```

---

## 📝 Property Card Example

```blade
<x-property-card
    image="https://example.com/image.jpg"
    name="Luxury Apartment"
    location="Lekki, Lagos"
    :guests="4"
    :price="75000"
    :rating="4.8"
    :verified="true"
/>
```

---

## 🎯 Hero Example

```blade
<x-hero 
    title="Find your perfect stay"
    highlightText="perfect"
    description="Browse verified properties"
    :stats="[
        ['value' => '10,000+', 'label' => 'Properties'],
        ['value' => '50', 'label' => 'Cities'],
    ]"
    backgroundImage="/images/hero-bg.jpg"
    :heroImages="['/img1.jpg', '/img2.jpg', '/img3.jpg']"
/>
```

---

## 🔄 Category Pills Example

```blade
<x-category-pills 
    :categories="[
        ['label' => 'All', 'active' => true],
        ['label' => 'Lekki', 'active' => false],
        ['label' => 'Ikoyi', 'active' => false],
    ]"
/>
```

---

## 🐛 Troubleshooting

### Component not showing?
```bash
php artisan view:clear
```

### Styles not working?
```bash
npm run build
```

### Alpine.js not responding?
Check browser console for errors and ensure:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

---

## 📚 Full Documentation

- **Component API:** See `COMPONENTS.md`
- **Refactoring Summary:** See `REFACTORING-SUMMARY.md`
- **Laravel Blade:** https://laravel.com/docs/blade
- **Alpine.js:** https://alpinejs.dev

---

## 💡 Tips

1. **Props vs Slots:** Use props for data, slots for HTML content
2. **Alpine State:** Keep component state local when possible
3. **Reusability:** If you use something twice, make it a component
4. **Naming:** Use kebab-case for component names (`my-component.blade.php`)
5. **Organization:** Group related components in subdirectories

---

## ✨ Component Pattern

```blade
{{-- Define props --}}
@props(['title', 'description' => 'Default'])

{{-- Alpine.js data (optional) --}}
<div x-data="myComponent()">
    <h2>{{ $title }}</h2>
    <p>{{ $description }}</p>
    
    {{-- Slot for custom content --}}
    {{ $slot }}
</div>

{{-- Scripts (optional) --}}
@push('scripts')
<script>
function myComponent() {
    return {
        // Component logic
    }
}
</script>
@endpush
```

---

That's it! You're ready to build with components. 🚀
