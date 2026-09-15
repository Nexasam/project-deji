# Project Nexus MVP 1 Client Presentation Runbook

**Presentation objective:** Show that Project Nexus is not just a property marketplace. It connects property publishing, secure availability, guest booking, owner oversight, role-based operations, finance, service recovery and verified reviews in one auditable flow.

**Recommended duration:** 35 minutes for the main presentation, with 10 minutes for questions.

**Presentation story:** Follow one booking at **Chevron Family Residence** from marketplace discovery to completed stay, then publish **Eko Pearl Executive Residence** from the platform-admin workspace.

> All payment actions in this presentation are simulated. The presentation accounts and passwords are development data only and must not be retained as production credentials.

## 1. Before the client joins

### Prepare the application

From the project root, run:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
npm run build
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8000
```

`php artisan db:seed --force` is repeatable. It creates the presentation accounts and resets **Eko Pearl Executive Residence** to a pending review state. It does not delete existing bookings. During rehearsals, always select currently green dates on the calendar instead of memorising dates.

If a completely clean database is required, use `php artisan migrate:fresh --seed` only against an explicitly disposable presentation database. It deletes all data in the selected database.

### Use four isolated browser sessions

Keep all four sessions open so the presentation feels like a connected live operation rather than repeated logins.

| Browser session | Persona | Start page |
|---|---|---|
| A — normal browser | Public visitor, then guest | `http://127.0.0.1:8000/` |
| B — separate profile | Property owner | `http://127.0.0.1:8000/login` |
| C — private/incognito | Operations staff | `http://127.0.0.1:8000/login` |
| D — another browser/profile | Platform administrator | `http://127.0.0.1:8000/admin/login` |

Set the browser zoom to 90–100%, close developer tools, hide bookmarks containing client-sensitive information, and test every session once before the meeting.

### Presentation credentials

| Persona | Email | Password | Business / scope |
|---|---|---|---|
| Guest | `guest.demo@verifiedshortlet.test` | `DemoPassword123!` | Guest bookings only |
| Coastline owner | `owner@coastlineresidences.test` | `Password123!` | Full Coastline Residences access |
| Lagoon owner | `owner@lagoonstays.test` | `Password123!` | Full Lagoon Stays access |
| Property manager | `manager.demo@verifiedshortlet.test` | `DemoPassword123!` | Chevron at Coastline; Admiralty at Lagoon |
| Reception | `reception.demo@verifiedshortlet.test` | `DemoPassword123!` | Chevron reservations and front desk |
| Accountant | `accountant.demo@verifiedshortlet.test` | `DemoPassword123!` | Coastline finance, business-wide |
| Operations manager | `operations.demo@verifiedshortlet.test` | `DemoPassword123!` | Chevron operations |
| Cleaner | `cleaner.demo@verifiedshortlet.test` | `DemoPassword123!` | Assigned Chevron tasks only |
| Maintenance technician | `maintenance.demo@verifiedshortlet.test` | `DemoPassword123!` | Assigned Chevron tasks only |
| Inspector | `inspector.demo@verifiedshortlet.test` | `DemoPassword123!` | Assigned Chevron inspections only |
| Customer support | `support.demo@verifiedshortlet.test` | `DemoPassword123!` | Chevron bookings and guest support |
| Platform administrator | `admin@verifiedshortlet.test` | `AdminPassword123!` | Platform property review |
| Verification & moderation admin | `verification.admin@verifiedshortlet.test` | `DemoPassword123!` | Businesses, properties and review moderation |
| Support & disputes admin | `disputes.admin@verifiedshortlet.test` | `DemoPassword123!` | User security and dispute case management |

The platform-admin email or password may differ if `PLATFORM_ADMIN_EMAIL` or `PLATFORM_ADMIN_PASSWORD` is overridden in the environment.

Guest, owner and business-staff accounts sign in at `http://127.0.0.1:8000/login`. All three platform-administration roles sign in at `http://127.0.0.1:8000/admin/login`.

## 2. Opening statement — 60 seconds

Open Browser A at `http://127.0.0.1:8000/`.

Say:

> “Project Nexus connects the full serviced-apartment journey. A guest discovers and books a verified stay; the owner sees revenue and operational work immediately; each staff member sees only the work they are authorised to perform; checkout triggers cleaning and inspection; and only a completed guest can publish a verified review.”

