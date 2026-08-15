# Forgot Password Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restyle the real forgot-password flow to match Project Nexus authentication screens and replace the customer walkthrough with an updated recording.

**Architecture:** Keep the existing Laravel password broker and route contract intact. Replace only the Blade presentation, verify the existing feature behavior, then exercise the rendered route and complete onboarding flow through Playwright.

**Tech Stack:** Laravel 13, Blade, Tailwind CSS 4, Alpine.js, Vite, PHPUnit, Playwright, FFmpeg.

## Global Constraints

- Preserve the existing `password.email` submission contract.
- Use role-neutral Project Nexus messaging.
- Do not change database schema, reset controllers, or mail behavior.
- Deliver an H.264 MP4 outside tracked source files.

---

### Task 1: Restyle the forgot-password view

**Files:**
- Modify: `resources/views/auth/forgot-password.blade.php`
- Test: `tests/Feature/Auth/PasswordResetTest.php`

**Interfaces:**
- Consumes: Laravel named routes `home`, `login`, and `password.email`.
- Produces: An accessible email form posting to `password.email` with unchanged field name `email`.

- [ ] Replace the default guest-layout markup with the shared auth split-screen composition.
- [ ] Retain CSRF, old input, session status, and email validation output.
- [ ] Run `php artisan test tests/Feature/Auth/PasswordResetTest.php` and confirm all cases pass.
- [ ] Run `npm run build` and confirm Vite exits successfully.

### Task 2: Rendered QA and updated walkthrough

**Files:**
- Create ignored artifact: `storage/app/demos/project-nexus-complete-customer-walkthrough-v2.mp4`

**Interfaces:**
- Consumes: the application at `http://127.0.0.1:8000` and local MySQL data.
- Produces: a customer-shareable H.264 walkthrough video.

- [ ] Verify `/forgot-password` at desktop and mobile widths with no page or console errors.
- [ ] Submit an unregistered demo email and confirm the success response does not expose account existence.
- [ ] Record landing → login → forgot password → login → registration → verification → business → nine property steps → dashboard.
- [ ] Decode-check the final MP4 and verify the persisted account, business, and property state.
