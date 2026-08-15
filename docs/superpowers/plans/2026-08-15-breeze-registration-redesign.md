# Breeze Registration Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extend the approved Project Nexus authentication design to Breeze registration.

**Architecture:** Replace only the registration Blade presentation while preserving every Breeze form contract and server-side validation field.

**Tech Stack:** Laravel Breeze, Blade, Alpine.js, Tailwind CSS 4, Vite

---

### Task 1: Registration presentation

- [x] Apply the approved two-column authentication design.
- [x] Preserve name, phone, email, terms, password, confirmation, CSRF, validation, and login navigation.
- [x] Verify routes, rendered HTML, build, and whitespace.