Do not begin with the owner dashboard. Starting with the customer experience gives every later operational screen a clear reason to exist.

## 3. Act One — Guest discovery and protected booking

### Step 1: Search without losing context

**URL:** `http://127.0.0.1:8000/`

1. Scroll to the marketplace search section.
2. Apply **Lekki**, a guest count and a price filter.
3. Point out that the page remains anchored at the search/results section.
4. Clear only the price restriction if Chevron is filtered out.

**Visible proof:** Search, category and location filters update the live marketplace without throwing the guest back to the top of the page.

**Presenter line:** “The marketplace is optimised for comparison, not page jumping.”

### Step 2: Explore a rich property page

**URL:** `http://127.0.0.1:8000/stays/chevron-family-residence`

1. Click two gallery thumbnails and return to the cover photo.
2. Point out verified status, full description, capacity, check-in/out times and cancellation terms.
3. Use **Jump to month** to move several months ahead.
4. Explain the legend: green dates are available; red dates are unavailable; orange is the guest's current selection.

**Visible proof:** The calendar supports browsing up to 18 months and combines native bookings, owner blocks and synchronized iCal reservations.

### Step 3: Sign in without losing the property

If Browser A is signed out, click **Already have an account? Log in**, or open:

`http://127.0.0.1:8000/login?redirect=/stays/chevron-family-residence`

Sign in as the demo guest. The browser should return to Chevron Family Residence.

### Step 4: Create one marketplace booking

1. Choose any three consecutive green nights at least seven days ahead.
2. Select **3 adults** and **1 child**.
3. Enter `+234 801 234 5678`.
4. Enter the special request: `Please prepare a baby cot and share arrival instructions before check-in.`
5. Click **Review booking**.
6. Pause on the confirmation modal. Show nights, subtotal, any eligible discount, service fee and final total.
7. Click **Confirm & book** once.

**Visible proof:** The resulting booking page includes the property, guest breakdown, dates, price breakdown, payment reference, receipt link, special request and confirmed status.

**Presenter line:** “The price and availability are calculated again on the server at confirmation. The browser cannot force stale dates or an old total through checkout.”

Keep this guest booking page open. The new booking reference beginning with `VS-` is the reference used in the rest of the presentation.

### Step 5: Prove double-booking protection visually

Open a signed-out/private tab at:

`http://127.0.0.1:8000/stays/chevron-family-residence`

Navigate to the month just booked. The reserved nights should now be red and unselectable.

Explain that this is not only a visual restriction: availability is rechecked inside a database transaction, the property record is locked, and each active booked day is allocated uniquely. Imported iCal nights use the same availability check.

## 4. Act Two — The property owner receives the booking

In Browser B, sign in as `owner@coastlineresidences.test`.

### Step 6: Owner command centre

**URL:** `http://127.0.0.1:8000/owner/dashboard`

Show today's indicators, portfolio summary and operational alerts.

Then open:

`http://127.0.0.1:8000/notifications`

**Visible proof:** The owner has an in-app notification for the new marketplace booking.

### Step 7: Booking workspace and audit trail

**URL:** `http://127.0.0.1:8000/owner/bookings`

1. Search for the guest name `Zainab` or the `VS-` reference.
2. Open **View details**.
3. Show the lifecycle bar, payment status, guest/contact information, special request, operational tasks and timeline.

**Visible proof:** A single booking record connects finance, tasks, lifecycle history and guest communication.

### Step 8: Shared calendar and iCal

Open:

1. `http://127.0.0.1:8000/owner/calendar`
2. `http://127.0.0.1:8000/owner/properties`
3. Click **Chevron Family Residence** and scroll to Calendar sync.

Show the newly reserved dates in the calendar. On the property page, explain:

- Airbnb and Booking.com accept their calendar export `.ics` URL and may be skipped when the property is not listed there.
- Verified Shortlet provides an outbound `.ics` URL to paste into the external channel.
- Manual sync and scheduled sync both feed the same availability engine.
- The private export token must not be copied into slides, recordings or chat.

Do not add a fake external URL during the client presentation.

### Step 9: Finance appears automatically

