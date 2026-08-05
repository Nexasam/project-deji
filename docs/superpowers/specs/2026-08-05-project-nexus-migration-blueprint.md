# Project Nexus Detailed Migration Blueprint

**Status:** Proposed for stakeholder study

**Companion document:** `2026-08-05-project-nexus-foundation-design.md`

**Database:** MySQL 8+

**Framework:** Laravel 13

## 1. Why this blueprint exists

The foundation design identifies the domains and tables. This blueprint goes one level deeper: it specifies the migration files, columns, data types, nullability, foreign-key behavior, indexes, uniqueness rules, relationship meaning, and rules that MySQL cannot enforce by itself.

The intended result is that a developer can study the database before any migration PHP is generated. This document remains a design artifact; it is not a replacement for reviewing the actual migration code when implementation begins.

## 2. Reading notation

- `PK` means primary key.
- `FK` means foreign key.
- `UQ` means unique constraint.
- `IDX` means non-unique index.
- `NN` means not nullable.
- `NULL` means optional.
- `R` means `ON DELETE RESTRICT`.
- `C` means `ON DELETE CASCADE`.
- `N` means `ON DELETE SET NULL`.
- `APP` means the relationship or rule is enforced by Laravel because a normal MySQL foreign key/check cannot express the complete business rule.
- `UUID` means `char(36)` UUIDv7 unless a later approved benchmark changes physical storage.
- Money means `decimal(19,4)`.
- Timestamps mean UTC `timestamp`/`datetime` with Laravel `created_at` and `updated_at` where the record is mutable.

## 3. Rules shared by all migration files

### 3.1 Naming and placement

All migrations remain in `database/migrations`. Normal Laravel commands therefore work without custom loaders:

```bash
php artisan migrate
php artisan migrate:status
php artisan migrate:rollback --step=1
```

The actual timestamp prefix will be generated when implementation begins. This document uses sequence numbers such as `010_` to show dependency order.

### 3.2 Standard columns

Unless a table definition says otherwise, a mutable tenant entity receives:

```text
id                 char(36) PK
business_id        char(36) NN FK -> businesses.id R
status             varchar(40) NN
version            unsignedInteger NN default 1
created_by         char(36) NULL FK -> users.id N
updated_by         char(36) NULL FK -> users.id N
created_at         timestamp NULL
updated_at         timestamp NULL
deleted_at         timestamp NULL, only when archival/soft deletion is valid
```

Exceptions:

- Pivot/value tables may use a composite unique key and omit actor/version fields.
- Immutable audit, ledger, history, movement, attempt and event tables do not have `updated_at` or `deleted_at`.
- A system process may create a record, so actor columns are nullable.
- `status` belongs only where a lifecycle exists; it is not added mechanically to every pivot.

### 3.3 Foreign-key deletion policy

- `RESTRICT`: default for historical, finance, booking, guest, property, operational and compliance connections.
- `CASCADE`: only for dependent configuration/value rows that have no independent history, such as a role-permission pivot or an unused draft checklist row.
- `SET NULL`: optional human actor or replaceable assignment, preserving the business record when a user later leaves.
- Soft deletion never triggers database cascading.

### 3.4 Tenant duplication rule

Important child records store `business_id` even where their parent already identifies the business. This is intentional for tenant-safe queries and indexes. Before insertion Laravel must prove:

```text
child.business_id = parent.business_id
```

For the most sensitive paths, composite keys reinforce this in MySQL. For example, `properties` has `UNIQUE (business_id, id)`, permitting `property_units (business_id, property_id)` to reference the matching pair. Laravel migrations may need explicit index/constraint names to stay within MySQL's identifier limit.

## 4. Relationship overview

```text
users
  └─< business_memberships >─ businesses
                                 ├─< properties ─< property_units
                                 │                   ├─< bookings >─ guests
                                 │                   ├─< availability_blocks
                                 │                   └─< inventory/assets/tasks
                                 ├─< employees
                                 ├─< suppliers
                                 ├─< invoices ─< invoice_lines
                                 ├─< payments ─< payment_allocations
                                 ├─< expenses
                                 ├─< workflow_instances ─< tasks
                                 ├─< integration_connections
                                 ├─< notifications/messages
                                 ├─< audit_events
                                 └─< reports/AI records
```

The booking is the operational hub, but it does not own all connected data. A guest survives beyond one booking; a payment is a separate financial transaction; calendar events are projections; tasks may exist without a booking; files are independently retained and attached through document/media records.

### 4.1 Core connection audit matrix

| Parent | Child connection | Cardinality | Required? | Delete rule | Reason |
|---|---|---:|---:|---|---|
| `users` | `business_memberships.user_id` | 1:N | yes | R | membership/history must retain identity reference |
| `businesses` | tenant `business_id` | 1:N | yes | R normally | a business with history is archived, not deleted |
| `businesses` | settings/role pivots | 1:N | yes | C selectively | disposable configuration can follow an unused business |
| `business_memberships` | `employees.membership_id` | 1:0..1 | yes | R | employee and user identity remain separate but connected |
| `roles` | `membership_roles.role_id` | 1:N | yes | R | assigned roles cannot disappear silently |
| `permissions` | `role_permissions.permission_id` | 1:N | yes | C for pivot | pivot has no independent history; permission changes are audited |
| `branches` | `properties.branch_id` | 1:N | no | N | property survives branch restructuring |
| `business_memberships` | branch/property manager fields | 1:N | no | N | property survives manager departure |
| `property_types` | `properties.property_type_id` | 1:N | yes | R | used type cannot be removed |
| `properties` | `property_units.property_id` | 1:N | yes | R | property/unit identity and history are permanent |
| `properties` | addresses/rules/amenity values | 1:N | yes | C only pre-history | ordinary archive never invokes cascade |
| `properties` | bookings/tasks/assets/etc. | 1:N | yes | R | historical operations must remain |
| `property_units` | `bookings.property_unit_id` | 1:N | yes | R | booked unit identity cannot disappear |
| `properties` | `bookings.property_id` | 1:N | yes | R | denormalised parent supports tenant/report queries and consistency checks |
| `guests` | `bookings.primary_guest_id` | 1:N | yes | R | returning guest history survives individual stays |
| `bookings` | `booking_guests.booking_id` | 1:N | yes | R | occupant history is protected |
| `guests` | `booking_guests.guest_id` | 1:N | yes | R | guest is anonymised/merged rather than physically deleted |
| `rate_plans` | `bookings.rate_plan_id` | 1:N | no | N | booking retains its charge/policy snapshot if plan is retired |
| `bookings` | status/change/cancellation rows | 1:N | yes | R | immutable booking history |
| `property_units` | holds/availability blocks | 1:N | yes | R | active/historic availability cause remains traceable |
| `bookings` | `booking_charges.booking_id` | 1:N | yes | R | commercial snapshot is immutable after posting |
| `bookings` | `invoices.booking_id` | 1:N | no | R | invoices may also be non-booking documents |
| `guests` | invoice/payment guest context | 1:N | no | R | financial evidence remains; personal fields may be anonymised |
| `invoices` | `invoice_lines.invoice_id` | 1:N | yes | R | posted financial document cannot lose lines |
| `payments` | `payment_attempts.payment_id` | 1:N | yes | R | all gateway attempts stay auditable |
| `payments` | `payment_allocations.payment_id` | 1:N | yes | R | allocation/reversal history is immutable |
| `invoices` | `payment_allocations.invoice_id` | 1:N | yes | R | invoice settlement trail remains |
| `payments` | `refunds.payment_id` | 1:N | yes | R | refund cannot exist without captured/original payment |
| `financial_accounts` | `journal_lines.account_id` | 1:N | yes | R | a used chart account is retired, never deleted |
| `journal_entries` | `journal_lines.journal_entry_id` | 1:N | yes | R | balanced posted entry remains complete |
| `workflow_templates` | template steps/instances | 1:N | yes | R after publish | running instances retain exact template version |
| `workflow_instances` | step instances/tasks | 1:N | no/yes | R | execution history cannot be broken |
| `tasks` | checklists/evidence/work extensions | 1:N | yes | R | completion evidence must survive |
| `properties` | `tasks.property_id` | 1:N | yes | R | every operational task has a property context |
| `bookings` | `tasks.booking_id` | 1:N | no | R | property tasks may exist without a stay |
| `employees/memberships` | task assignment | 1:N | no | N current, R history | current assignee clears; assignment log preserves actor |
| `tasks` | cleaning job | 1:0..1 | yes for type | R | typed extension cannot outlive task |
| `tasks` | inspection | 1:0..N | yes | R | re-inspection can create a new inspection record |
| `assets` | condition/work history | 1:N | yes | R | purchased asset is retired, not deleted |
| `inventory_items` | movements | 1:N | yes | R | stock balance derives from permanent movements |
| `files` | document versions/media | 1:N | yes | R | retention controls physical file deletion |
| `documents` | versions | 1:N | yes | R | version history is mandatory |
| `properties` | marketplace listing | 1:0..1 | yes | R | listing is a public projection of verified property |
| `bookings` | review | 1:0..1 | yes | R | proves verified stay |
| `reviews` | moderation/response | 1:N | yes | R | public moderation history remains |
| `conversations` | messages | 1:N | yes | R | communication timeline remains |
| integration connection | mappings/sync/webhooks | 1:N | yes | R | provider history must remain after disconnect |
| domain entity | calendar/activity projection | 1:N | controlled polymorphic | APP | multiple possible parent tables prevent ordinary FK |
| domain entity | audit event | 1:N | controlled polymorphic | APP/immutable | audit must survive entity archival/anonymisation |
| AI model run | AI outputs | 1:N | normally yes | R | output must remain explainable by model/prompt run |

