# Verified Shortlet Saturday MVP Release Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Produce a stable, demonstrable owner-workspace MVP release candidate by Saturday, 12 September 2026 at 18:00 WAT.

**Architecture:** Keep the migrated Project Nexus schema as the system of record and complete one coherent operational path: owner authentication → business context → property creation → booking creation → calendar availability → payment/expense reporting → operational task. Replace hard-coded owner-page arrays with business-scoped queries and small transaction services; defer external channel synchronization, AI generation, public guest checkout, and advanced accounting.

**Tech Stack:** PHP 8.3, Laravel 13, Blade, Alpine.js 3, Tailwind CSS 4, Vite 8, PHPUnit 12, MySQL 8+ for release migration verification, SQLite in-memory for fast feature feedback.

**Spec:** `docs/database/schema-guide.md`, `docs/database/schema-data-dictionary.md`, and `docs/superpowers/specs/2026-09-07-full-page-property-wizard-design.md`

## Global Constraints

- Do not rewrite the completed migration history; every schema correction is additive.
- Every owner query and mutation is scoped through the active `business_id`.
- MySQL 8+ is authoritative for migration, foreign-key, and index verification.
- Payment, finance, lifecycle, and audit history are append-only where the existing models require it.
- Channel records remain `pending`; Airbnb, Booking.com, WhatsApp, and calendar API synchronization are excluded.
- “Another flat in a property,” public marketplace checkout, AI-generated insights, staff invitations, and multiple bookable units are excluded.
- No hard-coded sample records may appear in the real owner workspace; demonstrations remain behind explicit preview links.
- Stop feature development Friday at 18:00 WAT. Saturday is release hardening only.

---

## Current baseline — Monday, 7 September at 13:25 WAT

### Working or substantially implemented

- Landing page, registration, login, email verification flow, password-reset backend, and business onboarding.
- Active-business and owner authorization middleware.
- Real owner dashboard property summary and real property index.
- Backend-powered ten-step property wizard with draft resume, amenities, media, assets, documents, pricing, pending channel references, review, and verification submission.
- Broad migrated domain schema covering 134 tables and 2,462 documented fields.
- Blade compilation and Vite production builds currently pass.
- Focused wizard/presentation suite passes; the full suite reaches 17 passing tests before the first failure.

### Release blockers and incomplete application surfaces

- `ForgotPasswordPageTest` fails because the forgot-password view is still the default Breeze design.
- `FirstPropertyOnboardingTest` retains an old redirect expectation for the retired property workflow.
- Bookings and Finance controllers return hard-coded arrays instead of tenant data.
- Calendar is a static view and is not driven by bookings or availability rows.
- Operations is a placeholder page.
- Real dashboard KPIs do not yet derive from bookings, finance, and tasks.
- Sidebar contains dead `#` links and static count badges.
- There is no tested end-to-end flow joining property, booking, payment, calendar, and operations.
- The new additive wizard migration has been exercised through SQLite tests but still needs a clean MySQL migration and rollback proof.

## MVP definition of done

By Saturday evening, an owner must be able to:

1. Register, authenticate, recover a password, and enter the correct business workspace.
2. Create and resume a property, upload its required content, and submit it for verification.
3. Create, edit, confirm, and cancel a direct/manual booking for one of their properties.
4. See that booking on a business-scoped calendar with overlap prevention.
5. Record a booking payment and a property expense and see accurate Finance totals.
6. Create and complete a property operational task.
7. See real property, booking, finance, and task summaries on the dashboard.
8. Complete the flow on desktop and mobile without broken routes, sample data leakage, console errors, or cross-business access.

## Delivery schedule

### Monday, 7 September — stabilize the foundation (13:30–19:00)

**Outcome:** The current branch is reviewable, migrations are safe, and the complete existing test suite is green before new domains are connected.

