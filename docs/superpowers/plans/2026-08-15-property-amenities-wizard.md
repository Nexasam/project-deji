# Property Amenities Wizard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement a secure, persistent Basics-to-Amenities property setup flow.

**Architecture:** Add property-bound setup routes and controller actions, a validated amenities request, a transactional sync service, a catalog seeder, and a professional owner-shell amenities view.

**Tech Stack:** Laravel 13, Blade, Eloquent, MySQL, Tailwind CSS 4

## Global Constraints

- Only draft properties owned by the active business may be configured.
- Amenity assignments must use the existing `amenities` and `property_amenities` tables.
- Removed assignments use soft deletion; property UUIDs and audit history remain unchanged.

---

### Task 1: Amenity catalog

- [x] Seed the shared active amenity catalog and register it with `DatabaseSeeder`.

### Task 2: Property-bound amenities persistence

- [x] Add validation, transactional synchronization, controller actions, and UUID routes.

### Task 3: Real amenities UI and navigation

- [x] Build the owner-shell amenities form with existing selections, Back, and Save actions.
- [x] Redirect Basics to Amenities and make generic continuation choose the next incomplete stage.

### Task 4: Verification

- [x] Verify PHP syntax, routes, asset compilation, and whitespace.
