# Manual iCal Synchronization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Deliver secure manual Airbnb and Booking.com iCal import/export for each owner property.

**Architecture:** Extend the existing external-calendar, sync-run, sync-item, and availability-block schema. Small services own URL safety, parsing, connection lifecycle, import reconciliation, and feed generation; controllers remain tenant-scoped adapters, while a queueable job and scheduler reuse the importer.

**Tech Stack:** Laravel 13, PHP 8.4, Eloquent, Laravel HTTP client/queues/scheduler, Blade/Alpine, PHPUnit, MySQL and SQLite.

**Spec:** `docs/superpowers/specs/2026-09-12-manual-ical-sync-design.md`

## Global Constraints

- Add migrations; do not edit completed migration history.
- Support Airbnb and Booking.com HTTPS calendar feeds only.
- Imported events create external availability blocks, never native bookings.
- Export native blocking bookings and owner manual blocks, never imported blocks.
- Keep all owner reads and writes scoped to the active business.
- Do not expose guest identity in exported feeds.
- Failed imports preserve existing availability blocks.

---

### Task 1: Calendar export persistence

**Files:**
- Create: `database/migrations/2026_09_12_000100_create_property_calendar_exports_table.php`
- Create: `app/Models/PropertyCalendarExport.php`
- Modify: `app/Models/Property.php`
- Test: `tests/Feature/Owner/ExternalCalendarSyncTest.php`

**Interfaces:**
- Produces one active encrypted/plain-plus-hashed export token per property and connection-event uniqueness.

- [ ] Write failing model/migration tests for encrypted token storage, property ownership, and token regeneration.
- [ ] Run focused tests and confirm missing table/model failures.
- [ ] Add the migration, model, and relationships.
- [ ] Run focused tests and confirm persistence passes.

### Task 2: Safe connection management

**Files:**
- Create: `app/Services/Calendar/ValidateExternalCalendarUrl.php`
- Create: `app/Services/Calendar/ManageExternalCalendarConnection.php`
- Create: `app/Http/Requests/Owner/StoreExternalCalendarConnectionRequest.php`
- Create: `app/Http/Controllers/Owner/OwnerExternalCalendarController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Owner/ExternalCalendarSyncTest.php`

**Interfaces:**
- Produces `save(Property, User, provider, url)`, `disable(...)`, and `regenerateExport(...)` tenant-safe operations.

- [ ] Write failing tests for provider/HTTPS/host validation, owner scoping, save, disable, and token rotation.
- [ ] Confirm failures are caused by missing routes/services.
- [ ] Implement request, URL validator, transactional manager, controller, and routes.
- [ ] Run focused tests until green.

### Task 3: iCalendar parser and safe fetcher

**Files:**
- Create: `app/Services/Calendar/IcalendarParser.php`
- Create: `app/Services/Calendar/FetchExternalCalendar.php`
- Test: `tests/Unit/Calendar/IcalendarParserTest.php`
- Test: `tests/Feature/Owner/ExternalCalendarSyncTest.php`

**Interfaces:**
- `IcalendarParser::parse(string): array` returns UID, start, end, cancelled.
- `FetchExternalCalendar::fetch(ExternalCalendarConnection): string` returns a bounded valid calendar body.

- [ ] Write failing folded-line/date/cancellation and HTTP failure tests.
- [ ] Confirm missing-class failures.
- [ ] Implement deterministic parsing and redirect-disabled bounded HTTP fetching.
- [ ] Run parser and fetch tests until green.

### Task 4: Transactional import reconciliation

**Files:**
- Create: `app/Services/Calendar/ImportExternalCalendar.php`
- Modify: `app/Http/Controllers/Owner/OwnerExternalCalendarController.php`
- Test: `tests/Feature/Owner/ExternalCalendarSyncTest.php`

**Interfaces:**
- `ImportExternalCalendar::sync(ExternalCalendarConnection, string $trigger, ?User $actor): ExternalCalendarSyncRun`.

- [ ] Write failing tests for create, repeat, date update, cancellation, disappearance, conflict, and failed-fetch preservation.
- [ ] Confirm imported blocks and sync history are absent.
- [ ] Implement fetch-before-transaction reconciliation and append-only run/items.
- [ ] Run focused tests until green.

### Task 5: Secure Verified Shortlet export feed

**Files:**
- Create: `app/Services/Calendar/BuildPropertyCalendarFeed.php`
- Create: `app/Http/Controllers/PropertyCalendarFeedController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Calendar/PropertyCalendarFeedTest.php`

**Interfaces:**
- Produces token-authenticated `text/calendar` containing native bookings and owner blocks with stable anonymous UIDs.

- [ ] Write failing authentication, privacy, date, inclusion, exclusion, and revocation tests.
- [ ] Confirm route/service failures.
- [ ] Implement feed builder and rate-limited public controller.
- [ ] Run focused tests until green.

### Task 6: Scheduled synchronization

**Files:**
- Create: `app/Jobs/SyncExternalCalendarConnection.php`
- Modify: `routes/console.php`
- Test: `tests/Feature/Console/SyncExternalCalendarsTest.php`

**Interfaces:**
- `calendars:sync` dispatches one unique job per active configured connection every 15 minutes.

- [ ] Write failing command/job selection tests.
- [ ] Confirm the command is absent.
- [ ] Implement the unique job, command, and scheduler registration.
- [ ] Run focused tests until green.

### Task 7: Owner setup and status UI

**Files:**
- Modify: `resources/views/property-add-channels.blade.php`
- Modify: `resources/views/owner/properties/show.blade.php`
- Modify: `resources/views/property-add-step9.blade.php`
- Modify: `app/Http/Controllers/Owner/PropertyWizardController.php`
- Test: `tests/Feature/Owner/ExternalCalendarSyncTest.php`

**Interfaces:**
- Owner can paste/save, sync now, disable, copy/regenerate export URL, and see safe status/history.

- [ ] Write failing presentation tests for controls, state, and full review visibility.
- [ ] Confirm UI assertions fail.
- [ ] Add the forms, status summaries, instructions, and property-detail entry point.
- [ ] Run focused tests until green.

### Task 8: Release verification

**Files:**
- Modify: `docs/admin-access.md` only if access instructions require a cross-link.
- Test: all test suites and rendered browser flow.

- [ ] Run focused calendar suites.
- [ ] Run the full SQLite suite.
- [ ] Run clean MySQL migration and focused MySQL calendar tests in an isolated database.
- [ ] Run Blade/route caches, Vite build, and `git diff --check`.
- [ ] Use Chrome at 1440×900 and 390×844 for paste/save, manual sync, imported lock, generated feed copy, and calendar rendering.
- [ ] Record limitations: provider polling interval is external and iCal does not sync pricing, guests, payments, or messages.
