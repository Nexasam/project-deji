# Project Nexus Foundation Architecture and Data Design

**Status:** Proposed for stakeholder review

**Date:** 5 August 2026

**Architecture choice:** Option 1 — pragmatic modular Laravel monolith

**Primary source:** *PROJECT NEXUS (2).pdf*, 190-page Software Requirements Specification (SRS)

## 1. Purpose of this document

This document turns the Project Nexus SRS into an implementable technical foundation for Laravel 13 and MySQL. It defines the architecture, module boundaries, database model, migration sequence, security boundaries, lifecycle rules, domain events, integration approach, testing strategy, and phased delivery model.

The design intentionally covers the complete product direction in the SRS while limiting the first implementation release to Phase 1. Later capabilities receive extension points now, not premature full implementations. This follows the SRS rule that each phase must build on the previous phase without major architectural redesign.

This is a design document, not evidence that the features have already been implemented. No business migrations or feature code should be written until this document is reviewed and accepted.

## 2. Feasibility and coverage commitment

The system is feasible in Laravel 13 with Blade, Alpine.js, Axios, MySQL, Redis, queue workers, object storage, and well-defined external adapters. A mid-level Laravel developer can maintain Option 1 because it uses normal Laravel concepts: controllers, Form Requests, policies, Eloquent models, services/actions, events, listeners, jobs, migrations, factories, and feature tests.

The 98% requirement will be measured through a requirements traceability matrix, not by subjective completion. Every SRS requirement will receive:

- a stable requirement ID;
- its SRS chapter and PDF page reference;
- its owning module;
- target phase;
- implementation item;
- automated test or documented manual acceptance test;
- status: planned, implemented, verified, deferred with reason, or out of scope by approval.

Coverage is calculated as verified requirements divided by applicable requirements. A requirement cannot count as covered merely because a screen exists. It must satisfy its business rule and acceptance evidence.

## 3. Scope interpretation

### 3.1 What Phase 1 delivers

Phase 1 includes the business dashboard, properties and availability, bookings and guests, calendar, core finance, cleaning and maintenance operations, marketplace listings and booking requests, reviews, notifications, basic reports, and the initial AI briefing/recommendation experience specified in SRS Chapter 16.

### 3.2 What is designed but deferred

The schema and interfaces preserve future support for custom workflow builders, multi-branch organisations, mobile offline work, QR operations, advanced analytics, promotions and referrals, enterprise approvals, internationalisation, franchise structures, vendor marketplaces, insurance, smart locks, IoT, energy, loyalty, financing, recruitment, and the academy.

Deferred features will not be represented by empty user interfaces. They receive only the minimum stable identifiers, module seams, metadata, and integration abstractions needed to avoid destructive redesign.

### 3.3 Explicit non-goals for the foundation stage

- Microservices.
- Event sourcing as the primary persistence model.
- A repository class around every Eloquent model.
- A command bus for ordinary CRUD.
- Separate databases per business.
- Building a data warehouse before operational reporting proves the need.
- Allowing AI to make irreversible financial or operational decisions without policy and approval controls.

## 4. Architectural decision

Project Nexus will start as a modular monolith: one Laravel deployment and one operational MySQL database, with strict domain modules and asynchronous processing where required.

This is the best balance between the SRS's scale ambitions and the team's maintainability needs. It avoids distributed-system complexity while preserving extraction paths. Modules may communicate through public application services and domain events; they should not reach into another module's internal query or mutation logic.

### 4.1 Runtime components

| Component | Responsibility |
|---|---|
| Laravel web/API application | Blade UI, versioned API, authentication, authorisation, orchestration |
| MySQL 8+ | authoritative transactional data |
| Redis | cache, queues, distributed locks, rate limits, short-lived sessions |
| Queue workers | notifications, files, integrations, reporting, AI and projections |
| Scheduler | reminders, recurring tasks, retention, syncs, daily briefing |
| S3-compatible object storage | photos, videos, receipts, identity files and document versions |
| Search adapter | MySQL search initially; dedicated engine when measured scale requires it |
| Analytics adapter | operational read models initially; warehouse/export pipeline later |
| External adapters | payment, channel manager, calendar, messaging, identity and AI providers |

### 4.2 Application layers

1. **Presentation:** Blade/Alpine/Axios and `/api/v1` resources.
2. **Application:** use-case actions, transaction boundaries, authorisation orchestration.
3. **Domain:** entity rules, status transitions, value objects, policies and domain events.
4. **Infrastructure:** Eloquent, queues, storage, third-party clients, logging and cache.

Controllers remain thin. They validate and authorise input, invoke one application action, and return a web redirect/view or standard API resource. Business transitions belong in actions/services, not controllers, observers, or Blade templates.

## 5. Module map and dependency rules

