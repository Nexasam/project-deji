# Project Nexus Workflow Schema Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extend the existing Project Nexus schema with normalized onboarding, property lifecycle, calendar, booking operations, inspection, maintenance, cancellation, finance, review, and event-workflow records plus complete Eloquent relationships.

**Architecture:** Existing foundational migrations are edited only for fields that belong to their original entities; new workflow entities are introduced in dependency-ordered migrations after the audit migration. Current state remains on principal tables, historical transitions live in append-only records, and `business_id` is repeated on tenant-owned tables for isolation and indexed queries.

**Tech Stack:** Laravel 13, PHP 8.4, Eloquent ORM, MySQL/MariaDB-compatible migrations, UUID primary and foreign keys.

## Global Constraints

- Work only on migrations, Eloquent models, casts, relationships, indexes, and lifecycle deletion safeguards.
- Do not create controllers, requests, services, listeners, jobs, UI, seeders, or tests in this phase.
- Do not run real migrations or alter a database; use `php artisan migrate --pretend --no-interaction` only.
- Use one property default nightly price and ISO currency; do not add advanced or seasonal rate-plan tables.
- Preserve all existing tables, fields, relationships, and historical-record safeguards unless this plan explicitly changes them.
- Use UUIDs for domain primary keys and domain foreign keys.
- Use fixed-precision `decimal(19, 4)` for money and `char(3)` for currency.
- Use `restrictOnDelete()` for historical parents, `nullOnDelete()` for optional actors, and cascades only for non-historical dependent configuration.
- Every tenant-owned entity must include and index `business_id`.
- Historical entities must reject physical deletion in their model `booted()` method.
- User explicitly deferred automated tests; verify with schema review, Pint, PHP lint, relationship audits, and migration preview.

---

### Task 1: Extend Business and Property Foundations

**Files:**
- Modify: `database/migrations/2026_08_05_000100_create_businesses_table.php`
- Modify: `database/migrations/2026_08_05_000200_create_properties_and_property_details_tables.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/Property.php`

**Interfaces:**
- Produces business onboarding/profile fields consumed by Task 2.
- Produces property pricing/publication/readiness fields consumed by Tasks 2–8.

- [ ] **Step 1: Extend the businesses table**

Add nullable `text description`, `string logo_disk(40)`, `string logo_path(2048)`, `string website_url(2048)`, `json social_links`, `string onboarding_status(40)` defaulting to `registered`, and nullable `timestamp onboarding_started_at`/`onboarding_completed_at`. Add indexes on `(onboarding_status, status)` and `(subscription_plan, status)`.

- [ ] **Step 2: Extend the properties table**

Add nullable `decimal default_nightly_price(19,4)`, nullable `char pricing_currency(3)`, `string publication_status(40)` default `draft`, `string readiness_status(40)` default `not_ready`, nullable completion/submission/verification/publication/archive timestamps, and nullable `verified_by`/`published_by` foreign UUIDs to users with `nullOnDelete()`. Add indexes on `(business_id, publication_status, status)`, `(business_id, readiness_status, status)`, and `(business_id, published_at)`.

- [ ] **Step 3: Update Business casts and fields**

Add the new attributes to `#[Fillable]`; cast `social_links` to array and onboarding timestamps to datetime. Add relationships later in the tasks that create their target models.

- [ ] **Step 4: Update Property casts, fields, and actors**

Add new attributes to `#[Fillable]`; cast price to `decimal:4` and lifecycle timestamps to datetime. Add `verifier()` and `publisher()` `BelongsTo` relationships using `verified_by` and `published_by`.

- [ ] **Step 5: Verify the foundation edits**

Run `vendor/bin/pint --test app/Models/Business.php app/Models/Property.php database/migrations/2026_08_05_000100_create_businesses_table.php database/migrations/2026_08_05_000200_create_properties_and_property_details_tables.php` and lint all four PHP files with `php -l`.

- [ ] **Step 6: Commit the foundation change**

```bash
git add app/Models/Business.php app/Models/Property.php database/migrations/2026_08_05_000100_create_businesses_table.php database/migrations/2026_08_05_000200_create_properties_and_property_details_tables.php
git commit -m "feat: extend business and property lifecycle schema"
```

### Task 2: Add Onboarding, Subscription, and Property Lifecycle History

**Files:**
- Create: `database/migrations/2026_08_05_000500_create_onboarding_and_property_lifecycle_tables.php`
- Create: `app/Models/BusinessOnboardingStep.php`
- Create: `app/Models/BusinessSubscription.php`
- Create: `app/Models/PropertyLifecycleEvent.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/User.php`

