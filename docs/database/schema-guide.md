# Project Nexus Database Schema Guide

## 1. Purpose of this guide

This is the conceptual companion to the [exhaustive schema data dictionary](schema-data-dictionary.md). The dictionary lists all 134 active tables and all 2,462 fields with their types, nullability, defaults, foreign-key targets, deletion rules, and indexes. This guide explains why the tables exist, how they interact, and which business rules must be enforced by the database or application.

The active schema is derived from `database/migrations`. Files under `database/migrations_old` are historical and are not part of the active database.

## 2. System-wide conventions

### UUID identity

Business entities use an `id` UUID as their permanent primary key. A UUID must never be changed or recycled. Human-readable values such as a booking reference, property code, SKU, or invoice number can change under controlled rules; the UUID remains the entity's identity.

Laravel framework tables such as `migrations` use their framework-defined identifiers rather than the business UUID convention.

### Tenant ownership

`business_id` identifies the business that owns a record. It is both a relationship and an authorization boundary. Application queries must normally be scoped by the active business context before filtering by property, booking, employee, or another child entity.

Important relationships use composite foreign keys such as `(business_id, property_id)` or `(business_id, booking_id)` where the existing parent schema makes this practical. These prevent a row from claiming one business while referencing another business's property or booking. Relationships that currently use a simple UUID foreign key still require service-layer tenant validation.

### Audit fields

Most business entities have:

- `created_at` and `updated_at` for database record timing.
- `created_by` and `updated_by` for the responsible user.
- `status` for the broad active/inactive/archived lifecycle.
- `deleted_at` where soft deletion is appropriate.

Domain-specific states are deliberately separate. For example, a booking has `booking_status`, `payment_status`, and general `status`; these answer different questions.

### Historical preservation

`RESTRICT` foreign keys protect historical relationships. `SET NULL` is mostly used for actor fields, allowing the business record to survive when a user account is removed or anonymized. `CASCADE` is reserved for subordinate definition or detail rows that have no independent meaning, such as knowledge chunks beneath a knowledge source.

Soft deletion means a record is operationally hidden but remains available for auditing and reporting. Append-only models such as payments, financial transactions, inventory movements, approval actions, AI feedback, and audit events also reject update or delete operations at the model layer where implemented.

### JSON fields

JSON is used for variable payloads, settings, evidence, snapshots, and provider metadata. It is not used for relationships that must be searched, ordered, permissioned, or constrained. Examples of appropriate JSON include workflow configuration, AI evidence, an address structure, and a frozen approval snapshot.

### Money

Amounts use fixed-precision decimal columns rather than floating point. A monetary value must be interpreted with its accompanying currency. Money movements are never inferred solely from a booking total: payments, allocations, expenses, refunds, revenue recognition, and the financial ledger remain separate.

## 3. Identity, authentication, roles, and access control

### Users and authentication

`users` is the single person/account table. It contains login identity and common profile information: name, contact details, verification state, language, emergency contact, marketing preferences, timezone, security counters, and activity timestamps. A user must have an account to book.

There is intentionally no separate core `guests` identity table. A user becomes a guest through the guest role, while booking-specific people and snapshots belong in `booking_guests`.

Supporting authentication tables are:

- `user_identities`: SSO identities such as Google, including provider identifiers and encrypted tokens.
- `user_mfa_methods`: configured multi-factor methods, secrets, recovery codes, and confirmation state.
- `user_refresh_tokens`: hashed API/mobile refresh tokens by device and expiry.
- `password_reset_tokens`: short-lived password reset challenges.
- `password_policies`: platform or business password and session rules.
- `sessions`: Laravel-managed web sessions.
- `impersonation_sessions`: explicit, time-bounded, audited platform-administrator impersonation.

### Membership and context switching

`business_memberships` connects a user to a business. It describes membership state, invitation/acceptance dates, employee-facing details, and whether the user currently participates in that tenant.

`user_business_contexts` stores the user's active business, membership, and role context. It makes switching from guest to owner, manager, accountant, or operational staff explicit without duplicating the user.

`user_workspace_preferences` stores per-business UI preferences such as default workspace, theme, language, currency display, dashboard widgets, KPIs, and notification preferences.

### RBAC tables

