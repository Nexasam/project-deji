# Platform Administration MVP Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a permission-enforced platform-administration workspace for businesses, users, properties, reviews, disputes, configuration, audit and operational health.

**Architecture:** Global platform roles are resolved through a dedicated permission service and route middleware. Focused transactional services own business, user, moderation and dispute state changes and record immutable audits; controllers remain request/view adapters. A shared Blade admin layout reuses the owner workspace design language across table-first modules.

**Tech Stack:** PHP 8.3+, Laravel 13, Eloquent/MySQL, Blade, Alpine.js, Tailwind CSS, PHPUnit 12.

**Spec:** `docs/superpowers/specs/2026-09-14-platform-administration-mvp-design.md`

## Completion status — 15 September 2026

All seven tasks in this plan are implemented. Final verification completed with:

- 36 platform-administration tests and 223 assertions passing against MySQL.
- 206 application tests and 1,253 assertions passing against MySQL.
- Blade view compilation and Laravel route caching passing.
- All 33 platform-administration routes registered successfully.
- Vite production asset build passing.
- Platform-administration PHP scope passing Pint.
- `git diff --check` passing.

The repository-wide Pint check still reports formatting debt in older files outside this platform-administration scope; those files were deliberately not mass-reformatted as part of this delivery.

## Global Constraints

- Preserve existing owner, guest, booking, calendar, finance and operations behaviour.
- No real payment/refund execution, subscriptions or impersonation.
- Every sensitive state change requires a reason and immutable audit event.
- Platform permissions are global and never depend on active business context.
- Suspended businesses cannot accept new bookings, but existing stays retain essential completion paths.
- Use the existing owner UI style; do not introduce a second design language or new frontend framework.
- Every behaviour change follows red-green-refactor TDD.

---

### Task 1: Platform permission foundation and admin shell

**Files:**
- Modify: `database/seeders/AccessControlSeeder.php`
- Create: `app/Services/Access/PlatformPermissionService.php`
- Create: `app/Http/Middleware/EnsurePlatformPermission.php`
- Modify: `app/Http/Middleware/EnsurePlatformAdmin.php`
- Modify: `app/Http/Controllers/Admin/AdminAuthenticatedSessionController.php`
- Modify: `bootstrap/app.php`
- Modify: `routes/web.php`
- Create: `resources/views/layouts/admin.blade.php`
- Create: `resources/views/partials/admin-sidebar.blade.php`
- Test: `tests/Feature/Admin/PlatformPermissionTest.php`

**Interfaces:**
- Produces: `PlatformPermissionService::allows(User $user, string $permission): bool`.
- Produces: route middleware alias `platform.permission:<permission-key>`.
- Produces: three seeded platform-role templates and least-privilege permission mappings.

- [ ] Write feature tests proving all three platform roles can authenticate but receive different allowed/forbidden routes, and super-admin self-protection remains possible.
- [ ] Run `DB_CONNECTION=sqlite DB_DATABASE=':memory:' php artisan test tests/Feature/Admin/PlatformPermissionTest.php` and confirm missing-role/middleware failures.
- [ ] Implement platform permission resolution, middleware, role seeds, authentication acceptance and the shared responsive admin shell.
- [ ] Run the focused test and existing `tests/Feature/Admin/PropertyPublishingTest.php` until green.
- [ ] Format changed PHP files with Pint.

### Task 2: Admin dashboard and property workspace integration

**Files:**
- Create: `app/Http/Controllers/Admin/AdminDashboardController.php`
- Create: `app/Services/Platform/AdminDashboardSummary.php`
- Modify: `app/Http/Controllers/Admin/AdminPropertyController.php`
- Modify: `app/Services/Property/PropertyPublishingService.php`
- Create: `resources/views/admin/dashboard.blade.php`
- Modify: `resources/views/admin/properties/index.blade.php`
- Modify: `resources/views/admin/properties/show.blade.php`
- Test: `tests/Feature/Admin/AdminDashboardTest.php`
- Modify test: `tests/Feature/Admin/PropertyPublishingTest.php`

**Interfaces:**
- Produces: `AdminDashboardSummary::for(User $admin): array` containing permission-filtered queue and health metrics.
- Produces: audited property publish, reject and unpublish transitions.

- [ ] Write tests for actionable dashboard counts and immutable property-decision audits.
- [ ] Run the new and existing property tests and confirm the missing dashboard/audit failures.
- [ ] Implement summary queries, routes, dashboard and property pages inside the shared layout.
- [ ] Run the focused tests until green and format PHP.

### Task 3: Business directory, verification and suspension enforcement

**Files:**
- Create: `app/Http/Controllers/Admin/AdminBusinessController.php`
- Create: `app/Services/Platform/ManageBusinessStatus.php`
- Create: `app/Http/Middleware/EnsureBusinessMutationAllowed.php`
- Modify: `app/Services/Marketplace/MarketplacePropertyQuery.php`
- Modify: `app/Services/Booking/CreateMarketplaceBooking.php`
- Modify: `bootstrap/app.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/businesses/index.blade.php`
- Create: `resources/views/admin/businesses/show.blade.php`
- Test: `tests/Feature/Admin/BusinessAdministrationTest.php`

**Interfaces:**
- Produces: `ManageBusinessStatus::{verify,reject,suspend,reactivate}(Business $business, User $actor, string $reason): Business`.
- Produces: `business.mutation` middleware for new supply and non-essential owner mutations.