| Module | Owns | May depend directly on |
|---|---|---|
| Identity & Access | users, sessions, MFA, memberships, roles, permissions | Shared Kernel, Business |
| Business | businesses, branches, settings, subscriptions | Identity, Shared Kernel |
| Property | properties, units, amenities, pricing, media, verification | Business, Identity |
| Guest | guest profiles, identities, preferences, deduplication | Business, Identity |
| Booking & Availability | bookings, stays, availability blocks, charges, cancellations | Property, Guest, Business |
| Calendar | unified event projection, recurrence, sync conflicts | Booking, Property, Operations |
| Finance | payments, refunds, invoices, expenses, ledger and reconciliation | Booking, Business, Identity |
| Operations | workflows, tasks, cleaning, inspection, maintenance, inventory | Property, Booking, Team |
| Team & Suppliers | employees, assignments, availability, suppliers | Identity, Business |
| Assets & Inventory | assets, maintenance schedules, stock movements | Property, Team |
| Documents & Media | files, versions, entity attachments | Business, Identity |
| Marketplace | public listings, requests, moderation, favourites | Property, Booking, Reviews |
| Reviews | review invitations, reviews, moderation and responses | Booking, Guest, Property |
| Notifications & Messaging | preferences, messages, deliveries, templates | Identity, Business |
| Integrations | connections, mappings, webhooks, sync jobs, idempotency | public service contracts only |
| AI | briefs, insights, predictions, recommendations, feedback | reporting/read APIs only |
| Reporting | KPI definitions, snapshots, exports and drill-down | read models from all modules |
| Audit & Governance | immutable audits, approvals, retention and legal actions | Shared Kernel |

Circular dependencies are prohibited. Cross-module consequences are handled through an application service or an event. For example, Booking confirms the booking and emits `BookingConfirmed`; Finance may create an invoice, Operations may schedule work, Calendar updates its projection, and Notifications queues messages.

## 6. Shared data conventions

### 6.1 Identifiers

- Every business entity uses an application-generated UUIDv7 primary key stored as `char(36)` during the first implementation.
- UUIDv7 is time-ordered, improves index locality over random UUIDv4, remains globally unique, and is easy for a mid-level Laravel team to inspect.
- Primary identifiers are immutable and never reused.
- Human references such as booking numbers and invoice numbers are separate, business-scoped fields.
- Public APIs expose UUIDs or public references, never guessed sequential identifiers.

Binary UUID storage may be adopted only after benchmarking and with transparent casts; it is not required for Phase 1.

### 6.2 Common entity columns

Core mutable entities use:

- `id` UUID primary key;
- `business_id` where tenant-owned;
- `status` as a controlled string backed by a PHP enum;
- `version` unsigned integer for optimistic concurrency where collaborative editing matters;
- `created_by` and `updated_by`, nullable for system-created records;
- `created_at`, `updated_at` in UTC;
- `deleted_at` only where soft deletion is legally and operationally valid.

Immutable financial and audit records are never soft-deleted. Reversals or corrective entries are appended.

### 6.3 Money, dates and time zones

- Monetary amounts use `decimal(19,4)` and never floating point.
- Currency uses ISO 4217 `char(3)` on every monetary document or immutable snapshot.
- The business has a default currency and time zone; transactions retain their original currency.
- Timestamps are stored in UTC. Local dates/times retain the applicable IANA timezone when business meaning depends on local time.
- Booking arrival/departure uses local `date` fields plus scheduled check-in/out times and property timezone.
- Exchange rates, where later required, are immutable snapshots attached to the transaction.

### 6.4 Enums and extensibility

Stable lifecycle states use PHP string enums plus validation and transition maps. MySQL native `ENUM` is avoided so new states can be deployed without table-altering migrations. User-configurable categories use reference tables.

JSON is allowed for provider payloads, presentation metadata, configurable rules, and snapshots. It must not hide query-critical relationships or monetary totals.

### 6.5 Indexing baseline

Every tenant-owned high-volume table begins composite indexes with `business_id`. Common patterns include:

- `(business_id, status, created_at)`;
- `(business_id, property_id, status)`;
- `(business_id, scheduled_for)`;
- unique `(business_id, reference)`;
- integration lookup `(provider, external_id)`.

Indexes are justified by actual query paths and verified with `EXPLAIN`; redundant single-column indexes are removed.

## 7. Multi-tenancy and access isolation

Project Nexus uses a shared-schema tenancy model. A business is the security tenant. A user may belong to multiple businesses and select an active business context.

Tenant isolation is enforced in five places:

1. authenticated business context middleware;
2. business-scoped route binding;
3. policies checking membership, role, property assignment and workflow state;
4. application queries requiring the resolved business ID;
5. automated cross-tenant isolation tests.

Global scopes alone are insufficient because console jobs and administrators can bypass them accidentally. Mutation actions accept a resolved business context explicitly. Cross-tenant foreign keys are prevented through service validation and, for sensitive joins, composite unique/foreign-key patterns where practical.

### 7.1 Identity and access tables

| Table | Purpose and important fields |
|---|---|
| `users` | identity, name, email, phone, password hash, locale, timezone, status, email/phone verification, last login |
| `user_identities` | Google, Microsoft, Apple provider subject IDs; unique provider/subject |
| `user_mfa_methods` | encrypted TOTP/recovery configuration and confirmed timestamp |
| `businesses` | legal/operational organisation, registration, tax, contact, currency, timezone, type, verification/status |
| `business_memberships` | business/user relationship, job title, employment status, joined/left dates, active flag |
| `roles` | system or business-defined role; tenant nullable for platform roles |
| `permissions` | centrally defined capability names such as `booking.confirm` |
| `role_permissions` | role-to-permission mapping |
| `membership_roles` | membership-to-role mapping |
| `property_membership_assignments` | optional property scope for a membership |
| `membership_permission_overrides` | explicit time-bounded allow/deny exceptions, carefully audited |
| `impersonation_sessions` | platform admin, target, reason, approval, start/end, IP; always audited |
| `branches` | Phase 2/3 organisation subdivision without changing tenant ownership |
| `business_settings` | typed settings with namespace/key, encrypted flag and validation schema version |