- `workspaces`: named application areas such as Properties, Bookings, Finance, Operations, and AI.
- `permissions`: atomic allowed actions such as `booking.cancel`, `payment.verify`, or `inventory.adjust`.
- `roles`: system templates and business-defined roles with scope and hierarchy.
- `user_roles`: assigns roles to users, optionally within a business membership and scope.
- `role_permissions`: many-to-many mapping from roles to atomic permissions.
- `role_workspaces`: declares whether a role has full, limited, or no workspace access.
- `membership_permission_overrides` is not an active table; individual exceptions are represented by the active schema's membership permission override model only if a future migration introduces its storage. Authorization must therefore rely on current role permissions and existing scope assignments.
- `permission_separation_rules`: warns or blocks dangerous permission combinations, such as allowing the same person to verify a payment and approve its refund.

Roles are capabilities, not separate identities. John can hold guest, business owner, and staff roles simultaneously. An employee record adds employment details; it does not replace John's user account or role assignments.

## 4. Business workspace

`businesses` is the tenant root. It stores the business name, description, branding, registration details, country, address, primary contact, tax information, business type, timezone, currency, subscription plan, onboarding state, verification state, and lifecycle status.

Related tables:

- `business_onboarding_steps`: progress and evidence for each onboarding requirement.
- `business_subscriptions`: subscription plan, provider references, billing period, trial, cancellation, and subscription state history.
- `business_memberships`: people belonging to the business.
- `business_automation_settings`: configurable booking and operational automation switches.
- `business_health_snapshots`: dated health scores and component metrics.
- `dashboard_briefings`: generated daily executive summaries with model provenance.
- `departments`: business-defined teams used to organize employees and workflow responsibility.

A property, booking, employee, payment, task, report, and most operational or financial records must resolve to exactly one business.

## 5. Property workspace and marketplace

### Permanent property profile

`properties` is the permanent digital twin of an accommodation. It stores identity and current operational attributes without replacing historical child records. Key groups include:

- Identity: property name, property code, and type.
- Location: address, latitude, longitude, and timezone.
- Capacity: guest capacity, bedrooms, and bathrooms.
- Content: description and operational notes.
- Lifecycle: verification, publication, operational, maintenance, readiness, and general status.
- Accountability: initially nullable owner and manager contact fields, plus normalized staff assignments.
- Audit: verification/publication actors and timestamps, creation/modification actors, timestamps, and soft deletion.

Deleting or archiving a property must not delete bookings, payments, tasks, assets, inspections, reviews, finance records, or audit history. Inactive properties remain reportable.

### Property descriptive tables

- `amenities`: global searchable amenity catalogue.
- `property_amenities`: property-to-amenity mapping with property-specific value or notes.
- `property_house_rules`: normalized, orderable house rules.
- `property_media`: images and videos sharing one media table, with storage, ordering, caption, primary-media, and processing fields.
- `property_cleaning_schedules`: recurring cleaning requirements and next-run timing.
- `property_access_instructions`: confidential arrival/access details with controlled availability windows.
- `property_staff_assignments`: owner, manager, operational, or other employee responsibility for a property.
- `property_lifecycle_events`: chronological property state and profile history.
- `property_health_snapshots`: dated readiness, operational, maintenance, and performance scoring.

### Marketplace, pricing, and promotion

- `property_marketplace_listings`: the public listing state, headline, marketplace content, publication/moderation timing, and search settings.
- `property_pricing_rules`: date-based, occupancy-based, seasonal, or other price adjustments.
- `property_promotions`: bookable promotions with discount rules, validity, limits, and publication state.

The private property record and public marketplace listing are separate so a property may exist operationally before publication and remain historical after being removed from the marketplace.

## 6. Booking and guest-stay workspace

### Booking root

`bookings` is the reservation root. It always belongs to one business and one property. It connects the primary guest user, dates, guest count, source, booking state, payment state, special requests, discounts/coupons, price snapshot, currency, creator, and lifecycle timestamps.

Availability-blocking status is a business rule:

- Does not block: enquiry, cancelled, refunded, no-show.
- Blocks: reserved, awaiting payment, confirmed, checked in.
- Historical and no longer blocks future dates: checked out, completed.

