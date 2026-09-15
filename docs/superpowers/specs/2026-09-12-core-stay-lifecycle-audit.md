# Project Nexus Core Stay Lifecycle Audit

**Source:** `PROJECT NEXUS (2).pdf` (190 pages)  
**Audit date:** 12 September 2026  
**Scope:** The smallest complete operational flow, not the entire SRS vision

**Delivery roadmap:** [September 2026 MVP Completion Roadmap](../plans/2026-09-12-september-mvp-completion-roadmap.md)

## Source requirement

Chapter 6 defines the product heartbeat as:

`Property → Verification → Marketplace → Booking → Payment → Calendar → Operations → Check-in → Stay → Check-out → Cleaning → Inspection → Property ready`

The SRS says a confirmed booking must affect the calendar, finance, housekeeping, notifications, guest communication, reports, and analytics. For the current MVP, analytics/AI and advanced reporting can remain deferred, but the operational chain cannot stop at payment confirmation.

## Current end-to-end status

### Working now

1. **Property creation and publishing**
   - Owner creates a property through the structured wizard.
   - Media, documents, amenities, capacity, pricing, marketplace copy, and channels are persisted.
   - Platform admin reviews, publishes, rejects, or unpublishes.
   - Only eligible verified/published/ready properties appear publicly.

2. **Guest discovery and consideration**
   - Public search and filters query real records.
   - Property cards show uploaded media with fallbacks.
   - Property detail has a gallery, amenities, pricing, capacity, and an 18-month calendar.
   - Imported iCal blocks and native bookings appear unavailable.

3. **Booking integrity and payment simulation**
   - Guest selects valid dates and guest counts.
   - Confirmation shows nights, subtotal, discount, service fee, and total.
   - Server ignores browser totals and calculates authoritative pricing.
   - Booking transaction locks the property and rechecks bookings, availability days, and blocks.
   - Confirmed nights are allocated with a database uniqueness constraint.
   - Failed simulated payment does not reserve dates.
   - Duplicate submission is protected by an idempotency key.

4. **Calendar synchronization**
   - Airbnb and Booking.com iCal feeds can be imported manually and every 15 minutes.
   - Native bookings and manual owner blocks are exported through a protected `.ics` URL.
   - Imported events block local booking without becoming fake finance/guest bookings.
   - Existing blocks survive feed failures.

5. **Booking visibility and cancellation**
   - Guest sees a rich booking list/detail.
   - The correct business owner sees the booking; another business cannot.
   - Guest cancellation is confirmed through a warning modal.
   - Cancellation evaluates refund eligibility, records history/refund, and releases native dates.

6. **Basic owner workspaces**
   - Owner has real booking, calendar, finance, dashboard, and operations data.
   - Owner can create generic operational tasks and move them from pending/assigned to in-progress and completed.
   - Owner can reschedule a booking after an availability check.

## Where the core flow currently breaks

### Gap 1 — Confirmation does not start operations

The SRS requires automatic pre-arrival cleaning, inspection, welcome, instructions, payment reminders, identity checks, and maintenance review. The current confirmation creates the booking, payment, status history, and occupied dates only.

**MVP requirement:** When a booking becomes confirmed, idempotently create booking-linked preparation tasks. At minimum:

- pre-arrival cleaning or readiness check;
- property inspection;
- guest welcome/check-in preparation.

Each task must include the booking, property, due time, status, and optional assignee. The owner booking page should show these tasks.

### Gap 2 — No usable check-in lifecycle

`BookingCheckIn` exists in the schema, but there is no check-in route, application service, guarded transition, or UI. Owners cannot record identity checked, balance status, deposit/key handover, or change `confirmed` to `checked_in`.

**MVP requirement:** Add an owner check-in action that:

- permits only an eligible confirmed booking;
- warns about incomplete preparation tasks or outstanding payment;
- records check-in time and operator;
- changes status to `checked_in`;
- appends immutable status history.

Advanced identity integrations and smart locks can remain deferred.

### Gap 3 — No in-stay support loop

The SRS allows guests to request cleaning/maintenance, contact support, and report issues. `GuestServiceRequest`, interactions, and incidents exist as schema/models but have no active guest/owner routes.

**MVP requirement:** Add one simple booking-linked request flow:

- guest submits a category, message, and urgency;
- owner sees and resolves it;
- activity stays attached to the booking history;
- maintenance/cleaning requests can create a linked operational task.

Real-time chat and WhatsApp API can remain deferred.

### Gap 4 — No checkout-to-readiness loop

`BookingCheckOut` exists, but checkout, cleaning generation, inspection, and property-ready transition are not connected. This is the largest break from the Chapter 6 lifecycle.

**MVP requirement:** Owner checkout must:

- transition `checked_in → checked_out`;
- record checkout time, condition note, and optional damage flag;
- generate one turnover cleaning task linked to the booking;
- after cleaning completion, generate an inspection task;
- after inspection approval, mark the booking completed and property operationally available/ready;
- after rejection, create follow-up cleaning or maintenance work.

Photo evidence, deposit disputes, and complex checklists can follow later.

### Gap 5 — Walk-in, phone, and WhatsApp promise a flow that does not exist

The UI says walk-in/phone is always available and WhatsApp represents manual bookings, while the owner bookings page explicitly says manual booking creation is disabled.

**MVP requirement:** Add owner-created bookings using the same availability and calendar allocation rules. Support source, guest name/contact, dates, counts, amount, payment state, and notes. Until implemented, remove claims that these channels are active.

### Gap 6 — No confirmation or operational notifications

The SRS requires guest/business confirmation and staff notifications. Authentication emails exist, but booking lifecycle notifications do not.

**MVP requirement:** Persist in-app notifications for:

- booking confirmed or payment failed;
- owner receives new booking;
- upcoming check-in;
- task assigned/overdue;
- cancellation/refund;
- iCal sync failure or conflict.

Email delivery can be queued and environment-dependent, but the in-app record must remain visible.

### Gap 7 — iCal prevents known conflicts, not unseen external bookings

Local conflict protection is correct. However, iCal polling has an unavoidable delay: Airbnb or Booking.com may accept a booking before its next feed import. When that later conflicts with a native booking, the importer records the conflict but there is no strong operational alert/resolution screen.

**MVP requirement:** Create a prominent calendar conflict alert for owner and platform admin. Never silently cancel either reservation. Show provider, property, dates, last successful sync, and required manual resolution. Production must continuously run both Laravel scheduler and queue worker.

### Gap 8 — Cancellation policy time is not fully sourced from the listing

The SRS expects policy-aware cancellation. Current cancellation implements one 48-hour rule but uses the 14:00 Africa/Lagos fallback rather than the listing's configured check-in time.

**MVP requirement:** Use the listing check-in time when present, retain 14:00 only as fallback, and display the exact rule before booking and cancellation.

### Gap 9 — Browser quote is informative but has no server review endpoint

Final creation recalculates safely, so a guest cannot manipulate the total. A stale page can nevertheless show one amount while the server charges a changed amount if price/promotion changes before submission.

**MVP requirement:** Add a server quote/review request before final confirmation. If the price changes, show the new total and require acknowledgement before simulated charge.

## Recommended MVP delivery order

### Phase A — Close the operational spine

1. Booking lifecycle transition service.
2. Booking-linked task automation on confirmation.
3. Owner check-in action and record.
4. Owner checkout action and turnover cleaning task.
5. Cleaning completion → inspection task → property ready/completed.

This converts the existing booking demo into an operating product.

### Phase B — Close channel and communication gaps

1. Manual owner booking for walk-in/phone/WhatsApp.
2. Booking and task in-app notifications.
3. iCal conflict incident and stale-sync warnings.
4. Listing-based cancellation time.

### Phase C — Improve guest completeness

1. Backend quote confirmation.
2. Guest special requests/contact information.
3. Simple in-stay service request.
4. Receipt/confirmation view or downloadable document.

## Explicitly deferred from the 190-page SRS

These are valuable, but not required to make the core loop coherent:

- AI workspace, predictions, recommendations, and dynamic pricing;
- advanced reports and analytics;
- live Paystack and production webhooks;
- Google/Outlook native APIs;
- two-way WhatsApp messaging;
- configurable workflow builder;
- recurring schedules and smart staff optimisation;
- loyalty, upsells, airport pickup, and sophisticated guest CRM;
- deposits, disputes, damage charging, and multi-level approvals;
- full invoice/tax/credit-note suite;
- review sentiment analysis and marketplace ranking;
- buildings with separately bookable units;
- staff mobile application and live location.

## Core acceptance test

The MVP is operationally complete when one automated test and one browser walkthrough prove:

1. owner creates a complete property;
2. admin publishes it;
3. external calendar block prevents selection/booking;
4. guest selects available dates and receives an authoritative quote;
5. confirmed booking allocates dates and appears for guest and correct owner;
6. confirmation generates booking-linked preparation tasks and notifications once;
7. owner completes readiness and checks guest in;
8. guest submits one service request and owner resolves it;
9. owner checks guest out;
10. turnover cleaning and inspection complete;
11. booking becomes completed and property becomes ready for its next booking;
12. finance retains the payment and calendar export contains the stay;
13. cancellation variant records refund outcome, releases dates, cleans up pending tasks, and notifies both sides;
14. second booking attempt for any occupied/imported date is rejected.

## Decision

Do not attempt to implement every workspace described in the SRS before launch. Complete the event chain surrounding a stay. The existing schema already contains most of the required entities; the priority is to connect them with guarded services, idempotent automations, scoped routes, usable screens, and one end-to-end acceptance test.
