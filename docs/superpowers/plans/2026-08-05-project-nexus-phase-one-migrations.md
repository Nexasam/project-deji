# Project Nexus Phase One Migrations Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Convert the approved Project Nexus database blueprint into dependency-safe Laravel 13 migrations that migrate and roll back cleanly on MySQL 8+.

**Architecture:** Keep every migration in Laravel's standard `database/migrations` directory and implement the schema in domain batches. Use UUIDv7-compatible `char(36)` keys, shared-schema business tenancy, explicit foreign-key delete behavior, money as `decimal(19,4)`, string lifecycle statuses and indexes led by `business_id` on tenant-heavy queries.

**Tech Stack:** PHP 8.3+, Laravel 13, MySQL 8+, PHPUnit 12, Blade/Alpine/Axios project scaffold.

## Global Constraints

- Treat MySQL as the authoritative migration-test database; SQLite tests do not prove MySQL foreign keys or index compatibility.
- Do not create Phase 2–4 tables in this implementation.
- Do not introduce MySQL native enums.
- Do not use cascading deletes for bookings, guests, properties, payments, refunds, invoices, journals, tasks, documents, inventory movements or audit history.
- Every foreign-key column must exactly match its referenced `char(36)` UUID type.
- Never edit an executed production migration; later schema adjustment uses a new migration.
- Each batch must pass fresh migration, schema assertions, rollback and re-migration before the next batch.
- Do not commit `.env`, credentials, `vendor`, `node_modules` or generated build output.

---

## File structure

**Modify:**

- `database/migrations/0001_01_01_000000_create_users_table.php` — replace numeric user ID with UUID and add identity lifecycle columns while retaining password resets and sessions.
- `.env.example` — make MySQL the documented default.
- `phpunit.xml` — stop presenting in-memory SQLite as sufficient for schema tests; use a dedicated MySQL test database through environment variables.

**Create:**

- `database/migrations/2026_08_06_0001xx_*.php` — identity, business, RBAC and team batch.
- `database/migrations/2026_08_06_0002xx_*.php` — property, files and guest batch.
- `database/migrations/2026_08_06_0003xx_*.php` — booking and availability batch.
- `database/migrations/2026_08_06_0004xx_*.php` — finance and ledger batch.
- `database/migrations/2026_08_06_0005xx_*.php` — workflows, operations, assets and inventory batch.
- `database/migrations/2026_08_06_0006xx_*.php` — calendar, marketplace and reviews batch.
- `database/migrations/2026_08_06_0007xx_*.php` — communication, integrations, audit, reporting and AI batch.
- `tests/Feature/Database/SchemaConventionsTest.php` — UUID, tenant, money and protected-delete assertions.
- `tests/Feature/Database/*SchemaTest.php` — domain-specific tables, columns, indexes and foreign-key behavior.
- `docs/database/phase-one-schema.md` — generated-file index and verified migration commands/results.

The complete columns, indexes and FK decisions consumed by every task are authoritative in `docs/superpowers/specs/2026-08-05-project-nexus-migration-blueprint.md`.

---

### Task 1: Establish MySQL migration verification

**Files:**

- Modify: `.env.example`
- Modify: `phpunit.xml`
- Create: `phpunit.mysql.xml`
- Create: `tests/Feature/Database/SchemaConventionsTest.php`

**Interfaces:**

- Consumes: XAMPP MySQL connection values supplied through local environment.
- Produces: `phpunit.mysql.xml` and schema-test conventions used by all later tasks.

- [ ] **Step 1: Add a schema test that requires MySQL**

```php
public function test_schema_suite_runs_on_mysql(): void
{
    $this->assertSame('mysql', DB::connection()->getDriverName());
    $this->assertGreaterThanOrEqual('8.0.0', DB::selectOne('select version() as version')->version);
}
```

- [ ] **Step 2: Run the test and prove current SQLite configuration fails**

Run: `php artisan test --filter=SchemaConventionsTest`

Expected: FAIL because the current `phpunit.xml` forces SQLite.

- [ ] **Step 3: Add dedicated MySQL test configuration**

