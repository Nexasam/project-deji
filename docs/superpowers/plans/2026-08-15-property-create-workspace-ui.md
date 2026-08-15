# Property Create Workspace UI Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the real property creation form so it remains inside the owner workspace and visually matches the established property wizard screens.

**Architecture:** Keep the existing controller, request, service and POST route unchanged. Replace only the Blade composition: shared owner sidebar, workspace header, compact nine-step progress, grouped form sections and responsive sticky actions.

**Tech Stack:** Laravel Blade, Tailwind CSS 4, Alpine/Vite assets.

## Global Constraints

- The owner sidebar remains visible on desktop and Properties stays selected.
- The active business and authenticated user remain visible.
- The form posts to `owner.properties.store` with CSRF protection.
- Every existing validated field and validation error remains available.
- The page represents Step 1 of 9 without pretending later static steps already persist data.
- Mobile layout must remain usable without horizontal overflow.

---

### Task 1: Recompose the property form inside the owner shell

**Files:**
- Modify: `resources/views/owner/properties/create.blade.php`

**Interfaces:**
- Consumes: `$business`, authenticated user, validation errors, old input, `owner.properties.store`.
- Produces: workspace-integrated Step 1 form with unchanged submission contract.

- [ ] Add the shared sidebar with `active=properties`.
- [ ] Add a standard owner header showing business, user and “Save & exit”.
- [ ] Add Step 1 of 9 progress and future-step labels.
- [ ] Group fields into Property identity, Location, and Capacity & pricing panels.
- [ ] Preserve all field names, values, constraints and errors.
- [ ] Add a bottom action bar with Cancel and “Save draft & continue”.

### Task 2: Verify

**Files:**
- Verify: `resources/views/owner/properties/create.blade.php`

**Interfaces:**
- Consumes: Blade/Tailwind build.
- Produces: buildable responsive UI.

- [ ] Confirm the GET and POST property routes.
- [ ] Build production assets with Vite.
- [ ] Run `git diff --check`.