**Interfaces:**
- Consumes `businesses`, `properties`, and `users`.
- Produces immutable lifecycle history and subscription history.

- [ ] **Step 1: Create onboarding step records**

Create `business_onboarding_steps` with UUID `id`; restricted `business_id`; `step_key(80)`; unsigned `sequence`; `state(40)` default `pending`; nullable `completed_at`; nullable `completed_by` user; nullable JSON `metadata`; `status(40)`; nullable user audit foreign keys; timestamps. Add unique `(business_id, step_key)` and index `(business_id, state, sequence)`.

- [ ] **Step 2: Create subscription history**

Create `business_subscriptions` with UUID; restricted business; `plan_key(80)`; `state(40)`; nullable provider/provider subscription/customer references; nullable trial/start/current-period/cancel/end timestamps; nullable JSON metadata; status/audit/timestamps. Index business/state/current-period end and provider/provider subscription reference.

- [ ] **Step 3: Create append-only property lifecycle events**

Create `property_lifecycle_events` with UUID; restricted business/property; `event_type(80)`; nullable previous/new state, actor user, reason, JSON metadata; `occurred_at`; `status(40)` default `recorded`; nullable created_by; created_at only. Index `(business_id, property_id, occurred_at)` and `(business_id, event_type, occurred_at)`.

- [ ] **Step 4: Implement models and relationships**

Give all three models `HasUuids`, exact fillable/casts, and `BelongsTo` links. Prevent deletion of `PropertyLifecycleEvent`. Add `onboardingSteps()`, `subscriptions()`, and `propertyLifecycleEvents()` to Business; `lifecycleEvents()` to Property; and completion/actor relationships to User.

- [ ] **Step 5: Verify and commit**

Run Pint test and PHP lint for the touched files, then commit as `feat: add onboarding and property lifecycle history`.

### Task 3: Add Availability, Booking Channels, and Booking Transition History

**Files:**
- Create: `database/migrations/2026_08_05_000510_create_booking_calendar_and_history_tables.php`
- Create: `app/Models/PropertyAvailabilityBlock.php`
- Create: `app/Models/BookingChannelLink.php`
- Create: `app/Models/BookingStatusHistory.php`
- Create: `app/Models/BookingDateChange.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/Booking.php`

**Interfaces:**
- Consumes businesses, properties, bookings, and users.
- Produces the canonical calendar ledger and immutable booking history.

- [ ] **Step 1: Create property availability blocks**

Create UUID, restricted business/property, optional restricted booking, `block_type(40)`, `starts_on`, exclusive `ends_on`, `state(40)` default `active`, nullable source provider/reference, reason and release fields, status/audit/timestamps. Index `(business_id, property_id, state, starts_on, ends_on)`, booking/state, and provider/reference. Do not add soft deletes.

- [ ] **Step 2: Create booking channel links**

Create UUID, restricted business/booking, `channel(80)`, external booking/property/account references, JSON metadata, synchronization timestamp/state, status/audit/timestamps. Add unique `(channel, external_booking_reference)` and business/booking/status index.

- [ ] **Step 3: Create booking status and date history**

Create append-only `booking_status_history` with previous/new status, reason, source, actor, metadata, occurrence time; create append-only `booking_date_changes` with old/new dates, reason, actor, availability state, metadata and occurrence time. Both carry restricted business/booking and created_at-only audit data.

- [ ] **Step 4: Implement models and relationships**

All history/calendar models use UUIDs, exact casts and relationship methods. Availability blocks, status history and date changes reject deletion. Add `availabilityBlocks()`, `channelLinks()`, `statusHistory()`, and `dateChanges()` to Booking; relevant collections to Business and Property.

- [ ] **Step 5: Verify and commit**

Run Pint, PHP lint, and migration preview. Commit as `feat: add booking calendar and lifecycle history`.

### Task 4: Add Arrival, Departure, Interaction, Request, and Incident Records

**Files:**
- Create: `database/migrations/2026_08_05_000520_create_booking_stay_workflow_tables.php`
- Create: `app/Models/BookingCheckIn.php`
- Create: `app/Models/BookingCheckOut.php`
- Create: `app/Models/BookingInteraction.php`
- Create: `app/Models/GuestServiceRequest.php`
- Create: `app/Models/BookingIncident.php`
- Modify: `app/Models/Booking.php`
- Modify: `app/Models/Guest.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/OperationalTask.php`
- Modify: `app/Models/Employee.php`

