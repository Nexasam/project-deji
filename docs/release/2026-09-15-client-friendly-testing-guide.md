# Verified Shortlet — Simple Client Testing Guide

## Purpose

This guide helps the client test the main Verified Shortlet MVP journey without technical knowledge.

**Test website:** [https://shortlet.emiplug.com](https://shortlet.emiplug.com)

**Estimated time:** 35–45 minutes

> Payments are simulated in this MVP. No real card or bank transaction will occur.

## Before starting

Use separate browsers or private/incognito windows so the Guest, Property Owner, Staff and Platform Administrator can remain signed in at the same time.

| Browser | User to keep signed in |
|---|---|
| Browser 1 | Guest |
| Browser 2 | Property Owner |
| Browser 3 | Staff member |
| Browser 4 | Platform Administrator |

## Test accounts

Business users and guests sign in at:

[https://shortlet.emiplug.com/login](https://shortlet.emiplug.com/login)

| User | Email | Password |
|---|---|---|
| Guest | `guest.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Property Owner | `owner@coastlineresidences.test` | `Password123!` |
| Property Manager | `manager.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Reception | `reception.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Accountant | `accountant.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Operations Manager | `operations.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Cleaner | `cleaner.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Maintenance Technician | `maintenance.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Inspector | `inspector.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Customer Support | `support.demo@verifiedshortlet.test` | `DemoPassword123!` |

Platform administrators sign in at:

[https://shortlet.emiplug.com/admin/login](https://shortlet.emiplug.com/admin/login)

| Administrator | Email | Password |
|---|---|---|
| Verification & Moderation | `verification.admin@verifiedshortlet.test` | `DemoPassword123!` |
| Support & Disputes | `disputes.admin@verifiedshortlet.test` | `DemoPassword123!` |
| Platform Super Administrator | Provided privately | Provided privately |

## Test 1 — Find a property

Open [https://shortlet.emiplug.com](https://shortlet.emiplug.com).

1. Scroll to the property-search section.
2. Search for **Lekki**.
3. Try the price, guest and bedroom filters.
4. Confirm the page remains around the property results after filtering.
5. Open **Chevron Family Residence**.

**Pass if:**

- Property cards display images, location, guest capacity and price.
- Filters change the displayed properties.
- Only published and verified properties appear.
- The page does not jump back to the top unnecessarily.

## Test 2 — Review property details and availability

Open:

[https://shortlet.emiplug.com/stays/chevron-family-residence](https://shortlet.emiplug.com/stays/chevron-family-residence)

1. Select different image thumbnails.
2. Review the property description, amenities, guest capacity and policies.
3. Move the availability calendar several months forward.
4. Identify available and unavailable dates.

**Pass if:**

- Selecting a thumbnail changes the main image.
- The page shows useful property and booking information.
- The calendar can browse up to 18 months ahead.
- Booked dates cannot be selected.

## Test 3 — Make a guest booking

Sign in as the Guest.

1. Return to Chevron Family Residence.
2. Choose three consecutive available nights.
3. Select the number of adults and children.
4. Enter a telephone number and optional request.
5. Select **Review booking**.
6. Check the confirmation window carefully.
7. Confirm the booking once.

**Pass if:**

- The confirmation shows check-in, checkout, nights and guests.
- It shows the nightly amount, subtotal, discount where applicable, service fee and total.
- The confirmed booking has a `VS-` reference.
- The booking page shows property information, dates, payment breakdown, special request and timeline.
- A printable receipt is available.

Copy the new `VS-` booking reference for the remaining tests.

## Test 4 — Confirm double-booking protection

In a separate private window:

1. Open Chevron Family Residence.
2. Navigate to the month just booked.
3. Try to select the same nights.

**Pass if:**

- The newly booked nights are unavailable.
- The application does not allow an overlapping booking.

## Test 5 — Review the owner dashboard

Sign in as the Property Owner.

Open these pages in order:

1. [Owner dashboard](https://shortlet.emiplug.com/owner/dashboard)
2. [Notifications](https://shortlet.emiplug.com/notifications)
3. [Bookings](https://shortlet.emiplug.com/owner/bookings)
4. [Calendar](https://shortlet.emiplug.com/owner/calendar)
5. [Finance](https://shortlet.emiplug.com/owner/finance)

Search for the new `VS-` reference.

**Pass if:**

- The owner can find and open the new booking.
- The booking contains guest details, payment, tasks, requests and history.
- The entire booked range appears on the calendar.
- The payment contributes to the Finance information.
- The owner receives relevant notifications.

## Test 6 — Review properties and onboarding

As the Property Owner, open:

[https://shortlet.emiplug.com/owner/properties](https://shortlet.emiplug.com/owner/properties)

1. Try property search and list/grid controls.
2. Confirm property cards have images and clear statuses.
3. Open a property and review its details.
4. Start a test property only if the group wants to review onboarding.
5. Walk through location, property information, amenities, media, inventory, documents, pricing and booking channels.
6. Confirm Airbnb and Booking.com can be skipped.
7. Review the final summary and submission-confirmation window.

**Pass if:**

- Draft information is retained between steps.
- Multiple media and documents can be previewed.
- The final review clearly summarises the listing.
- Submission requires confirmation.
- Publishing, verification and setup readiness are explained separately.

## Test 7 — Review team access

As the Property Owner, open:

[https://shortlet.emiplug.com/owner/team](https://shortlet.emiplug.com/owner/team)

1. Find the Cleaner, Reception, Accountant and Operations Manager.
2. Review each person's role and property assignment.
3. Open **Edit access** for the Cleaner, but close it without saving.

**Pass if:**

- The owner can manage staff role and property access.
- Staff roles do not all receive the same menu or authority.
- A property assignment limits the records available to that employee.

## Test 8 — Assign and complete cleaning

As the Property Owner, open:

[https://shortlet.emiplug.com/owner/operations](https://shortlet.emiplug.com/owner/operations)

1. Find a cleaning task for the test booking, or create a test cleaning task.
2. Assign it to **Grace Cleaner**.

In Browser 3, sign in as the Cleaner.

1. Open [Assigned tasks](https://shortlet.emiplug.com/staff/tasks).
2. Start the assigned task.
3. Complete the checklist.
4. Add completion notes.
5. Optionally upload completion evidence.
6. Complete the task.

**Pass if:**

- The Cleaner sees assigned work rather than the full owner workspace.
- The completed notes and evidence return to authorised management.
- Opening `/owner/finance` as the Cleaner returns `403 Forbidden`.
- Opening `/owner/bookings` as the Cleaner returns `403 Forbidden`.

## Test 9 — Reception and guest arrival

Sign in as Reception.

1. Open [Bookings](https://shortlet.emiplug.com/owner/bookings).
2. Search for the test booking.
3. Review the structured check-in and checkout controls.
4. If the booking is currently eligible, complete check-in.
5. Review the [manual booking page](https://shortlet.emiplug.com/owner/bookings/create/manual) without creating a conflicting reservation.

**Pass if:**

- Reception can manage reservations and guest arrival/departure.
- Reception cannot open the Finance workspace.
- Manual booking uses the same availability protection as the marketplace.

## Test 10 — Guest support

For an eligible checked-in booking:

1. Sign in as the Guest and open **My bookings**.
2. Submit a maintenance or cleaning request.
3. Sign in as Customer Support.
4. Find the same booking and request.
5. Acknowledge and resolve it with a note.
6. Refresh the Guest booking page.

**Pass if:**

- The Guest can see the request and its progress.
- Authorised support staff can acknowledge and resolve it.
- The resolution appears to the Guest.
- Request activity appears in the booking history.

## Test 11 — Checkout, turnover and inspection

For an eligible booking:

1. Reception records checkout and room condition.
2. The owner opens Operations and finds the turnover-cleaning task.
3. The owner assigns turnover cleaning to the Cleaner.
4. The Cleaner completes the task and checklist.
5. The owner assigns the resulting inspection to the Inspector.
6. The Inspector records a pass result and findings.

**Pass if:**

- Checkout creates turnover work.
- Completing turnover creates an inspection.
- A passed inspection returns the property to Available.
- The booking becomes Completed.

## Test 12 — Verified review

After the booking becomes Completed:

1. Sign in as the Guest and submit a review.
2. Sign in as the Property Owner and add one response.
3. Return to the public property page.

**Pass if:**

- Only the completed-stay Guest can review.
- The review is marked as a verified stay.
- The owner response appears beneath the review.
- A second review or response is rejected.

## Test 13 — Accountant access

Sign in as the Accountant.

1. Open [Finance](https://shortlet.emiplug.com/owner/finance).
2. Filter by property and date.
3. Review revenue, payments and expenses.
4. Try opening `/owner/calendar` and `/owner/operations`.

**Pass if:**

- Finance information is available to the Accountant.
- Unrelated Calendar and Operations administration is denied.

## Test 14 — Platform property approval

Sign in as the Verification & Moderation Administrator.

1. Open [Platform properties](https://shortlet.emiplug.com/admin/properties).
2. Find **Eko Pearl Executive Residence**.
3. Review its business, location, media, amenities, price and listing information.
4. Publish it with a clear approval reason.
5. Open its public listing.

**Pass if:**

- The administrator can see enough information to make the decision.
- Publication requires confirmation.
- The approval is retained in platform history.
- The property becomes available publicly.

## Test 15 — Moderation and disputes

As the Verification & Moderation Administrator:

1. Open [Review moderation](https://shortlet.emiplug.com/admin/reviews).
2. Open **Needs a moderation decision**.
3. Review its booking and owner-response context.
4. Restore or hide it with a reason.

As the Support & Disputes Administrator:

1. Open [Disputes](https://shortlet.emiplug.com/admin/disputes).
2. Open `DSP-DEMO-OPEN`.
3. Review the guest, property, booking and financial context.
4. Move it to **Under review** and add a note.

**Pass if:**

- Moderation retains the original content and decision history.
- The dispute retains its assignee, priority, status, evidence and timeline.
- Each specialised administrator is prevented from performing unrelated administrator actions.

## Test 16 — iCal and channel availability

Use a genuine test Airbnb or Booking.com `.ics` export URL.

1. Sign in as the Property Owner.
2. Open a property and locate **Calendar sync**.
3. Add the external `.ics` URL.
4. Run manual synchronisation.
5. Open the Owner Calendar and find the imported dates.
6. Open the public property page and try those dates.
7. Copy the Verified Shortlet outbound `.ics` URL and open it externally.

**Pass if:**

- The external reservation appears on the Owner Calendar.
- Imported dates cannot be booked through the marketplace or manual booking.
- The Verified Shortlet outbound link returns a valid calendar file over HTTPS.
- Invalid or unsafe URLs are rejected.

Do not include the private outbound calendar token in screenshots, recordings or public messages.

## Final acceptance checklist

Mark each item after testing:

- [ ] Marketplace search and filters work.
- [ ] Property images, details and calendar display correctly.
- [ ] Guest booking and simulated payment work.
- [ ] Price calculation and receipt are correct.
- [ ] The same dates cannot be booked twice.
- [ ] Owner dashboard, booking, calendar and Finance reflect the booking.
- [ ] Property onboarding and platform approval work.
- [ ] Owner can manage team roles and property assignments.
- [ ] Cleaner sees only assigned work.
- [ ] Reception can manage booking arrival/departure.
- [ ] Guest support request and resolution work.
- [ ] Checkout creates turnover cleaning and inspection.
- [ ] Completed Guest can leave one verified review.
- [ ] Accountant receives Finance-only access.
- [ ] Platform review moderation works.
- [ ] Platform dispute management works.
- [ ] Genuine external iCal dates prevent double booking.
- [ ] Desktop display is acceptable.
- [ ] Mobile display is acceptable.
- [ ] No blocking error remains.

## What remains outside this MVP

The current MVP intentionally does not process real payments. It also excludes native mobile applications, subscription billing, enterprise accounting/reconciliation and production-scale background infrastructure.

These are later-stage improvements and do not prevent testing the complete simulated-payment property and booking workflow described above.

## How to report a problem

For every issue, record:

1. User account or role.
2. Page address.
3. What was clicked.
4. What was expected.
5. What actually happened.
6. Screenshot or short screen recording.
7. Booking or property reference where applicable.

This information will allow the issue to be reproduced and resolved quickly.
