# Full-Page Property Wizard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the static numbered property screens with a secure, resumable, database-backed ten-step wizard that submits a property for verification.

**Architecture:** A dedicated `PropertyWizardController` renders and saves property-scoped steps while reusing existing property domain services and relations. Step 1 creates the draft; Steps 2–10 mutate that draft under active-business authorization. Additive schema fields cover only booking mode, floor area, and property-level pending channels.

**Tech Stack:** Laravel 12, PHP 8.2+, Blade, Alpine.js, Eloquent, PHPUnit, Tailwind/Vite.

**Spec:** `docs/superpowers/specs/2026-09-07-full-page-property-wizard-design.md`

## Global Constraints

- Preserve historical migrations; add a new migration only.
- Support only “brand-new property”; “Another flat” stays disabled.
- Channel state is local `pending`; no external API claims.
- Map/geocoding is deferred; address and optional coordinates persist.
- Every owner route requires authentication, active business context, and owner role.
- Preserve the approved full-page visual design.

---

### Task 1: Secure routes and draft creation

**Files:**
- Modify: `routes/web.php`
- Create: `app/Http/Controllers/Owner/PropertyWizardController.php`
- Create: `app/Http/Requests/Owner/StartPropertyWizardRequest.php`
- Modify: `resources/views/property-add-step1.blade.php`
- Test: `tests/Feature/Owner/PropertyWizardTest.php`

**Interfaces:**
- Produces: `PropertyWizardController::start(StartPropertyWizardRequest, ActiveBusinessContext): RedirectResponse`
- Produces route parameters named `property` for Steps 2–success.

- [ ] Write tests proving guests redirect to login, cross-business records are inaccessible, and Step 1 creates one draft.
- [ ] Run `php artisan test tests/Feature/Owner/PropertyWizardTest.php` and confirm authorization/draft tests fail.
- [ ] Group real owner routes under `auth`, `business.context`, `business.owner`; add Step 1 GET/POST and property-scoped route skeletons.
- [ ] Convert Step 1 to a POST form with `property_kind=brand_new`; disable and label the other-flat card.
- [ ] Run the focused tests and confirm they pass.

### Task 2: Additive schema and workflow model

**Files:**
- Create: `database/migrations/2026_09_07_000100_extend_properties_for_full_page_wizard.php`
- Create: `app/Models/PropertyChannelConnection.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Services/Property/PropertySetupWorkflow.php`
- Test: `tests/Unit/PropertySetupWorkflowTest.php`

**Interfaces:**
- Adds `properties.booking_mode`, `properties.floor_area_sqm`.
- Adds `property_channel_connections` unique on `(property_id, provider)`.
- Produces workflow keys `property-kind`, `location`, `basics`, `amenities`, `media`, `assets`, `documents`, `name-price`, `channels`, `review`.

- [ ] Write failing workflow-order, skip-rule, and resume tests.
- [ ] Run the unit test and confirm failure against old keys.
- [ ] Add the migration/model relation and update fillable/casts.
- [ ] Update workflow constants, route mapping, completion inference, and compatibility behavior.
- [ ] Run unit tests and migration tests.

### Task 3: Persist location and basics

**Files:**
- Create: `app/Http/Requests/Owner/StorePropertyLocationRequest.php`
- Create: `app/Http/Requests/Owner/StorePropertyBasicsRequest.php`
- Modify: `app/Http/Controllers/Owner/PropertyWizardController.php`
- Modify: `resources/views/property-add-step2.blade.php`
- Modify: `resources/views/property-add-step3.blade.php`
- Test: `tests/Feature/Owner/PropertyWizardTest.php`

**Interfaces:**
- Produces `storeStep2(...): RedirectResponse` and `storeStep3(...): RedirectResponse`.
- Stores address `{state, city, line_1}`, optional coordinates, property type, booking mode, capacity, bedrooms, bathrooms, and floor area.

- [ ] Add failing persistence, validation, repopulation, and next-route tests.
- [ ] Run focused tests and confirm expected failures.
- [ ] Implement dedicated requests and transactional controller mutations.
- [ ] Convert Step 2 and Step 3 controls into named server forms populated from `old()` and the draft.
- [ ] Run focused tests and confirm pass.

### Task 4: Persist amenities and media