**Interfaces:**
- Consumes bookings, guests, properties, operational tasks, employees, users, and documents.
- Produces the permanent stay history used by operations and finance.

- [ ] **Step 1: Create check-in and check-out records**

Create one-to-one UUID records with unique restricted `booking_id` and repeated restricted business/property. Check-in stores identity, balance, deposit and access states, access reference, checklist/blocking JSON, completion/cancellation timestamps, employee/user actors, status/audit/timestamps. Check-out stores actual departure, access-return state, room/damage/deposit states, released amount/currency, handover notes, employee/user actors, status/audit/timestamps.

- [ ] **Step 2: Create booking interactions**

Create append-only interaction records with restricted business/booking, optional guest/user/employee participant, interaction type, direction, channel, summary, content, metadata, `occurred_at`, status, created_by and created_at. Index booking timeline and participant timelines.

- [ ] **Step 3: Create guest service requests**

Create UUID with restricted business/property/booking/guest, optional operational task and assigned employee, request type, priority, description, resolution, lifecycle state/timestamps, status/audit/timestamps. Index guest/booking/state and assignee/state/created time.

- [ ] **Step 4: Create booking incidents**

Create UUID with restricted business/property/booking, optional reporter user/employee, operational task and document, incident type, severity, description, financial impact/currency, resolution, reported/resolved timestamps, state/status/audit/timestamps. The maintenance issue link is added in Task 6 after that table exists.

- [ ] **Step 5: Implement models and bidirectional relationships**

Use `HasOne` for Booking check-in/check-out and `HasMany` for interactions, requests, and incidents. Historical check-in/out/interactions/incidents reject deletion; active service requests use soft deletes only if cancellation history remains queryable.

- [ ] **Step 6: Verify and commit**

Run Pint, lint, and migration preview; commit as `feat: add booking stay workflow records`.

### Task 5: Normalize Task Checklists and Add Inspection Records

**Files:**
- Review only: `database/migrations/2026_08_05_000330_create_operational_tasks_table.php`
- Create: `database/migrations/2026_08_05_000530_create_task_evidence_and_inspection_tables.php`
- Create: `app/Models/OperationalTaskChecklistItem.php`
- Create: `app/Models/OperationalTaskEvidence.php`
- Create: `app/Models/Inspection.php`
- Create: `app/Models/InspectionItem.php`
- Modify: `app/Models/OperationalTask.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/Booking.php`
- Modify: `app/Models/Employee.php`

**Interfaces:**
- Consumes operational tasks and core property/booking/employee entities.
- Produces normalized cleaning evidence and inspection outcomes consumed by maintenance.

- [ ] **Step 1: Retain legacy JSON during transition**

Keep existing `checklist` and `attachments` JSON columns for compatibility, but document child tables as canonical for new records. Do not remove data-bearing columns before application migration exists.

- [ ] **Step 2: Create checklist and evidence child tables**

Checklist items carry restricted business/task, sequence, label/instructions, required/completed flags, completed employee/user/time, notes, status/audit/timestamps and a unique task/sequence. Evidence carries restricted business/task, evidence type, media type, disk/path/external URL, caption, uploader, captured time, metadata and status/audit/timestamps.

- [ ] **Step 3: Create inspections and items**

Inspections carry restricted business/property; optional restricted booking/task, inspector and preceding inspection; type, state, result, score, findings, recommendations, lifecycle/approval actors and timestamps, status/audit/timestamps. Items carry restricted business/inspection, optional corrective task, sequence/category/item, expected/observed values, result/severity, notes/evidence metadata, status/audit/timestamps.

- [ ] **Step 4: Implement models and relationships**

Add task `checklistItems()`, `evidence()`, `inspections()`; inspection parent/follow-ups/items/corrective relationships; property, booking and employee inspection collections. Completed inspection records and items reject deletion.

- [ ] **Step 5: Verify and commit**

Run Pint, lint, and migration preview; commit as `feat: add task evidence and inspections`.

### Task 6: Add Maintenance Issue Lifecycle

**Files:**
- Create: `database/migrations/2026_08_05_000540_create_maintenance_issues_table.php`
- Create: `app/Models/MaintenanceIssue.php`
- Modify: `app/Models/AssetMaintenanceRecord.php`
- Modify: `app/Models/Asset.php`
- Modify: `app/Models/Inspection.php`
- Modify: `app/Models/BookingIncident.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/Booking.php`
- Modify: `app/Models/OperationalTask.php`

**Interfaces:**
- Consumes inspections, tasks, assets, employees, bookings, and incidents.
- Produces a single issue lifecycle linked to asset service history.