Role names are defaults, not hardcoded access decisions. The SRS role matrix becomes seeded roles and permissions that each business can clone/customise within protected limits.

## 8. Detailed schema by domain

The tables below describe the authoritative design. “Phase 1” means create and use now. “Foundation” means create only if required by a Phase 1 relationship. “Later” means documented but not migrated until its phase.

### 8.1 Property domain

| Table | Phase | Key design |
|---|---:|---|
| `properties` | 1 | business, branch nullable, stable code/name/type, address fields, coordinates, timezone, capacity, bedroom/bathroom counts, description, owner/manager membership, lifecycle and verification status |
| `property_units` | 1 | bookable unit; permits a single apartment and multi-unit hotel under one model |
| `property_addresses` | 1 | structured address and geo data; one current primary address |
| `amenities` | 1 | platform catalogue |
| `property_amenity` | 1 | amenity value/details per property or unit |
| `property_house_rules` | 1 | typed rule, description, active order |
| `property_pricing_rules` | 1 | base/nightly rate, date range, days, occupancy adjustment, priority, currency |
| `property_check_rules` | 1 | check-in/out windows, minimum/maximum stay, notice and turnover constraints |
| `property_verifications` | 1 | submission, checklist result, reviewer, decision, reason, timestamps |
| `property_assignments` | 1 | manager/employee/supplier responsibility and effective dates |
| `property_status_history` | 1 | append-only status transitions with actor/reason |
| `property_health_snapshots` | 1 | dated explainable score and component values; derived, not source of truth |

Properties with historical activity are archived, not deleted. A unit is the conflict boundary for availability; for a single-unit property one default unit is created automatically.

### 8.2 Guest domain

| Table | Phase | Key design |
|---|---:|---|
| `guests` | 1 | business-owned long-term guest profile, names, email, phone, nationality, language, status and privacy state |
| `guest_contacts` | 1 | multiple email/phone/address contacts with verification and primary flags |
| `guest_identities` | 1 | identity type, country, masked number, verification state; sensitive file kept in Documents |
| `guest_emergency_contacts` | 1 | name, relationship, phone |
| `guest_preferences` | 1 | operational preferences and consent-safe notes |
| `guest_consents` | 1 | purpose, channel, granted/revoked timestamps and evidence |
| `guest_merge_candidates` | 1 | duplicate score, reasons, resolution; never auto-merge without safe rules |
| `guest_merges` | 1 | immutable source-to-survivor history |
| `guest_notes` | 1 | internal notes with visibility and author |
| `guest_loyalty_accounts` | Later | programme and points balance derived from entries |

Guests are tenant-owned because notes, consents, risk classifications and history differ by business. A future privacy-safe platform identity can link profiles without exposing one business's data to another.

### 8.3 Booking and availability domain

| Table | Phase | Key design |
|---|---:|---|
| `bookings` | 1 | business/property/unit, primary guest, reference, source, arrival/departure dates, guest counts, status, payment status, currency, totals snapshot, special requests, source metadata, version |
| `booking_guests` | 1 | all occupants, lead-guest flag and stay role |
| `booking_charges` | 1 | immutable priced line snapshots: accommodation, cleaning, tax, discount, damage, late fee, extras |
| `booking_status_history` | 1 | append-only validated transitions and reasons |
| `booking_date_changes` | 1 | original/new dates, repricing, actor and approval |
| `booking_cancellations` | 1 | policy snapshot, initiator, reason, fee and refundable amount |
| `booking_extensions` | 1 | requested dates, price delta and approval state |
| `booking_sources` | 1 | configurable source catalogue, including marketplace, channel, phone and manual |
| `rate_plans` | 1 | property/unit rate identity, cancellation/deposit policies and active dates |
| `availability_blocks` | 1 | unit, half-open date interval `[start, end)`, type, source entity, status, reason |
| `availability_daily` | 1 | per-unit/date read model for fast calendar/search, availability state and source |
| `booking_holds` | 1 | expiring reservation holds during checkout/payment |
| `special_requests` | 1 | structured booking requests with fulfilment state |

Authoritative occupancy comes from confirmed booking intervals and active blocks; `availability_daily` is a rebuildable projection. Search can read the projection, but confirmation must recheck the authoritative intervals inside a transaction.

#### Preventing double bookings

MySQL has no general exclusion constraint for overlapping date ranges. Booking confirmation therefore:

1. validates `departure_date > arrival_date`;
2. acquires a short distributed lock for the unit;
3. starts a database transaction and locks relevant unit/availability rows;
4. queries overlapping confirmed/reserved/checked-in intervals using `start < requested_end AND end > requested_start`;
5. expires stale holds;
6. confirms only if no conflict exists;
7. writes status history and commits;
8. updates projections asynchronously and synchronously invalidates availability cache.

An idempotency key prevents duplicate checkout submissions. Concurrency feature tests must attempt simultaneous confirmation for the same unit and dates and prove only one succeeds.

### 8.4 Calendar domain