Use database name `project_nexa_26_testing` by default, local host `127.0.0.1`, port `3306`, and environment-overridable credentials. Do not place a real password in source control.

- [ ] **Step 4: Run the MySQL connection test**

Run: `php artisan test --configuration phpunit.mysql.xml --filter=SchemaConventionsTest`

Expected: PASS against MySQL 8+. If the database does not exist, create only the explicitly named test database after user approval.

- [ ] **Step 5: Commit**

```bash
git add .env.example phpunit.xml phpunit.mysql.xml tests/Feature/Database/SchemaConventionsTest.php
git commit -m "test: establish MySQL schema verification"
```

### Task 2: Convert identity and Laravel infrastructure to UUID

**Files:**

- Modify: `database/migrations/0001_01_01_000000_create_users_table.php`
- Create: migrations `000100` through `000105` from blueprint Batch A.
- Create: `tests/Feature/Database/IdentitySchemaTest.php`

**Interfaces:**

- Produces: `users.id char(36)`, authentication support, social identities, MFA and devices used by every actor FK.

- [ ] Write failing schema tests asserting UUID user ID, unique email/provider subject, nullable social-only password, session user FK, MFA/device tables and safe delete behavior.
- [ ] Run `php artisan test --configuration phpunit.mysql.xml --filter=IdentitySchemaTest` and confirm missing-column/table failures.
- [ ] Implement Batch A exactly as blueprint sections 5 and 3.2–3.3.
- [ ] Run `php artisan migrate:fresh` on the dedicated test database, then the identity tests.
- [ ] Run `php artisan migrate:rollback --step=6`, re-run `php artisan migrate`, and confirm both commands succeed.
- [ ] Commit with `feat: add UUID identity schema`.

### Task 3: Add business tenancy, subscriptions and RBAC

**Files:**

- Create: migrations `000110` through `000119` from blueprint Batch B.
- Create: `tests/Feature/Database/BusinessAccessSchemaTest.php`

**Interfaces:**

- Consumes: `users.id`.
- Produces: businesses, memberships, branches, settings, verification, subscriptions, roles, permissions, overrides and impersonation.

- [ ] Write failing tests for every Batch B table, business-scoped uniqueness, membership uniqueness, nullable platform roles and restricted membership deletion.
- [ ] Run the test and confirm failure on missing `businesses`.
- [ ] Implement parent tables before pivots; add branch manager FK only after memberships exist.
- [ ] Assert `business_id`/actor UUID types with `information_schema.columns` and delete rules through `information_schema.referential_constraints`.
- [ ] Fresh-migrate, roll back the batch, re-migrate and run all database tests.
- [ ] Commit with `feat: add business tenancy and access schema`.

### Task 4: Add employees and suppliers

**Files:**

- Create: migrations `000120` through `000125` from blueprint Batch C.
- Create: `tests/Feature/Database/TeamSchemaTest.php`

**Interfaces:**

- Consumes: businesses and memberships.
- Produces: departments, employees, employee availability/skills and suppliers.

- [ ] Write failing tests for one employment profile per membership/business, employee number uniqueness, manager-cycle FK order and supplier code uniqueness.
- [ ] Implement Batch C, adding department manager only after employees exist.
- [ ] Prove a referenced former employee cannot be physically deleted while history exists.
- [ ] Fresh-migrate, rollback/re-migrate Batch C and run tests.
- [ ] Commit with `feat: add team and supplier schema`.

### Task 5: Add properties, units, files and guests

**Files:**

- Create: migrations `000130` through `000155` from blueprint Batches D–F.
- Create: `tests/Feature/Database/PropertyGuestSchemaTest.php`

**Interfaces:**

- Consumes: businesses, branches, memberships, employees and suppliers.
- Produces: permanent properties, independently bookable units, pricing/check rules, verification, files/documents/media and independent guest profiles.

