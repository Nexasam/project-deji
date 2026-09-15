# September 2026 MVP Completion Roadmap

**Deadline:** 30 September 2026  
**Source of scope:** Chapter 16.3 of `PROJECT NEXUS (2).pdf`  
**Supporting audit:** [Core Stay Lifecycle Audit](../specs/2026-09-12-core-stay-lifecycle-audit.md)

**Executable checklist:** [MVP 1 Practical Task Checklist](2026-09-13-mvp1-practical-task-checklist.md)

**Post-MVP audit:** [Functional Backlog and Access-Control Audit](2026-09-14-post-mvp-functional-backlog-and-access-control-audit.md)

## What “completed this month” means

The complete 190-page vision cannot responsibly be delivered in the remaining part of September. Chapter 16 already separates that vision into releases. This roadmap targets the Phase 1 MVP and the core Chapter 6 hospitality lifecycle.

The September product is complete when one property can move through this loop without spreadsheets or hidden manual database work:

`Create → Verify → Publish → Discover → Book → Pay → Prepare → Check in → Support stay → Check out → Clean → Inspect → Ready again`

The booking must remain synchronized with availability, calendar, finance, operations, notifications, and guest/owner history throughout that loop.

## Repository-to-SRS scorecard

| SRS Phase 1 area | Evidence already implemented | Remaining September work |
|---|---|---|
| Business dashboard | Real properties, bookings, payments and task KPIs through `OwnerDashboardSummary` | Actionable notification centre, today’s cleaning/inspection/maintenance and critical quick actions |
| Property management | Ten-step wizard, media/documents, amenities, pricing, verification, publishing and rich owner details | House rules/check-in policy visibility, operational state/history and private-document security audit |
| Marketplace | Real eligible listings, filters, anchored results, media gallery, availability calendar and booking form | Server quote review, explicit terms, receipt and verified-stay reviews |
| Booking management | Transactional marketplace booking, simulated payment, cancellation, reschedule, owner/guest views | Manual bookings, full lifecycle transitions, richer timeline, check-in/out and owner cancellation/no-show |
| Calendar | Native bookings, manual blocks, iCal import/export, polling and overlap protection | Operational events, prominent conflict/stale-sync alerts and worker-health monitoring |
| Finance | Payment/refund/expense records, manual finance entries and owner summaries | Booking balance/paid/refund closeout, receipt and basic revenue/expense report |
| Operations | Manual property tasks, employee assignment, start and complete transitions | Booking-linked cleaning schedules, inspections, maintenance and automatic workflow triggers |
| Staff | Runtime permissions, property scope, team administration, business switching, permission-aware navigation and assigned staff task execution | Invitation resend/acceptance refinement, capability management and broader role acceptance testing |
| Notifications | Notification/delivery schema plus authentication mail | Product notification service and UI for booking/payment/task/calendar events |
| Guest records | Authenticated guest ownership and booking history | Contact/primary-guest data, special requests and in-stay service requests |
| Reviews | Review/invitation/response schema | Verified-stay invitation, guest submission and owner response |
| AI Phase 1 | Presentation components only | Optional rules-based morning brief/basic recommendations after core operations are stable |
| Security/release | Tenant scopes, auth, validation, audit/history foundation and prior release checks | Document privacy, all new authorizations, MySQL concurrency, queues, accessibility and release rehearsal |

## Release blockers

### 1. Operations is still a task list

The working Operations page can manually create a property task, assign an employee, start it and complete it. The SRS requires cleaning workflows, maintenance requests, inspections and task assignment connected to the booking lifecycle.

September minimum:

- confirmed booking creates pre-arrival readiness work once;
- checkout creates turnover cleaning once;
- cleaning completion creates inspection once;
- failed inspection creates corrective cleaning or maintenance;
- passed inspection returns the property to ready;
- tasks show booking, property, guest window, assignee, due time and dependency;
- staff can view and complete permitted work;
- completed work remains auditable.

### 2. Booking lifecycle stops at `confirmed`

Models exist for check-in and checkout, but no active routes/services execute them. Add guarded, transactional transitions for:

`awaiting_payment → confirmed → checked_in → checked_out → completed`

Also support terminal paths `cancelled`, `refunded`, and `no_show`. Every transition must write immutable history and trigger operational/notification effects idempotently.

### 3. Manual channels are advertised but unavailable

Walk-in/phone and WhatsApp cannot be considered enabled until owners can create bookings for them. Manual creation must share marketplace capacity, overlap and availability-allocation rules.

### 4. Product-event notifications are absent

Implement persistent notifications for new booking, payment, cancellation/refund, upcoming arrival, checkout, assigned/overdue task, failed inspection, and calendar conflict/sync failure.

### 5. Cleaning, inspections and maintenance are schema-only

`PropertyCleaningSchedule`, `Inspection`, `InspectionItem`, and `MaintenanceIssue` exist, but lack complete application services, authorization, routes and active screens.

### 6. Staff workflow access — resolved for MVP baseline

Implemented owner routes now use granular permissions. Property managers, reception, accountants and operations managers can enter only their mapped workspaces; property assignments scope their records; cleaners, maintenance technicians and inspectors retain an assigned-task-only surface. Team provisioning, role assignment/deactivation and multi-business switching are active. Advanced capability management and separation-of-duty governance remain post-MVP hardening.

### 7. Guest communication and in-stay requests are absent

The platform needs durable booking confirmation, arrival instructions and one booking-linked service/issue request flow. Live chat and WhatsApp APIs can remain deferred.