The database provides availability-day uniqueness, but overlap prevention must be performed transactionally by the booking/availability service.

### Guests and people

`booking_guests` lists every person attached to a booking, including the primary guest and companions. It stores a historical snapshot of name, contact, nationality, verification, and guest type. This is necessary even though users and roles exist: not every companion controls the booking, and historical booking documents must not change when a user edits their current profile.

### Booking history and lifecycle

- `booking_status_history`: append-style record of every booking status transition.
- `booking_date_changes`: old/new arrival and departure dates, reason, actor, and timing.
- `booking_cancellations`: cancellation reason, policy snapshot, financial consequences, and cancellation actor.
- `booking_check_ins`: operational check-in facts and verification.
- `booking_check_outs`: operational check-out facts, condition, charges, and completion.
- `booking_channel_links`: provider identifiers linking bookings to external channels.
- `booking_staff_assignments`: employees responsible for the booking or guest journey.

### Communication, service, and incidents

- `booking_interactions`: calls, emails, WhatsApp, notes, and other communications, including direction, visibility, delivery, and external reference.
- `guest_service_requests`: guest requests associated with a stay, their priority, assignment, SLA, and completion.
- `booking_incidents`: safety, damage, behavior, or operational incidents and their resolution.
- `booking_disputes`: financial or service disputes, evidence, responsibility, outcome, and closure.

### Booking financial presentation

- `booking_financial_documents`: invoices, receipts, credit notes, and related booking documents.
- `booking_financial_document_items`: normalized line items, quantities, unit prices, tax, discounts, and totals.
- `booking_payment_installments`: expected deposit, balance, or scheduled payment amounts and due dates.
- `booking_financial_allocations`: allocates booking charges across properties, cost centres, revenue categories, or other financial dimensions.
- `cancellation_policies`: reusable cancellation rule definitions that can be snapshotted onto a booking cancellation.

These tables describe what should be charged or presented. They do not prove that money moved; `payments` and `financial_transactions` do that.

## 7. Calendar and availability

- `property_availability_days`: one row per property night, representing whether that night is available, held, booked, or blocked and identifying its source. Its uniqueness protects against two active consumers claiming the same night.
- `property_availability_blocks`: explicit manual or operational date ranges that remove inventory from sale.
- `external_calendar_connections`: credentials and configuration for Airbnb, Booking.com, iCal, Google, Outlook, or similar calendars.
- `external_calendar_sync_runs`: one import/export synchronization attempt, including status, timing, counts, and errors.
- `external_calendar_sync_items`: individual remote items processed during a sync, with mapping and conflict outcome.

The calendar is intentionally an availability view, not a second booking system. Bookings and explicit blocks are sources; availability days are the normalized lock/search layer.

## 8. Payments and finance

### Payments

`payments` records actual payment transactions. It stores business, booking, amount, currency, method, provider, provider/reference identifiers, purpose, transaction direction, status, paid/verified times, verifier, receipt metadata, original payment for refunds, and audit fields.

Multiple payments can belong to one booking. Deposits, balances, damage deposits, cleaning charges, late fees, additional charges, and refunds are separate rows. Refunds must reference an original non-refund payment from the same business, booking, and currency. Payment deletion is prohibited by the model.

### Expenses and approvals

- `expenses`: business spending with property, booking, supplier, category, amount, tax, status, receipt, and approval state.
- `expense_recurring_schedules`: rules for producing repeating expenses.
- `expense_approval_events`: specialized historical approval activity retained alongside the generic approval engine.
- `refund_requests`: refund proposal, amount, reason, approval state, and eventual refund payment.

### Financial structure

- `financial_accounts`: cash, bank, receivable, provider-clearing, revenue, expense, and similar accounts.
- `cost_centres`: reporting dimensions such as a property, department, or operational unit.
- `tax_categories`: effective-dated tax types, rates, and inclusive/exclusive behavior.
- `currency_exchange_rates`: effective conversion rates and data source.
- `currency_conversions`: the exact rate and amounts used for a particular conversion.

### Immutable financial ledger

`financial_transactions` records each recognized inflow, outflow, adjustment, transfer, or other financial event. It can reference accounts, payments, expenses, refunds, bookings, properties, and cost centres. Historical financial meaning must not be reconstructed by reading mutable booking totals.