“Cascade only pre-history” means the database relationship may technically cascade for a value row, but application policy prohibits physically deleting the parent after meaningful activity exists. Historical parents use archive/status transitions.

## 5. Migration batch A — Laravel infrastructure and identity

### `001_create_users_table.php`

Columns:

- `id UUID PK`.
- `first_name varchar(100) NN`, `last_name varchar(100) NN`.
- `email varchar(255) NN`, normalised to lowercase by APP.
- `phone_e164 varchar(32) NULL`.
- `password varchar(255) NULL`; nullable for social-only accounts until a password is set.
- `preferred_locale varchar(10) NN default 'en'`.
- `timezone varchar(64) NN default 'UTC'`.
- `status varchar(40) NN default 'active'`.
- `email_verified_at`, `phone_verified_at`, `last_login_at`, `last_seen_at` nullable timestamps.
- `remember_token varchar(100) NULL`.
- `created_at`, `updated_at`, `deleted_at`.

Keys: `UQ(email)`, conditional uniqueness of non-null phone enforced by MySQL unique index, `IDX(status, created_at)`.

Deletion: users with history are suspended/anonymised, not physically deleted. The nullable password supports SSO without creating fake passwords.

### `002_create_authentication_support_tables.php`

Tables and columns:

- `password_reset_tokens`: `email PK`, `token`, `created_at`.
- `sessions`: Laravel session ID PK, nullable `user_id FK users N`, IP, user agent, payload, last activity; indexes on user and last activity.
- `personal_access_tokens`: UUID PK, `tokenable_type`, `tokenable_id`, name, token hash unique, abilities text, last used, expires, timestamps.

Token polymorphism is framework infrastructure; business ownership is resolved through the authenticated user/membership, never trusted from token input.

### `003_create_queue_cache_and_lock_tables.php`

Laravel standard `jobs`, `job_batches`, `failed_jobs`, `cache`, and `cache_locks`. Failed job payloads may contain sensitive data, so APP redacts job arguments and production access is restricted.

### `004_create_user_identities_table.php`

Columns: `id UUID`, `user_id FK users R`, `provider varchar(40)`, `provider_subject varchar(255)`, nullable provider email/name/avatar metadata, `last_used_at`, timestamps.

Keys: `UQ(provider, provider_subject)`, `UQ(user_id, provider)` where only one identity per provider is allowed, `IDX(user_id)`.

### `005_create_user_mfa_methods_table.php`

Columns: `id UUID`, `user_id FK users C`, `type varchar(30)`, encrypted `secret text`, encrypted `recovery_codes text`, `confirmed_at`, `last_used_at`, timestamps.

Keys: `IDX(user_id, type)`. Secrets must never enter audit before/after JSON.

### `006_create_user_devices_table.php`

Columns: `id UUID`, `user_id FK users C`, device fingerprint hash, name, platform, push token encrypted/nullable, trusted timestamp, last IP, last seen, revoked timestamp, timestamps.

Keys: `UQ(user_id, fingerprint_hash)`, `IDX(user_id, revoked_at)`.

## 6. Migration batch B — businesses, membership and permissions

### `010_create_businesses_table.php`

Columns:

- `id UUID PK`.
- legal/display names, unique platform slug, registration number nullable.
- business type, country code, default currency, IANA timezone.
- email, phone, website nullable.
- tax identifier encrypted/nullable and tax settings JSON nullable.
- subscription state/reference nullable; detailed billing becomes a later table.
- `verification_status`, `status`, `version`.
- `created_by/updated_by FK users N`, timestamps, soft deletes.

Keys: `UQ(slug)`, `IDX(country_code, status)`, `IDX(verification_status)`, `UQ(id, id)` is unnecessary because PK already unique; composite tenant references instead use parent composite indexes where required.

### `011_create_business_addresses_table.php`

Columns: `id`, `business_id FK R`, type, address lines, city, state, postal code, country code, latitude/longitude decimals nullable, `is_primary`, timestamps.

Keys: `IDX(business_id, type)`. APP permits only one primary address of each type because MySQL cannot express a portable partial unique index.

### `012_create_business_settings_table.php`

Columns: `id`, `business_id FK C`, namespace, key, value JSON, value type, `is_encrypted`, schema version, timestamps.

Keys: `UQ(business_id, namespace, key)`. APP validates the JSON against a versioned settings definition and encrypts sensitive values.

### `013_create_branches_table.php`

Columns: standard tenant columns plus code, name, address link nullable, timezone/currency overrides nullable, manager membership nullable.

