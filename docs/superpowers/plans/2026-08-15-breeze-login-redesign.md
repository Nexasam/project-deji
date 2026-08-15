# Breeze Login Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restyle the Breeze login route as a polished Project Nexus authentication screen.

**Architecture:** Keep the Breeze request/route contract and replace only `auth/login.blade.php` with a responsive two-column Blade surface using existing project assets and form styles.

**Tech Stack:** Laravel Breeze, Blade, Alpine.js, Tailwind CSS 4, Vite

## Global Constraints

- Do not change authentication controllers, middleware, route names, or credential fields.
- Preserve accessible labels, autocomplete, focus, status, and validation feedback.
- Use the established orange/slate Project Nexus visual language.

---

### Task 1: Login presentation

- [x] Replace the default Breeze component card with the approved responsive two-column login screen.
- [x] Preserve and verify all Breeze authentication form contracts.
- [x] Verify route registration, Blade-facing strings, asset compilation, and whitespace.
