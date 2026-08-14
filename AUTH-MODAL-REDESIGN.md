# Authentication Modals - Design Update

## Overview
Updated login and signup modals to match the new design specifications with improved UI/UX and visual consistency.

## Changes Made

### 1. Login Modal (`resources/views/components/auth/login-modal.blade.php`)

**Design Updates:**
- **Background Color**: Changed from white to light gray (#E8E8E8)
- **Title**: "Welcome back" with subtitle "Log in to manage your properties"
- **Close Button**: Orange background (#FF5A00) with white X icon, positioned top-right
- **Border Radius**: Increased to rounded-3xl for softer appearance
- **Typography**: 
  - Title: 32px bold
  - Labels: 13px semibold
  - Input text: 14px
- **Input Fields**: 
  - White background with no borders (border-0)
  - Rounded-xl corners
  - Larger padding (py-3)
  - Orange focus ring
- **Password Field**: Added show/hide toggle button
- **Forgot Password**: Positioned below password field, underlined link
- **Button**: Full-width orange button with bolder text
- **Social Login**: Grid layout with 3 buttons (Google, Apple, Facebook) showing only icons
- **Removed**: "Remember me" checkbox and logo

### 2. Signup Modal (`resources/views/components/auth/signup-modal.blade.php`)

**Design Updates:**
- **Background Color**: Changed from white to light gray (#E8E8E8)
- **Title**: "Sign Up" with subtitle "Let us get you onboard."
- **Close Button**: Orange background (#FF5A00) with white X icon, positioned top-right
- **Border Radius**: Increased to rounded-3xl for softer appearance
- **Typography**: 
  - Title: 32px bold
  - Labels: 13px semibold
  - Input text: 14px
- **Input Fields**: 
  - White background with no borders (border-0)
  - Rounded-xl corners
  - Larger padding (py-3)
  - Orange focus ring
  - Placeholder text matches design (e.g., "e.g. Sarah Jenkins")
- **Phone Number Field**: 
  - Added country code dropdown selector with flags
  - Default: Nigeria (+234) 🇳🇬
  - Options: USA, UK, Ghana, Kenya
  - Split layout with dropdown on left, input on right
- **Password Field**: 
  - Added show/hide toggle button
  - Single password field (removed confirmation field)
  - Placeholder: "Min. 8 characters"
  - HTML5 validation: minlength="8"
- **Terms Checkbox**: Improved spacing and styling
- **Button**: Full-width orange button with bolder text
- **Social Signup**: Grid layout with 3 buttons (Google, Apple, Facebook) showing only icons
- **Removed**: 
  - Logo
  - Password confirmation field
  - Account type selection (Book stays / List property)

### 3. Common Features

**Both Modals Now Include:**
- **Alpine.js Password Toggle**: `x-data="{ showPassword: false }"` for show/hide functionality
- **Enhanced Colors**:
  - Background: #E8E8E8
  - Primary Orange: #FF5A00
  - Hover Orange: #E65100
  - Text Dark: #2D2D2D
  - Text Light: #6B6B6B
  - Placeholder: #9CA3AF
- **Social Auth Icons**:
  - Google (colorful logo)
  - Apple (black icon)
  - Facebook (blue icon)
- **Improved Transitions**: Smoother hover and focus states
- **Better Typography**: More consistent sizing and weights
- **Enhanced Accessibility**: Proper labels, placeholders, and screen reader text

## New Functionality

### Password Visibility Toggle
Both modals now include a clickable "Show/Hide" button inside password fields:
```html
<button 
    type="button"
    @click="showPassword = !showPassword"
    class="absolute right-4 top-1/2 -translate-y-1/2 text-[#FF5A00]"
>
    <span x-text="showPassword ? 'Hide' : 'Show'"></span>
</button>
```

### Country Code Selector (Signup Only)
The phone number field includes a dropdown with country flags:
- Nigeria 🇳🇬 +234 (default)
- USA 🇺🇸 +1
- UK 🇬🇧 +44
- Ghana 🇬🇭 +233
- Kenya 🇰🇪 +254

## Visual Improvements

1. **Consistency**: Both modals share the same design language
2. **Modern Look**: Softer corners, cleaner inputs, better spacing
3. **Better Hierarchy**: Clear visual distinction between primary and secondary actions
4. **Improved UX**: Password visibility toggle, clearer labels, better placeholders
5. **Mobile Responsive**: Scrollable content with max-h-[90vh] on signup modal

## Form Data Structure

### Login Form POST to `/login`
```
- email: string (required)
- password: string (required)
```

### Signup Form POST to `/register`
```
- name: string (required)
- email: string (required)
- phone: string (required)
- password: string (required, min 8 characters)
- terms: checkbox (required)
```

## Backend Integration Notes

1. **Password Confirmation Removed**: Backend should handle password strength validation
2. **Account Type Removed**: Can be determined post-registration or based on user actions
3. **Phone Number**: Will be submitted with country code prefix from dropdown
4. **Social Auth**: Buttons are placeholders - OAuth integration needs to be implemented

## Next Steps

1. ✅ Update modal designs
2. ⏳ Implement backend authentication routes
3. ⏳ Add form validation with error messages
4. ⏳ Implement OAuth for Google, Apple, Facebook
5. ⏳ Add forgot password functionality
6. ⏳ Add success/error toast notifications
7. ⏳ Implement proper password reset flow
8. ⏳ Add email verification

## Testing

Verify these scenarios:
- [ ] Modals open correctly from all trigger points
- [ ] Password show/hide toggle works on both modals
- [ ] Country code selector displays correctly
- [ ] Close button works (click, ESC key, click outside)
- [ ] Form validation triggers on submit
- [ ] Mobile responsive layout displays correctly
- [ ] Smooth animations and transitions
- [ ] Social auth buttons are clickable
- [ ] Switch between login and signup works
- [ ] All placeholders match design

## Design Specifications

- **Modal Background**: #E8E8E8
- **Close Button**: #FF5A00 (orange)
- **Input Background**: #FFFFFF (white)
- **Primary Button**: #FF5A00 (orange)
- **Border Radius**: 1.5rem (modal), 0.75rem (inputs/buttons)
- **Font Sizes**: 32px (title), 15px (subtitle), 13px (labels), 14px (inputs)
- **Spacing**: Consistent 5-unit gap between form fields