Keys: `UQ(business_id, code)`, `IDX(business_id, status)`. Manager FK is added after memberships exist to avoid a dependency cycle, using migration `018`.

### `014_create_business_memberships_table.php`

Columns: `id`, `business_id FK businesses R`, `user_id FK users R`, membership status, job title, invited/accepted/joined/left timestamps, inviter nullable FK users N, `is_owner`, timestamps, soft deletes.

Keys: `UQ(business_id, user_id)`, `IDX(user_id, status)`, `IDX(business_id, status)`, `UQ(business_id, id)` for tenant-composite child references.

Rule: exactly one effective owner is protected by an ownership-transfer transaction and approval, not a simple unique key, because ownership history and temporary states are required.

### `015_create_roles_and_permissions_tables.php`

- `permissions`: UUID, unique `name`, module, description, risk level, status, timestamps.
- `roles`: UUID, nullable `business_id FK businesses C` (`NULL` for platform template), name, slug, description, `is_system`, status, timestamps.
- `role_permissions`: `role_id FK C`, `permission_id FK C`, granted timestamp/actor; composite PK/UQ.
- `membership_roles`: `membership_id FK C`, `role_id FK R`, assigned/expiry timestamps, assigned actor; UQ on pair.

APP ensures a business membership receives only a platform role or a role owned by the same business.

### `016_create_membership_permission_overrides_table.php`

Columns: `id`, business, membership, permission, effect (`allow|deny`), reason, effective/expiry dates, approving membership nullable, timestamps.

Keys: `UQ(membership_id, permission_id, effective_from)`, tenant/status indexes. High-risk allows require approval and audit.

### `017_create_business_verification_and_subscription_tables.php`

- `business_verifications`: business FK R, submission number, verification type, status, submitted/reviewed actors, checklist snapshot, decision/rejection reason, submitted/reviewed/expiry timestamps. A decided submission is immutable; resubmission creates a new record.
- `subscription_plans`: platform-owned code/name, billing period, currency/base price, trial days, status and version.
- `subscription_plan_features`: plan, feature code, enabled flag and optional numeric/JSON limit; UQ plan/feature.
- `business_subscriptions`: business, plan, provider/customer/subscription references, status, trial/current-period/cancelled timestamps, price/currency snapshot.
- `subscription_usage`: subscription, feature code, period start/end, quantity and measured timestamp; UQ subscription/feature/period.

Keys enforce unique plan codes, business subscription/provider references and verification submission numbers. APP ensures only one effective subscription per business at a time. Subscription records control commercial access, while RBAC controls whether a particular person may use an enabled capability.

### `018_add_branch_manager_foreign_key.php`

Adds nullable `manager_membership_id -> business_memberships.id N` after both tables exist. APP confirms manager and branch belong to the same business.

### `019_create_impersonation_sessions_table.php`

Columns: UUID, platform admin user FK R, target user FK R, target business nullable FK R, reason, approval reference nullable, started/ended/terminated timestamps, IP, user agent, correlation ID.

No updates overwrite the original reason. Every action during impersonation retains both real actor and effective actor in audit data.

## 7. Migration batch C — teams and suppliers

### `020_create_departments_table.php`

Columns: standard tenant identity, code, name, manager employee nullable (added later), status, timestamps. Keys `UQ(business_id, code)`.

### `021_create_employees_table.php`

Columns: standard tenant columns, `membership_id FK business_memberships R`, employee number, department nullable FK departments N, employment type, start/end dates, emergency contact JSON encrypted, status.

Keys: `UQ(business_id, membership_id)`, `UQ(business_id, employee_number)`, `IDX(business_id, department_id, status)`.

An employee is a business employment profile; authentication stays in users and authorisation stays in memberships. Former employees remain referenced.

### `022_add_department_manager_foreign_key.php`

Adds nullable manager employee FK with `SET NULL`; APP checks same business.

### `023_create_employee_availability_table.php`

Columns: UUID, business, employee FK R, start/end datetime, timezone, availability type/status, reason, recurrence rule nullable, actor/timestamps.

Keys: `IDX(business_id, employee_id, starts_at, ends_at)`. APP checks end after start and detects conflicting availability where configured.

### `024_create_skills_and_employee_skills_tables.php`

- `skills`: business nullable for platform catalogue, code/name/status.
- `employee_skills`: employee/skill, proficiency, verified by/date, expiry, UQ pair.

### `025_create_suppliers_table.php`

Columns: standard tenant columns, code, legal/display name, category, contact fields, address JSON, tax number encrypted, payment details encrypted/tokenised, rating nullable, status.

Keys: `UQ(business_id, code)`, `IDX(business_id, category, status)`.

## 8. Migration batch D — properties and bookable units

### `030_create_property_types_table.php`

Columns: UUID, nullable business for custom types, code, name, description, active flag, timestamps. Keys: uniqueness on platform code and business/code enforced by APP plus indexes because nullable composite uniqueness has special MySQL behavior.

### `031_create_properties_table.php`

Columns:

- standard tenant columns;
- nullable `branch_id FK branches N`;
- `property_type_id FK property_types R`;
- code, name, slug, description/summary;
- capacity, bedrooms, bathrooms, floor count nullable;
- timezone, default currency;
- `owner_membership_id` and `manager_membership_id` nullable FKs N;
- verification, publication, maintenance and readiness statuses;
- check-in/out local times nullable;
- marketplace enabled flag;
- timestamps and soft deletion.

Keys: `UQ(business_id, code)`, `UQ(business_id, slug)`, `UQ(business_id, id)`, indexes for business/status, branch/status, verification/publication, manager/status.

`RESTRICT` from bookings prevents physical property deletion. APP enforces that branch, owner and manager share the property business.

### `032_create_property_addresses_table.php`

Columns: UUID, business, property FK R, address lines, locality/city/state/postcode/country, latitude `decimal(10,7)`, longitude `decimal(10,7)`, access instructions encrypted/nullable, timestamps.

Keys: `UQ(property_id)` for one current physical address, geo/search indexes selected after query benchmarking.

### `033_create_property_units_table.php`

Columns: standard tenant columns, `property_id FK R`, unit code/name, unit type, floor nullable, capacity, bedroom/bathroom counts, description, default flag, sellable flag, readiness status.

Keys: `UQ(property_id, unit_code)`, `UQ(business_id, id)`, `IDX(business_id, property_id, status)`, `IDX(property_id, sellable, readiness_status)`.

Each single apartment receives one default unit. Bookings point to units so a hotel can book rooms independently without later changing the booking foreign key.

### `034_create_amenities_and_property_amenity_table.php`

- `amenities`: UUID, category, code unique, name, icon, value type, status.
- `property_amenity`: business, property FK C, nullable unit FK C, amenity FK R, value JSON nullable, timestamps.

Keys: UQ on `(property_id, unit_id, amenity_id)` with APP handling nullable unit semantics. APP confirms a supplied unit belongs to the property.

### `035_create_property_house_rules_table.php`

Columns: UUID, business, property FK C, nullable unit FK C, rule type, title, description, structured value JSON, sort order, active flag, actor/timestamps.

APP validates structured values by rule type.

### `036_create_property_check_and_pricing_rules.php`