**URL:** `http://127.0.0.1:8000/owner/finance`

Filter to Chevron if helpful and show the booking payment in transaction history and revenue by property.

**Presenter line:** “In this MVP the provider charge is simulated, but the booking, payment ledger, receipt and finance reporting flow are connected exactly where the real gateway will attach later.”

### Step 10: Owner controls the team

**URL:** `http://127.0.0.1:8000/owner/team`

Show the seeded team, roles and Chevron property chips. Open **Edit access** for one person, but do not save a change.

**Visible proof:** Roles decide actions, and property assignment narrows the records the person may access.

### Step 11: Assign and complete pre-arrival cleaning

**URL:** `http://127.0.0.1:8000/owner/operations`

1. Find **Pre-arrival cleaning and readiness** for the new `VS-` booking.
2. Assign it to **Grace Cleaner**.
3. Switch to Browser C and sign in as `cleaner.demo@verifiedshortlet.test`.
4. The cleaner should land at `http://127.0.0.1:8000/staff/tasks`.
5. Start the assigned task.
6. Tick all required checklist items.
7. Enter `Property cleaned, linen replaced and guest essentials restocked.`
8. Complete the task. Evidence upload is optional for the live presentation.

**Visible proof:** Grace sees only work assigned to Grace, not the whole business operations board.

## 5. Act Three — Check-in, guest support and checkout

### Step 12: Reception handles arrival

In Browser C, sign out and sign in as `reception.demo@verifiedshortlet.test`.

**URL:** `http://127.0.0.1:8000/owner/bookings`

1. Open the `VS-` booking.
2. Click **Check in**.
3. Mark identity and balance checks complete, choose an access method, and confirm.

**Visible proof:** The booking becomes **Checked in**, the lifecycle and timeline update, and the property becomes occupied.

Reception can also demonstrate owner-recorded reservations at:

`http://127.0.0.1:8000/owner/bookings/create/manual`

Do not save a second booking during the main story. Explain that marketplace and manual bookings use the same availability protection.

### Step 13: Guest requests help during the stay

Return to Browser A and refresh the guest booking page. If needed, open:

`http://127.0.0.1:8000/guest/bookings`

1. Open the active booking.
2. Under **Need help during your stay?**, choose **Maintenance** and **High**.
3. Enter `The bedroom air conditioner is running but is not cooling properly.`
4. Send the request.

**Visible proof:** The guest sees the request status, and urgent cleaning/maintenance requests create linked operational work automatically.

### Step 14: Customer support closes the loop

In Browser C, sign out and sign in as `support.demo@verifiedshortlet.test`.

**URL:** `http://127.0.0.1:8000/owner/bookings`

Open the booking, acknowledge the request, then resolve it with:

`Technician attended, cleaned the filter and restored normal cooling.`

Return briefly to Browser A and refresh to show the resolution to the guest.

### Step 15: Reception completes checkout

Return Browser C to the reception account, open the booking and click **Check out**.

Use:

- Access returned: **Returned**
- Room condition: **Requires cleaning**
- Damage status: **No damage**
- Handover notes: `Guest departed successfully. No damage reported.`

**Visible proof:** Checkout automatically creates a turnover-cleaning task and changes the property's operational state to cleaning.

## 6. Act Four — Automated turnover, inspection and verified review

### Step 16: Owner assigns turnover work

In Browser B, refresh:

`http://127.0.0.1:8000/owner/operations`

Find **Turnover cleaning after checkout** for the `VS-` booking and assign it to **Grace Cleaner**.

### Step 17: Cleaner completes turnover

In Browser C, sign in as Grace Cleaner again.

1. Open `http://127.0.0.1:8000/staff/tasks`.
2. Start the turnover task.
3. Tick every required checklist item.
4. Enter `Turnover complete; property cleaned, restocked and photographed.`
5. Complete the task.

**Visible proof:** Completion automatically creates **Post-cleaning property inspection** and changes the property state to inspection.

### Step 18: Inspector certifies readiness

In Browser B, assign the new inspection task to **Ife Inspector**.

In Browser C, sign out and sign in as `inspector.demo@verifiedshortlet.test`.

