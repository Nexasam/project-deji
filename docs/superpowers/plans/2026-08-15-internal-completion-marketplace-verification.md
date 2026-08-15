# Internal Completion and Marketplace Verification Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Separate owner-controlled internal property activation from optional platform marketplace verification.

**Architecture:** Step 9 completes setup as active/unpublished. A separate business-scoped action evaluates marketplace readiness and submits the listing to platform compliance.

**Tech Stack:** Laravel 13, Blade, Eloquent

---

### Task 1: Lifecycle separation
- [x] Split internal setup blockers from marketplace verification blockers.
- [x] Complete setup without changing verification status.
- [x] Add optional marketplace verification submission from the property portfolio.
- [x] Verify routes, syntax, rendered labels and assets.