**Files:**
- Modify: `app/Http/Controllers/Owner/PropertyWizardController.php`
- Create: `app/Http/Requests/Owner/StoreWizardAmenitiesRequest.php`
- Modify: `resources/views/property-add-step4.blade.php`
- Modify: `resources/views/property-add-step5.blade.php`
- Modify: `database/seeders/AmenitySeeder.php` if canonical codes are missing
- Test: `tests/Feature/Owner/PropertyWizardTest.php`

**Interfaces:**
- Reuses `SyncPropertyAmenitiesService` and `StorePropertyMediaService`.
- Media endpoints save uploads and delete only media belonging to the scoped draft.

- [ ] Add failing amenity sync and media upload/removal tests using fake storage.
- [ ] Run tests and verify failures.
- [ ] Load canonical amenities, wire checkboxes, and sync submitted IDs.
- [ ] Wire multipart image/video upload, saved previews, deletion, and primary ordering into Step 5.
- [ ] Run focused tests and confirm pass.

### Task 5: Persist assets and documents

**Files:**
- Modify: `app/Http/Controllers/Owner/PropertyWizardController.php`
- Create: `app/Http/Requests/Owner/StoreWizardAssetsRequest.php`
- Create: `app/Http/Requests/Owner/StoreWizardDocumentsRequest.php`
- Modify: `resources/views/property-add-step6.blade.php`
- Modify: `resources/views/property-add-step7.blade.php`
- Test: `tests/Feature/Owner/PropertyWizardTest.php`

**Interfaces:**
- Synchronizes selected/custom asset names idempotently.
- Stores private documents through `SavePropertySetupService`; skip uses workflow skip state.

- [ ] Add failing asset, document, delete/ownership, and optional-skip tests.
- [ ] Run focused tests and verify failures.
- [ ] Implement requests/controller endpoints and transactional synchronization.
- [ ] Convert Steps 6–7 to real forms and saved-record lists.
- [ ] Run focused tests and confirm pass.

### Task 6: Persist name, price, and pending channels

**Files:**
- Modify: `app/Http/Controllers/Owner/PropertyWizardController.php`
- Create: `app/Http/Requests/Owner/StorePropertyNamePriceRequest.php`
- Create: `app/Http/Requests/Owner/StorePropertyChannelsRequest.php`
- Modify: `resources/views/property-add-step8.blade.php`
- Modify: `resources/views/property-add-channels.blade.php`
- Test: `tests/Feature/Owner/PropertyWizardTest.php`

**Interfaces:**
- Updates property name, description, price and optional longer-stay promotion.
- Upserts channel records by provider with `connection_status=pending`.

- [ ] Add failing name/price validation, channel upsert, no-duplicate, and skip tests.
- [ ] Run focused tests and verify failures.
- [ ] Implement dedicated requests and transactional saves.
- [ ] Convert Steps 8–9 to named forms populated from saved state; display “Connection pending.”
- [ ] Run focused tests and confirm pass.

### Task 7: Dynamic review, idempotent submission, and success

**Files:**
- Modify: `app/Http/Controllers/Owner/PropertyWizardController.php`
- Modify: `app/Services/Property/PropertyPublicationReadinessService.php`
- Modify: `resources/views/property-add-step9.blade.php`
- Modify: `resources/views/property-add-success.blade.php`
- Modify: `app/Http/Controllers/Owner/OwnerPropertyController.php`
- Test: `tests/Feature/Owner/PropertyWizardTest.php`

**Interfaces:**
- `submit(...): RedirectResponse` transitions a ready draft once and creates one lifecycle event.
- `success(...): View` accepts only an owned submitted property.

- [ ] Add failing dynamic-review, readiness, duplicate-submit, lifecycle, and guarded-success tests.
- [ ] Run focused tests and verify failures.
- [ ] Replace review samples with relations and functional Edit links.
- [ ] Implement transactional idempotent pending-verification submission and guarded success.
- [ ] Redirect legacy wizard and make resume resolve new full-page routes.
- [ ] Run focused tests and confirm pass.

### Task 8: Finance consistency and full verification

**Files:**
- Modify: `resources/views/owner/finance.blade.php`
- Modify: `tests/Unit/LandingPagePresentationTest.php`

**Interfaces:**
- Finance body uses `bg-[#ECECEC]`.

- [ ] Add a failing source regression test for the Finance background.
- [ ] Run the focused test and confirm failure.
- [ ] Change only the Finance page-shell background class.
- [ ] Run `php artisan test`, `npm run build`, and `git diff --check`.
- [ ] Inspect route middleware with `php artisan route:list -v` and confirm all wizard mutations are protected.
