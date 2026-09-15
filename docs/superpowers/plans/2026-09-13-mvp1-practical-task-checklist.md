# MVP 1 Practical Task Checklist

**Target:** Owner-operated pilot with simulated payments  
**Deadline:** 30 September 2026  
**Rule:** Finish the complete stay loop before adding broad SRS features.

**Status updated:** 13 September 2026. Checked items are implemented and covered by automated verification; unchecked release items require the final deployment/browser rehearsal.

## MVP 1 outcome

A real user must be able to complete this flow without database edits or an external spreadsheet:

`Publish property → Find available dates → Book/pay → Owner receives booking → Prepare property → Check guest in → Check guest out → Clean → Inspect → Property ready again`

The same availability rules must protect marketplace, manual and imported iCal dates.

## Step 1 — Protect sensitive property documents

- [x] Change verification/property documents from the `public` disk to a private disk.
- [x] Stream previews/downloads only through authenticated, tenant-scoped controllers.
- [x] Keep public property photos/videos on the public disk.
- [x] Add tests proving another owner, guest and unauthenticated user cannot retrieve a document.
- [x] Provide a safe migration command for already-uploaded public documents.

**Done when:** knowing a document path is insufficient to access it.

## Step 2 — Create one authoritative booking lifecycle service

- [x] Define allowed transitions: `awaiting_payment → confirmed → checked_in → checked_out → completed`.
- [x] Support `cancelled`, `refunded` and `no_show` terminal paths.
- [x] Lock the booking during every transition.
- [x] Append `booking_status_history` for every successful transition.
- [x] Reject invalid/repeated transitions without creating duplicate effects.
- [x] Dispatch one domain event per successful transition using an idempotency key.
- [x] Add service tests for every allowed and rejected transition.

**Done when:** controllers cannot change booking status directly.

## Step 3 — Complete marketplace checkout accuracy

- [x] Add a server quote endpoint using `BookingPricingService`.
- [x] Have the confirmation modal request that quote before final submission.
- [x] If price changed, display the new total and require confirmation again.
- [x] Display house rules, cancellation terms and configured check-in/out times before booking.
- [x] Capture guest phone number and optional special requests.
- [x] Use listing check-in time for the 48-hour cancellation calculation; use 14:00 only as fallback.
- [x] Keep the final locked availability recheck and client-price rejection.

**Done when:** the amount acknowledged by the guest equals the authoritative amount charged/recorded.

## Step 4 — Add owner-created manual bookings

- [x] Add “Create booking” to the owner Bookings workspace.
- [x] Capture property, source, lead guest, phone/email, dates, adults/children, price, payment state and notes.
- [x] Support `walk_in`, `phone`, `whatsapp`, `referral` and `corporate` sources.
- [x] Reuse `PropertyAvailabilityService` and the same date allocation rules as marketplace checkout.
- [x] Create guest, booking, payment/history and availability records transactionally.
- [x] Add duplicate submission protection.
- [x] Add tests for conflict, capacity and cross-business access.

**Done when:** advertised manual channels create real protected bookings visible in Bookings, Calendar and Finance.

## Step 5 — Add owner check-in, no-show and checkout actions

- [x] Add confirmation modals to each action.
- [x] Check-in only a confirmed booking.
- [x] Warn about outstanding balance and incomplete preparation work.
- [x] Record operator, actual time, notes and basic key/identity confirmation in `booking_check_ins`.
- [x] Mark eligible missed arrivals as no-show.
- [x] Checkout only a checked-in booking.
- [x] Record operator, actual time, condition notes and damage flag in `booking_check_outs`.
- [x] Show all transitions on owner and guest booking timelines.

**Done when:** the owner can move a genuine stay from confirmed through checked out using the UI.

## Step 6 — Automate cleaning and inspection

- [x] On confirmation, create one booking-linked pre-arrival readiness task.
- [x] Use the property's cleaning preferences/default schedule to calculate its due time.
- [x] On checkout, create one booking-linked turnover-cleaning task.
- [x] Let owner assign a cleaner and change due time/priority.
- [x] Add a minimum cleaning checklist and completion notes.
- [x] Allow before/after image evidence.
- [x] On cleaning completion, create one inspection task.
- [x] Inspection records pass/fail, notes and optional evidence.
- [x] Passed inspection completes the booking and marks the property ready.
- [x] Failed inspection creates either repeat cleaning or a maintenance issue/task.
- [x] Make every automation idempotent so refreshes/retries cannot duplicate tasks.

**Done when:** checkout automatically drives the property back to ready through cleaning and inspection.

## Step 7 — Give operational staff a usable task surface

- [x] Add a mobile-friendly “My tasks” page for assigned employees.
- [x] Permit cleaners/inspectors/maintenance staff to see only assigned or role-permitted tasks.
- [x] Allow start, complete, checklist, notes and evidence actions.
- [x] Prevent staff from accessing owner finance, other businesses or unrelated guest information.
- [x] Let owners reassign work.
- [x] Add overdue highlighting and role/tenant authorization tests.