`revenue_recognition_policies` defines when revenue should be recognized—for example at payment, check-in, nightly, check-out, or completion. `revenue_entries` records the resulting recognized revenue by date and source.

### Reconciliation, forecasting, and reporting

- `financial_reconciliations`: one reconciliation session for an account/provider and statement period.
- `financial_reconciliation_items`: matched, unmatched, or adjusted items within a reconciliation.
- `financial_forecast_snapshots`: dated financial projections with assumptions and model provenance.
- `financial_report_definitions`: saved report type, filters, grouping, columns, and output configuration.
- `financial_report_schedules`: recurring delivery settings for a report definition.
- `financial_report_runs`: immutable execution/output record for a generated report.

This is a practical immutable financial journal, not a complete double-entry general ledger. A future accounting module may introduce journal entries and balanced debit/credit lines without replacing these operational finance records.

## 9. Employees, operations, inspections, and maintenance

### Employees

`employees` stores business-specific staff information: membership, department, employee code, employment state, availability, emergency contact, start/end dates, notes, and lifecycle. Former employees remain referenced in historical assignments and activity.

- `employee_skills`: searchable skill name/key, proficiency, experience, primary-skill flag, and verification.
- `employee_certifications`: issuing organization, certificate number, validity, verification, and optional document evidence.

Permissions remain role-based. Skills and certifications describe operational suitability, not authorization.

### Operational tasks

`operational_tasks` stores work such as cleaning, maintenance, inspection, inventory count, guest welcome, photography, deep cleaning, and repairs. A task must belong to a property but may exist without a booking. It supports parent tasks, workflow linkage, primary assignee, priority, state, due/SLA timing, start/completion/verification, recurrence, escalation, generation source, and manual creation reason.

- `operational_task_checklist_items`: ordered required or optional completion items.
- `operational_task_assignments`: multiple primary/supporting employee assignments and acceptance/rejection/release history.
- `operational_task_dependencies`: prerequisite task graph and lag.
- `operational_task_attachments`: before/after photos, receipts, evidence, and file metadata.

Completed tasks should become read-only operational history. Failed verification or inspection should generate follow-up work rather than rewriting the original result.

### Inspections and maintenance

- `inspections`: a scheduled or ad-hoc inspection for a property, booking, asset, employee inspector, and task.
- `inspection_items`: individual checklist results, severity, evidence, and failure details.
- `maintenance_issues`: an identified defect linked to property, booking, asset, inspection, reporter, assignee, priority, SLA, and resolution.
- `asset_maintenance_schedules`: preventive maintenance frequency and next service date.
- `asset_maintenance_records`: each scheduled or reactive maintenance occurrence, work, supplier, cost, and result.

## 10. Assets, suppliers, and inventory

`suppliers` stores business-specific vendor contact details and notes.

`assets` stores durable purchased items such as televisions, generators, routers, mattresses, or furniture. It includes property, supplier, asset code, category, serial number, QR identity, purchase/warranty dates, condition, replacement value, and currency. Purchased assets cannot be physically deleted through the model.

- `asset_media`: photographs, manuals, condition evidence, and other asset file metadata.
- `asset_assignments`: custody/history assigning exactly one asset to one property, employee, booking, or operational task until return.

Inventory is separate because it represents consumable/countable stock:

- `inventory_items`: SKU, category, unit of measure, supplier, cost, and reorder defaults.
- `inventory_locations`: property stores, warehouses, closets, or vehicles.
- `inventory_stock_levels`: current on-hand and reserved quantity per item/location.
- `inventory_movements`: immutable receipts, consumption, transfers, adjustments, damage, and returns, with operational context.

Stock levels are the current projection; movements are the audit truth. Adjusting stock requires a movement rather than silently editing history.

## 11. Documents

`documents` stores searchable document metadata. Its polymorphic owner may be a property, booking, guest/user, employee, asset, inspection, maintenance issue, or another valid business entity. Important fields cover title, category, number, issuer, searchable text, issue/effective/expiry dates, reminder state, confidentiality, verification, current version, and lifecycle.

- `document_versions`: immutable file versions, storage paths, MIME type, checksum, uploader, and version number.
- `document_permissions`: explicit user, role, or other principal access grants and expiry.

