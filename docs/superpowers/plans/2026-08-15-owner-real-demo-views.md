# Owner Real and Demo Views Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Provide clearly separated real and sample-data presentations for the owner dashboard and properties routes.

**Architecture:** Both canonical routes remain unchanged. Controllers select demo templates only when the explicit query value is `view=demo`; all other values render business-scoped database templates. A reusable switch component links between modes and demo screens display a persistent sample-data banner.

**Tech Stack:** Laravel 13, Blade, Tailwind CSS 4, Eloquent, MySQL.

## Global Constraints

- `/owner/dashboard` and `/owner/properties` default to real database data.
- `?view=demo` is presentation-only and must not change or seed database records.
- Demo screens must visibly say “Sample data”.
- Real screens must show an “Add your first property” action when the active business has no properties.
- Demo and real screens remain authenticated, verified, owner-authorized, and business-context protected.

---

### Task 1: Add explicit view selection

**Files:**
- Modify: `app/Http/Controllers/Owner/OwnerDashboardController.php`
- Modify: `app/Http/Controllers/Owner/OwnerPropertyController.php`
- Modify: `tests/Feature/Owner/FirstPropertyOnboardingTest.php`

**Interfaces:**
- Consumes: `Request::query('view')`.
- Produces: real view by default and demo view only for the exact string `demo`.

- [ ] Add tests asserting default real copy and `?view=demo` sample copy.
- [ ] Run the focused test to capture the pre-implementation result or the existing schema blocker.
- [ ] Implement strict controller selection without persisting presentation state.

### Task 2: Build reusable presentation controls and views

**Files:**
- Create: `resources/views/components/owner/view-switch.blade.php`
- Create: `resources/views/owner/demo/dashboard.blade.php`
- Create: `resources/views/owner/properties/index.blade.php`
- Modify: `resources/views/dashboard.blade.php`
- Modify: `resources/views/properties.blade.php`

**Interfaces:**
- Consumes: route name and current `view` query.
- Produces: visible Real view / Demo view control and sample-data warning.

- [ ] Add the switch to both real templates.
- [ ] Build the demo dashboard around the existing sample `partials.dashboard-content`.
- [ ] Add a sample-data banner and switch to the existing dummy properties screen.
- [ ] Build the real properties index from active-business property records, including the zero-property action.

### Task 3: Verify

**Files:**
- Verify all files above.

**Interfaces:**
- Consumes: Laravel routes, Blade, Vite.
- Produces: formatted and buildable owner presentations.

- [ ] Format controllers and tests with Pint.
- [ ] Confirm route registration and absence of new public demo routes.
- [ ] Build frontend assets.
- [ ] Run `git diff --check`.