- `property_check_rules`: property/unit, minimum/maximum stay, advance notice hours, turnover minutes, local check-in/out windows, effective dates.
- `property_pricing_rules`: property/unit, name/type, amount or percentage, currency, start/end dates, weekdays bitmask/JSON, minimum stay, priority, stack behavior, active flag.

Keys: date/query indexes on business/property/unit/effective range. APP prevents invalid ranges, mixed amount/percentage definitions and ambiguous same-priority collisions.

### `037_create_property_assignments_table.php`

Columns: UUID, business, property FK R, membership FK R, responsibility type, starts/ends, status, actor/timestamps.

Keys: `IDX(business_id, property_id, status)`, `IDX(membership_id, status)`. Same-business and overlapping primary-manager rules are APP enforced.

### `038_create_property_verifications_table.php`

Columns: UUID, business, property FK R, submission number, status, submitted/reviewed users nullable, submitted/reviewed times, checklist snapshot JSON, decision reason, expiry nullable, timestamps.

Keys: `UQ(business_id, submission_number)`, `IDX(property_id, status, created_at)`. Verification records are never edited after decision; resubmission creates a new record.

### `039_create_property_status_history_table.php`

Immutable columns: UUID, business, property FK R, status type, previous/new values, reason, actor nullable, source, correlation ID, occurred timestamp.

Keys: `IDX(property_id, occurred_at)`, `IDX(business_id, status_type, new_value)`. No update/delete path in the application.

## 9. Migration batch E — files and documents

### `040_create_files_table.php`

Columns: UUID, business FK R, storage disk, object key, original name, MIME, extension, size unsigned big integer, SHA-256 checksum, visibility, sensitivity, malware-scan status/result, uploader nullable, uploaded timestamp, retention/deletion timestamps.

Keys: `UQ(storage_disk, object_key)`, `IDX(business_id, checksum)`, `IDX(business_id, scan_status)`. Physical deletion is a controlled retention job, not a cascade.

### `041_create_documents_table.php`

Columns: standard tenant identity, type, title, description, `owner_type` and `owner_id`, current version number, sensitivity, expiry date, retention class, legal-hold flag, status.

Keys: `IDX(business_id, owner_type, owner_id)`, `IDX(business_id, type, status)`, `IDX(business_id, expires_on)`.

The owner uses a controlled polymorphic map. APP checks owner existence, same business and permission. MySQL cannot create a normal FK to multiple possible tables.

### `042_create_document_versions_table.php`

Immutable columns: UUID, business, document FK R, file FK R, version number, change note, created by, created timestamp.

Keys: `UQ(document_id, version_number)`, `UQ(document_id, file_id)`. A database transaction adds the version and updates the document's current version.

### `043_create_media_assets_and_entity_media.php`

- `media_assets`: UUID, business, file FK R, type, width/height/duration, processing status, alt text, metadata JSON, timestamps.
- `entity_media`: UUID, business, media asset FK R, `owner_type/id`, role, sort order, primary flag, timestamps.

APP permits one primary item per owner/role and validates same-business polymorphic ownership.

### `044_create_document_access_logs.php`

Immutable columns: UUID, business, document/version, user nullable, action, source, IP/device hash, occurred timestamp. `RESTRICT` keeps evidence even after access is revoked.

## 10. Migration batch F — guests

### `050_create_guests_table.php`

Columns: standard tenant columns, title, first/middle/last names, display name, primary email/phone normalised, nationality code, preferred language, date of birth nullable/encrypted as required, identity verification status, risk/loyalty placeholders, privacy state, last stay date.

Keys: `UQ(business_id, id)`, indexes on normalised email, phone and display name. Email/phone are not globally unique because families/shared contacts and incomplete profiles exist; duplicates become candidates.

### `051_create_guest_contacts_table.php`

Columns: UUID, business, guest FK R, type, label, value encrypted where needed, normalised value/hash for search, primary/verified flags, verified time, timestamps.

Keys: `IDX(business_id, type, normalised_hash)`, `IDX(guest_id, type, is_primary)`. APP ensures one primary per type.

### `052_create_guest_identities_table.php`

Columns: UUID, business, guest FK R, identity type/country, number encrypted, last four, verification provider/reference, status, verified/expiry dates, document FK nullable R, timestamps.

Keys: `IDX(business_id, identity_type, number_hash)`, `IDX(guest_id, status)`. Access is separately permission-controlled.

### `053_create_guest_preferences_consents_and_notes.php`

- `guest_preferences`: guest, key, typed value JSON, source, confidence, timestamps; UQ guest/key.
- `guest_consents`: guest, purpose, channel, state, policy version, evidence/source, granted/revoked timestamps; append history rather than overwrite evidence.
- `guest_notes`: guest, author membership, category, content, visibility, pinned flag, timestamps/soft delete.

### `054_create_guest_emergency_contacts.php`

Columns: UUID, business, guest FK C, name, relationship, phone/email encrypted as appropriate, primary flag, timestamps.

### `055_create_guest_merge_tables.php`

- `guest_merge_candidates`: business, first/second guest FKs R, score, matched attributes JSON, status, resolution actor/time.
- `guest_merges`: immutable business, source guest FK R, survivor guest FK R, actor, reason, field-resolution snapshot, occurred timestamp.

Keys canonicalise guest pairs in APP so A/B and B/A cannot both be open. Merging repoints allowed foreign keys transactionally but preserves the source profile as merged, never deletes history.

## 11. Migration batch G — bookings and availability

### `060_create_booking_sources_and_rate_plans.php`

- `booking_sources`: UUID, business nullable, code, name, type, integration provider nullable, commission defaults, status.
- `rate_plans`: standard tenant columns, property/unit FKs, code/name, meal/cancellation/deposit policy JSON snapshots, currency, base rate, effective dates, status.

Keys: UQ business/code with platform-default handling; UQ property/code for rate plans; date/status indexes.

### `061_create_bookings_table.php`

Columns:

- standard tenant columns without ordinary soft delete;
- `property_id FK properties R`, `property_unit_id FK property_units R`;
- `primary_guest_id FK guests R`, `booking_source_id FK booking_sources R`, `rate_plan_id FK rate_plans N`;
- business-unique reference and nullable external reference;
- arrival/departure dates and scheduled check-in/out UTC timestamps nullable;
- adult/child/infant counts;
- booking and payment statuses;
- currency, subtotal, discount, tax, fee, total, amount paid/due Money snapshots;
- special-request summary and internal note nullable;
- source/quote metadata JSON;
- hold expiry nullable, confirmed/cancelled/completed timestamps;
- version and actor/timestamps.

Keys: `UQ(business_id, reference)`, `UQ(business_id, id)`, `IDX(business_id, property_unit_id, status, arrival_date, departure_date)`, guest/date, source/date and status/date indexes.

APP enforces same-business connections, unit/property consistency, departure after arrival, nonnegative totals and allowed lifecycle transitions. No FK can prevent date-range overlap.

### `062_create_booking_guests_table.php`

Columns: UUID, business, booking FK R, guest FK R, role, lead flag, arrival/departure nullable, timestamps.

