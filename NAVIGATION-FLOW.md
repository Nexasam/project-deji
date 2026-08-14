# Verified Shortlet - Navigation Flow

## Complete User Journey

### 1. Landing Page (`/`)
**File**: `resources/views/welcome.blade.php`

**Components**:
- Navbar with Login/Signup buttons
- Hero section with search
- Category pills
- Property cards
- Footer

**Actions**:
- Click "Login" → Opens login modal
- Click "Sign up" → Opens signup modal  
- Click "List your property" → Goes to `/property/add/step1`
- Search properties → Filters results

---

### 2. Authentication Modals
**Files**: 
- `resources/views/components/auth/login-modal.blade.php`
- `resources/views/components/auth/signup-modal.blade.php`

**Login Modal**:
- Email & Password fields
- "Remember me" checkbox
- "Forgot password?" link
- Google sign-in button
- Link to switch to signup modal
- **After login** → Redirects to `/dashboard`

**Signup Modal**:
- Full name, Email, Phone, Password fields
- Account type selection (Book stays / List property)
- Terms checkbox
- Google sign-up button
- Link to switch to login modal
- **After signup** → Redirects to `/dashboard`

---

### 3. Dashboard (`/dashboard`)
**File**: `resources/views/dashboard.blade.php`

**Features**:
- Sidebar navigation (Dashboard active)
- Business Health Score (87)
- Quick Actions: Add property, New booking, Assign task
- KPI Cards: Total earnings, Active listings, Occupancy rate, Outstanding tasks
- Financial Snapshot chart
- AI Host Insight Alert
- Upcoming Activities Calendar
- Recent Activity feed
- Your Listings grid

**Actions**:
- Click "Properties" in sidebar → Goes to `/properties`
- Click "+ Add property" → Goes to `/property/add/step1`
- Click any property card → Opens property detail panel

---

### 4. Properties List (`/properties`)
**File**: `resources/views/properties.blade.php`

**Features**:
- Sidebar navigation (Properties active)
- 3 Location stat cards (All Locations, Egbeda, Island)
- Search bar and status filter
- Grid/List view toggle
- 8 Property cards with different statuses:
  - OCCUPIED (orange)
  - AVAILABLE (green)
  - CLEANING (blue)
  - INSPECTION (purple)
  - BLOCKED (dark gray)

**Actions**:
- Click any property card → Opens property detail side panel
- Click "+ Add property" → Goes to `/property/add/step1`

---

### 5. Property Detail Side Panel
**Displayed on**: `/properties` (slides in from right)

**Tabs**:
1. **Overview** → Shows property stats, AI insights, operational readiness
2. **Bookings** (`/property/detail`) → Table of bookings with guest, dates, channel, status, amount
3. **Finance** (`/property/finance`) → Revenue chart, expenses, net profit
4. **Operations** → Maintenance, cleaning, inspection schedules
5. **Assets** → Interior items, condition tracking
6. **Documents** → Lease agreements, reports, receipts
7. **Reviews** → Guest reviews and ratings
8. **Marketplace** → Listing on external platforms
9. **History** → Activity log

**Actions**:
- Click X button → Closes panel, returns to properties grid
- Switch tabs → Shows different property information

---

### 6. Property Listing Flow (9 Steps)
**Entry Point**: Click "+ Add property" button from dashboard or properties page

#### Step 1 (`/property/add/step1`)
- Choose: "A brand-new property" or "Another flat in a property"
- Next → Goes to Step 2

#### Step 1-flat Variant (`/property/add/step1-flat`)
- Select existing property to add flat to
- Next → Goes to Step 2

#### Step 2 (`/property/add/step2`)
- Location selection
- Map search and address input
- Company/Location dropdown
- Next → Goes to Step 3

#### Step 3 (`/property/add/step3`)
- Property type (Apartment, Self-contained, Duplex, etc.)
- Booking type (Entire place, Private room, Shared room)
- Capacity & size (Guests, Bedrooms, Bathrooms, Size)
- Next → Goes to Step 4

#### Step 4 (`/property/add/step4`)
- Amenities selection (4 categories)
- Pre-select common amenities
- Next → Goes to Step 5

#### Step 5 (`/property/add/step5`)
- Photo upload (cover photo + gallery)
- Video upload (optional)
- Next → Goes to Step 6

