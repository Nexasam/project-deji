# Project Nexus Workflow Schema Design

## Scope

This design translates SRS sections 5.14 and 6.1–6.18 into a relational Laravel/MySQL schema. It extends the existing business, property, guest, booking, payment, operational-task, asset, document, notification, audit, and RBAC foundations. This phase covers migrations, Eloquent models, relationships, database indexes, and lifecycle safeguards only.

The implementation deliberately excludes controllers, services, UI, event listeners, external calendar adapters, AI algorithms, seeders, and automated tests. Those will be introduced in later phases. Property pricing initially consists of one default nightly price and currency; advanced rate plans are deferred.

## Design Principles

1. Current state remains directly queryable on the principal entity.
2. Lifecycle changes are preserved in immutable or soft-deleted historical records.
3. `audit_events` records who changed data; `domain_events` records business facts that occurred.
4. Every tenant-owned record carries `business_id` for isolation and efficient tenant queries.
5. Every domain entity uses a UUID, timestamps, status, and creator/updater references where meaningful.
6. Historical records use restrictive foreign keys and cannot be physically deleted.
7. Nullable actor and assignee relationships use `nullOnDelete()` so account removal does not destroy history.
8. Cascading deletion is reserved for dependent configuration records with no independent historical value.

## Business Onboarding

### Business extensions

The existing `businesses` table will gain profile and onboarding state required by the workflow:

- `description`
- `logo_disk` and `logo_path`
- `website_url`
- `social_links` JSON
- `onboarding_status`
- `onboarding_started_at`
- `onboarding_completed_at`

Email verification remains attached to the owner's user account. A business cannot advance beyond the verification step until the owner is verified. The service layer will enforce this rule later.

### Business onboarding steps

`business_onboarding_steps` records the durable progress of registration, email verification, profile completion, subscription selection, optional team invitation, and first-property creation. It stores step key, sequence, state, completion timestamp, completing user, metadata, status, and audit columns. A unique key on `(business_id, step_key)` prevents duplicate step identities.

### Business subscriptions

`business_subscriptions` preserves subscription history rather than relying solely on `businesses.subscription_plan`. It stores the plan key, subscription state, trial and billing dates, provider identifiers, cancellation data, and audit columns. The existing plan string can remain as a current-state cache during early development.

## Property Lifecycle and Pricing

### Property extensions

The existing `properties` table will gain:

- `default_nightly_price` as a fixed-precision decimal
- `pricing_currency` using an ISO currency code
- `publication_status`
- `readiness_status`
- `information_completed_at`
- `media_completed_at`
- `verification_submitted_at`
- `verified_at` and `verified_by`
- `published_at` and `published_by`
- `archived_at`

`status` continues to represent the record lifecycle. `verification_status`, `publication_status`, `readiness_status`, and `maintenance_status` describe independent operational concerns. Publication eligibility—address, GPS, photos, pricing, amenities, capacity, and house rules—will be enforced by application rules rather than nullable-column constraints.

### Property lifecycle events

`property_lifecycle_events` is append-only and records transitions such as information completed, media completed, verification submitted, approved, rejected, published, made ready, made unavailable, and archived. It stores the prior and new state, actor, reason, metadata, occurrence time, and standard ownership fields.

## Availability and Calendar

`property_availability_blocks` is the canonical availability ledger. Each row blocks a half-open interval `[starts_on, ends_on)` and identifies its source:

- booking hold
- confirmed booking
- maintenance closure
- owner/manual block
- external calendar

It relates to a business and property and may relate to a booking or external calendar connection. It stores source type/reference, block state, reason, release timestamp, and audit fields. Indexes support property/date/status availability searches.

MySQL cannot enforce non-overlapping date ranges with an ordinary constraint. The later booking service must check conflicts and create/release blocks within a database transaction while locking relevant active rows. Adjacent stays are permitted because departure dates are exclusive.

`booking_channel_links` maps a booking to Airbnb, Booking.com, marketplace, travel-agent, and future channel identifiers without placing provider-specific columns on bookings.

## Booking Lifecycle

### Booking history

`booking_status_history` records every booking-state transition, including previous state, new state, reason, actor, source, metadata, and occurrence time. It is append-only.

`booking_date_changes` preserves original and revised arrival/departure dates, reason, actor, availability-processing state, and occurrence time. The later date-change service must atomically update the booking and replace its availability block.