1. Open `http://127.0.0.1:8000/staff/tasks`.
2. Start the inspection.
3. Select **Pass — property ready**.
4. Enter `Cleanliness, inventory, amenities and property condition verified.`
5. Record the inspection.

**Visible proof:** The booking becomes completed, the property returns to available, and the guest receives a review invitation.

### Step 19: Verified guest review and owner response

In Browser A, refresh the guest booking page.

1. Give the stay five stars.
2. Set the optional category scores.
3. Use the title `A smooth and well-managed stay`.
4. Use the review: `The apartment was clean, check-in was organised and the support team responded quickly when we needed help.`
5. Publish the review.

In Browser B, open the booking and respond:

`Thank you for staying with us, Zainab. We are delighted that the team made your visit comfortable and we look forward to welcoming you again.`

Finally reopen:

`http://127.0.0.1:8000/stays/chevron-family-residence`

Scroll to **Guest reviews**.

**Visible proof:** Only a completed booking guest can leave one verified review, and the approved owner response appears with it on the marketplace.

## 7. Act Five — Platform trust, moderation and case management

In Browser D, sign in at `http://127.0.0.1:8000/admin/login`.

For the broad control-centre tour, use the Platform Super Administrator. To prove least privilege, repeat one short segment with either specialised administrator account: the verification administrator cannot lock users or manage disputes, while the support administrator cannot publish properties or moderate reviews.

### Step 20: Open the platform control centre

**URL:** `http://127.0.0.1:8000/admin`

Show the actionable business-verification, property-review, review-attention, dispute, restricted-user and operational-health counts. Point out that the sidebar changes with platform permissions and direct URLs enforce the same boundary.

### Step 21: Review a submitted property

**URL:** `http://127.0.0.1:8000/admin/properties`

1. Find **Eko Pearl Executive Residence**.
2. Open the review record.
3. Show the business, marketplace title, slug, type, capacity, price, location, description, amenities, media and booking channels.
4. Point out that the administrator may publish or reject with a recorded correction reason.

### Step 22: Publish and prove the result

Click **Publish property**, enter a clear approval reason in the confirmation modal, and confirm.

Then open:

`http://127.0.0.1:8000/stays/eko-pearl-executive-residence`

**Visible proof:** Publishing updates property verification and marketplace eligibility together, and the stay becomes immediately discoverable.

**Presenter line:** “Owners prepare and submit; the platform makes the trust decision; only approved listings enter the public marketplace.”

### Step 23: Business verification and safe suspension

Open `http://127.0.0.1:8000/admin/businesses`, then inspect **Coastline Residences**. Show its operator profile, team, property portfolio, booking footprint and administrative history. Explain that suspension preserves publication records but immediately removes the operator from new marketplace discovery and booking, while existing stay completion remains available. Do not suspend the main demo operator during the presentation unless this is a disposable rehearsal database.

### Step 24: Review moderation without content deletion

Open `http://127.0.0.1:8000/admin/reviews` and select **Needs a moderation decision**. Show that the original review, owner response, booking context and moderation timeline are retained. Restore the hidden review with a reason, verify it becomes public, then hide it again only if you want to leave the repeatable demo state unchanged.

### Step 25: Dispute case management

Open `http://127.0.0.1:8000/admin/disputes` and select `DSP-DEMO-OPEN`. Show the linked booking, guest, property, business, payment/support evidence, priority, assignee and immutable timeline. Move it to **Under review** with a case note. Explain that a resolution can record an approved amount but deliberately cannot execute a refund in this simulated-payment MVP.

### Step 26: Account security, configuration and audit

1. Open `http://127.0.0.1:8000/admin/users` and inspect a demo account. Explain immediate database-session termination, self-lock protection and super-admin protection; do not lock a persona needed later.
2. Open `http://127.0.0.1:8000/admin/settings` and show the small approved configuration catalogue. Do not enable **Require verified businesses** until the presentation operators themselves are verified.
3. Open `http://127.0.0.1:8000/admin/audit` and filter by business or event type to show the permanent decision trail.

## 8. Role-based browser test matrix

Use this section for client questions or the formal acceptance test after the presentation. It is not necessary to perform every row in the main 35-minute story.

