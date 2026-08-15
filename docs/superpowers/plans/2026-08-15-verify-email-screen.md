# Verify Email Screen Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the generic Breeze `/verify-email` view with a responsive Project Nexus verification screen that retains real resend and logout actions.

**Architecture:** Keep the existing authenticated Breeze route and controller unchanged. Implement the presentation entirely in the existing Blade view using the shared Manrope guest layout and Tailwind utilities.

**Tech Stack:** Laravel 13, Blade, Tailwind CSS 4, Vite, PHPUnit.

## Global Constraints

- Preserve `verification.send` as a POST form with CSRF protection.
- Preserve `logout` as a POST form with CSRF protection.
- Display the authenticated user's email safely through Blade escaping.
- Use the existing orange brand accent and Manrope auth typography.
- Do not add fake social authentication, JavaScript redirects, or database changes.

---

### Task 1: Implement and verify the email-verification screen

**Files:**
- Modify: `resources/views/auth/verify-email.blade.php`
- Test: `tests/Feature/Auth/EmailVerificationTest.php`

**Interfaces:**
- Consumes: authenticated `User`, `session('status')`, routes `verification.send` and `logout`.
- Produces: responsive verification instructions and working POST actions.

- [ ] **Step 1: Confirm the existing verification behavior test passes before the visual change**

Run:

```bash
php artisan test tests/Feature/Auth/EmailVerificationTest.php
```

Expected: existing verification route and signed-link behavior pass.

- [ ] **Step 2: Replace the generic Breeze markup**

Build a centered verification panel containing the Project Nexus wordmark, envelope icon, “Check your email” heading, escaped `auth()->user()->email`, spam-folder guidance, resend form, resend success state, and logout form.

- [ ] **Step 3: Verify Blade compilation and auth behavior**

Run:

```bash
php artisan view:cache
php artisan test tests/Feature/Auth/EmailVerificationTest.php
```

Expected: views compile and the verification feature tests pass.

- [ ] **Step 4: Build frontend assets**

Run:

```bash
npm run build
```

Expected: Vite completes successfully with no Tailwind compilation errors.

- [ ] **Step 5: Check repository whitespace**

Run:

```bash
git diff --check
```

Expected: no whitespace errors.
