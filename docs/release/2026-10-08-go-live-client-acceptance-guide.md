# Verified Shortlet — Go-Live Deployment and Client Acceptance Guide

This guide starts with an empty operational database containing only two separated platform-owner accounts: the primary operator and the settlement approver. From that point onward, each participant creates and manages records within their own responsibility: the property owner registers the business, creates properties and manages staff; the guest registers and makes bookings; and the platform owners verify, approve, moderate and oversee the platform. Platform owners must not create the host's operational records on the host's behalf.

## 1. Go-live acceptance objective

Prove this complete connected journey:

**Platform configuration → property-owner registration → business onboarding → business verification → property creation → property approval/publication → guest registration and identity → protected booking and payment → notifications → calendar/iCal protection → arrival instructions → staff assignment → cleaning and maintenance → check-in → guest support → checkout → turnover and inspection → completed stay → guest and owner reviews → platform moderation → finance allocation and settlement.**

Use separate browser profiles for the platform owner, business owner, guest and staff. Do not repeatedly log different people into one browser session.

### Responsibility boundary

| Persona | Creates or manages | Must not do |
|---|---|---|
| Platform owner | Platform configuration, business verification, property approval/publication, trust badges, moderation, disputes, security, fee policy and settlements | Create the host's business, property, staff, pricing, availability or booking records |
| Property/business owner | Own registration, business onboarding, properties, pricing, availability, house rules, arrival instructions, staff, roles and property assignments | Approve their own business/property or perform platform moderation |
| Guest | Own registration, identity details, favourites, booking, payment, messages, support requests and stay reviews | Access owner or platform administration |
| Staff member | Only the tasks and property records granted by role and property assignment | Grant their own permissions or access unrelated properties |

The only exception is the explicitly marked **Presentation sandbox**. Its platform-owner rebuild button creates disposable test fixtures for demonstrations; it is not the real onboarding workflow and must never be used to create a live host.

## 2. Production deployment

### 2.1 Back up first

Before changing the release, back up the production database and `storage/app` uploads. Do not run `migrate:fresh`, `db:wipe`, or the presentation-data seeder against production.

### 2.2 Required production environment

Set these values on the server, using unique credentials:

```dotenv
APP_NAME="Verified Shortlet"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://shortlet.emiplug.com

SESSION_SECURE_COOKIE=true
SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=sync
ALLOW_SYNC_QUEUE=true
FILESYSTEM_DISK=local

PLATFORM_ADMIN_EMAIL=<CLIENT_PLATFORM_OWNER_EMAIL>
PLATFORM_ADMIN_PASSWORD=<UNIQUE_ONE_TIME_PASSWORD>
PLATFORM_APPROVER_EMAIL=<SECOND_PLATFORM_OWNER_EMAIL>
PLATFORM_APPROVER_PASSWORD=<SECOND_UNIQUE_ONE_TIME_PASSWORD>
```

On the current shared host, `QUEUE_CONNECTION=sync`, `CACHE_STORE=file`, and local media storage are acceptable for the controlled initial test. `ALLOW_SYNC_QUEUE=true` records this deliberate shared-host exception in the production preflight. Queued work executes inside the web request and may make responses slower. Move to `QUEUE_CONNECTION=database`, set `ALLOW_SYNC_QUEUE=false`, and run a persistent worker when the hosting environment supports one. Keep the document root pointed at Laravel's `public/` directory.

### 2.3 Deploy the release

Build frontend assets locally before pushing because the shared host does not provide Node.js:

```bash
npm ci
npm run build
```

Commit the release only after tests pass. Then, on the server:

```bash
cd /home/emiprbyj/shortlet.emiplug.com
php artisan down --retry=60
git pull --ff-only origin dev
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan optimize
php artisan up
```

If `storage:link` reports that the link already exists, inspect it rather than deleting it blindly.

### 2.4 Seed two separated platform owners only

Run this once after migrations:

```bash
php artisan db:seed --class=Database\\Seeders\\GoLivePlatformOwnerSeeder --force
```