| Table | Phase | Key design |
|---|---:|---|
| `calendar_events` | 1 | business/property, source type/id, event type, start/end, all-day, status, visibility, assignee; rebuildable projection where sourced elsewhere |
| `calendar_recurrence_rules` | 1 | RFC-like recurrence configuration, timezone and end condition |
| `calendar_occurrence_exceptions` | 1 | cancelled or changed recurrence occurrences |
| `calendar_conflicts` | 1 | conflicting entities, type, severity, detected/resolved details |
| `external_calendars` | 1 | provider connection/calendar IDs and direction |
| `calendar_sync_events` | 1 | import/export attempt, cursor, result and error |

Bookings and operational tasks remain authoritative in their modules. The calendar is a unified operational projection and never creates duplicate booking truth.

### 8.5 Finance domain

| Table | Phase | Key design |
|---|---:|---|
| `payments` | 1 | payer/booking, direction, amount/currency, method/provider, reference, status, paid/verified timestamps, verifier, idempotency key; immutable after posting except controlled status transition |
| `payment_attempts` | 1 | each gateway/cash verification attempt and sanitised provider response |
| `payment_allocations` | 1 | allocate a payment across invoices/charges |
| `refunds` | 1 | original payment, amount, reason, provider reference, approval and lifecycle |
| `invoices` | 1 | business/booking/guest, unique number, issue/due dates, currency, subtotal/tax/discount/total/balance snapshots and status |
| `invoice_lines` | 1 | description, quantity, unit price, tax, discount and line total |
| `credit_notes` | 1 | corrective document linked to invoice/refund |
| `expenses` | 1 | property/cost centre/category/supplier, amount/currency, date, recurrence, tax and approval state |
| `expense_categories` | 1 | configurable categories seeded from SRS |
| `cost_centres` | 1 | business/property/branch reporting dimension |
| `suppliers` | 1 | business supplier profile and payment details stored encrypted/tokenised |
| `financial_accounts` | 1 | chart of accounts |
| `journal_entries` | 1 | immutable posted header, source entity and posting date |
| `journal_lines` | 1 | account, debit or credit, currency and dimensions; each journal balances |
| `reconciliations` | 1 | statement period, account, status and reconciler |
| `reconciliation_items` | 1 | external/internal match state |
| `financial_adjustments` | 1 | approved corrective request producing journal entries |
| `tax_rates` | 1 | jurisdiction, rate and effective dates |
| `commission_rules` | Foundation | source/property rule and effective dates |
| `settlements` | Foundation | provider/channel settlement and fees |

Bookings describe a commercial stay; payments describe movement of money. They are never the same entity. Refunds are separate records and generate reversing ledger entries. Posted payments, refunds and journals cannot be deleted or overwritten. Corrections append a reversal and replacement.

The general ledger uses double entry. For every posted `journal_entry`, total debits must equal total credits in the entry currency. Posting occurs in one database transaction and is idempotent by source type/source ID/event key.

### 8.6 Operations, workflow and team domain

| Table | Phase | Key design |
|---|---:|---|
| `employees` | 1 | membership-linked employment profile, department, dates, status and emergency details |
| `employee_availability` | 1 | available/unavailable time intervals and reason |
| `employee_skills` | 1 | skill catalogue assignment |
| `workflow_templates` | 1 | versioned predefined workflow identity, trigger and active state |
| `workflow_template_steps` | 1 | ordered action/manual step, SLA, required capability and rule metadata |
| `workflow_instances` | 1 | template version snapshot, source entity, started/completed status |
| `workflow_step_instances` | 1 | actual step state, assignee, due time and result |
| `tasks` | 1 | property/booking/workflow, type, priority, status, assignee, due/completed times, recurrence and version |
| `task_checklist_items` | 1 | ordered requirement, completion and evidence requirement |
| `task_assignments` | 1 | assignment/reassignment history |
| `task_evidence` | 1 | file link, evidence type, captured by/time and geo metadata where consented |
| `cleaning_jobs` | 1 | task extension with cleaning type, before/after condition and readiness outcome |
| `inspections` | 1 | property/booking/task, inspector, type, score, outcome and verification |
| `inspection_items` | 1 | checklist result, condition, notes and evidence |
| `maintenance_requests` | 1 | source, category, severity, description, SLA and lifecycle |
| `maintenance_work_logs` | 1 | technician, action, time, parts and cost |
| `incidents` | 1 | safety/damage/guest/operational incident, severity and resolution |
| `approval_definitions` | 1 | business action, threshold/rules and level configuration |
| `approval_requests` | 1 | action snapshot, requester, amount, state and expiry |
| `approval_steps` | 1 | ordered approver role/user, decision, reason and timestamps |
| `delegations` | 2 | approver delegation and effective period |

Standard workflow templates from SRS Appendix 17.3 are seed data. Templates are versioned; changing a template does not mutate running instances. Phase 1 supports predefined workflows and limited configuration. The visual custom builder remains Phase 2.

### 8.7 Assets and inventory

| Table | Phase | Key design |
|---|---:|---|
| `asset_categories` | 1 | business category and maintenance defaults |
| `assets` | 1 | property, serial, purchase/warranty data, condition, value, supplier and lifecycle |
| `asset_condition_history` | 1 | append-only inspection/condition changes |
| `asset_maintenance_schedules` | 1 | frequency, next due date and workflow template |
| `inventory_items` | 1 | property/store, SKU, unit, reorder threshold and active status |
| `inventory_locations` | 1 | business/property stock location |
| `inventory_movements` | 1 | immutable receipt, issue, transfer, adjustment or write-off |
| `inventory_counts` | 1 | stocktake header and approval |
| `inventory_count_lines` | 1 | expected/actual quantities and variance |