| Role | URLs/actions that should work | One boundary to demonstrate |
|---|---|---|
| Business owner | `/owner/dashboard`, `/owner/properties`, `/owner/bookings`, `/owner/calendar`, `/owner/finance`, `/owner/operations`, `/owner/team` | Full business control; platform admin remains separate |
| Property manager | Dashboard, properties, bookings, calendar, operations, team invitations; switch Coastline/Lagoon from sidebar | `/owner/finance` returns forbidden; only Chevron or Admiralty appears in its respective business |
| Reception | Dashboard, property view, bookings, manual booking, date changes, cancellation, check-in/out, calendar blocks | `/owner/operations` and `/owner/finance` return forbidden |
| Accountant | Dashboard, property view, booking history, `/owner/finance`, payment/expense reporting | `/owner/calendar` and `/owner/operations` return forbidden |
| Operations manager | Dashboard, scoped properties, booking history, calendar, operations and team invitation | `/owner/finance` returns forbidden |
| Cleaner | `/owner/` redirects to `/staff/tasks`; start and complete only assigned work | Cannot see another employee's task or finance workspace |
| Maintenance technician | `/staff/tasks`; complete assigned maintenance work with evidence | Cannot see unassigned tasks or finance workspace |
| Inspector | `/staff/tasks`; pass/fail assigned booking inspections with evidence | Cannot inspect another property outside assignment |
| Customer support | Dashboard, scoped property and booking records; acknowledge/resolve guest requests | No finance or operations-board access |
| Platform super administrator | `/admin`, all platform modules, configuration | Cannot lock their own current account |
| Verification & moderation admin | Businesses, properties, reviews and contextual dispute viewing | Cannot lock users, manage disputes or edit configuration |
| Support & disputes admin | Users, business/property context, reviews and dispute management | Cannot verify businesses, publish properties, moderate reviews or edit configuration |
| Guest | Marketplace, own bookings, receipt, in-stay request, eligible cancellation, completed-stay review | Cannot open another guest's booking or owner workspace |

Do not spend presentation time forcing several ugly 403 pages. Demonstrate one clean access boundary, then explain that navigation is permission-aware and direct URLs are also protected server-side.

### How `/owner/team` access actually works

`/owner/team` does **two separate jobs**. It is not merely a property-access screen:

1. **Role permissions control modules and actions.** The selected role determines whether a person may open or act in Properties, Bookings, Calendar, Finance, Operations and Team administration. For example, an accountant can open Finance but cannot open Calendar; reception can manage reservations and check-in/out but cannot open Finance; a cleaner can complete assigned tasks but cannot administer the business.
2. **Property scope controls which records are visible inside the permitted modules.** Selecting Chevron means the person sees only Chevron records in modules their role already permits. Leaving the property selection empty means business-wide records, but still only within that role's permissions.

Both boundaries are enforced on the server. Hiding a sidebar item is only the user-interface reflection of the permission decision; manually entering an unauthorised URL still returns `403 Forbidden`. Cross-business and out-of-scope property records are also rejected.

### Business-role module and action matrix

Use this table when the client asks what each team role can actually do. “Scoped” means the role is further limited by the properties selected on `/owner/team`.

| Role | Dashboard | Properties | Bookings | Calendar | Finance | Operations | Team administration |
|---|---|---|---|---|---|---|---|
| Business owner | Full | Full setup, edit, documents, pricing and listing | Full lifecycle | Full blocks and iCal | Full MVP finance | Full task control | Invite, change role/scope, deactivate |
| Property manager | Full, scoped | Full, scoped | Full, scoped | Full, scoped | No finance workspace; may record permitted operational payment information | Full, scoped | Invite team; cannot grant owner role |
| Reception | Limited | View/edit operational property data, scoped | Create/manual booking, amend, cancel, check-in/out and guest communication | View and block dates, scoped | No finance workspace; may record a payment | No management board | None |
| Accountant | Limited | View and property finance context | Read booking/payment history | None | Full MVP ledger, revenue, expense and reports | None | None |
| Operations manager | Full, scoped | Operational property management, scoped | Read history, scoped | View/block dates, scoped | None | Create, assign, schedule, verify and complete tasks | Invite operational staff; cannot grant owner role |
| Customer support | Limited, scoped | Read, scoped | Read/create/amend/cancel and resolve guest requests, scoped | None | None | No task-management board | None |
| Cleaner | Task entry only | Only minimal assigned-property context | No booking workspace | No calendar workspace | None | Only tasks assigned to that cleaner | None |
| Maintenance technician | Task entry only | Only minimal assigned-property context | No booking workspace | No calendar workspace | None | Only assigned maintenance tasks | None |
| Inspector | Task entry only | Only minimal assigned-property context | No booking workspace | No calendar workspace | None | Only assigned inspections; records pass/fail evidence | None |