Every document must have an owner. Replacing a file creates a new version rather than overwriting the existing historical version.

## 12. Notifications and reviews

### Notifications

`notifications` represents one logical message for a user, including event/category, title, body, priority, related entity, read state, action link, and expiry.

`notification_deliveries` represents each channel attempt—email, SMS, WhatsApp, push, or in-app—with provider reference, delivery status, attempts, timing, and failure detail. One notification can therefore succeed in-app while an SMS delivery fails and retries.

### Reviews

- `review_invitations`: time-limited invitation/token for a verified guest stay.
- `reviews`: booking/property review, overall and category ratings, content, verified-stay status, moderation, sentiment, and publication timing.
- `review_responses`: business response and moderation/publication state.
- `review_analyses`: AI themes, sentiment, risks, praise, and model provenance derived from a review.

A review remains tied to the booking and guest that produced it. Analysis can be regenerated without rewriting the guest's original review.

## 13. Events and workflow orchestration

### Events

`domain_events` is a durable event/outbox record. It stores event and aggregate identity, correlation/causation, idempotency, payload, occurrence time, publication state, retry scheduling, and errors. Events enable modules to react independently to booking confirmation, payment receipt, check-out, maintenance, or AI decisions.

### Workflow definitions versus execution

- `workflow_templates`: reusable, versioned definition and trigger.
- `workflow_template_steps`: ordered task/action definitions, responsible role/department, duration, SLA, checklists, approval, verification, failure, and escalation configuration.
- `workflow_runs`: a historical execution tied to its triggering domain event and optional template.
- `workflow_steps`: the ordered execution state, attempts, input/output, retry, and errors for each run.

Definition changes never rewrite execution history. Runs and steps retain keys, versions, inputs, and outputs even when a newer template version becomes active.

## 14. Generic approval engine

- `approval_workflows`: reusable subject-specific definition and version.
- `approval_workflow_steps`: ordered role/permission requirements, quorum, monetary thresholds, currency, conditions, and escalation timing.
- `approval_requests`: one request for a polymorphic subject with a frozen subject snapshot, requester, current step, due date, and outcome.
- `approval_request_steps`: frozen copy of each required stage so later definition edits do not alter an open or completed request.
- `approval_actions`: immutable submit, approve, reject, return, escalate, delegate, or cancel action.
- `approval_delegations`: time-limited authority transfer between users, optionally restricted to one approval workflow.

The generic engine can progressively replace module-specific approval logic, but specialized historical rows such as expense approval events remain valid audit records.

## 15. Audit, privacy, and retention governance

`audit_events` records the affected polymorphic entity, action type, description, before/after values, metadata, source channel, actor type, correlation/request/device identifiers, IP address, user agent, occurrence time, and actor. The model prohibits update and deletion.

- `data_retention_policies`: platform or business rule by data category, retention trigger/duration, terminal action, legal basis, legal-hold support, and configuration.
- `data_retention_executions`: each policy run, cutoff, timing, examined/affected/held counts, errors, and executor.
- `data_subject_requests`: privacy access, correction, export, restriction, or erasure request, including identity verification, deadlines, resolution, and legal-hold explanation.

Erasure does not automatically mean physical deletion. Financial, safety, fraud, and audit duties may require restricted retention or anonymization.

## 16. Dashboard and AI workspace

### Dashboard intelligence

- `dashboard_briefings`: dated generated executive summary, source metrics, generation state, model/prompt version, duration, and errors.
- `business_health_snapshots`: dated overall business score with component scores and explanations.
- `property_health_snapshots`: dated property readiness/performance score and contributing metrics.
- `financial_forecast_snapshots`: finance-specific projections.

Snapshots preserve what the system believed at a point in time instead of rewriting history when current data changes.

### Recommendations and learning

`ai_recommendations` stores category, explanation, evidence, proposed action, priority/risk, confidence, model provenance, validity, acceptance/dismissal/execution, snoozing, and supersession.

`ai_recommendation_feedback` is an immutable learning trail recording acceptance, rejection, dismissal, snoozing, partial application, action taken, and later outcome. Recommendation state answers “what is displayed now”; feedback answers “what happened over time.”

