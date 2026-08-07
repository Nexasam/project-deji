# Business Schema Design

**Date:** 7 August 2026

**Status:** Approved for specification review

## Purpose

Create a small, authoritative `businesses` table as the ownership root for later property, booking, employee, and report records. The design replaces the archived business migration and deliberately excludes onboarding workflows, subscription history, logos, websites, social links, audit-user references, and other speculative fields.

## Chosen approach

Use one lean `businesses` table. Keep structured but country-dependent address and tax details in JSON rather than introducing supporting tables before their query and lifecycle requirements are known. Store the primary contact as ordinary business contact details; it does not reference a user account.

## Table design

The `businesses` table contains:

| Column | Type and constraints | Purpose |
|---|---|---|
| `id` | UUID primary key | Stable business identifier |
| `name` | string, required | Business name |
| `registration_number` | string, nullable | Government or corporate registration reference |
| `country_code` | `char(2)`, required | ISO 3166-1 alpha-2 country code |
| `address` | JSON, nullable | Country-dependent structured address |
| `primary_contact_name` | string, nullable | Primary contact's display name |
| `email` | string, nullable | Primary business contact email |
| `phone_number` | string, nullable | Primary business contact telephone number |
| `tax_information` | text, nullable | Encrypted JSON tax details |
| `business_type` | string, required | Business classification without a premature fixed catalogue |
| `timezone` | string, required, default `UTC` | IANA time-zone identifier |
| `currency` | `char(3)`, required | ISO 4217 currency code |
| `subscription_plan` | string, nullable | Current subscription-plan key |
| `verification_status` | string, required, default `unverified` | Current verification state |
| `status` | string, required, default `active` | Current operational state |
| `created_at`, `updated_at` | timestamps | Creation and last-update dates |
| `deleted_at` | nullable timestamp | Recoverable soft deletion |

The pair `(country_code, registration_number)` is unique when a registration number is present. Indexes support verification/status filtering, country/business-type filtering, subscription/status filtering, and email lookup.

## Application enums

Database columns remain strings so lifecycle additions do not require altering a MySQL native enum. PHP backed enums define and validate the supported values.

`BusinessVerificationStatus` values:

- `unverified`
- `pending`
- `verified`
- `rejected`

`BusinessStatus` values:

- `active`
- `inactive`
- `suspended`

The Eloquent model casts both fields to their corresponding enums. The model also casts `address` to an array and `tax_information` to an encrypted array.

## Ownership rules

The following rules belong to dependent tables and are not stored as a `business_rules` business column:

- A property must have a non-null `business_id` foreign key.
- A booking must have a non-null `business_id` foreign key.
- An employee must have a non-null `business_id` foreign key.
- A report must have a non-null `business_id` foreign key.

Each foreign key uses restrictive deletion, preventing a business with dependent operational records from being physically deleted. Soft deletion remains available for deactivating a business without destroying its ownership history.

## Model changes

The `Business` model will retain only fillable fields present in the new table. It will use `HasFactory`, `HasUuids`, and `SoftDeletes`. Relationships remain only when backed by an active migration; relationships for later schema flows will be added alongside those migrations.

The business factory will generate values supported by the new schema and use the enum values in its `verified` and `inactive` states.

## Verification

Implementation verification will cover:

- migration up and down behavior;
- defaults for verification and operational status;
- the country/registration uniqueness rule;
- enum casts;
- address and encrypted tax-information casts;
- UUID generation and soft deletion;
- factory compatibility.

## Deferred decisions

Later flows may introduce normalized addresses, multiple business contacts, subscription history, country-specific tax records, or additional lifecycle states. They will be added only when a concrete workflow requires them.