Inventory balance is derived from immutable movements, not directly edited. Write-offs above configured thresholds require approval.

### 8.8 Documents and media

| Table | Phase | Key design |
|---|---:|---|
| `files` | 1 | storage disk/key, original name, MIME, size, checksum, scan status, uploader and sensitivity |
| `documents` | 1 | business, document type, owner entity, title, status, expiry and retention class |
| `document_versions` | 1 | document/file, version number, notes and creator; immutable |
| `media_assets` | 1 | file, media type, alt text, processing status and dimensions/duration |
| `entity_media` | 1 | explicit polymorphic presentation link, sort order and primary flag |
| `document_access_logs` | 1 | sensitive document view/download record |

Uploads use private object storage by default and temporary signed URLs. Malware scanning and MIME/content validation happen before a file becomes available. Deleting a logical document does not immediately destroy a version under legal retention.

### 8.9 Marketplace and reviews

| Table | Phase | Key design |
|---|---:|---|
| `marketplace_listings` | 1 | verified property projection, title, slug, publication state, summary, policies and ranking fields |
| `listing_units` | 1 | public unit/rate-plan mapping |
| `listing_publication_history` | 1 | moderation/publication action and reason |
| `booking_requests` | 1 | guest, listing/unit, dates, quoted snapshot, expiry and conversion booking |
| `favourites` | 1 | authenticated public user/listing unique relation |
| `reviews` | 1 | verified booking, guest, property, scores, content, status and sentiment metadata |
| `review_invitations` | 1 | booking, token hash, expiry and use time |
| `review_responses` | 1 | business response, moderator state |
| `review_moderation_actions` | 1 | actor/action/reason history |

Only verified/published properties with required media and availability are publicly discoverable. “Verified stay” requires a completed booking connected to the reviewer. Ranking is a transparent projection from approved signals; businesses cannot write their own ranking values.

### 8.10 Notifications, communication and integrations

| Table | Phase | Key design |
|---|---:|---|
| `notification_preferences` | 1 | membership/category/channel enablement and quiet hours |
| `notification_templates` | 1 | versioned event/channel/locale template |
| `notifications` | 1 | recipient, category, title/body/data, read/archived state |
| `notification_deliveries` | 1 | channel/provider, attempt, status, response code and timestamps |
| `conversations` | 1 | business, guest/booking context and visibility |
| `messages` | 1 | conversation, sender, channel, direction, content, internal flag and external ID |
| `integration_connections` | 1 | business/provider, scopes, encrypted credentials, status and health |
| `external_entity_mappings` | 1 | provider/external ID to internal type/ID; unique per connection |
| `integration_sync_runs` | 1 | cursor, start/end, result, counts and error summary |
| `webhook_inbox` | 1 | provider event ID, signature state, payload, processing status; deduplicated |
| `webhook_outbox` | 1 | destination, event, payload, attempts, next attempt and terminal status |
| `idempotency_keys` | 1 | business/client/key, request hash, response/status and expiry |
| `integration_conflicts` | 1 | conflicting values, policy, resolution and actor |

Inbound webhooks are authenticated, stored once, acknowledged quickly, and processed by queue. Outbound side effects use a transactional outbox so database commits cannot be lost between persistence and queue publication. Secrets are encrypted and never included in logs or audit value snapshots.

### 8.11 AI and reporting

| Table | Phase | Key design |
|---|---:|---|
| `ai_briefs` | 1 | business/date, generated summary, evidence links, model run and status |
| `ai_insights` | 1 | subject entity, category, observation, severity, confidence and evidence |
| `ai_recommendations` | 1 | recommended action, rationale, confidence, impact estimate, approval need, expiry and lifecycle |
| `ai_recommendation_feedback` | 1 | accept/dismiss/rating/reason and actor |
| `ai_conversations` | 1 | membership/business context and title |
| `ai_messages` | 1 | role, redacted content, citations/evidence and model run |
| `ai_model_runs` | 1 | provider/model/prompt version, latency, token/cost metadata, safety result and trace ID |
| `automation_policies` | 1 | allowed action, thresholds, approval and active state |
| `kpi_definitions` | 1 | stable formula version, dimensions and drill-down resolver |
| `daily_business_metrics` | 1 | rebuildable daily KPI snapshot |
| `daily_property_metrics` | 1 | rebuildable property KPI snapshot |
| `report_exports` | 1 | report/filter snapshot, storage file and lifecycle |
| `knowledge_documents` | Later | approved AI knowledge source and indexing state |

AI output is advisory by default. Every insight links to supporting data where practical, observes the requesting user's permissions, records the model/prompt version, and accepts feedback. An AI recommendation never modifies a booking, payment, refund, price or task unless an explicit automation policy permits it; consequential actions pass through the same authorisation and approval services as humans.

Reporting snapshots are disposable read models. Users can drill from each KPI to authoritative records. Definitions are versioned so historic reports remain explainable when formulas change.

### 8.12 Audit, governance and retention

| Table | Phase | Key design |
|---|---:|---|
| `audit_events` | 1 | immutable tenant, actor/system, action, entity, before/after redacted JSON, source, IP/device, correlation and timestamp |
| `activity_events` | 1 | user-friendly entity timeline projection sourced from events/audits |
| `data_retention_policies` | 1 | business/data class, period and legal basis override |
| `legal_holds` | Foundation | entity/scope, reason, effective/released dates |
| `privacy_requests` | 1 | access/export/correction/erasure request and verified workflow |
| `data_export_jobs` | 1 | privacy/report export status and private file |