- [ ] Write failing tests for property code/slug uniqueness, mandatory unit/property relationship, one address per property, version uniqueness, guest tenant indexes and merge-history restrictions.
- [ ] Implement property reference/configuration tables, then properties and units, then attachments, then guests.
- [ ] Add composite `(business_id, id)` indexes needed for later tenant-consistent references.
- [ ] Test that a property referenced by a unit cannot be physically deleted and a historical document version cannot cascade away.
- [ ] Fresh-migrate, rollback/re-migrate the batch and run tests.
- [ ] Commit with `feat: add property document and guest schema`.

### Task 6: Add bookings and conflict-safe availability storage

**Files:**

- Create: migrations `000160` through `000169` from blueprint Batch G.
- Create: `tests/Feature/Database/BookingAvailabilitySchemaTest.php`

**Interfaces:**

- Consumes: businesses, properties/units, guests and users.
- Produces: booking source/rate plan, booking commercial snapshot, occupants, histories, holds and availability projection.

- [ ] Write failing tests for booking reference uniqueness, mandatory property/unit/guest, interval indexes, immutable history connections and hold idempotency.
- [ ] Implement Batch G in parent-to-child order with no cascade on booking history.
- [ ] Add MySQL check constraints only for basic facts such as positive guest counts and departure after arrival where safe; document overlap as an application transaction rule.
- [ ] Test deleting a booked property/unit/guest fails with FK restriction.
- [ ] Fresh-migrate, rollback/re-migrate Batch G and run tests.
- [ ] Commit with `feat: add booking and availability schema`.

### Task 7: Add finance and immutable ledger

**Files:**

- Create: migrations `000170` through `000180` from blueprint Batch H.
- Create: `tests/Feature/Database/FinanceSchemaTest.php`

**Interfaces:**

- Consumes: bookings, guests, properties, suppliers, files and users.
- Produces: invoice/payment/refund/expense structures, chart of accounts, journal lines, reconciliation and settlement.

- [ ] Write failing tests for `decimal(19,4)`, ISO currency fields, business reference uniqueness, payment/refund idempotency and all protected finance FKs.
- [ ] Implement finance references, commercial documents, payment flow, expenses and ledger in dependency order.
- [ ] Add checks preventing a journal line with both debit and credit positive; leave full balance enforcement to transactional posting tests in the Finance implementation phase.
- [ ] Test that posted-source parents cannot be physically deleted through a cascade.
- [ ] Fresh-migrate, rollback/re-migrate and run tests.
- [ ] Commit with `feat: add finance and ledger schema`.

### Task 8: Add workflows, operations and approvals

**Files:**

- Create: migrations `000190` through `000198` from blueprint Batch I.
- Create: `tests/Feature/Database/OperationsSchemaTest.php`

**Interfaces:**

- Consumes: properties, bookings, memberships, employees, suppliers, files.
- Produces: versioned workflow templates/instances, tasks/evidence, cleaning, inspections, maintenance, incidents and approvals.

- [ ] Write failing tests for template/step uniqueness, one cleaning extension per task, inspection/work-log restrictions and approval hierarchy tables.
- [ ] Implement Batch I, ensuring nullable current assignments use `SET NULL` while historical assignment rows use `RESTRICT`.
- [ ] Fresh-migrate, rollback/re-migrate and run tests.
- [ ] Commit with `feat: add workflow and operations schema`.

### Task 9: Add assets and immutable inventory

**Files:**

- Create: migrations `000200` through `000204` from blueprint Batch J.
- Create: `tests/Feature/Database/AssetInventorySchemaTest.php`

**Interfaces:**

- Consumes: properties, units, suppliers, employees, workflows and approvals.
- Produces: asset history/schedules and inventory locations/items/movements/counts.

- [ ] Write failing tests for asset tag/SKU uniqueness, immutable movement references, count line uniqueness and protected item deletion.
- [ ] Implement Batch J with quantity `decimal(14,4)` and money `decimal(19,4)`.
- [ ] Fresh-migrate, rollback/re-migrate and run tests.
- [ ] Commit with `feat: add asset and inventory schema`.

### Task 10: Add calendar, marketplace and reviews

**Files:**

- Create: migrations `000210` through `000215` from blueprint Batch K.
- Create: `tests/Feature/Database/MarketplaceCalendarSchemaTest.php`