`ai_prediction_snapshots` stores generalized non-financial predictions such as cancellation risk, maintenance risk, demand, readiness, or guest satisfaction. It retains prediction target/date, value or label, confidence, factors, evidence, validity, and model provenance.

### Conversation and knowledge

- `ai_business_settings`: per-business AI enablement, capabilities, data use, automation limits, approval thresholds, autonomous actions, and conversation retention.
- `ai_conversations`: user/business/workspace chat thread and optional polymorphic context.
- `ai_conversation_messages`: ordered user, assistant, system, and tool messages with citations, tool calls, model, token usage, duration, and failures.
- `ai_knowledge_sources`: approved document, policy, manual, or URI registered for indexing with scope and indexing state.
- `ai_knowledge_chunks`: ordered text segments, hashes, token counts, provider-neutral vector reference, and metadata.

The schema deliberately does not store database-specific vector columns. `vector_reference` can point to PostgreSQL/pgvector or an external vector store later.

## 17. Laravel framework support

`migrations` is Laravel's migration ledger. It records which migration files ran and in which batch. It is operational framework state, not a Project Nexus business entity.

`password_reset_tokens` and `sessions` are also framework-facing tables but are described with identity because they participate in authentication.

Cache and queue migrations are not currently active. If they are added later, their tables should be documented as infrastructure rather than business-domain entities.

## 18. Core relationship map

```text
User
├── User Roles ── Role ── Permissions / Workspaces
├── Business Membership ── Business
│   └── Employee profile
└── Bookings as primary guest

Business
├── Properties
│   ├── Bookings
│   │   ├── Booking Guests
│   │   ├── Payments / Financial Documents / Allocations
│   │   ├── Interactions / Requests / Incidents / Disputes
│   │   └── Reviews
│   ├── Availability Days / Blocks / Calendar Sync
│   ├── Assets / Maintenance / Inventory Locations
│   ├── Inspections / Operational Tasks
│   └── Listing / Pricing / Promotions / Media / Documents
├── Employees / Departments
├── Financial Ledger / Expenses / Revenue / Reconciliation / Reports
├── Workflow Templates / Runs / Approvals
├── Notifications / Documents / Audit
└── Dashboard and AI intelligence
```

## 19. Rules enforced in different layers

### Primarily database-enforced

- Required parent relationships through non-null foreign keys.
- Tenant consistency where composite foreign keys exist.
- Stable UUID primary keys.
- Unique business references, codes, SKUs, and ordered child positions.
- One stock-level row per item/location.
- One availability row per property/date under the defined uniqueness rules.
- Restrictive deletion of referenced historical records.

### Primarily model-enforced

- Payment amount must be greater than zero.
- Refunds must reference a valid original payment from the same business, booking, and currency.
- Payments, assets, inventory movements, approval actions, AI feedback, and audit events cannot be deleted or rewritten where their models define immutability.
- Asset assignment must have exactly one target.

### Primarily service/application-enforced

- Authorization based on active business, role, permission, and property scope.
- Booking overlap checks and transactional availability locking.
- Valid state transitions for bookings, tasks, properties, approvals, payments, and documents.
- Workflow triggering, retry, escalation, and idempotency.
- Keeping current stock levels synchronized with immutable movements.
- Revenue recognition and financial reconciliation behavior.
- GDPR identity verification, anonymization, legal holds, and retention execution.
- AI approval thresholds, explainability, safe automation, and knowledge access.

## 20. How to use the data dictionary

For each table, the dictionary contains:

- Its business purpose.
- Every field, including inherited audit and soft-deletion fields.
- Database type.
- Whether a value is required.
- Database default.
- Field meaning or foreign-key target and deletion behavior.
- Named indexes and their indexed columns.

Use this handbook to understand the design and the [data dictionary](schema-data-dictionary.md) when implementing requests, validation, services, policies, resources, reports, or API documentation.

Regenerate the dictionary after migration changes with:

```bash
schema_db=$(mktemp /tmp/nexa-dictionary-XXXXXX.sqlite)
DB_CONNECTION=sqlite DB_DATABASE="$schema_db" php artisan migrate:fresh --force
php scripts/generate_schema_dictionary.php "$schema_db" docs/database/schema-data-dictionary.md
rm "$schema_db"
```
