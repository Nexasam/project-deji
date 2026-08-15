# Real Dashboard Preview and Draft Continuation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Display labelled sample dashboard content below real data and support secure continuation of draft property basics.

**Architecture:** Reuse the dashboard sample partial below the real dashboard content. Add business-scoped edit/update controller actions and a focused update service, while reusing the existing request and Blade form.

**Tech Stack:** Laravel 13, Blade, Eloquent, Tailwind CSS 4

## Global Constraints

- Sample figures must never appear as real totals.
- Property lookup must be scoped to the active business.
- Updating a draft must preserve its UUID, code, business, lifecycle state, and creation audit fields.

---

### Task 1: Labelled sample dashboard preview

**Files:**
- Modify: `resources/views/dashboard.blade.php`

- [x] Add the labelled sample operations section after the real property section.

### Task 2: Secure draft property continuation

**Files:**
- Create: `app/Services/Property/UpdatePropertyService.php`
- Modify: `app/Http/Controllers/Owner/OwnerPropertyController.php`
- Modify: `routes/web.php`
- Modify: `resources/views/owner/properties/create.blade.php`
- Modify: `resources/views/owner/properties/index.blade.php`
- Modify: `resources/views/dashboard.blade.php`

- [x] Add business-scoped edit/update endpoints.
- [x] Reuse and prefill the basics form for the selected draft.
- [x] Add `Continue setup` actions to real property displays.
- [x] Verify routes, PHP syntax, asset compilation, and whitespace.