Keys: `UQ(booking_id, guest_id)`, indexes by guest and booking. APP ensures exactly one lead/primary match and same business.

### `063_create_booking_charges_table.php`

Columns: UUID, business, booking FK R, type, description, quantity decimal(12,4), unit amount, subtotal, tax, discount, total, currency, source type/id nullable, pricing-rule snapshot JSON, posted timestamp, actor/timestamps.

Keys: booking/type index. Posted charges are corrected through credit/reversal lines, not deleted.

### `064_create_booking_status_history.php`

Immutable: UUID, business, booking FK R, from/to status, reason code/text, actor/effective actor nullable, source, metadata, correlation ID, occurred timestamp.

Keys: booking/time and business/to-status/time indexes.

### `065_create_booking_changes_cancellations_extensions.php`

- `booking_date_changes`: old/new intervals, price delta, reason, approval, actor, occurred time.
- `booking_cancellations`: initiator type, policy snapshot, reason, fee/refundable amount, approval/status, timestamps.
- `booking_extensions`: requested/new departure, price delta, status, approval and timestamps.

All reference booking with `RESTRICT`; accepted changes occur in the same transaction as booking/version/history updates.

### `066_create_booking_holds_table.php`

Columns: UUID, business, property/unit, nullable user/guest, start/end dates, token hash, status, expires at, converted booking nullable, idempotency key, timestamps.

Keys: unit/status/interval, expiry/status, UQ token hash, UQ business/idempotency key. Expired holds are ignored and cleaned asynchronously.

### `067_create_availability_blocks_table.php`

Columns: standard tenant identity, property/unit FKs, block type, start/end dates, source type/id, reason, external source/reference nullable, status, timestamps.

Keys: unit/status/date interval, property/date, external source/reference. APP checks overlap rules and same-property unit.

### `068_create_availability_daily_table.php`

Columns: business, property, unit, calendar date, state, booking/block source nullable, minimum stay/rate snapshot optional, generated timestamp.

Keys: composite PK/UQ `(property_unit_id, calendar_date)`, tenant/date/state index. This is a rebuildable projection; cascading its rows with a never-used unit is safe, but ordinary property deletion remains restricted elsewhere.

### `069_create_special_requests_table.php`

Columns: UUID, business, booking FK R, type, description, priority, assignee nullable, fulfilment status, due/completed times, charge nullable, timestamps.

## 12. Migration batch H — finance

### `070_create_finance_reference_tables.php`

- `expense_categories`: business nullable, code/name, parent nullable, default ledger account added later, status.
- `cost_centres`: business, nullable property/branch, code/name/status.
- `tax_rates`: business, jurisdiction, code/name, percentage decimal(9,6), inclusive flag, effective dates/status.
- `payment_methods`: platform/business code, provider requirement and status.

Keys are business/code unique with explicit handling of platform defaults.

### `071_create_invoices_table.php`

Columns: standard tenant identity without soft deletion, booking nullable FK R, guest nullable FK R, property nullable FK R, invoice number, type, issue/due dates, currency, subtotal/discount/tax/total/paid/balance Money, status, issued/void timestamps, billing-address and tax snapshots JSON.

Keys: `UQ(business_id, invoice_number)`, booking/status, guest/status and due-date/status indexes. APP validates totals from lines and requires approval to void posted invoices.

### `072_create_invoice_lines_table.php`

Columns: UUID, business, invoice FK R, nullable booking charge FK R, type/description, quantity, unit price, discount, tax, line total, tax snapshot and sort order.

Keys: invoice/order and charge indexes. Posted lines are immutable.

### `073_create_payments_table.php`

Columns: standard tenant identity without soft deletion, nullable booking/guest/invoice context, payment method, direction, type, provider, provider account/reference, business reference, amount/currency, status, received/verified/reconciled timestamps, verifier nullable, idempotency key, failure code/message redacted, metadata.

Keys: `UQ(business_id, reference)`, conditional provider-reference uniqueness expressed as `UQ(business_id, provider, provider_reference)`, `UQ(business_id, idempotency_key)`, booking/status/date and status/date indexes.

APP prevents amount mutation after success and ensures allocation totals never exceed available payment amount.

### `074_create_payment_attempts_and_allocations.php`

- `payment_attempts`: immutable payment FK, attempt number, provider request ID, amount, status, sanitised request/response JSON, started/completed timestamps; UQ payment/attempt.
- `payment_allocations`: immutable payment FK, invoice FK, optional invoice line, amount/currency, allocated/reversed timestamps, reversal link nullable.

Allocation reversals are new records, not deletes.

### `075_create_refunds_table.php`

Columns: standard tenant record without soft delete, original payment FK R, booking nullable, refund reference, provider reference nullable, amount/currency, reason, status, requester/approver nullable, requested/approved/processed timestamps, failure detail redacted, idempotency key.

Keys: UQ business/reference, UQ business/idempotency key, original payment/status and status/date indexes. APP ensures cumulative successful refunds do not exceed the successful captured payment.

### `076_create_credit_notes_table.php`

Columns: UUID, business, invoice FK R, refund nullable FK R, number, issue date, currency, amount, reason, status, actor/timestamps. Keys UQ business/number. Credit-note lines may reuse a dedicated `credit_note_lines` value table rather than JSON.

### `077_create_expenses_table.php`

Columns: standard tenant identity, property/booking nullable, supplier/category/cost centre, reference, description, expense date, amount/tax/total and currency, tax rate nullable, recurrence rule nullable, payment status, approval status, document nullable, timestamps.

Keys: UQ business/reference when non-null, category/date, supplier/date, property/date, approval status/date. Same-business checks are APP enforced.

### `078_create_financial_accounts_table.php`

Columns: UUID, business, account code/name/type/subtype, parent account nullable self-FK R, currency nullable, system flag, normal balance, status, timestamps.

Keys: `UQ(business_id, account_code)`, parent/status index. Used accounts cannot be deleted.

### `079_create_journal_entries_and_lines.php`

- `journal_entries`: immutable UUID, business, journal number, posting date, description, source type/id, currency, status, reversal-of nullable self-FK R, posted by/time, correlation/idempotency keys.
- `journal_lines`: immutable UUID, business, journal entry FK R, account FK R, debit and credit Money defaults 0, currency, exchange-rate snapshot, property/booking/cost-centre dimensions nullable, memo.

Keys: UQ business/journal number, UQ business/source type/source ID/source event, account/date via header projection/index strategy, dimension indexes.

APP posts all lines in one transaction, requires exactly one of debit/credit to be positive, and requires entry debits equal credits. A draft may be rebuilt; a posted entry is append-only and corrected by a reversing entry.

### `080_create_reconciliations_and_settlements.php`

- `reconciliations`: account, period, opening/closing balance, status, reconciler and completed time.
- `reconciliation_items`: reconciliation, payment/journal/external reference, amount, match status and confidence.
- `settlements`: provider/channel, period, gross/fees/net/currency, external reference/status.
- `settlement_items`: settlement to payments/refunds with amount.

Keys enforce one reconciliation per account/period and one provider settlement reference per business.

## 13. Migration batch I — workflows and operations

### `090_create_workflow_templates_and_steps.php`