- [ ] **Step 1: Create maintenance issues**

Create UUID with restricted business/property; optional restricted booking/asset/inspection/task; optional reporter user/employee, assignee and verifier; origin/category/priority/title/description/diagnosis/resolution; estimated/actual cost and currency; lifecycle state and reported/due/accepted/started/completed/verified/closed timestamps; status/audit/timestamps. Add tenant/property/state/due, assignee/state/due, booking/state, asset/state, and inspection indexes.

- [ ] **Step 2: Link asset maintenance and booking incidents**

After creating `maintenance_issues`, use `Schema::table` in this same `000540` migration to add nullable restricted `maintenance_issue_id` foreign keys and indexes to `asset_maintenance_records` and `booking_incidents`. In `down()`, drop both foreign keys and columns before dropping `maintenance_issues`. Do not modify migration `000400` to reference a table created later.

- [ ] **Step 3: Implement model graph**

Add all `BelongsTo`, `HasMany`, and optional `HasOne` relationships. Maintenance issues and asset service records reject deletion. Cast money/date fields precisely.

- [ ] **Step 4: Verify and commit**

Run Pint, lint, and migration preview; commit as `feat: add maintenance issue lifecycle`.

### Task 7: Add Cancellation, Expense, and Booking Financial Allocation Records

**Files:**
- Create: `database/migrations/2026_08_05_000550_create_cancellation_and_finance_tables.php`
- Create: `app/Models/CancellationPolicy.php`
- Create: `app/Models/BookingCancellation.php`
- Create: `app/Models/Expense.php`
- Create: `app/Models/BookingFinancialAllocation.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/Booking.php`
- Modify: `app/Models/Guest.php`
- Modify: `app/Models/Payment.php`
- Modify: `app/Models/Supplier.php`
- Modify: `app/Models/Employee.php`
- Modify: `app/Models/MaintenanceIssue.php`

**Interfaces:**
- Consumes payments and operational entities.
- Produces auditable cancellation evaluation and finance records.

- [ ] **Step 1: Create cancellation policies**

Create UUID with restricted business, optional restricted property, policy type/name/description, JSON rules, default flag, effective dates, status/audit/timestamps/soft delete. Index tenant/property/status/effective dates. Application logic later guarantees one effective default per scope because nullable unique keys are insufficient in MySQL.

- [ ] **Step 2: Create booking cancellations**

Create UUID with restricted business/booking; optional policy, requester user/guest, approver; requester type, reason, policy snapshot JSON, refundable base, calculated refund, cancellation fee, currency, processing state, request/approval/completion timestamps, status/audit/timestamps. Prevent deletion and index booking/state and business/requested time.

- [ ] **Step 3: Create expenses**

Create UUID with restricted business and optional restricted property/booking/task/maintenance issue/supplier/employee; category, description, amount/currency, payee, method/provider/reference, approval/payment state, incurred/due/paid dates, verifier/approver, status/audit/timestamps. Expenses are historical and reject deletion.

- [ ] **Step 4: Create booking financial allocations**

Create UUID with restricted business/booking, optional restricted payment/expense, allocation type, amount/currency, description, `recognized_at`, status/audit/timestamps. Prevent deletion and index booking/type/date, payment, expense, and tenant/date.

- [ ] **Step 5: Implement models and relationships**

Implement casts and all bidirectional collections. Keep refunds as Payment rows linked by `original_payment_id`; do not calculate or store profit directly.

- [ ] **Step 6: Verify and commit**

Run Pint, lint, and migration preview; commit as `feat: add cancellation and finance schema`.

### Task 8: Add Reviews and Responses

**Files:**
- Create: `database/migrations/2026_08_05_000560_create_reviews_and_responses_tables.php`
- Create: `app/Models/Review.php`
- Create: `app/Models/ReviewResponse.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/Booking.php`
- Modify: `app/Models/Guest.php`
- Modify: `app/Models/User.php`

**Interfaces:**
- Consumes completed booking/guest/property identities.
- Produces moderated verified-stay review history for the marketplace and future AI processing.

- [ ] **Step 1: Create reviews**

Create UUID with restricted business/property/booking/guest, optional user; overall rating plus nullable cleanliness/accuracy/location/value/communication ratings, title/content, verified-stay flag, moderation/publication state, nullable sentiment score/label/metadata, submitted/published timestamps, status/audit/timestamps. Add unique `(booking_id, guest_id)` and marketplace/property publication indexes.

- [ ] **Step 2: Create review responses**