**Interfaces:**

- Consumes: properties/units, bookings, tasks, users and guests.
- Produces: calendar projection/conflicts, public listings/requests/favourites and verified-stay review storage.

- [ ] Write failing tests for event source indexes, one listing per property, platform slug uniqueness, request idempotency and one review per booking.
- [ ] Implement Batch K; document controlled polymorphic links as application-validated.
- [ ] Fresh-migrate, rollback/re-migrate and run tests.
- [ ] Commit with `feat: add calendar marketplace and review schema`.

### Task 11: Add communication and reliable integrations

**Files:**

- Create: migrations `000220` through `000224` from blueprint Batch L.
- Create: `tests/Feature/Database/IntegrationCommunicationSchemaTest.php`

**Interfaces:**

- Consumes: businesses, memberships, users, guests, bookings and domain identifiers.
- Produces: conversations/messages, notifications/deliveries, provider connections, mapping/sync, webhook inbox/outbox and idempotency.

- [ ] Write failing tests for conversation/time indexes, notification recipient indexes, encrypted-capable credential columns, external mapping uniqueness, webhook deduplication and outbox event uniqueness.
- [ ] Implement Batch L without logging or seeding real secrets.
- [ ] Fresh-migrate, rollback/re-migrate and run tests.
- [ ] Commit with `feat: add messaging and integration schema`.

### Task 12: Add governance, metrics and explainable AI storage

**Files:**

- Create: migrations `000230` through `000234` from blueprint Batch M.
- Create: `tests/Feature/Database/GovernanceAiSchemaTest.php`

**Interfaces:**

- Consumes: businesses, users, memberships, files and controlled domain identifiers.
- Produces: immutable audits/activity, retention/privacy/legal holds, KPI snapshots/report exports and traceable AI outputs/policies.

- [ ] Write failing tests for audit indexes/no soft deletes, KPI snapshot uniqueness, one daily brief per business/date and AI model-run references.
- [ ] Implement Batch M with controlled polymorphic columns and documented application validation.
- [ ] Fresh-migrate, rollback/re-migrate and run tests.
- [ ] Commit with `feat: add governance reporting and AI schema`.

### Task 13: Verify the complete migration graph

**Files:**

- Modify: `tests/Feature/Database/SchemaConventionsTest.php`
- Create: `docs/database/phase-one-schema.md`

**Interfaces:**

- Consumes: all Phase 1 migrations.
- Produces: evidence that normal Artisan migration commands work on MySQL.

- [ ] Add a test enumerating every expected Phase 1 table and asserting InnoDB, utf8mb4, UUID PK type, money precision, tenant indexes and protected delete rules.
- [ ] Run `php artisan migrate:fresh --seed --env=testing` against only the dedicated MySQL database.
- [ ] Run `php artisan test --configuration phpunit.mysql.xml tests/Feature/Database` and require zero failures.
- [ ] Run `php artisan migrate:rollback`, confirm all Project Nexus tables drop in reverse dependency order, then run `php artisan migrate` again.
- [ ] Run `php artisan schema:dump` only if the team explicitly wants a baseline; do not replace readable migrations during initial development.
- [ ] Record exact commands/results and migration batch map in `docs/database/phase-one-schema.md`.
- [ ] Commit with `test: verify complete phase one schema`.

### Task 14: Final quality and handover

**Files:**

- Modify: migration files only if checks identify concrete defects.
- Modify: `docs/database/phase-one-schema.md`

**Interfaces:**

- Produces: reviewed migration-only implementation ready for model/application work.

- [ ] Run `vendor/bin/pint --test` and correct formatting with `vendor/bin/pint`.
- [ ] Run `composer test` for the normal suite and the dedicated MySQL database suite separately.
- [ ] Inspect MySQL constraints and indexes using `information_schema`; compare them to the migration blueprint.
- [ ] Run `git diff --check` and confirm no credentials, generated vendor files or unrelated scaffold changes are staged.
- [ ] Update the blueprint only for deliberate deviations, explaining each decision.
- [ ] Commit with `docs: record verified phase one migrations`.
