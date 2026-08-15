# Breeze Login Redesign Design

## Goal

Replace the default Breeze login presentation with a professional Project Nexus authentication screen while preserving Breeze behaviour.

## Composition

Desktop uses a dark slate brand panel and a focused white form panel. Mobile hides the supporting brand narrative and keeps a compact brand header above the form. Manrope, slate, orange, 12-pixel controls, strong borders, and accessible focus/error states align the screen with the public site and owner onboarding.

## Behaviour

The existing login POST route, CSRF token, email/password names, old email value, validation errors, session status, remember-me checkbox, password-reset link, registration link, autofocus, and autocomplete attributes remain intact. A local Alpine password visibility control changes only input presentation.

