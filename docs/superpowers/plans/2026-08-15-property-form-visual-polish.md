# Property Form Visual Polish Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Upgrade the owner property-creation form to a professional, consistent visual system without changing its persistence, validation, or authorization behavior.

**Architecture:** Add reusable, page-scoped form-control classes to the main stylesheet and apply them to the existing Blade form. Preserve every field name, route, CSRF token, old input value, validation error, and business-derived value.

**Tech Stack:** Laravel 13, Blade, Tailwind CSS 4, Vite

## Global Constraints

- Keep the form action as `owner.properties.store` and retain `POST` plus CSRF protection.
- Do not change controller, service, request-validation, authorization, or database behavior.
- Keep all existing input names and server-side validation error bindings.
- Use the established orange/slate owner-dashboard visual language.

---

### Task 1: Professional property form controls

**Files:**
- Modify: `resources/css/app.css`
- Modify: `resources/views/owner/properties/create.blade.php`

**Interfaces:**
- Consumes: Existing `StorePropertyRequest` field names and `owner.properties.store` route.
- Produces: Reusable `.property-form-*` presentation classes and accessible styled form markup.

- [x] **Step 1: Add scoped visual control styles**

Add styles for section accents, labels, required markers, inputs, selects, textareas, icons, currency prefixes, helper copy, invalid states, disabled states, and action buttons.

- [x] **Step 2: Apply the control system to the Blade form**

Update only presentation markup while preserving all server-facing attributes and error bindings.

- [x] **Step 3: Verify compiled assets and routes**

Run: `npm run build`

Expected: Vite completes successfully.

Run: `php artisan route:list --except-vendor | rg 'owner/properties'`

Expected: Owner property index, create, and store routes remain registered.

Run: `git diff --check`

Expected: No whitespace errors.