### Check-in and check-out

`booking_check_ins` stores the arrival control record: identity-verification state, outstanding-balance state, security-deposit state, access method, key/smart-lock reference, checklist, blocking issues, completion/cancellation timestamps, and completing employee/user.

`booking_check_outs` stores actual departure, access return, room condition, damage state, damage notes, deposit-release state/amount, handover notes, completion timestamp, and completing employee/user.

Each booking has at most one active check-in and one active check-out record. Historical correction metadata is retained rather than replacing the records silently.

### Stay interactions, requests, and incidents

`booking_interactions` forms the chronological booking history for messages, calls, extension requests, internal notes, support contacts, system actions, and other interactions. It stores direction, channel, participant, summary/content, metadata, occurrence time, and audit fields.

`guest_service_requests` captures guest-initiated cleaning, maintenance, support, extension, amenity, and other requests. It links to the booking, property, guest, optional resulting operational task, assignee, priority, request state, description, resolution, and lifecycle timestamps.

`booking_incidents` records damage, complaints, safety/security events, policy breaches, and operational issues. It links to the booking, property, reporter, optional task/maintenance issue/document, severity, financial impact, resolution, and lifecycle timestamps.

## Operational Tasks, Cleaning, and Inspection

The existing `operational_tasks` table remains the single work-assignment queue. Cleaning, inspection, maintenance, guest welcome, inventory, and preparation are task types rather than separate assignment systems.

`operational_task_checklist_items` normalizes checklist entries with sequence, instructions, required flag, completion state, completion actor/time, notes, and status.

`operational_task_evidence` stores before/after/general evidence as file references with media type, caption, uploader, capture time, metadata, and status. Documents that require versioning or permission control continue to use the existing document subsystem.

`inspections` records cleaning, readiness, inventory, damage, maintenance, and quality inspections. It links to a property, optional booking, operational task, inspector employee, and optional preceding inspection. It stores result, score, findings, recommendations, started/completed/approved timestamps, and approval actor.

`inspection_items` stores the individual checklist results, category, expected/observed values, result, severity, notes, evidence metadata, and optional corrective task. Rejected inspections can generate another cleaning task or a maintenance issue while retaining the failed inspection.

## Maintenance

`maintenance_issues` represents the complete issue workflow: reported, assigned, accepted, in progress, completed, verified, and closed. It belongs to a business and property and may reference a booking, asset, inspection, operational task, reporter user/employee, assignee, and verifier. It stores origin, category, priority, description, diagnosis, resolution, cost estimate/actual cost, currency, due date, and lifecycle timestamps.

Existing `asset_maintenance_records` remains the service history for a physical asset and will optionally reference a broader `maintenance_issue`. This avoids duplicating asset-specific warranty, supplier, technician, and service history concepts.

## Cancellation

`cancellation_policies` stores flexible, moderate, strict, and custom policies. A policy belongs to a business and may optionally be property-specific. It stores timing/refund rules as structured JSON, description, effective dates, default flag, and lifecycle/audit fields.

`booking_cancellations` records each cancellation evaluation. It links to the booking and applicable policy and stores an immutable policy snapshot, requester type/user/guest, reason, calculated refund, cancellation fee, currency, processing state, request/approval/completion timestamps, and approving actor.

Cancellation does not delete or archive away the booking record. It transitions the booking, releases active calendar blocks, adjusts forecasts, and creates refund payment transactions when required. These coordinated operations belong in a later transaction-safe service.

## Finance

Payments remain transfers of money and never directly represent profit.

`expenses` records business, property, booking, task, maintenance, supplier, and employee-related costs. It stores category, amount, currency, payee, provider/reference, incurred/due/paid dates, approval state, verification/payment actors, description, and audit fields.

`booking_financial_allocations` assigns revenue, refund, fee, commission, tax, cleaning charge, damage charge, expense, or adjustment amounts to a booking. It may reference a payment or expense. This creates an auditable basis for revenue and profit reporting without storing a misleading mutable profit value on bookings.

Refunds remain distinct payment rows linked through `original_payment_id`. Receipts and invoices should use the existing document/version system, with payment receipt paths retained only as a compatibility shortcut if needed.

## Reviews

