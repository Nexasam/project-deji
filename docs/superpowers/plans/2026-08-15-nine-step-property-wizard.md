# Nine-Step Property Wizard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace disconnected setup screens with one resumable, auditable, UUID-bound nine-step owner workflow.

**Architecture:** Track step state in `property_setup_steps`, centralize navigation in `PropertySetupWorkflow`, persist each step through existing domain tables, and evaluate publication using `PropertyPublicationReadinessService`.

**Tech Stack:** Laravel 13, Blade, Eloquent, MySQL, public filesystem, Tailwind CSS 4

---

### Task 1: Workflow foundation
- [x] Add progress migration, model, relation, initialization, completion, skipping and resume logic.

### Task 2: Persistent stages
- [x] Connect Basics, Amenities and Media to workflow progress.
- [x] Implement House Rules, Operations, Assets, Documents and Marketplace persistence.

### Task 3: Review and verification
- [x] Add live-data readiness blockers, warnings and verification submission.

### Task 4: Unified UX and verification
- [x] Add consistent Back, Save and continue, Skip for now and Exit setup behaviour.
- [x] Verify database migration, live progress initialization, routes, syntax, assets and whitespace.