- `workflow_templates`: UUID, business nullable for platform template, code/name/category, trigger event, version, definition status, effective dates.
- `workflow_template_steps`: template FK C, step key/name/type, sequence, dependency keys JSON, default assignee rule, SLA minutes, required permission, configuration JSON.

Keys: UQ template/step key and template/sequence. Published template versions are immutable; edits clone a new version.

### `091_create_workflow_instances_and_step_instances.php`

- `workflow_instances`: business, template/version, source type/id, property/booking nullable, status, started/due/completed times, context snapshot.
- `workflow_step_instances`: instance, template step key, status, assigned membership nullable, due/start/completion, result JSON, attempt.

Keys support business/status/due and source lookup. APP validates polymorphic source and template tenant access.

### `092_create_tasks_table.php`

Columns: standard tenant identity, workflow/step nullable, property FK R, booking nullable FK R, task type, title/description, priority/status, current assignee membership/employee nullable, due/start/completed/verified timestamps, recurrence rule nullable, creation source, reason required for manual creation, version.

Keys: business/status/due, assignee/status/due, property/status, booking/status, workflow step. Completed tasks are read-only except append-only amendments/events.

### `093_create_task_checklists_assignments_and_evidence.php`

- `task_checklist_items`: task, template key, label, required/evidence flags, state, completed actor/time, sort order.
- `task_assignments`: immutable task, from/to membership, reason, actor/time.
- `task_evidence`: immutable task/checklist item, file FK R, type, captured actor/time, optional consented geo/device metadata.

### `094_create_cleaning_jobs.php`

One-to-one task extension: `task_id UQ FK R`, business/property/booking, cleaning type, before/after condition, supply/linen states, readiness result, cleaner and inspector nullable, timestamps. APP requires the linked task type to be cleaning.

### `095_create_inspections_and_items.php`

- `inspections`: business, task/property/booking, inspector, type, score, outcome, started/completed/verified times, follow-up task nullable.
- `inspection_items`: inspection, checklist key/category, expected condition, result, score, notes, evidence requirement.

Keys: inspection/task unique when one inspection per task, property/date/outcome indexes. Failed outcomes create follow-up work idempotently.

### `096_create_maintenance_requests_and_work_logs.php`

- `maintenance_requests`: business, property/unit/asset/booking nullable, source type/id, category, severity, title/description, status, reporter/assignee/supplier, SLA/due/resolved/closed times, estimated/actual cost.
- `maintenance_work_logs`: immutable request, employee/supplier, action, started/ended, labour/parts cost, notes, evidence.

Keys: property/status/severity, assignee/status/due, asset/status. APP requires at least one valid source and same-business links.

### `097_create_incidents_table.php`

Columns: standard tenant record, property/booking/guest nullable, type/severity, occurred/reported times, description, immediate action, status, reporter/owner, resolution, privacy classification and authority-reference nullable.

Sensitive incidents receive restricted permissions and audit-on-view.

### `098_create_approval_definitions_requests_and_steps.php`

- `approval_definitions`: business, action code, name, priority, condition JSON, level count, active/effective dates.
- `approval_definition_steps`: definition, level, approver role/permission/user rule, SLA/escalation.
- `approval_requests`: business, definition, subject type/id, requester, subject snapshot, amount/currency nullable, status, expires/completed.
- `approval_steps`: request, level, assigned approver, status, decision/reason/timestamps.

APP validates controlled polymorphic subjects and executes the approved action through the original domain service, never by directly updating arbitrary tables.

## 14. Migration batch J — assets and inventory

### `100_create_asset_categories_and_assets.php`

- `asset_categories`: business, code/name, default maintenance interval, status.
- `assets`: standard tenant identity, property/unit, category, supplier nullable, asset tag, serial, purchase/warranty dates, purchase/replacement values/currency, condition/status, maintenance schedule summary.

Keys: UQ business/asset tag, optional business/serial index, property/status and warranty expiry indexes. Purchased assets are retired, never deleted.

### `101_create_asset_condition_and_maintenance_schedules.php`

- `asset_condition_history`: immutable asset, condition, score, source type/id, notes, actor/time.
- `asset_maintenance_schedules`: asset, workflow template, interval/configuration, last/next due, status.

### `102_create_inventory_locations_and_items.php`

- `inventory_locations`: business, property nullable, code/name/type/status.
- `inventory_items`: business, property nullable, SKU, name/category/unit, reorder point/quantity, preferred supplier, status.

Keys: UQ business/location code, UQ business/SKU, property/status and reorder query indexes.

### `103_create_inventory_movements.php`

Immutable columns: UUID, business, item, from/to locations nullable, movement type, quantity `decimal(14,4)`, unit cost/currency nullable, source type/id, reason, approval nullable, actor/time, reversal-of nullable.

APP requires direction appropriate to type, prevents unapproved negative stock where configured, and reverses via new movement. Balance is the sum of movements, optionally projected.

### `104_create_inventory_counts_and_lines.php`

- `inventory_counts`: location, scheduled/started/completed, status, counter/approver.
- `inventory_count_lines`: count/item, expected/actual/variance, adjustment movement nullable.

Keys: UQ count/item. Completing an approved count writes variance movements transactionally.

## 15. Migration batch K — calendar, marketplace and reviews

### `110_create_calendar_events.php`

Columns: UUID, business, property/unit nullable, source type/id, event type, title, start/end UTC, all-day, timezone, status, visibility, assignee membership nullable, metadata, timestamps.

Keys: business/start/end, property/start/end, assignee/start/status, UQ source type/source ID/event type where appropriate. APP controls source map. Source-backed events are projections and cannot directly mutate their booking/task.

### `111_create_calendar_recurrence_exceptions_and_conflicts.php`

- recurrence rules linked to event with timezone/pattern/end;
- occurrence exceptions by original occurrence timestamp;
- conflicts with first/second source, type/severity, detected/resolved data.

Conflict uniqueness includes a stable conflict fingerprint to avoid repeated open alerts.

### `112_create_marketplace_listings.php`

Columns: UUID, business/property UQ FK R, slug unique platform-wide, title/summary/description snapshots, publication status, instant-book flag, ranking score projection, published/suspended times, version/timestamps.

Keys: UQ property, UQ slug, status/ranking and business/status. APP permits publication only after property verification, required media/rules and sellable units.

### `113_create_listing_units_and_booking_requests.php`

- `listing_units`: listing/unit/rate plan, public name, occupancy, active/order; UQ listing/unit/rate.
- `booking_requests`: listing/unit, public user/guest nullable, dates/counts, quote snapshot and currency, status, expires, converted booking nullable, idempotency key.

Confirmation still goes through Booking's authoritative lock/overlap service.

### `114_create_favourites.php`

Columns: UUID, user FK C, listing FK C, created timestamp. `UQ(user_id, listing_id)`. Favourites have no historical financial meaning, so cascade is safe.

### `115_create_reviews_invitations_and_responses.php`

- `review_invitations`: booking UQ, guest, token hash UQ, expiry/use timestamps.
- `reviews`: business, booking UQ, property, guest, overall and component scores, title/content, verification/moderation status, sentiment metadata, published time.
- `review_responses`: review, business, author membership, content, moderation status/timestamps.
- `review_moderation_actions`: immutable review, moderator, action/reason/time.