Audit rows are append-only at the application privilege level. Sensitive credentials, complete payment data, MFA secrets and raw identity numbers are never copied into audit JSON. Default retention follows the SRS: bookings and activity permanent, financial records at least seven years subject to jurisdiction, audit permanent, notifications 12 months, AI recommendations 24 months, and messages/documents configurable within legal constraints.

Erasure does not destroy protected booking, finance or audit evidence. It anonymises eligible personal fields, removes unnecessary files, and records the legal decision.

## 9. Lifecycle state machines

Status transitions are explicit actions, not arbitrary updates.

### 9.1 Booking

Primary path: `enquiry -> reserved -> awaiting_payment -> confirmed -> checked_in -> checked_out -> completed`.

Alternative terminal/branch states: `cancelled`, `no_show`, `refunded`; `extended` is recorded as an extension event and returns to the appropriate active stay state rather than becoming an ambiguous permanent status.

### 9.2 Property

`draft -> pending_verification -> verified -> published -> unavailable -> archived`, with documented return paths from unavailable and rejected verification.

### 9.3 Payment and refund

Payment: `initiated -> pending -> successful -> verified -> reconciled`; alternatives `failed`, `cancelled`, `partially_refunded`, `refunded`.

Refund: `requested -> awaiting_approval -> approved -> processing -> successful`; alternatives `rejected`, `failed`, `cancelled`.

### 9.4 Tasks and maintenance

Task: `pending -> assigned -> in_progress -> completed -> verified`; alternatives `cancelled`, `overdue`, `rejected`.

Maintenance: `reported -> triaged -> assigned -> in_progress -> resolved -> closed`, with `reopened` represented by a new transition and work log.

Every transition defines permitted origin states, required permission, validation, approval condition, domain event, audit message, and notification consequences.

## 10. Domain events and asynchronous work

A domain event means an important occurrence inside the software, not a website domain. Events are immutable facts named in past tense.

Core Phase 1 events include:

- `BusinessRegistered`, `BusinessVerified`, `MembershipInvited`, `RoleChanged`;
- `PropertyCreated`, `PropertyVerificationSubmitted`, `PropertyVerified`, `PropertyPublished`;
- `BookingCreated`, `BookingDatesChanged`, `BookingConfirmed`, `GuestCheckedIn`, `GuestCheckedOut`, `BookingCompleted`, `BookingCancelled`;
- `PaymentReceived`, `PaymentVerified`, `PaymentFailed`, `RefundRequested`, `RefundSucceeded`;
- `TaskCreated`, `TaskAssigned`, `TaskCompleted`, `InspectionPassed`, `InspectionFailed`, `MaintenanceReported`, `MaintenanceResolved`;
- `DocumentUploaded`, `DocumentExpiring`, `ReviewSubmitted`, `ReviewPublished`;
- `IntegrationSyncFailed`, `CalendarConflictDetected`, `AIRecommendationGenerated`.

Events fire only after the authoritative transaction commits. Cross-module listeners must be idempotent. Slow or external effects are queued. Each event carries an event UUID, tenant, occurred time, actor, correlation ID, causation ID, subject identifiers and a versioned payload.

An `outbox_messages` table records publishable events in the same transaction as the domain change. Workers claim, publish and mark them, allowing safe retries without losing side effects.

## 11. API and web application design

- All reusable capabilities live behind application actions consumed by both Blade controllers and `/api/v1` controllers.
- APIs use resource-oriented URLs, standard methods, cursor pagination for large feeds, filtering, sorting and sparse includes where justified.
- Responses use a consistent envelope and machine-readable error codes.
- Validation failures use HTTP 422; unauthenticated 401; forbidden 403; missing 404; conflict/state/concurrency 409; rate limit 429.
- Mutation endpoints accept an idempotency key where retries can duplicate bookings, payments, refunds, tasks or external calls.
- Optimistic concurrency uses an expected version or ETag for editable collaborative records.
- OpenAPI is generated/maintained with endpoint examples and remains version-controlled.
- Web and API policies are identical; no Blade-only security.

Blade renders the initial accessible page. Alpine manages local interaction. Axios performs partial mutations and receives structured validation errors. Critical workflows remain usable without depending on a large client-side SPA state store.

## 12. Authentication decision

Authentication must support password login, email verification, password reset, secure session expiry, optional MFA, and later Google/Microsoft/Apple sign-in. The application should use a maintained Laravel authentication backend and custom Blade/Alpine screens.

Laravel Breeze should not be treated as the architecture. If a Laravel 13-compatible Breeze/starter package is verified at implementation time, it may accelerate views and basic routes; otherwise Laravel Fortify or Laravel's current official starter mechanism supplies the backend flows. In either case, multi-business context, memberships, RBAC, MFA, impersonation audits and API authentication remain Project Nexus domain work.

For first-party browser sessions, secure session cookies and CSRF are preferred. Mobile/public API tokens use Laravel Sanctum-style scoped tokens or the current supported equivalent, with token rotation, expiry, revocation and device records.

## 13. Security and compliance controls