The current MVP exposes Finance as a connected operational summary and ledger. Advanced accounting controls, bank reconciliation, tax management, payouts and live refunds remain later-phase functions even though the permission catalogue reserves those capability names.

### Cleaner and owner-administration acceptance test

This is the best short demonstration that role permissions are not cosmetic.

1. Sign in as the Coastline owner and open `http://127.0.0.1:8000/owner/team`.
2. Find **Grace Cleaner**. Confirm the role is **Cleaner** and the property chip is **Chevron Family Residence**.
3. Open **Edit access** and show that the owner can change the cleaner's job title, role and property scope. Close without saving during the main presentation.
4. Open `http://127.0.0.1:8000/owner/operations`, assign a Chevron cleaning task to Grace and retain the task reference.
5. In a separate browser, sign in as `cleaner.demo@verifiedshortlet.test`. Open `http://127.0.0.1:8000/owner/`; it should route to `http://127.0.0.1:8000/staff/tasks`.
6. Confirm Grace sees the assigned task, property, due time, priority and checklist, but does not see an unrelated employee's task.
7. Start the task, complete its checklist, add completion notes and optionally upload private evidence.
8. Directly enter `http://127.0.0.1:8000/owner/finance`; expect `403 Forbidden`.
9. Directly enter `http://127.0.0.1:8000/owner/bookings`; expect `403 Forbidden`.
10. Return to the owner session, refresh Operations and confirm the task status, notes and evidence are visible to authorised management.

**Defence statement:** “The owner administers people, roles, property scope and task assignment. The cleaner receives enough context to execute assigned work, but not guest, booking, finance or wider portfolio administration.”

## 9. Systematic browser links

### Public and guest

1. Marketplace: `http://127.0.0.1:8000/`
2. Chevron stay: `http://127.0.0.1:8000/stays/chevron-family-residence`
3. Guest login returning to Chevron: `http://127.0.0.1:8000/login?redirect=/stays/chevron-family-residence`
4. Guest booking list: `http://127.0.0.1:8000/guest/bookings`
5. Guest notifications: `http://127.0.0.1:8000/notifications`
6. Published demo stay: `http://127.0.0.1:8000/stays/eko-pearl-executive-residence`

Booking-detail and receipt URLs contain generated booking IDs. Open them from **My bookings** instead of pasting a saved UUID.

### Property owner and business roles

1. Login: `http://127.0.0.1:8000/login`
2. Smart role entry: `http://127.0.0.1:8000/owner/`
3. Dashboard: `http://127.0.0.1:8000/owner/dashboard`
4. Properties: `http://127.0.0.1:8000/owner/properties`
5. Property wizard: `http://127.0.0.1:8000/owner/properties/create/step1`
6. Bookings: `http://127.0.0.1:8000/owner/bookings`
7. Manual booking: `http://127.0.0.1:8000/owner/bookings/create/manual`
8. Calendar: `http://127.0.0.1:8000/owner/calendar`
9. Finance: `http://127.0.0.1:8000/owner/finance`
10. Operations: `http://127.0.0.1:8000/owner/operations`
11. Team and access: `http://127.0.0.1:8000/owner/team`
12. Notifications: `http://127.0.0.1:8000/notifications`

Property and booking details use generated UUIDs. Open them from their list pages so the walkthrough remains valid after reseeding.

### Task-only staff

1. Login: `http://127.0.0.1:8000/login`
2. Smart role entry: `http://127.0.0.1:8000/owner/`
3. Assigned tasks: `http://127.0.0.1:8000/staff/tasks`
4. Notifications: `http://127.0.0.1:8000/notifications`

### Platform administrator