- [ ] Write tests proving state transitions/audits, marketplace exclusion, direct checkout rejection, mutation blocking and essential existing-stay actions.
- [ ] Run the business test and confirm failures occur because routes/services are absent.
- [ ] Implement transactional status service, directory/detail UI and suspension enforcement.
- [ ] Run business, marketplace, booking-lifecycle and owner workspace tests until green.
- [ ] Format changed PHP files.

### Task 4: User directory and account security controls

**Files:**
- Create: `app/Http/Controllers/Admin/AdminUserController.php`
- Create: `app/Services/Platform/ManageUserSecurity.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/users/index.blade.php`
- Create: `resources/views/admin/users/show.blade.php`
- Test: `tests/Feature/Admin/UserSecurityAdministrationTest.php`

**Interfaces:**
- Produces: `ManageUserSecurity::lock(User $target, User $actor, string $reason): User`.
- Produces: `ManageUserSecurity::unlock(User $target, User $actor, string $reason): User`.

- [ ] Write tests for directory visibility, immediate database-session termination, authentication denial, unlock reset, self-lock rejection and platform-role protection.
- [ ] Run the user-security test and confirm absent-route/service failures.
- [ ] Implement security service, filtered directory/detail screens, confirmation actions and audit events.
- [ ] Run user-security and authentication tests until green.
- [ ] Format changed PHP files.

### Task 5: Review and owner-response moderation

**Files:**
- Create: `app/Http/Controllers/Admin/AdminReviewController.php`
- Create: `app/Services/Platform/ModerateMarketplaceContent.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/reviews/index.blade.php`
- Create: `resources/views/admin/reviews/show.blade.php`
- Test: `tests/Feature/Admin/ReviewModerationTest.php`

**Interfaces:**
- Produces: `ModerateMarketplaceContent::{hideReview,restoreReview,hideResponse,restoreResponse}` with typed review/response subjects, actor and mandatory reason.

- [ ] Write tests showing hidden content leaves marketplace output without deletion, restore republishes it, reasons are audited and unauthorised platform roles receive 403.
- [ ] Run the review-moderation test and confirm missing-route/service failures.
- [ ] Implement transactional moderation service, filters, case view and hide/restore modals.
- [ ] Run review moderation, verified-stay review and marketplace tests until green.
- [ ] Format changed PHP files.

### Task 6: Admin-created dispute case management

**Files:**
- Create: `database/migrations/2026_09_14_000200_add_platform_management_fields_to_booking_disputes.php`
- Modify: `app/Models/BookingDispute.php`
- Create: `app/Http/Controllers/Admin/AdminDisputeController.php`
- Create: `app/Services/Platform/ManageBookingDispute.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/disputes/index.blade.php`
- Create: `resources/views/admin/disputes/create.blade.php`
- Create: `resources/views/admin/disputes/show.blade.php`
- Test: `tests/Feature/Admin/DisputeManagementTest.php`

**Interfaces:**
- Produces: dispute fields `priority`, `assigned_to`, `due_at`, `approved_amount`.
- Produces: `ManageBookingDispute::create(Booking $booking, User $actor, array $data): BookingDispute` and controlled `assign`/`transition` methods.

- [ ] Write tests for admin-only creation from a booking, assignment, every allowed transition, invalid transition rollback, resolution data, audit timeline and absence of payment/refund mutation.
- [ ] Run the dispute test and confirm schema/route/service failures.
- [ ] Add the forward migration, model relations, transactional service, controllers and case-management views.
- [ ] Run migration tests plus dispute, booking and finance regressions until green.
- [ ] Format changed PHP files.

### Task 7: Configuration, audit history, demo data and release verification

**Files:**
- Create: `app/Http/Controllers/Admin/AdminPlatformSettingController.php`
- Create: `app/Http/Controllers/Admin/AdminAuditController.php`
- Modify: `app/Services/Platform/PlatformSettings.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/settings/index.blade.php`
- Create: `resources/views/admin/audit/index.blade.php`
- Modify: `database/seeders/PresentationDemoSeeder.php`
- Test: `tests/Feature/Admin/PlatformConfigurationTest.php`
- Test: `tests/Feature/Admin/PlatformAuditWorkspaceTest.php`
- Modify: `tests/Feature/Presentation/PresentationDemoSeederTest.php`
- Modify: `docs/release/2026-09-14-client-presentation-browser-runbook.md`

**Interfaces:**
- Produces: validated, audited platform setting updates.
- Produces: searchable immutable platform audit history.
- Produces: deterministic verification-admin, support-admin, hidden-review and open-dispute presentation records.

- [ ] Write tests for setting type/range validation, configuration permission, before/after audit values, audit filters and deterministic presentation records.
- [ ] Run the new tests and confirm missing controller/UI/demo-data failures.
- [ ] Implement configuration/audit controllers and views, complete demo seed data and presentation documentation.
- [ ] Run all admin tests, then the full SQLite suite.
- [ ] Run MySQL migrations and focused admin tests, Blade cache, route cache, Vite build, Pint and `git diff --check`.

## Self-review

- The plan covers every approved design module; subscriptions, real refunds and impersonation remain explicitly excluded.
- State-changing interfaces use consistent actor/reason arguments and audit events.
- Tests exercise public effects, database state and authorization rather than source text.
- No placeholder implementation steps or undefined later-task interfaces remain.