Create UUID with restricted business/review, optional responder user, content, moderation/publication state and timestamps, status/audit/timestamps. Add review timeline and publication indexes.

- [ ] **Step 3: Implement models and relationships**

Use decimal rating/sentiment casts, date casts, and all parent/child links. Reviews and responses reject deletion. Verified-stay eligibility remains a later service rule.

- [ ] **Step 4: Verify and commit**

Run Pint, lint, and migration preview; commit as `feat: add verified stay reviews`.

### Task 9: Add Domain Event Outbox and Workflow Execution History

**Files:**
- Create: `database/migrations/2026_08_05_000570_create_domain_event_and_workflow_tables.php`
- Create: `app/Models/DomainEvent.php`
- Create: `app/Models/WorkflowRun.php`
- Create: `app/Models/WorkflowStep.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/Booking.php`
- Modify: `app/Models/User.php`

**Interfaces:**
- Consumes all aggregate UUIDs without introducing polymorphic foreign-key constraints.
- Produces an append-only outbox and idempotent workflow execution ledger.

- [ ] **Step 1: Create domain events**

Create UUID with optional restricted business/property/booking and nullable actor user; event name, aggregate type/UUID, JSON payload/metadata, correlation/causation UUIDs, `occurred_at`, publication state, attempts, nullable next-attempt/published/failed timestamps and error, status and created_at. Add aggregate timeline, business/event timeline, publication queue, correlation, and causation indexes.

- [ ] **Step 2: Create workflow runs**

Create UUID with restricted domain event, optional restricted business/property/booking; workflow key/version, unique idempotency key, state, JSON input/output, attempts, start/finish/next retry timestamps, error, status and timestamps. Index domain event/state and retry queue.

- [ ] **Step 3: Create workflow steps**

Create UUID with restricted workflow run; unsigned sequence; step key/handler key, state, JSON input/output, attempts, timing/retry/error fields, status and timestamps. Add unique `(workflow_run_id, sequence)` and run/state indexes.

- [ ] **Step 4: Implement immutable models and relationships**

DomainEvent, WorkflowRun, and WorkflowStep use UUIDs, exact JSON/date casts, relationship graph, and reject deletion because retry/history records must remain auditable.

- [ ] **Step 5: Verify and commit**

Run Pint, lint, and migration preview; commit as `feat: add domain event workflow ledger`.

### Task 10: Complete Relationship Audit and Schema Verification

**Files:**
- Modify only files found incomplete during the audit.
- Review: every file in `app/Models` and `database/migrations`.

**Interfaces:**
- Consumes all prior tasks.
- Produces a coherent migration graph that can be executed later with ordinary `php artisan migrate`.

- [ ] **Step 1: Audit all migration identifiers**

Run `rg -n "foreignId\\(|\\$table->id\\(" database/migrations`. Domain tables must have no integer identifiers; Laravel queue infrastructure may retain integer IDs.

- [ ] **Step 2: Audit tenant ownership and timestamps**

List every `Schema::create` declaration and confirm each tenant-owned table has restricted `business_id`, UUID primary key, appropriate status, timestamps, creator/updater fields where meaningful, and tenant-first indexes.

- [ ] **Step 3: Audit deletion semantics**

Confirm historical models reject physical deletion, optional actor foreign keys use `nullOnDelete()`, and every migration `down()` removes constraints/tables in reverse dependency order.

- [ ] **Step 4: Audit bidirectional relationships**

For every foreign key, confirm the child model has the matching `BelongsTo` and principal parents expose the useful `HasOne`/`HasMany` relationship. Confirm relationship foreign-key names match migration column names exactly.

- [ ] **Step 5: Format and lint**

Run:

```bash
vendor/bin/pint app/Models database/migrations
for file in $(rg --files app/Models database/migrations | rg '\.php$'); do php -l "$file" >/dev/null || exit 1; done
```

Expected: Pint completes successfully and every PHP file exits lint with status zero.

- [ ] **Step 6: Preview the complete migration graph**

Run:

```bash
php artisan migrate --pretend --no-interaction >/tmp/project-nexus-workflow-migrations.txt
```

Expected: exit status zero, every new `000500`–`000570` migration appears, and no missing-table, duplicate-column, or foreign-key ordering error is reported.

- [ ] **Step 7: Inspect final scope**

Run `git status --short` and `git diff --stat`. Confirm no controller, route, view, service, job, listener, seeder, or test file changed.

- [ ] **Step 8: Commit final audit corrections**

If the audit required corrections, commit only those files as `fix: align workflow schema relationships`. If no corrections were needed, do not create an empty commit.