APP verifies completed stay, score range and same booking/property/guest connection.

## 16. Migration batch L — messaging, notifications and integrations

### `120_create_conversations_and_messages.php`

- `conversations`: business, booking/guest nullable, type, subject, visibility, status, last message time.
- `conversation_participants`: conversation, participant type/id, role, joined/left; controlled polymorphism.
- `messages`: business, conversation, sender type/id, channel, direction, content, internal flag, external ID, sent/delivered/read/failed timestamps, status.

Keys support conversation/time, booking, guest and external channel IDs. Internal messages are never returned through guest API resources.

### `121_create_notification_preferences_templates_notifications.php`

- preferences: membership/user, category, channel, enabled, quiet hours/timezone; UQ recipient/category/channel.
- templates: business nullable, event code/channel/locale/version, subject/body, status; UQ scope/event/channel/locale/version.
- notifications: business, recipient user/membership, event code/category, title/body/data, source type/id, read/archive/expiry timestamps.
- deliveries: notification, channel/provider, destination masked, attempt, status, provider reference, timestamps/error.

### `122_create_integration_connections.php`

Columns: standard tenant identity, provider/category/name, encrypted credentials/configuration, granted scopes, webhook secret encrypted, sync direction, last sync/health/error, token expiry, status.

Keys: UQ business/provider/name, business/category/status. Secrets are excluded from model array/JSON and audit values.

### `123_create_external_mappings_and_sync_runs.php`

- mappings: connection, entity type/internal ID/external ID, version/etag, last synced, metadata; UQ connection/entity type/internal ID and connection/entity type/external ID.
- sync runs: connection, direction/resource, cursor, start/end, status, received/created/updated/skipped/failed counts, error summary.
- integration conflicts: connection, entity, field/value snapshots, policy/status/resolution.

### `124_create_webhook_inbox_outbox_and_idempotency.php`

- `webhook_inbox`: connection/provider, external event ID, event type, signature status, raw payload encrypted/redacted as required, received/processed timestamps, attempts/status/error. UQ provider/external event.
- `webhook_outbox`: business, destination/connection, event UUID/type, payload, status, attempts, next attempt, delivered time. UQ destination/event UUID.
- `idempotency_keys`: business, client identity, key, request method/path/hash, response code/body, processing/completed/expiry times. UQ business/client/key.
- `outbox_messages`: event UUID UQ, business, event type/version, subject, payload, occurred/published/failed times, attempts.

These tables are the reliability boundary between committed MySQL data and external/queued side effects.

## 17. Migration batch M — governance, reporting and AI

### `130_create_audit_and_activity_events.php`

- `audit_events`: immutable UUID, business nullable for platform action, real/effective actor nullable, action, entity type/id, redacted before/after JSON, source, IP, device, correlation/causation IDs, occurred timestamp.
- `activity_events`: immutable user-facing timeline, business, entity/source, category/title/summary, actor, visibility, occurred timestamp.

Keys: business/entity/time, business/action/time, actor/time, correlation ID. Application DB credentials receive no update/delete permission on audit rows in production.

### `131_create_retention_privacy_and_legal_hold_tables.php`

- retention policies by business/data class/jurisdiction/version;
- privacy requests with verified requester, type, scope, due/status/decision;
- legal holds with scope/reason/authority/effective/release;
- data export jobs with private file and expiry.

APP checks legal holds before erasure or physical file cleanup.

### `132_create_kpi_definitions_and_metric_snapshots.php`

- `kpi_definitions`: code UQ, name, formula version, dimensions, drill-down resolver, status.
- `daily_business_metrics`: business/date/metric/version/value/currency/dimensions hash; UQ business/date/metric/version/dimensions hash.
- `daily_property_metrics`: adds property to the unique key.
- `report_exports`: business, requester, report code, filter snapshot, status, file, expiry.

Snapshots are rebuildable; authoritative facts remain in booking/finance/operations tables.

### `133_create_ai_model_runs_and_outputs.php`

- `ai_model_runs`: business, provider/model, purpose, prompt/template version, input evidence references, safety/redaction state, latency/tokens/cost/currency, trace ID/status.
- `ai_briefs`: business/date UQ, model run, content, evidence JSON, status.
- `ai_insights`: business, subject type/id, category, observation, severity, confidence decimal, evidence, status/expiry.
- `ai_recommendations`: business, subject, title/description/rationale, proposed action JSON, confidence/impact/priority, approval requirement, status/snooze/expiry, model run.
- `ai_recommendation_feedback`: recommendation, membership, action/rating/reason, timestamp.
- `ai_conversations/messages`: business/membership conversation and messages with model run/evidence.

APP validates confidence range, permissions on cited subjects and automation policy before executing an action.

### `134_create_automation_policies_and_runs.php`

- policies: business, action code, subject scope, condition JSON, approval threshold, active/effective dates, version.
- runs: policy, trigger event, subject, decision, explanation, approval request nullable, started/completed/status.

Runs are immutable decision evidence. A policy never bypasses the target domain service.

## 17A. Later-phase migration map

The following migrations are intentionally not part of the Phase 1 database build. They are named now to prove that the Phase 1 keys and relationships can support the later SRS roadmap without destructive redesign. Each later phase will receive its own reviewed field-level specification before these files are generated.

### Phase 2 — growth platform

- `200_create_workflow_drafts_and_builder_versions.php`: visual workflow drafts, nodes, edges, validation results and publication versions. Published definitions continue feeding the existing workflow-template tables.
- `201_create_teams_shifts_and_rosters.php`: operational teams, memberships, shifts, rota assignments and coverage requirements linked to existing employees/properties.
- `202_create_mobile_offline_sync_tables.php`: per-device sync cursors, change sets, conflict records and acknowledgement checkpoints. Operational truth stays in the existing task/inspection/maintenance tables.
- `203_create_qr_resources_and_scans.php`: revocable QR resource tokens, property/asset/task destinations and immutable scan events.
- `204_create_promotions_coupons_and_redemptions.php`: promotion/coupon rules, eligible properties/rate plans, codes, budgets and immutable redemption records linked to booking charges.
- `205_create_referral_programmes_and_rewards.php`: programme, referrer/referee relationship, qualification event, reward and reversal records.
- `206_create_advanced_report_definitions.php`: business-defined reports, columns, filters, schedules and recipients; execution continues through report exports.

Branches already exist in the foundation, so Phase 2 multi-branch support adds UI, policies and reporting rather than changing every tenant foreign key.

### Phase 3 — enterprise and international platform