`reviews` belongs to a business, property, booking, and guest, and optionally to the guest's user account. It stores rating dimensions, written review, verified-stay flag, moderation/publication state, sentiment placeholders, submission/publication timestamps, and audit fields. A unique booking/guest constraint prevents duplicate reviews for the same stay.

`review_responses` stores business responses separately with responder, content, moderation/publication state, timestamps, and audit fields. Reviews and responses are historical and cannot be physically deleted.

A verified-stay badge requires a completed or checked-out booking belonging to the reviewing guest. This cross-entity rule will be enforced by the application layer.

## Domain Events and Workflow Processing

`domain_events` is an append-only outbox/business-event table. It stores event name, aggregate type/UUID, business, property, booking, actor, payload, metadata, correlation/causation identifiers, occurrence time, publication state, attempt count, publication time, and failure details.

Examples include:

- `business.onboarding_completed`
- `property.published`
- `booking.confirmed`
- `payment.verified`
- `guest.checked_in`
- `inspection.rejected`
- `maintenance.closed`

`workflow_runs` records an automation instance triggered by one domain event. It stores workflow key/version, entity context, execution state, idempotency key, input/output, timing, retry, and error information.

`workflow_steps` records ordered actions within a workflow run, including handler key, execution state, input/output, attempt count, timing, retry timing, and error information.

Unique idempotency keys prevent duplicate automation when events are retried. Domain events express business facts; the existing immutable audit trail continues to express data/security accountability.

## User Duality and Tenant Isolation

A person may simultaneously be a marketplace guest and a member/owner of one or more businesses. This requires no user category switch: the user has a guest profile for marketplace activity and business memberships for operational access.

A host cannot book a property owned by a business in which that user has an active owner/host membership. This cannot be represented by a simple foreign key and will be enforced transactionally before booking creation. The check follows `guest.user_id -> business_memberships -> property.business_id`.

All tenant-facing queries must resolve the active business context and include `business_id`. Duplicate business keys on related operational tables intentionally support tenant filtering; application validation must ensure every referenced property, booking, guest, employee, task, and payment belongs to that same business.

## Model Relationships

The principal Eloquent relationships will include:

- Business has onboarding steps, subscriptions, availability blocks, policies, expenses, domain events, and workflow runs.
- Property has availability blocks, lifecycle events, inspections, maintenance issues, reviews, expenses, and cancellation policies.
- Booking has status history, date changes, channel links, availability blocks, check-in, check-out, interactions, service requests, incidents, inspections, cancellations, allocations, reviews, and domain events.
- Guest has bookings, requests, cancellations, and reviews.
- OperationalTask has checklist items, evidence, inspections, requests, incidents, and maintenance issues.
- Inspection has items, follow-up inspections, corrective tasks, and maintenance issues.
- MaintenanceIssue may have an asset maintenance record and related operational task.
- Payment and Expense have booking financial allocations.
- DomainEvent has workflow runs; WorkflowRun has ordered workflow steps.

Models for immutable records will reject deletion in `booted()`. Models representing recoverable current-state records will use soft deletion where appropriate.

## Indexing and Integrity

Indexes will prioritize:

- tenant plus lifecycle state
- property plus date range/state
- booking chronological history
- assigned employee plus task/issue state and due date
- workflow/event publication and retry queues
- external-provider identifiers
- financial reporting by business, booking, date, and category

Monetary columns use fixed-precision decimals and explicit currency codes. JSON is limited to flexible snapshots, checklists/evidence metadata, provider payloads, social links, and policy rules; relationships and searchable operational states remain normalized columns.

## Migration Strategy

Because this is a new project and the migrations have not been deployed, existing foundational migration files may be edited when the new requirement belongs naturally to those tables. New domain concepts receive migrations ordered after the current audit migration. Migration `down()` methods will drop tables in reverse dependency order.

The implementation will be checked using Laravel Pint, PHP syntax validation, and `php artisan migrate --pretend`. It will not execute migrations or automated tests during this schema-only phase unless the user later requests them.

## Deferred Work

The following are intentionally deferred:

- seasonal, weekend, occupancy, promotional, or channel-specific pricing
- external calendar connection credentials and provider-specific synchronization payloads
- cancellation/refund calculation services
- availability locking services
- workflow listeners and job handlers
- AI forecasts, recommendations, and sentiment processing
- marketplace ranking algorithms
- default seed data
- controllers, policies, validation requests, UI, and tests

