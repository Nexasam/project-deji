# Business Schema Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create the fresh businesses migration and align its enums, Eloquent model, factory, and tests.

**Architecture:** The businesses table is the tenant ownership root. Lifecycle values are stored as strings and exposed through PHP backed enums; country-dependent address and tax data remain structured model casts.

**Tech Stack:** PHP 8.3, Laravel 13, Eloquent, PHPUnit 12, SQLite test database, MySQL production schema.

## Global Constraints

- Use a UUID primary key and Laravel timestamps plus soft deletion.
- Keep primary contact data as plain fields with no user relationship.
- Do not add a `business_rules` column; dependent schemas will enforce ownership through required foreign keys.
- Verification states are `unverified`, `pending`, `verified`, and `rejected`.
- Business states are `active`, `inactive`, and `suspended`.

---

### Task 1: Business persistence contract

**Files:**
- Create: `database/migrations/2026_08_07_000100_create_businesses_table.php`
- Create: `app/Enums/BusinessVerificationStatus.php`
- Create: `app/Enums/BusinessStatus.php`
- Modify: `app/Models/Business.php`
- Modify: `database/factories/BusinessFactory.php`

**Interfaces:**
- Produces: `App\Enums\BusinessVerificationStatus`, `App\Enums\BusinessStatus`, and the `App\Models\Business` persistence contract.

- [ ] **Step 1: Implement the minimal schema and model contract**

Create string-backed enums for the approved values. Create the businesses migration with the approved columns, defaults, indexes, compound uniqueness, timestamps, and soft deletion. Reduce the model's fillable and casts to active fields, and align the factory with the enums.

- [ ] **Step 2: Run syntax, schema, and code-quality checks**

Run: `vendor/bin/pint --test app/Enums app/Models/Business.php database/factories/BusinessFactory.php database/migrations/2026_08_07_000100_create_businesses_table.php`

Run: `php -l` for each changed PHP file and `php artisan migrate:fresh --pretend`.

Expected: all syntax checks, formatting checks, and the migration dry run pass. Automated tests are explicitly deferred at the user's request.