- Deny access by default; central permissions plus policies.
- Enforce business and property scope in web, API, queue and automation contexts.
- Hash passwords with Laravel's current secure default; encrypt MFA and integration secrets.
- Rate-limit login, password reset, public search, booking attempts, API tokens and webhooks.
- Regenerate sessions after authentication/privilege changes; record new-device security events.
- Use CSRF protection, secure/HTTP-only/SameSite cookies, CSP, HSTS and standard security headers.
- Validate upload content, size and ownership; private storage and signed access.
- Never store raw card details; use PCI-compliant gateway tokens/hosted collection.
- Redact secrets and sensitive PII from logs, error reports, events and AI prompts.
- Require reason and audit for impersonation and high-risk administrative action.
- Support NDPA and UK GDPR privacy workflows; make jurisdictional rules configurable.
- Perform dependency, static-analysis, authorisation and tenant-isolation checks in CI.

## 14. Error handling, observability and reliability

Every request/job receives a correlation ID. Logs are structured with environment, tenant ID where permitted, user, module, action and correlation ID. Technical errors remain internal; users receive safe messages and a reference.

Errors are categorised as validation, business rule, security, integration, system or AI. Retriable external failures use exponential backoff with jitter and a terminal dead-letter state. Non-retriable validation/provider rejections are not blindly retried.

Operational health covers HTTP, database, Redis, queue lag, scheduler heartbeat, storage, integration health and failed jobs. Alerts focus on user impact: payment webhook backlog, availability sync failures, queue latency, elevated conflict rate and error-rate thresholds.

The deployment target is at least 99.9% availability. Backups must be encrypted and restoration tested. Recovery objectives will be approved before production; a proposed Phase 1 baseline is RPO 15 minutes and RTO 4 hours.

## 15. Performance and scale design

The design targets the SRS thresholds: login and cached dashboard within two seconds, booking/property search within one second, calendar within three seconds, standard reports within ten seconds, typical AI response within five seconds, and near-real-time notifications.

Techniques include:

- tenant-leading composite indexes;
- eager loading and strict prevention of N+1 queries in tests/development;
- daily metric and calendar/availability projections;
- short-lived tenant-aware caches with event invalidation;
- cursor pagination for histories and audits;
- queued media, notifications, reports, sync and AI work;
- chunked exports and streaming downloads;
- read replicas and table partitioning only when production metrics justify them.

No cache key may omit the business context for tenant-owned data.

## 16. Migration sequence

Migrations are small, dependency-ordered, reversible where data safety permits, and never combine unrelated domains. Production migrations use expand/migrate/contract for destructive schema evolution.

Recommended order:

1. Laravel infrastructure: users, sessions, cache, jobs, failed jobs, personal access tokens.
2. Shared UUID/audit actor conventions and businesses.
3. Business settings, branches and memberships.
4. Roles, permissions, membership roles, overrides and property assignment support.
5. Employees, suppliers and organisation catalogues.
6. Properties, units, addresses, amenities, rules, pricing and verification.
7. Files, documents, versions and media.
8. Guests, contacts, identities, consents, preferences and duplicate review.
9. Booking sources, rate plans, bookings, occupants, charges and histories.
10. Availability blocks, daily projection, holds and conflicts.
11. Invoices, payments, allocations, refunds, expenses and tax/cost centres.
12. Ledger accounts, journals, lines, reconciliation and settlements.
13. Workflow templates/versions, instances, tasks, checklists and evidence.
14. Cleaning, inspection, maintenance and incidents.
15. Assets, inventory locations/items/movements and counts.
16. Calendar projection, recurrence and external sync records.
17. Marketplace listings, booking requests, favourites and moderation.
18. Reviews, invitations, responses and moderation.
19. Conversations, messages, notification preferences/templates/deliveries.
20. Integration connections, mappings, inbox, outbox, idempotency and sync logs.
21. Approvals, delegations, retention, privacy and legal hold support.
22. Audit/activity tables, with database privilege restrictions documented.
23. KPI definitions, daily snapshots and report exports.
24. AI model runs, briefs, insights, recommendations, feedback and conversations.

Foreign-key deletion policies are deliberate: restrict for historical/financial relationships, set null only for optional actors, and cascade only for value rows that cannot have independent historical meaning. No cascade may erase bookings, payments, journals, tasks, documents or audit history.

## 17. Seeders, factories and reference data

Seeders provide:

- platform permissions and default role templates from SRS Appendix 17.5;
- booking sources, property types, payment methods, task types and expense categories;
- standard workflow catalogue from Appendix 17.3;
- notification categories and default matrix from Appendix 17.6;
- lifecycle definitions/transition permissions;
- KPI definition versions;
- a platform super administrator created only through a secure deployment command, never a known production password.

Factories must create coherent states: a booking factory creates a business, property unit and guest; a successful payment has allocations; a completed stay can create a verified review. Demo seed data is explicitly blocked in production.

## 18. Testing strategy

### 18.1 Required test layers

- **Unit tests:** money/value objects, transition maps, policy rule objects, pricing and KPI formulas.
- **Feature tests:** every use case through API and important Blade/Ajax routes.
- **Database tests:** constraints, indexes, soft deletion, immutable record guards and migration rollback on a disposable database.
- **Tenant-isolation tests:** a user in Business A cannot list, bind, mutate, export, download or infer Business B data.
- **Concurrency tests:** simultaneous booking confirmation, payment webhooks, refund approval and task reassignment.
- **Contract tests:** payment/channel/calendar/message adapters and webhook signature fixtures.
- **Queue tests:** idempotence, retry classification, outbox delivery and dead-letter behaviour.
- **Security tests:** permissions matrix, property scopes, impersonation, file access, mass assignment and rate limits.
- **Browser tests:** critical onboarding, property publication, booking, check-in/out, refund approval and operations flows.
- **Performance tests:** SRS response thresholds against production-shaped data.
- **Acceptance tests:** requirement IDs linked to the SRS traceability matrix.