**Done when:** operational work does not require the property owner to impersonate every employee.

## Step 8 — Add minimum product notifications

- [x] Persist an in-app notification for booking confirmed.
- [x] Notify the correct owner about a new booking or cancellation.
- [x] Notify assigned staff of new/reassigned work.
- [x] Notify owner about overdue work or failed inspection.
- [x] Notify guest of confirmation, cancellation/refund and booking status changes.
- [x] Alert owner/admin about repeated iCal failures and external/native conflicts.
- [x] Add unread count and notification list in the navigation/dashboard.
- [x] Queue email delivery where configured without losing the in-app record on email failure.

**Done when:** important lifecycle changes are visible without manually opening every workspace.

## Step 9 — Make the Booking workspace the operational case file

- [x] Add a clear lifecycle/status rail.
- [x] Show guest/contact, stay, payment/balance, property and source summaries.
- [x] Show linked cleaning, inspection and maintenance work.
- [x] Show payments, cancellation/refund and date-change records.
- [x] Build one chronological timeline from status history and relevant events.
- [x] Keep quick actions for reschedule, check-in, checkout, no-show and cancellation state-aware.

**Done when:** the owner can understand and manage one stay without switching across unrelated screens.

## Step 10 — Close basic finance and calendar operations

- [x] Show per-booking subtotal, discount, fee, total, paid, refunded and outstanding.
- [x] Provide a printable/downloadable confirmation receipt.
- [x] Ensure manual payments and expenses remain business/property/booking scoped.
- [x] Add a basic date/property revenue and expense report.
- [x] Display booking, cleaning, inspection, maintenance and manual blocks on the owner calendar.
- [x] Surface iCal last-sync/stale/failure/conflict status prominently.
- [x] Document and verify production scheduler and queue-worker commands.

**Done when:** money and time-based work agree with the booking record.

## Step 11 — Add one simple in-stay support flow

- [x] Guest submits a booking-linked request with category, message and urgency.
- [x] Owner views, acknowledges and resolves the request.
- [x] Cleaning/maintenance categories can create a linked task.
- [x] Request activity appears on the booking timeline.
- [x] Both sides receive an in-app notification.

**Done when:** a checked-in guest can report a real issue and see its resolution state.

## Step 12 — Release verification

- [x] Run fresh MySQL migrations and seed twice without duplicates.
- [x] Run the full PHPUnit suite on SQLite and MySQL.
- [x] Run a dedicated MySQL concurrent-booking test.
- [x] Compile Blade, route cache, config cache and production Vite assets.
- [ ] Exercise owner, admin, guest and staff flows on desktop and mobile.
- [x] Verify every new mutation is tenant/role scoped through feature tests.
- [x] Verify private-document access and upload limits.
- [x] Run automated scheduler/queue/iCal sync and failure-recovery checks.
- [ ] Rehearse database backup and restore.
- [x] Record production environment variables, cron/worker setup and rollback steps.
- [ ] Fix all P0 defects; document accepted P1 limitations.

**Done when:** the complete acceptance journey passes with no direct database intervention.

## MVP 1 extension — Verified-stay reviews

- [x] Create one time-limited review invitation when a booking becomes completed.
- [x] Allow only the booking guest to submit one review after a completed stay.
- [x] Capture overall, cleanliness, communication, location, value and accuracy ratings.
- [x] Mark eligible reviews as verified stays and publish them on the marketplace listing.
- [x] Let the correct business owner post one public response.
- [x] Notify the guest and owner at the relevant review events.
- [x] Test incomplete stays, duplicate reviews/responses and cross-user/business access.

**Done when:** marketplace ratings come from completed bookings rather than presentation-only data.

## MVP 1 launch gate

MVP 1 is ready only if all answers below are **yes**:

- [x] Can an owner create a property and can an admin publish it?
- [x] Can a guest find it, see accurate availability and book it?
- [x] Is price calculated by the server and payment clearly marked simulated?
- [x] Can concurrent guests or known iCal blocks never occupy the same night?
- [x] Can an owner record offline/manual bookings safely?
- [x] Does confirmation create the required operational preparation?
- [x] Can owner/staff check in, support and check out the guest?
- [x] Does checkout generate cleaning and inspection automatically?
- [x] Does passed inspection return the property to ready?
- [x] Are booking, finance, calendar and operational records consistent?
- [x] Are cancellations/refunds/date releases consistent?
- [x] Are important events and calendar conflicts visible as notifications?
- [x] Are sensitive documents private and all tenant boundaries tested?
- [ ] Does the full MySQL and browser release gate pass?

## Can wait until after MVP 1

- AI assistant and predictive recommendations;
- full accounting/tax/invoice suite;
- configurable workflow builder;
- recurring operations and workforce optimisation;
- Google/Outlook/WhatsApp native APIs;
- real payment gateway/webhooks;
- advanced reports, inventory and suppliers;
- loyalty, upsells, smart locks and mobile apps.

These should not delay the core owner-operated pilot.