#### Step 6 (`/property/add/step6`)
- Interior items (Smart TV, Air conditioner, etc.)
- Custom items input
- Next → Goes to Step 7

#### Step 7 (`/property/add/step7`)
- Document upload (lease agreements, reports, receipts)
- Drag-and-drop or click to upload
- Next → Goes to Step 8

#### Step 8 (`/property/add/step8`)
- Property name
- Description
- Nightly rate (₦)
- Weekly/monthly discount (optional)
- Multiple bookable rooms checkbox
- Next → Goes to Step 9

#### Step 9 (`/property/add/step9`)
- Review all information
- 7 sections with Edit buttons:
  - Location
  - The basics
  - Amenities
  - Photos & video
  - Assets
  - Documents
  - Name & price
- Click "Review listing" → Goes to `/property/add/success`

#### Success Page (`/property/add/success`)
- Hourglass icon with "Pending approval" message
- Explanation of review process (usually <24 hours)
- Actions:
  - "Back to Properties" → Goes to `/properties`
  - "Add another property" → Goes to `/property/add/step1`

---

## Route Summary

```php
Route::get('/', function () {
    return view('welcome');  // Landing page
});

Route::get('/dashboard', function () {
    return view('dashboard');  // Main dashboard
});

Route::get('/properties', function () {
    return view('properties');  // Properties list
});

Route::get('/property/detail', function () {
    return view('property-detail');  // Property detail with Bookings tab
});

Route::get('/property/finance', function () {
    return view('property-finance');  // Property detail with Finance tab
});

Route::get('/property/add/step1', function () {
    return view('property-add-step1');  // Add property - Step 1
});

Route::get('/property/add/step1-flat', function () {
    return view('property-add-step1-flat');  // Add property - Step 1 (flat variant)
});

Route::get('/property/add/step2', function () {
    return view('property-add-step2');  // Add property - Step 2
});

// ... Steps 3-9 ...

Route::get('/property/add/success', function () {
    return view('property-add-success');  // Property submitted success page
});
```

---

## Key Features

### Alpine.js State Management
- Modal state for login/signup
- Property detail panel visibility
- Tab switching in property detail
- Form data in multi-step property creation

### Consistent Design System
- **Colors**: Orange accent #FF5A00, Dark surfaces #1D1D1F/#222222, Page background #F4F4F4
- **Logo**: `/logo1.png` (orange VS location pin icon) with white text "Verified Shortlet"
- **Components**: Laravel Blade Components + Alpine.js for interactivity
- **Icons**: SVG icons for all UI elements
- **Badges**: Color-coded status badges (OCCUPIED-orange, AVAILABLE-green, CLEANING-blue, etc.)

### Navigation
- **Sidebar**: Persistent left navigation with sections (Overview, Manage, System)
- **Top Header**: Search bar, notifications, profile dropdown
- **Breadcrumbs**: Shows current location path
- **Back buttons**: All forms have Back links to previous steps

---

## Missing Connections (To Implement)

1. **Login/Signup** → Should POST to authentication endpoints and redirect to `/dashboard`
2. **Property Cards** → Should link to `/property/detail` or open side panel with property data
3. **Form Submissions** → Multi-step form should save data and submit at final step
4. **Edit Buttons** → Step 9 review page edit buttons should link back to specific steps
5. **Sidebar Active States** → Update active state based on current route

---

## Files Structure

```
resources/views/
├── welcome.blade.php                    # Landing page
├── dashboard.blade.php                  # Main dashboard
├── properties.blade.php                 # Properties list
├── property-detail.blade.php            # Property detail (Bookings tab)
├── property-finance.blade.php           # Property detail (Finance tab)
├── property-add-step1.blade.php         # Add property - Step 1
├── property-add-step2.blade.php         # Add property - Step 2
├── ... (steps 3-9)
├── property-add-success.blade.php       # Success page
├── layouts/
│   └── app.blade.php                    # Main layout
├── components/
│   ├── auth/
│   │   ├── login-modal.blade.php
│   │   └── signup-modal.blade.php
│   ├── navbar.blade.php
│   ├── hero.blade.php
│   ├── search-bar.blade.php
│   ├── category-pills.blade.php
│   ├── property-card.blade.php
│   └── footer.blade.php
└── partials/
    ├── sidebar.blade.php                # Dashboard sidebar
    ├── header.blade.php                 # Dashboard header
    └── dashboard-content.blade.php      # Dashboard main content
```