### 18.2 Definition of done

A requirement is complete only when code is reviewed, migrations are safe, automated tests pass, policy and tenant checks exist, audit/events are verified, documentation is updated, accessibility is checked where UI-facing, and its traceability row links to evidence.

## 19. Phased implementation plan at architecture level

### Foundation increment

Set up MySQL, environment validation, module conventions, UUIDs, authentication, tenant context, RBAC, audit/outbox, API standards, CI and the traceability register.

### Phase 1A — Business, team and property

Deliver onboarding, membership, settings, staff, property/unit profiles, documents/media, verification, pricing and publication readiness.

### Phase 1B — Guests, bookings and availability

Deliver guest history, booking lifecycle, pricing snapshots, booking holds, conflict-safe confirmation, unified calendar and external calendar foundations.

### Phase 1C — Finance

Deliver invoices, multiple payments, refunds and approvals, expenses, immutable ledger, basic cash flow, reconciliation and traceable reports.

### Phase 1D — Operations

Deliver predefined workflows, cleaning, inspections, maintenance, task assignment/evidence, property readiness and notifications.

### Phase 1E — Marketplace, reviews and AI baseline

Deliver verified listings/search, booking requests, verified-stay reviews, executive briefing, evidence-backed recommendations and feedback.

### Phase 1F — hardening and launch

Complete integrations, performance tests, accessibility, privacy, backup restoration, observability, penetration remediation, operational runbooks and SRS acceptance audit.

Phases 2–4 are planned as separate approved specs when their delivery begins. They reuse these identities and boundaries instead of expanding the Phase 1 implementation plan into an unmanageable multi-year task.

## 20. Documentation produced during implementation

The repository will maintain:

- this architecture decision/design;
- requirements traceability matrix;
- migration/data dictionary per module;
- generated ERD plus a human-readable relationship guide;
- lifecycle transition catalogue;
- permission matrix;
- event catalogue and listener ownership;
- OpenAPI specification and examples;
- integration contracts and webhook runbooks;
- finance posting rules;
- deployment, backup/restore and incident runbooks;
- architectural decision records for material deviations;
- release notes and SRS coverage report.

Each implementation checkpoint will state what was changed, why it was designed that way, migrations added, events emitted, security implications, tests run and remaining SRS coverage. This is the detailed handover the user requested.

## 21. Decisions requiring confirmation before their implementation increment

These do not block approval of the foundation architecture, but must be decided before the named module is implemented:

1. Initial launch jurisdictions, default currency and tax/invoice requirements.
2. Whether “property” can contain multiple simultaneously bookable units at Phase 1 launch. This design recommends yes because hotels are in scope and the unit abstraction is inexpensive now.
3. Initial payment gateways and whether cash/bank transfer requires dual verification.
4. Initial marketplace booking model: instant booking, request-to-book, or both. This design supports both and recommends request-to-book first unless operations guarantee real-time inventory.
5. Property/business identity-verification provider and mandatory document checklist.
6. Subscription plans, trial, billing cycle and business usage limits.
7. Approved email/SMS providers and WhatsApp timing.
8. Exact RPO/RTO and production hosting region.
9. AI provider, data residency terms, prompt retention rules and per-business cost limits.
10. Whether branches are exposed in Phase 1 or retained as a hidden foundation for Phase 2.

## 22. Risks and mitigations

| Risk | Mitigation |
|---|---|
| 190-page scope becomes one oversized release | requirement decomposition, Phase 1 increments and review gates |
| Tenant data leakage | explicit context, policies, scoped binding and isolation tests |
| Double bookings | unit locks, transactional overlap check, holds, idempotency and sync conflict records |
| Financial inconsistency | immutable payments/refunds, double-entry ledger, transactional posting and reconciliation |
| Cross-module coupling | module-owned mutations, services and versioned events |
| Event duplication/loss | transactional outbox, idempotent listeners and correlation IDs |
| External provider outage | queues, retries, circuit/health state, inbox/outbox and reconciliation |
| AI gives unsupported advice | permission-aware evidence, confidence, approval policies and feedback |
| Reporting slows operations | projections/snapshots, queued exports and later warehouse adapter |
| Premature enterprise complexity | extension points only; later features receive separate specs |
| SRS requirement missed | page-linked traceability matrix and release coverage audit |

## 23. Acceptance criteria for this design

This foundation design is accepted when stakeholders agree that:

- Option 1 is the chosen architecture;
- business tenancy and access isolation are correct;
- the module boundaries and ownership are understandable;
- core entities and future extension points cover the SRS;
- booking conflicts and financial integrity have enforceable designs;
- domain events are understood as internal business occurrences;
- implementation will begin with the foundation and Phase 1 only;
- unresolved product decisions may be answered at their implementation checkpoint;
- progress will be documented and measured through traceability and tests.

Once accepted, the next artifact is a detailed, file-by-file implementation plan. Only after that plan is approved should migrations and feature code begin.