1. Admin login: `http://127.0.0.1:8000/admin/login`
2. Platform dashboard: `http://127.0.0.1:8000/admin`
3. Business directory: `http://127.0.0.1:8000/admin/businesses`
4. User and security directory: `http://127.0.0.1:8000/admin/users`
5. Property review queue: `http://127.0.0.1:8000/admin/properties`
6. Review moderation: `http://127.0.0.1:8000/admin/reviews`
7. Dispute management: `http://127.0.0.1:8000/admin/disputes`
8. Platform configuration: `http://127.0.0.1:8000/admin/settings`
9. Audit history: `http://127.0.0.1:8000/admin/audit`
10. Published listing proof: `http://127.0.0.1:8000/stays/eko-pearl-executive-residence`

Admin property-review URLs contain generated UUIDs. Open Eko Pearl from the review queue.

## 10. Property-owner acceptance checklist

After the presentation, test the owner experience independently:

- Create a draft from `/owner/properties/create/step1` and complete all ten steps.
- Upload multiple photos, a video and multiple documents; remove one item and verify previews.
- Leave Airbnb and Booking.com blank and confirm the optional channels can be skipped.
- Review the final listing, confirm the submission modal and submit for platform review.
- Verify the owner property page explains **Publishing**, **Verification** and **Setup** instead of showing unexplained duplicate status labels.
- Publish through platform admin, then verify the property card image and public gallery.
- Connect one valid external `.ics` URL in a controlled test environment, sync it and verify imported dates are unavailable publicly.
- Create a manual availability block, then verify marketplace and manual booking both reject the blocked dates.
- Create a manual booking from reception and confirm it appears in bookings, calendar, finance and operations.
- Cancel one expendable booking and verify the warning modal, refund outcome, released dates and cancelled tasks.
- Invite a disposable staff member, assign a role and property, test access, then deactivate the membership.

## 10A. Complete promise-to-proof acceptance flow

Use this after the story presentation when the client wants systematic evidence against the delivered update.

| Promised area | Test action | Expected evidence | Important limitation to state |
|---|---|---|---|
| Marketplace and search | Filter location, price, type, beds, guests and dates from `/`; open a stay | Results remain anchored; only eligible published stays display; real gallery and details render | Search is web MVP; no native mobile app |
| Guest identity and isolation | Log in as demo guest; open My bookings; attempt another guest's generated detail URL in a controlled test | Own records open; foreign record is not exposed | Do not show real personal data in a presentation database |
| Protected booking | Select green dates, review quote, confirm once; revisit same dates in another browser | Server-calculated total and receipt; dates become unavailable; overlap is rejected | Payment provider result is simulated |
| Cancellation | Cancel an eligible expendable booking through the warning modal | Reason/history stored, refund outcome recorded, dates released, obsolete tasks cancelled | No live money movement |
| Property setup | Complete ten wizard steps including multiple media/documents and optional channel skips | Draft resumes, previews render, final review is rich, confirmation modal precedes submission | Admin approval remains mandatory before publication |
| Platform property review | Open pending Eko Pearl in admin, inspect all details, publish with a reason | Marketplace listing appears and audit history records the decision | Use reject/unpublish only on disposable demo data |
| Calendar and iCal | View native booking range; add/release an owner block; sync a controlled `.ics`; inspect outbound export | Every occupied night is painted; all sources prevent booking the same dates | Public HTTPS hosting is required for realistic third-party callbacks/import tests |
| Booking lifecycle | Manual/marketplace booking → check-in → support → checkout → turnover → inspection | Status timeline, operational state and notifications advance in order | Use separate browser personas to make handoffs clear |
| Cleaning and operations | Assign cleaner; complete checklist/evidence; assign inspector and pass | Cleaner sees only assigned work; completion creates inspection; pass restores availability | Inventory automation is not a full stock-management product yet |
| Guest support | Guest creates in-stay request; authorised support acknowledges/resolves | Linked booking/property context and status history appear to both sides | External call-centre integrations are deferred |
| Verified reviews | Complete stay through passed inspection; guest reviews once; owner responds | Verified review and approved owner response appear publicly | Platform moderation can hide/restore without deleting history |
| Finance and reporting | Open Finance after a booking/payment; filter by property and inspect transaction/revenue/expense summaries | Booking amount and payment ledger reconcile to the same source record | Advanced accounting, payouts and real gateway settlement are deferred |
| Team, roles and scope | Run the cleaner test above; repeat Finance with accountant and Bookings with reception | Correct navigation is shown; direct unauthorised URLs return 403; assigned properties narrow records | Custom per-user override administration is not yet exposed as a general owner UI |
| Notifications | Trigger booking, assignment, support resolution and review invitation; open `/notifications` | Role-relevant events appear, can be opened and marked read | Email delivery needs a deployed queue worker and valid mail configuration |
| Platform trust administration | Show dashboard, businesses, users, properties, reviews, disputes, settings and audit | Specialised admin navigation and direct-route permissions differ by role | Subscription administration and audited impersonation remain later work |