This command is idempotent. It installs required roles and permissions and creates or updates only the primary platform owner and the separately configured settlement approver. Both accounts must use different email addresses and unique one-time passwords. It does not create demo businesses, properties, guests, bookings or staff.

### 2.5 Optional presentation sandbox

The production starting point remains the two separated platform-owner accounts above. When the client needs a repeatable guided demonstration, the primary platform owner can open **Admin → Maintenance → Presentation sandbox** and select **Clear and reseed sandbox**. This administrative action creates disposable fixtures only: one marked business—**Verified Shortlet Sandbox Host**—whose properties display a **Test property** badge. It does not represent how a real host joins the platform.

The adjacent **Clear sandbox only** action removes that marked host and its related test users, properties, bookings, payments, tasks, messages and reviews. Both actions require confirmation, are recorded in platform audit history, and use a safety check designed to stop rather than select unrelated live data.

Platform login:

```text
https://shortlet.emiplug.com/admin/login
```

Both platform owners should change their temporary passwords after first sign-in. Do not place either real password in this guide or source control.

## 3. Platform-owner configuration

Sign in as the platform owner and open **Platform Configuration**.

### 3.1 Payments for tonight

For controlled acceptance without provider keys:

1. Set Payment environment to **Test mode**.
2. Enable **Allow internal test checkout**.
3. Enable Paystack and/or Flutterwave and select the default provider. Guests may choose either enabled provider at checkout.
4. Save with a meaningful audit reason.

This permits a non-financial checkout only in Test mode. Live mode always requires valid production credentials.

When credentials become available:

- Paystack callback: `https://shortlet.emiplug.com/payments/paystack/callback`
- Paystack webhook: `https://shortlet.emiplug.com/webhooks/paystack`
- Flutterwave callback: `https://shortlet.emiplug.com/payments/flutterwave/callback`
- Flutterwave webhook: `https://shortlet.emiplug.com/webhooks/flutterwave`

Enter test/live keys in Platform Configuration. Configure webhook URLs and the Flutterwave secret hash in the provider dashboards. Perform one controlled real payment before changing the acceptance status to live-verified.

### 3.2 Gmail SMTP

Use a dedicated Gmail/Google Workspace mailbox, not a team member's personal mailbox.

1. Enable Google 2-Step Verification for the mailbox.
2. Generate a 16-character Google App Password for Verified Shortlet.
3. In Platform Configuration, set:

```text
Email delivery enabled: Yes
Transport: SMTP
Host: smtp.gmail.com
Port: 587
Security/scheme: smtp (STARTTLS)
Username: the full Gmail address
Password: the Google App Password, not the normal Gmail password
From address: the same Gmail address
From name: Verified Shortlet
```

4. Save the configuration.
5. Use **Send test email** and verify receipt, sender name and spam placement.

Google requires 2-Step Verification before an App Password can be generated. Changing the Google account password revokes existing App Passwords.

### 3.3 Media, identity, AI and calendars

- Keep media storage set to **Local** tonight. Existing images are stored on the server and will not move automatically when S3 is enabled later.
- Keep identity in **Test mode** until Dojah credentials are available. Live mode must not be enabled without valid credentials.
- Keep AI disabled tonight. OpenAI credentials are not required for the core booking journey.
- Keep automatic calendar synchronization enabled. A genuine external `.ics` URL is required for the final iCal acceptance step.

## 4. Browser preparation

Open five isolated sessions:

| Session | Persona | Starting URL |
|---|---|---|
| A | Platform owner | `/admin/login` |
| B | Business/property owner | `/register` |
| C | Guest | `/register` |
| D | Cleaner or other staff | `/login` |
| E | Settlement approver | `/admin/login` |

Use real test email addresses the team can inspect. Record every generated booking reference and property name in the acceptance notes.

## 5. Business-owner registration and onboarding

In Session B, the prospective property owner—not the platform owner—must:

1. Register a new account with name, email, phone and a secure password of at least eight characters.
2. Verify the email using the Gmail-delivered message.
3. Choose the business/operator path.
4. Complete business onboarding at `/onboarding/business`:
   - registered or trading name;
   - primary contact;
   - email and phone;
   - Nigerian location;
   - business type and registration information where available.
   - the business administrator's 11-digit NIN and BVN, with explicit verification consent.
5. Confirm that the owner reaches the business workspace but cannot publish unapproved inventory.

Expected result: one user can hold both guest and business access, with **Dashboard/Business** switching shown only when applicable.

## 6. Platform verification of the business

In Session A:

1. Open **Businesses** and select the newly registered business.
2. Review the submitted business details and contact information.
3. Check the property owner or business host's masked NIN and BVN results and provider references returned during onboarding.
4. Approve or reject the business and provide a clear reason for the decision.
5. Confirm that the decision, reviewing platform owner and timestamp appear in the platform audit history.

The platform owner cannot manually declare an NIN or BVN valid. The configured identity provider performs verification during onboarding; only masked digits, provider references and results are retained for the wider business-approval decision.

Expected result: the business becomes **Verified** after approval. Property approval remains a separate process. Owner settlements remain unavailable until the required business verification, property owner or business host NIN/BVN checks and settlement bank-account verification have all passed.

## 7. Property creation by the owner

Return to Session B and open **Properties → Add property**. Initial property creation and submission must be completed strictly by the business owner—not by a staff member or the platform owner.

Complete every wizard section:

1. Property identity and complete address.
2. Type, capacity, bedrooms, beds and bathrooms.
3. Amenities and inventory.
4. Check-in and checkout times.
5. House rules and arrival instructions.
6. Pricing and eligible longer-stay promotions.
7. Photos and documents.
8. Optional YouTube property walkthrough URL. Use a public or unlisted video that the host is authorized to share; the application stores the URL and embeds YouTube rather than accepting a video-file upload.
9. Marketplace title, summary and description.
10. Optional external calendar connection.
11. Preview the property, confirm any YouTube walkthrough embeds correctly, and submit for platform review.

Recommended acceptance property:

```text
Name: Go-Live Waterfront Test Residence
Area: Lekki, Lagos
Check-in: 14:00
Checkout: 12:00
Nightly price: a small controlled test amount
Capacity: 4
```

Add rules such as no smoking, no parties, quiet hours and access-return requirements. Add a clear fully-paid arrival guide describing gate access, key collection, Wi-Fi and emergency contact.

Expected result: the owner sees a Pending review state; the public marketplace does not show the property yet.

## 8. Platform property approval

In Session A:

1. Open **Properties** and select the submitted property.
2. Inspect content, media, documents, pricing, times and rules.
3. Approve and publish it with an audit reason.
4. Enable **Super host** only if the platform team has determined the host qualifies; otherwise leave the badge empty.

Expected result: the property appears publicly, its full address and availability display correctly, and no generic Verified watermark appears on marketplace cards.

## 9. Guest registration, identity and favourites

In Session C:

1. Register as an individual guest and verify the email.
2. Open **Settings** and enter an 11-digit test NIN.
3. Use the inline **Verify** action.
4. Return to the marketplace and locate the new property.
5. Test **Share** and **Save**.

Expected result: NIN shows Verified in Test mode; Share opens the device share sheet or copies the URL; Save updates immediately and the Favourites badge count changes without navigation.

## 10. Booking, pricing and payment

1. Open the new property.
2. Choose available check-in and checkout dates.
3. Change the checkout date and confirm that check-in remains selected.
4. Change check-in explicitly and confirm the calendar permits it without a page reload.
5. Enter adults, children, phone and a special request.
6. Confirm real-time pricing, eligible discount and final amount.
7. Continue to checkout.
8. Choose Paystack or Flutterwave.
9. Select full payment for the primary acceptance flow.
10. Accept house rules and complete Test-mode checkout.

Expected result:

- booking reference is created once;
- status becomes Confirmed and Paid;
- receipt is available;
- booking dates become unavailable immediately;
- guest, owner and platform owner receive notifications;
- platform fee and owner balance allocations are recorded;
- arrival instructions become visible because payment is complete.

Repeat later with installment payment to verify the configured minimum deposit, outstanding balance, balance due date and check-in restriction before full payment.

## 11. Double-booking and iCal protection

### Native booking protection

Open the property in another browser and try selecting the same booked dates.

Expected result: dates are shown unavailable and cannot be submitted.

### External iCal acceptance

1. Obtain a genuine Airbnb/Booking.com-compatible public `.ics` feed.
2. As owner, add it under the property calendar connections.
3. Run manual sync or use the platform maintenance scheduler action.
4. Confirm the imported event spans the complete date range.
5. Try booking those dates publicly.
6. Copy the Verified Shortlet outbound `.ics` URL into an external calendar platform and verify export.

Expected result: imported dates use the same conflict engine as native bookings and owner blocks. Never expose the private outbound token in screenshots or public chat.

## 12. Owner booking and notification workspace

In Session B:

1. Open Notifications and find the new booking notification.
2. Open Bookings and search by the new reference.
3. Verify guest counts, payment status, dates, special request and contact actions.
4. Use Messages to reply to the guest.
5. Review Finance and confirm gross payment, fee allocation and owner balance.
6. Review Calendar and confirm the occupied range is displayed as one stay.

Expected result: the same booking connects guest communication, payment, calendar, finance, operations and audit history.

## 13. Team roles, permissions and property scope

As the business owner, open **Team**.

1. Create or use a Cleaner role.
2. Configure module/action access as required:
   - task viewing and completion: read/write;
   - finance: no access;
   - bookings: no broad access;
   - dashboard analytics: owner only.
3. Invite a cleaner using a real controlled email.
4. Assign only the new property.
5. Create an Accountant role/member:
   - Finance read/write as appropriate;
   - no cleaning or property-operations control.
6. Create or review Reception, Operations Manager, Maintenance, Inspector and Support roles.
7. Demonstrate deactivation and reactivation of a member.

Expected result: roles determine permitted functionality; property assignment independently limits the records inside that functionality. Direct unauthorized URLs return 403, not merely hidden navigation.

## 14. Pre-arrival work and cleaner proof

As owner/operations manager:

1. Open Operations.
2. Find the pre-arrival readiness task created by the booking.
3. Assign it to the cleaner for the new property.

In Session D, sign in as the cleaner:

1. Open the assigned-task workspace.
2. Confirm only assigned property/task context is visible.
3. Start the task.
4. Complete checklist items, add notes and upload evidence where available.
5. Complete the task.
6. Attempt `/owner/finance` and `/owner/bookings` directly.

Expected result: task completion succeeds; unrelated owner pages return 403.

## 15. Check-in instructions and guest support

As owner/reception:

1. Confirm payment is fully paid.
2. Confirm the arrival guide is complete.
3. Check the guest in with identity, balance and access details.

As guest:

1. Open the booking.
2. Verify the arrival guide, key/access instructions, rules, address and host contact actions.
3. Send a booking message.
4. Create a maintenance or guest-support request.

As support/operations:

1. Open the linked booking/request.
2. Assign or resolve the operational work.
3. Confirm the guest sees the updated resolution.

## 16. Checkout, turnover and inspection

As reception/owner:

1. Check the guest out.
2. Record key return, room condition, damage state and handover notes.

As cleaner:

1. Complete the automatically created turnover-cleaning task.

As inspector/owner:

1. Complete inspection and evidence requirements.
2. Mark the operational flow complete.

Expected result: property progresses from Occupied → Cleaning/Inspection → Available, and the completed stay becomes review-eligible.

## 17. Two-sided reviews and platform moderation

As guest:

1. Open the completed booking.
2. Submit overall and category ratings plus written feedback.

Expected result: review is marked as a completed-stay review and remains pending platform moderation.

As platform owner:

1. Open Reviews.
2. Inspect booking/payment context, questionnaire version and weighted score.
3. Approve or reject with an audit reason.

