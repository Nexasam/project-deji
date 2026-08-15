# Property Media Wizard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Persist property images and videos and continue the UUID-bound setup sequence.

**Architecture:** Use a Form Request for file validation, a filesystem-aware service for persistence, business-scoped controller endpoints, and owner-shell Blade stages.

**Tech Stack:** Laravel 13, Blade, Eloquent, public filesystem disk, Tailwind CSS 4

## Global Constraints

- Only active-business draft properties may be modified.
- Media removal must soft-delete the record and retain the stored object.
- Storage paths must be namespaced by business and property UUID.

---

### Task 1: Media persistence

- [x] Add upload validation and a focused media storage service.
- [x] Add property-bound upload and removal endpoints.

### Task 2: Media interface and wizard navigation

- [x] Replace the handoff with a real multi-file Media form and saved-media gallery.
- [x] Advance successful submission to House Rules.
- [x] Apply compact Basics introduction copy.

### Task 3: Verification

- [x] Verify PHP syntax, routes, asset compilation, and whitespace.