### 8. Finance needs basic closeout

September needs booking total, paid, refunded and outstanding; a receipt; and a basic property/date revenue-expense report. Full accounting, forecasting and tax engines remain later releases.

### 9. iCal conflicts need escalation

Known imported blocks prevent booking. Polling cannot see an external reservation until sync. Any later external/native collision must create a high-severity owner/admin incident; queue and scheduler health must be monitored.

### 10. Sensitive documents require a privacy audit

Verification documents must use private storage with authenticated, tenant-scoped streaming. Difficult-to-guess public paths are not sufficient protection.

## Dated delivery plan

### 12–14 September — Lifecycle foundation

- Freeze the Phase 1 boundary.
- Implement one guarded booking transition service and state map.
- Define idempotent events for confirmation, check-in/out, cleaning, inspection and cancellation.
- Use listing check-in time in cancellation calculation.
- Add server quote review and price-change acknowledgement.
- Write focused tests before UI work.

**Exit gate:** transition and quote tests pass without regressing checkout.

### 15–18 September — Booking and communication spine

- Add manual owner booking for walk-in, phone and WhatsApp.
- Add owner check-in, no-show, cancellation and checkout actions with modals.
- Capture primary guest contact and special requests.
- Build booking timeline from status, payments, date changes, tasks and cancellations.
- Implement persistent in-app notifications and unread alerts.
- Generate a booking confirmation/receipt view.

**Exit gate:** marketplace and manual bookings reach checked-out with correct calendar, payment and history records.

### 19–23 September — Operations MVP

- Generate booking-linked pre-arrival cleaning/readiness and inspection work.
- Implement cleaning schedules with task linkage.
- Add checklists and before/after evidence.
- Add inspection pass/fail, notes and evidence.
- Turn failed inspections into correction/maintenance work.
- Generate turnover cleaning at checkout and ready/completed state after inspection.
- Add minimal staff task inbox and scoped transitions.
- Add overdue and failed-inspection notifications.

**Exit gate:** one booking drives preparation, check-in, checkout, cleaning, inspection and ready state without direct database changes.

### 24–26 September — Guest stay, finance, reviews and calendar alerts

- Add guest service request and owner resolution.
- Convert maintenance/cleaning requests into linked operational work.
- Add verified-stay review invitation/submission and owner response.
- Add per-booking finance closeout, receipt and basic revenue/expense report.
- Add stale iCal, repeated-failure and external/native conflict alerts.
- Show operational events in the calendar.

**Exit gate:** guest support, operational closeout, basic finance and calendar risks work coherently.

### 27–28 September — Dashboard and security closure

- Surface house rules, check-in/out policy, cancellation terms and safe arrival instructions.
- Make dashboard show today’s arrivals, departures, cleaning, inspections, maintenance, payments and alerts.
- Complete verification-document storage/streaming security work.
- Verify tenant and role authorization for every new action.
- Add rules-based morning briefing only if core operations are stable.

**Exit gate:** owner understands today’s work/risks quickly; sensitive documents are not public.

### 29–30 September — Release hardening only

- Clean MySQL migrations and seed repeatability.
- Full PHPUnit and dedicated concurrent double-booking test on MySQL.
- Blade, route/config cache and Vite production build.
- Scheduler, queue, iCal polling and failure recovery test.
- Desktop/mobile journeys for owner, admin, guest and staff.
- Accessibility/responsive review.
- Backup/restore rehearsal and documented production runbook.

**Exit gate:** no P0 defect, cross-business leak, double booking, broken transition or undocumented required process.

## Explicitly excluded from September

- no-code workflow builder and advanced scheduling optimisation;
- full inventory/procurement and supplier workflows;
- full tax/accounting/approval engine;
- live Google/Outlook/WhatsApp APIs;
- predictive/autonomous AI;
- enterprise hierarchy, multi-country and multi-currency;
- native staff/guest mobile apps;
- smart locks, IoT, loyalty and partner marketplace;
- advanced analytics and custom scheduled reports.

## Non-negotiable acceptance journey

1. Owner creates property; admin publishes it.
2. Imported calendar dates cannot be booked.
3. Guest receives server quote and confirms simulated payment.
4. Booking, payment, availability, receipt, notifications and preparation workflow are created once.
5. Correct owner sees it; foreign owner cannot; staff sees only assigned/permitted work.
6. Readiness is completed and guest is checked in.
7. Guest service request is created and resolved.
8. Guest is checked out and turnover cleaning is generated.
9. Cleaning evidence/checklist is completed; inspection passes or creates corrective work.
10. Booking completes and property becomes ready.
11. Finance and calendar export reflect the stay.
12. Cancellation variant calculates policy/refund, releases dates, cleans obsolete tasks and notifies both sides.
13. Concurrent and known iCal-blocked booking attempts are rejected.
14. External/native conflict creates a visible incident without silent cancellation.

## Launch classification

- **Internal demo:** nearly ready, subject to regression verification.
- **Owner-operated pilot with simulated payments:** achievable by 30 September with this roadmap.
- **Public production taking real money:** requires live payment initialization, signed webhooks, reconciliation, real mail delivery, monitoring and production infrastructure beyond the simulated-payment MVP.

## Client presentation

Use the browser-by-browser walkthrough, seeded role credentials and recovery notes in [`docs/release/2026-09-14-client-presentation-browser-runbook.md`](../../release/2026-09-14-client-presentation-browser-runbook.md).