As owner:

1. Respond to the approved property review.
2. Record a good/neutral/bad guest-stay review from the booking.

Expected result: approved property review appears publicly; hidden/rejected content does not. Historical scoring retains the questionnaire version and weights used at submission.

## 18. Owner balance and settlement acceptance

Using the two platform-owner sessions:

1. Open the business and confirm business/NIN/BVN verification.
2. Open Settlements.
3. Add and verify the owner's settlement account.
4. Select available owner-balance allocations and create a batch.
5. Confirm the primary platform owner cannot approve the batch they created.
6. In Session E, sign in with the separately configured settlement-approver account and approve the batch.
7. Complete it with the external transfer reference, or reverse it with a reason where testing reversal.

Expected result: allocation cannot be settled twice; creator cannot approve their own batch; all transitions are audited. Actual bank transfer automation remains dependent on the final payout-provider decision.

## 19. Platform trust, safety and operations checks

Demonstrate:

- business suspension and reactivation;
- user lock/unlock and session invalidation;
- property unpublish/reject controls;
- review moderation;
- disputes with booking and financial context;
- audit history;
- maintenance console log viewing;
- cache clear, migration status/action and scheduled-command execution.

Do not use Git pull or migration actions from the browser during the live client presentation unless a backup exists and the exact release has already been approved.

## 20. Final acceptance checklist

Mark each item Pass/Fail with evidence:

- [ ] Both platform owners sign in and only those two initial accounts were seeded.
- [ ] Property owner—not the platform owner—created the business and completed onboarding.
- [ ] Property owner—not the platform owner—created and submitted the property.
- [ ] Gmail test email is received.
- [ ] Business registration and onboarding work.
- [ ] Platform business/NIN/BVN verification is audited.
- [ ] Property wizard, media and documents work.
- [ ] Optional YouTube walkthrough URL validates and embeds without uploading a video file.
- [ ] Pending property is not public.
- [ ] Approved property is public.
- [ ] Guest registration, email verification and NIN work.
- [ ] Share, Save and favourite count work.
- [ ] Booking calendar selection/change works smoothly.
- [ ] Full Test-mode booking completes once.
- [ ] Receipt, payment, platform fee and owner balance exist.
- [ ] Guest, owner and platform notifications exist.
- [ ] Booked dates cannot be booked again.
- [ ] Genuine iCal import blocks dates and export is readable externally.
- [ ] Arrival instructions unlock only after full payment.
- [ ] Booking messages, call-host action and support request work.
- [ ] Cleaner sees only assigned work/property.
- [ ] Accountant sees finance but not unrelated operations.
- [ ] Direct unauthorized routes return 403.
- [ ] Check-in, checkout, cleaning, inspection and completion work.
- [ ] Guest review waits for platform moderation.
- [ ] Owner response and guest-stay review work.
- [ ] Settlement eligibility and audited batch controls work.
- [ ] Settlement creator cannot self-approve; the second platform owner can approve.
- [ ] Mobile and desktop checks pass.
- [ ] Logs contain no unexplained repeated exceptions.
- [ ] Backup and restoration procedure is recorded.

## 21. Items that still need third-party live evidence

These are implementation-ready but must not be called live-verified until tested with actual credentials:

- Paystack live transaction, callback, webhook and refund;
- Flutterwave live transaction, callback, webhook and refund;
- Gmail/SMTP delivery and queue behavior;
- Dojah live NIN/BVN verification;
- genuine external iCal import/export;
- S3-compatible storage migration, when scheduled later;
- OpenAI concierge, when enabled later;
- actual owner bank transfer/payout provider, once selected.

## 22. Presenter closing statement

> “This test began with only the two separated platform-owner accounts. Every business, property, staff assignment, booking, payment record, calendar lock, operational task and review was created through the application. The evidence therefore demonstrates a connected operating platform—not disconnected screens or preloaded presentation records. Production activation now depends on the recorded acceptance results, listed third-party credentials and final live-provider checks.”