- [ ] **13:30–14:00 — Protect the current work.** Review `git diff`, separate the completed wizard/UI work from unrelated changes, run `git diff --check`, and create a named checkpoint commit without staging secrets or generated build output.
- [ ] **14:00–15:00 — Repair authentication presentation.** Add/adjust `tests/Feature/Auth/ForgotPasswordPageTest.php`, update `resources/views/auth/forgot-password.blade.php` to the approved Project Nexus auth shell, and prove the focused auth suite passes.
- [ ] **15:00–16:00 — Normalize property workflow tests and routes.** Update `tests/Feature/Owner/FirstPropertyOnboardingTest.php` to expect the property-scoped wizard, remove or redirect obsolete competing endpoints in `routes/web.php`, and ensure `PropertySetupWorkflow` never generates deleted route names.
- [ ] **16:00–17:00 — Verify the full wizard contract.** Add missing tests for document deletion, asset deselection, channel clearing, draft resume, cross-business mutations, and guarded success access around `PropertyWizardController`.
- [ ] **17:00–18:00 — Prove migrations on MySQL.** Against an explicitly named disposable test database, run `php artisan migrate:fresh`, `php artisan migrate:rollback`, and `php artisan migrate`; verify `2026_09_07_000100_extend_properties_for_full_page_wizard.php` without editing earlier migrations.
- [ ] **18:00–19:00 — Establish the release baseline.** Run the full PHPUnit suite, `php artisan view:cache`, `npm run build`, and route inspection. Record failures, command output, and accepted warnings in the release checklist.

**Monday gate:** zero known test failures; wizard happy path passes; clean MySQL migration/rollback/re-migration; no obsolete wizard route is reachable as a competing workflow.

### Tuesday, 8 September — real bookings (09:00–18:00)

**Outcome:** `/owner/bookings` is powered by the existing booking schema and supports the direct/manual booking lifecycle.

**Primary files:** `OwnerBookingsController.php`, new booking Form Requests and services under `app/Services/Booking`, `resources/views/owner/bookings.blade.php`, `Booking.php`, related booking models, `routes/web.php`, and `tests/Feature/Owner/BookingManagementTest.php`.

- [ ] **09:00–10:00 — Lock the booking contract with failing tests.** Cover business scoping, property selection, date validation, guest count, pricing/currency snapshot, unique reference generation, and cross-tenant rejection.
- [ ] **10:00–12:00 — Implement transactional booking creation.** Add `CreateBookingService::create(ActiveBusinessContext $context, User $actor, array $data): Booking`; create the booking, primary guest snapshot, initial status history, and availability claims in one transaction.
- [ ] **12:00–13:00 — Add overlap protection.** Lock relevant availability rows/ranges and reject a second blocking booking for the same property nights with a user-facing validation error.
- [ ] **14:00–15:30 — Replace hard-coded booking arrays.** Query the active business with property/guest relations and derive KPI counts from real states.
- [ ] **15:30–16:30 — Add lifecycle actions.** Implement edit, confirm, and cancel transitions with explicit allowed-from states and append booking status history.
- [ ] **16:30–17:30 — Connect the UI.** Make “New booking,” filters, booking rows, and detail state operate on real records; provide an honest empty state.
- [ ] **17:30–18:00 — Verify.** Run booking tests, full suite, Blade cache, Vite build, and manual desktop/mobile booking creation.

**Tuesday gate:** one real booking can be created and updated; overlaps are rejected; no hard-coded booking names or KPIs remain; another business cannot see or mutate it.

### Wednesday, 9 September — calendar and availability (09:00–18:00)

**Outcome:** `/owner/calendar` is a real availability view sourced from bookings and manual blocks.

**Primary files:** `OwnerCalendarController.php`, new `PropertyAvailabilityService`, calendar requests/controllers, `resources/views/calendar.blade.php`, availability models, `routes/web.php`, and `tests/Feature/Owner/AvailabilityCalendarTest.php`.

- [ ] **09:00–10:00 — Write failing calendar tests.** Cover business/property filters, booking projection, manual block creation/removal, date boundaries, and cross-business access.
- [ ] **10:00–12:00 — Build availability queries.** Return date-windowed booking and block data from the authoritative tables without creating a second booking source.
- [ ] **12:00–13:00 — Implement manual blocks.** Add validated create/delete actions using `property_availability_blocks`; reject blocks that conflict with blocking bookings.
- [ ] **14:00–16:00 — Connect calendar UI.** Replace static fixtures, render real property rows/events, preserve filters in the query string, and add loading/empty/error states.
- [ ] **16:00–17:00 — Reconcile booking transitions.** Ensure cancellation releases future availability and booking date edits update claims transactionally.
- [ ] **17:00–18:00 — Verify.** Test month boundaries, same-day checkout/check-in behavior, mobile horizontal scrolling, and full-suite regression.

**Wednesday gate:** bookings and blocks appear on the correct dates; conflicts cannot be created; cancellations release dates; tenant isolation passes.

### Thursday, 10 September — payments, expenses, and real Finance (09:00–18:00)

