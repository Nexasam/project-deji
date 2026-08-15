# First Property Onboarding Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the owner dashboard’s sample data with a business-scoped empty state and allow the owner to create the business’s first property as a real draft record.

**Architecture:** `ActiveBusinessContext` remains the sole source of tenancy; neither the browser nor form submits `business_id`. A Form Request validates property details, a focused service writes the draft atomically, and owner controllers query only the active business before rendering real Blade states.

**Tech Stack:** Laravel 13, Eloquent UUID models, Blade, Tailwind CSS 4, MySQL.

## Global Constraints

- Every created property belongs to the active business resolved by middleware.
- The form must never accept `business_id`, verification state, publication state, or audit actor IDs.
- New properties start `unverified`, `draft`, `not_ready`, `unavailable`, and `active`.
- Business verification does not block draft creation.
- Property codes are generated server-side and unique within the active business.
- Dashboard content must use database records or an honest empty state; no sample business metrics remain.

---

### Task 1: Add business-scoped property creation

**Files:**
- Create: `app/Http/Requests/Owner/StorePropertyRequest.php`
- Create: `app/Services/Property/CreatePropertyService.php`
- Modify: `app/Http/Controllers/Owner/OwnerPropertyController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Owner/FirstPropertyOnboardingTest.php`

**Interfaces:**
- Consumes: `ActiveBusinessContext`, authenticated `User`, validated property attributes.
- Produces: `CreatePropertyService::create(ActiveBusinessContext $context, User $actor, array $attributes): Property`.

- [ ] Write a feature test proving the POST route ignores tenant input and persists the property under the active business.
- [ ] Run the focused test and confirm it fails because the store route does not exist.
- [ ] Implement normalized validation for name, property type, address, capacity, bedrooms, bathrooms, description, and optional nightly price.
- [ ] Implement atomic draft creation with a collision-safe business-scoped code and audit actor IDs.
- [ ] Add `OwnerPropertyController::store()` and the named `owner.properties.store` POST route.

### Task 2: Replace sample dashboard data with real states

**Files:**
- Modify: `app/Http/Controllers/Owner/OwnerDashboardController.php`
- Replace: `resources/views/dashboard.blade.php`
- Create: `resources/views/owner/properties/create.blade.php`
- Modify: `app/Http/Controllers/Owner/OwnerPropertyController.php`

**Interfaces:**
- Consumes: active business, authenticated user, active business properties.
- Produces: zero-property call to action or a real property portfolio summary.

- [ ] Query active-business properties in the dashboard controller.
- [ ] Render the business name, authenticated user, honest property counts, and zero-property call to action.
- [ ] Render real property rows after a draft exists without fabricated revenue, occupancy, bookings, or tasks.
- [ ] Render a responsive property form posting to `owner.properties.store` with validation feedback.
- [ ] Redirect successful creation to `owner.dashboard` with a success message.

### Task 3: Verify integration

**Files:**
- Verify all files above.

**Interfaces:**
- Consumes: Laravel routes, Blade compiler, Vite build.
- Produces: verified route and asset state.

- [ ] Format changed PHP files with Pint.
- [ ] Run the focused feature test; if schema-wide legacy migrations block it, report the exact migration failure separately.
- [ ] Confirm owner property GET/POST routes with `php artisan route:list`.
- [ ] Build assets with `npm run build`.
- [ ] Run `git diff --check`.