## 10B. Rehearsal sign-off sheet

Run this once on the exact hosted environment before sharing the client URL. Record the tester, time and evidence screenshot/reference for each line.

- [ ] HTTPS, application URL, storage links and uploaded media work from a non-local device.
- [ ] Database migrations and presentation seeder complete successfully.
- [ ] Guest, owner, cleaner, reception, accountant and all three admin credentials work.
- [ ] A new booking is immediately unavailable in a second isolated browser.
- [ ] Owner calendar paints the full stay range, including completed historical bookings where applicable.
- [ ] One controlled Airbnb/Booking.com-compatible `.ics` feed imports and blocks its dates.
- [ ] Verified Shortlet outbound `.ics` downloads through the public HTTPS URL and contains active occupancy.
- [ ] Scheduler invokes calendar synchronization and the queue worker processes notification email jobs.
- [ ] Cleaner sees only the assigned task and cannot open Finance or Bookings.
- [ ] Reception can check in/out but cannot open Finance or the management Operations board.
- [ ] Accountant can open Finance but cannot manage Calendar or Operations.
- [ ] Property manager sees only assigned properties and cannot open the Finance workspace.
- [ ] Platform specialised admins are denied actions outside their platform permissions.
- [ ] Property publication, review moderation and dispute transition each appear in audit history.
- [ ] Desktop and mobile layouts complete the golden path without console or missing-resource errors.
- [ ] Backup and restore are tested on the hosting environment.

### Latest automated evidence

On 15 September 2026, the focused defence suite passed **75 tests with 474 assertions**. It covered business permissions and property scope, cleaner task isolation, presentation data, platform administration, external-calendar sync, marketplace checkout, booking lifecycle and verified-stay reviews. Browser rehearsal on the deployed host is still required because automated application tests do not prove external DNS, HTTPS, third-party iCal reachability, queue supervision or mail delivery.

## 11. If something unexpected happens

| Situation | Recovery during the presentation |
|---|---|
| Chosen dates are red | Choose the next three green nights. Do not stop to diagnose during the client session. |
| Demo account opens the wrong page | Open `/owner/`; it routes business users to their permitted workspace and task-only staff to `/staff/tasks`. |
| Booking is not at the top of the owner list | Search `Zainab` or the `VS-` reference copied from the guest page. |
| A task is not visible to the cleaner | Confirm the owner assigned that exact booking-linked task to Grace Cleaner, then refresh `/staff/tasks`. |
| Inspection is not present | Confirm the **turnover** cleaning task—not only pre-arrival cleaning—was completed. |
| Review form is not present | Confirm the inspection passed and the booking status is **Completed**. |
| Eko Pearl is already published | Run `php artisan db:seed --class=PresentationDemoSeeder --force`, refresh admin, and sign in again if the session was invalidated. |
| Styling is missing | Run `npm run build`, then hard-refresh the browser. |
| Uploaded media is missing | Run `php artisan storage:link`; verify the file exists on the configured disk. |

## 12. Closing statement — 60 seconds

End on the public Eko Pearl listing or Chevron's verified review.

Say:

> “What we have demonstrated is a complete MVP operating loop: trusted supply enters through owner setup and platform approval; demand converts through a protected marketplace booking; money and availability stay consistent; operations are generated from the booking lifecycle; staff access is limited by role and property; and the completed stay produces verified reputation. The next release can deepen integrations and real payments without rebuilding this core.”

Then invite questions by persona: guest experience, owner control, staff operations, platform trust, or release readiness.