**Outcome:** `/owner/finance` reports actual business data and supports minimal payment and expense entry.

**Primary files:** `OwnerFinanceController.php`, payment/expense Form Requests and services, `resources/views/owner/finance.blade.php`, finance models, `routes/web.php`, and `tests/Feature/Owner/FinanceWorkspaceTest.php`.

- [ ] **09:00–10:00 — Write failing finance tests.** Cover payment/expense creation, fixed-precision amounts, active currency, business/property/booking ownership, KPI aggregation, and immutable history.
- [ ] **10:00–12:00 — Implement payment recording.** Add a transactional manual-payment service using the existing `payments` and financial transaction structures; prevent duplicate external/manual references.
- [ ] **12:00–13:00 — Implement expense recording.** Add a minimal property expense form with category, amount, date, description, and optional receipt.
- [ ] **14:00–15:30 — Replace hard-coded Finance data.** Calculate revenue, expenses, net, pending payments, and property breakdowns from scoped queries; populate transactions from actual records.
- [ ] **15:30–16:30 — Connect UI controls.** Make Add expense, search, type/date/property filters, and empty states functional. Keep cards white on the `slate-50` workspace canvas.
- [ ] **16:30–17:15 — Reconcile booking payment status.** Derive or update booking payment state consistently after successful payment recording.
- [ ] **17:15–18:00 — Verify.** Test decimal behavior, negative/zero rejection, cross-tenant references, totals, mobile table overflow, full suite, and build.

**Thursday gate:** a real payment and expense change Finance totals correctly; no controller fixture arrays remain; finance records preserve history and tenant boundaries.

### Friday, 11 September — operations and real dashboard (09:00–18:00)

**Outcome:** Owners can manage basic operational tasks, and the dashboard summarizes only real business records.

**Primary files:** `OwnerOperationsController.php`, task requests/services, `resources/views/owner/operations.blade.php`, `OwnerDashboardController.php`, `resources/views/dashboard.blade.php`, sidebar partials, `routes/web.php`, and owner workspace feature tests.

- [ ] **09:00–10:00 — Write failing task tests.** Cover create, assign optional employee, start, complete, due date/priority validation, append-only completion facts, and tenant isolation.
- [ ] **10:00–12:00 — Implement task lifecycle.** Add business/property-scoped task creation and explicit state transitions using `operational_tasks` and status history/evidence structures already present.
- [ ] **12:00–13:00 — Build the Operations UI.** Replace the placeholder with real task KPIs, filters, compact task cards/table, empty state, and create/update actions.
- [ ] **14:00–15:30 — Power the dashboard.** Derive property count/statuses, upcoming arrivals/departures, received revenue, unpaid bookings, and open/urgent task counts from real records.
- [ ] **15:30–16:15 — Finish navigation.** Replace in-scope sidebar `#` links with real routes, remove static badges, and mark out-of-scope destinations disabled or hidden rather than clickable.
- [ ] **16:15–17:00 — Integration scenario.** Automate owner → property → booking → payment → calendar → task → dashboard assertions in `tests/Feature/Owner/OwnerMvpJourneyTest.php`.
- [ ] **17:00–18:00 — Feature freeze.** Run full tests and build, review the release scope, commit the last feature changes, and move all enhancements to a post-MVP list.

**Friday gate:** Operations is no longer a placeholder; dashboard metrics are real; the primary journey passes; no new feature code after 18:00.

### Saturday, 12 September — hardening and release candidate (09:00–18:00)

**Outcome:** A reproducible, backed-up release candidate is ready by 18:00 WAT.

