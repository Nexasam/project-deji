# Forgot Password Redesign

## Purpose

Bring `/forgot-password` into the established Project Nexus authentication experience while retaining Laravel Breeze's real password-reset request behavior.

## Design

The page uses the same responsive two-panel composition as login and registration. On large screens, the left panel retains the role-neutral animated hospitality story, Project Nexus identity, and security-oriented supporting copy. On smaller screens, that panel is hidden and a compact brand/header row provides home navigation.

The right panel contains a focused reset form: heading, concise instructions, one accessible email field with the shared polished form styling, validation feedback, session success feedback, a primary orange “Send reset link” action, and a “Back to sign in” link.

## Behavior

- Keep `POST password.email`, CSRF protection, old email input, and Breeze validation/session behavior.
- Do not modify password broker, mail, routing, controllers, or database schema.
- Preserve keyboard focus, autocomplete, error associations, responsive layout, and role-neutral language.
- Re-record the customer walkthrough to include landing, login, forgot password, registration, verification, business onboarding, property setup, and completed dashboard.

## Verification

- Existing password-reset feature tests pass.
- Vite production build passes.
- Rendered desktop and mobile pages contain no application console errors or framework error overlays.
- The final MP4 decodes successfully and visibly includes the redesigned route.