- `300_create_organisations_and_business_relationships.php`: enterprise parent organisations, business membership, ownership percentage and effective dates without replacing `business_id` as the isolation boundary.
- `301_create_enterprise_roles_and_permission_policies.php`: organisation role templates and inherited policies, with business-level overrides and complete audit.
- `302_create_approval_delegations_and_escalations.php`: effective delegation, escalation schedules and substitute approvers extending existing approvals.
- `303_create_franchise_models_and_compliance_checks.php`: franchisor/franchisee agreements, standards, inspections and compliance status.
- `304_create_locales_translations_and_content_variants.php`: supported locales and translatable business/property/listing content.
- `305_create_exchange_rates_and_currency_revaluations.php`: immutable rate snapshots, sources and ledger revaluation batches; original transaction currency never changes.
- `306_create_tax_jurisdictions_and_fiscal_documents.php`: jurisdiction rules, registrations, fiscal invoice references and statutory reporting exports.
- `307_create_api_clients_scopes_and_usage.php`: enterprise OAuth/API clients, allowed scopes/IP policies, webhook destinations, usage and throttling dimensions.
- `308_create_benchmark_cohorts_and_anonymised_metrics.php`: consented cohort definitions and aggregated, non-identifying benchmark facts.

### Phase 4 — hospitality ecosystem

- `400_create_vendor_marketplace_profiles_and_services.php`: supplier marketplace profiles, service catalogues, coverage areas, pricing and verification.
- `401_create_service_orders_and_vendor_settlements.php`: property service orders, fulfilment milestones, disputes, commissions and settlement linkage.
- `402_create_insurance_policies_claims_and_events.php`: policy metadata, covered properties/assets, incidents, claim evidence and status history.
- `403_create_smart_devices_locks_and_access_grants.php`: provider devices, property/unit bindings, health events and short-lived booking/staff access grants.
- `404_create_iot_telemetry_and_alert_rules.php`: device measurements, retention tiers, alert rules and generated incidents; high-volume telemetry may move to specialised storage behind the module contract.
- `405_create_energy_accounts_readings_and_recommendations.php`: meters, readings, tariffs and optimisation evidence.
- `406_create_loyalty_programmes_accounts_and_entries.php`: programme rules, guest accounts and immutable earn/redeem/expire/reverse entries.
- `407_create_financing_applications_offers_and_repayments.php`: permission-restricted application, underwriting consent, offers and payment schedule; regulated provider remains authoritative.
- `408_create_recruitment_jobs_candidates_and_applications.php`: business vacancies, consented candidate records, applications and hiring workflow.
- `409_create_academy_courses_enrolments_and_certificates.php`: learning content, enrolment progress, assessment attempts and verifiable certificates.

This map is not permission to create speculative tables today. It is evidence that the chosen Phase 1 identifiers—business, property, unit, guest, booking, membership, employee, supplier, asset, finance and event IDs—are stable anchors for the later ecosystem.

## 18. Connections MySQL can enforce

MySQL foreign keys and constraints will enforce:

- a referenced record exists;
- historical parents cannot be physically removed while referenced;
- unique business references such as booking/invoice/payment numbers;
- one row per simple one-to-one relationship;
- uniqueness of provider event/idempotency identifiers;
- basic nonnegative/range checks where MySQL check constraints are appropriate;
- dependent pivot cleanup only where explicitly safe.

## 19. Connections Laravel must enforce

Laravel transactions, policies and domain actions must enforce:

- all connected records belong to the same business;
- a property unit belongs to the booking property;
- role and property assignment belongs to the active membership/business;
- no booking/hold/block interval conflict;
- allowed lifecycle transition and required permission/approval;
- one current primary contact/address/media item when nullable keys prevent a portable partial unique constraint;
- invoice/charge/payment/refund arithmetic and currency agreement;
- cumulative refunds do not exceed captured payment;
- journal entries balance before posting;
- published financial/operational history is append-only;
- polymorphic owner/source IDs exist and are permitted types;
- workflow step dependencies and template version immutability;
- failed inspections create exactly one required follow-up per event;
- marketplace publication prerequisites;
- verified-stay review linkage;
- AI evidence is tenant- and permission-safe;
- retention and legal-hold rules before anonymisation/deletion.

These rules must live in reusable application/domain actions invoked by web, API, queues and automations. They must not exist only in a controller.

## 20. Transaction boundaries

The following operations require explicit `DB::transaction()` blocks:

- business creation plus owner membership/default roles;
- property creation plus default unit;
- booking confirmation plus conflict recheck/status history/outbox;
- date change plus availability/status/price changes;
- payment success plus allocation/ledger/outbox;
- refund success plus payment status/ledger/outbox;
- invoice posting plus immutable lines/journal;
- journal posting and reversal;
- workflow start plus initial steps/tasks;
- inspection failure plus maintenance/follow-up task;
- approved inventory count plus variance movements;
- guest merge plus foreign-key repointing/history;
- property publication plus listing projection/history;
- approval completion plus approved domain action.

External HTTP calls do not remain open inside a long database transaction. Store intent/outbox state, commit, then call externally in a queued job with idempotency and reconciliation.

## 21. Migration safety rules

### Development

`php artisan migrate:fresh --seed` is permitted only against an explicitly disposable local/testing database. Environment safeguards should refuse suspicious database hosts/names.

### Production

- Run `php artisan migrate --force` during a controlled deployment.
- Back up and verify available disk space first for large alterations.
- Never modify a migration already executed in production.
- Add nullable column/index first, backfill in resumable chunks, validate, then enforce non-null in a later deployment.
- Create large indexes with the safest MySQL method supported by the deployed version.
- Avoid renaming/dropping in the same release that application code stops using a column.
- Use expand/migrate/contract and retain rollback-compatible code during deployment.
- Do not run `migrate:fresh`, `db:wipe`, destructive seeders, or broad table truncation.

## 22. Migration verification checklist

Each generated migration must be checked for:

1. Dependency order and rollback order.
2. Exact matching type/length/collation of every FK pair.
3. Explicit delete behavior.
4. Tenant-leading indexes for actual list/filter queries.
5. Unique keys scoped to the correct business.
6. No accidental cascade into history or finance.
7. MySQL index-name and row-size limits.
8. Decimal precision and currency presence.
9. UTC/local-date/timezone meaning.
10. Data migration/backfill strategy for non-empty tables.
11. `up()` and safe `down()` behavior.
12. Tests on a real MySQL database, not SQLite alone.
13. `php artisan migrate:fresh --seed` on a disposable database.
14. Full rollback/re-migrate during development.
15. Schema inspection confirming indexes and FKs actually exist.

## 23. Deliberate decisions and remaining confirmations

This blueprint deliberately chooses:

- `char(36)` UUIDv7 for readability and maintainability;
- shared-schema business tenancy;
- a property/unit split;
- independent guest profiles;
- booking charge snapshots;
- payment/refund separation;
- double-entry ledger;
- immutable histories/movements/audits;
- explicit availability projections plus authoritative overlap checks;
- controlled polymorphism only for cross-domain attachments/events;
- standard Laravel migration location.

Before generating code, the stakeholder should confirm:

1. Multi-unit property support from Phase 1 (recommended: yes).
2. Initial countries/currencies and statutory invoice/tax fields.
3. Whether subscription billing tables belong in the first foundation batch.
4. Whether employee records can exist without login accounts; this blueprint currently requires membership for staff who operate the platform.
5. Marketplace booking mode at launch.
6. Initial payment/channel/identity providers.

After those decisions, the implementation plan can divide the migration generation into reviewable batches. Each batch will include actual Laravel migration code, models/relations, factories, seeders and MySQL-backed tests.