- [ ] **09:00–10:00 — Fresh environment rehearsal.** Starting from clean dependencies/config and an empty MySQL test database, run setup, migrations, seed essential roles/permissions/amenities, and build assets.
- [ ] **10:00–12:00 — Automated release gate.** Run full PHPUnit on MySQL, focused SQLite feedback suite, `php artisan view:cache`, `php artisan route:cache`, `php artisan config:cache`, `npm run build`, and `git diff --check`.
- [ ] **12:00–14:00 — Browser journey QA.** Test desktop 1440×900 and mobile 390×844: landing/register/login/password reset, business onboarding, property wizard, booking, calendar, payment, expense, task, dashboard, logout/login persistence, and Back/refresh behavior.
- [ ] **14:00–15:00 — Security and failure QA.** Test guest redirects, CSRF, validation messages, upload MIME/size limits, cross-business UUID access, duplicate submissions, booking overlaps, payment idempotency, and unauthorized lifecycle transitions.
- [ ] **15:00–16:00 — Data/recovery rehearsal.** Back up the target database, run migrations with `--pretend`, apply them, verify row counts/statuses, and document rollback/recovery steps. Do not use destructive production rollback.
- [ ] **16:00–17:00 — Fix release blockers only.** Accept only severity-one issues: data loss, broken primary flow, authorization leak, migration failure, 500 error, or unusable mobile layout. Defer cosmetic changes.
- [ ] **17:00–17:30 — Final smoke test.** Repeat the primary owner journey on the release build and verify logs contain no new application errors.
- [ ] **17:30–18:00 — Tag and handoff.** Create the release commit/tag, record migration/build/test outputs, known limitations, seed/admin access procedure, backup location, and post-release monitoring checklist.

**Saturday release gate:** all automated tests green on MySQL; primary desktop/mobile journeys pass; no critical/high security issue; migration and backup rehearsal succeed; known limitations are documented.

## Daily operating rhythm

- **09:00–09:15:** review previous gate, logs, and today’s acceptance tests.
- **09:15–12:00:** first test-driven implementation block.
- **12:00–13:00:** integrate and commit a working vertical slice.
- **14:00–16:30:** second implementation/UI block.
- **16:30–17:30:** focused tests, mobile check, and fixes.
- **17:30–18:00:** full regression, build, commit, and written handoff.
- Keep commits small enough to revert by capability: tests, service/domain, UI/routes, then integration.

## Required test matrix

| Area | Required evidence |
|---|---|
| Authentication | Register, login, logout, verify email, request/reset password |
| Tenancy | Cross-business reads and mutations return 404/403 |
| Property | Full wizard, resume, upload/remove, review, idempotent submit |
| Booking | Create/edit/confirm/cancel, status history, overlap rejection |
| Calendar | Booking projection, blocks, boundaries, cancellation release |
| Finance | Payment/expense, decimal totals, idempotency, immutable records |
| Operations | Task create/start/complete, priority/due date, tenant scoping |
| Integration | One automated end-to-end owner journey |
| Presentation | Blade cache, Vite build, desktop/mobile primary flows, no console errors |
| Database | MySQL fresh migration, rollback rehearsal, re-migration, indexes/FKs |

## Scope cuts if the schedule slips

Cut in this order without damaging the coherent MVP:

1. CSV/report export and saved filters.
2. Expense receipt upload.
3. Booking edit after confirmation; retain create/confirm/cancel.
4. Employee assignment on tasks; retain unassigned task lifecycle.
5. Manual calendar blocks UI; retain booking-driven read-only calendar.
6. Charts; retain accurate KPI cards and transaction tables.

Never cut tenant authorization, booking overlap protection, payment integrity, migration proof, mobile primary-flow QA, or backups.

## Explicitly post-MVP

- External Airbnb/Booking.com/WhatsApp/iCal/Google/Outlook synchronization.
- Guest marketplace search, wishlist, checkout, payment gateway, and reviews.
- Multiple independently bookable rooms/units and “Another flat in a property.”
- AI concierge, generated dashboard briefings, recommendations, and predictions.
- Advanced approvals, reconciliation, revenue recognition, forecasts, scheduled reports, subscriptions, and full accounting ledger.
- Staff invitation, role editor, granular permission administration, supplier/inventory UI, and automated cleaning workflows.

## Release commands

Run against the dedicated test environment first:

```bash
php artisan migrate:fresh --seed
php artisan test
php artisan view:cache
php artisan route:cache
php artisan config:cache
npm run build
git diff --check
```

For the target environment, back up first, then use non-destructive forward migration:

```bash
php artisan migrate --pretend
php artisan migrate --force
php artisan optimize
```

## Final release checklist

- [ ] Full test suite is green on MySQL 8+.
- [ ] Primary owner journey is green on desktop and mobile.
- [ ] No sample data appears in real workspace pages.
- [ ] No active in-scope navigation points to `#` or preview routes.
- [ ] No secrets, uploads, test databases, or generated temporary artifacts are committed.
- [ ] Production database backup exists and restoration steps are written down.
- [ ] Forward migrations and rollback strategy have been reviewed.
- [ ] Known limitations and deferred integrations are visible and honest.
- [ ] Release commit/tag and verification outputs are recorded before 18:00 WAT Saturday.
