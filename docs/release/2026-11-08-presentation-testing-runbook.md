# Verified Shortlet — Presentation Testing Runbook

Revision date: 8 November 2026

This is the short, client-facing runbook for tonight’s presentation. It is designed to demonstrate the connected product journey without mixing account sessions or creating accidental live records.

Use the older [client go-live testing guide](2026-10-10-client-go-live-testing-guide.txt) for full acceptance evidence, third-party verification and issue recording. Use this runbook for the smooth presentation sequence.

## Recommended order

Run the presentation in this order:

1. Platform preparation and safety
2. Property-owner workspace and staff operations
3. Guest journey
4. Marketplace experience
5. Optional platform-owner moderation and settlement close-out

This order ensures the property is ready before the guest books it, and the guest journey is complete before the public marketplace walkthrough.

## Session setup

Prepare separate browser profiles or devices:

| Session | Account | Demonstration purpose |
|---|---|---|
| A | Primary platform owner | Configuration, business/property approval, moderation and oversight |
| B | Business/property owner | Property, staff, operations and finance workspace |
| C | Guest | Registration, identity, search, booking, payment and stay |
| D | Cleaner/technician | Assigned tasks, checklists, evidence and issue reporting |
| E | Settlement approver | Separate settlement approval, only if the close-out is demonstrated |

Never switch accounts inside one browser profile during the presentation.

## Before guests arrive

The coordinator confirms:

- The presentation URL opens securely.
- The correct test or live payment mode is visible to the platform owner.
- Test email addresses and phone numbers are ready.
- The presentation property is published and has usable availability.
- One non-overlapping date range is reserved for the booking demonstration.
- No real guest identity, payment card or secret provider key will be shown.
- If using the Presentation Sandbox, every fixture property visibly carries **Test property**.
- If channel import is not provider-certified, show it as **Provider connection pending** rather than attempting a live connection.

## 1. Platform preparation — Session A

Demonstrate only the controls needed for the client story:

1. Open Platform Configuration.
2. Confirm payment environment and enabled payment providers.
3. Confirm notification delivery and send a test email if time permits.
4. Confirm identity-verification environment.
5. Confirm the audit history is available.
6. Confirm the platform owner can access sensitive settings and the host/guest cannot.
7. If demonstrating channel import, show the Channels section:
   - provider selection;
   - import enable/disable;
   - encrypted credential fields;
   - webhook secret field.
8. Explain that live Rentals United authorisation requires approved credentials and provider certification.

Do not paste credentials into screenshots or presentation chat.

## 2. Property owner and staff — Sessions B and D

### Property owner

1. Open the owner dashboard and confirm the business workspace.
2. Open Properties and show the published presentation property.
3. Open Operations and show task counts, priorities and due dates.
4. Create or review one operational task.
5. Assign it to the cleaner or technician.
6. Open Team & access and show role/property scope.
7. Open Finance and show property-scoped transactions without exposing secrets.

### Cleaner or technician

1. Sign in through Session D.
2. Open Assigned tasks.
3. Accept and start the assigned task.
4. Complete the checklist and add evidence.
5. For a maintenance task, demonstrate the issue form with category, priority, description, due date and photo evidence.
6. Confirm Finance and unrelated bookings/properties are not visible.

### Owner verification

Return to Session B and confirm the task progress, evidence and issue status are visible to the authorised owner/manager.

## 3. Guest journey — Session C

Use a fresh test guest account where possible.

1. Register as an individual guest.
2. Verify the email address.
3. Complete identity verification or show the clear pending/failure state if the provider is not enabled for the demo.
4. Open the marketplace and search by location, dates and guest count.
5. Open the presentation property.
6. Demonstrate amenities, capacity, house rules, price breakdown and public address privacy.
7. Use Save/Favourites and Share.
8. Select dates and confirm night count and total update.
9. Continue to checkout and choose the configured payment provider.
10. Complete full payment in the selected test/live mode.
11. Confirm one booking, receipt, notification and outstanding balance state where applicable.
12. Open the booking and show arrival instructions only at the permitted payment stage.

Do not repeat the final payment button. If payment fails, record the result and use the prepared fallback booking rather than improvising.

## 4. Marketplace close-up

Use the guest session or a signed-out private window:

- Search results match the selected filters.
- Property cards align consistently on desktop and mobile.
- Exact private address is not exposed publicly.
- Share, Save and Favourites work without unexpected navigation.
- Booked dates cannot be selected.
- The listing title and description do not expose private operational data.
- The property remains available only after the intended approval/publication state.

## 5. Optional stay operations and close-out

Only demonstrate this if time and test data allow:

1. Owner/reception checks the guest in.
2. Guest sends a message and a support request.
3. Operations assigns and resolves the request.
4. Owner checks the guest out.
5. Cleaner completes turnover.
6. Inspector records pass or corrective work.
7. Guest submits a review.
8. Platform owner moderates the review.
9. Primary platform owner creates a settlement batch.
10. Settlement approver reviews and approves it in Session E.

Never use the same platform-owner account to create and approve a settlement batch.

## Optional channel-import demonstration

Use this only when the provider integration is approved and credentials have been tested:

1. Platform owner enables global channel import.
2. Platform owner grants access to the selected business.
3. Host opens Properties → Import listing.
4. Host authorises the external channel in its secure provider window.
5. Host selects a listing.
6. Verified Shortlet creates a draft property.
7. Host reviews imported title, description, photos, amenities and available mapping.
8. Host completes missing operational fields and submits the draft for approval.

If provider access is not ready, show the prepared permission/configuration screens and say: “The connection is enabled by the platform owner; provider certification is the remaining step.”

## Presentation recovery rules

- If a live provider is slow, use the prepared seeded property and explain the integration boundary.
- If email is delayed, show the in-app notification and record email as a follow-up test.
- If a payment callback is delayed, do not retry repeatedly; use the prepared test booking.
- If a role cannot access a page, keep the forbidden result as evidence and return to the authorised session.
- Do not clear, reseed or delete live records during the presentation.

## Sign-off notes

Record only Pass, Fail, Not Tested or Deferred during the presentation. For any failure, capture the account role, page, expected result, actual result and reference ID. Never include passwords, API keys, full NIN/BVN values or payment-card information.

