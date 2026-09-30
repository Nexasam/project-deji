# Guest UX restructure tracker

This tracker compares the new guest UX PDF pack with the live Laravel product. The preserved pre-change implementation is in `docs/release/ux-flow-backups/2026-09-30-guest-flow-before-new-ux/`.

## Phase 1 — guest foundation (implemented)

- Redesigned the guest dashboard welcome area using the new dark welcome treatment.
- Added live dashboard totals for upcoming trips, completed stays and saved properties.
- Retained the existing reservation filters, next actions, message entry points and review prompts.
- Added a marketplace recommendations section restricted to eligible, verified and published listings.
- Added `/guest/calendar`, backed only by the authenticated guest's bookings.
- Added month navigation, trip status colours, upcoming-trip cards and links to booking details.
- Added `/guest/settings` with guest profile, phone and password controls.
- Added Calendar and Settings to desktop and mobile guest navigation with active states.
- Kept `/profile` operational for backwards compatibility.
- Added feature coverage for calendar authorization/data isolation and settings updates.

## Phase 2 — PDF-accurate guest shell, dashboard and Explore Stays (implemented)

- Rebuilt the authenticated guest header around the PDF's navigation order: Dashboard, Explore stays, Bookings, Calendar, Messages and Settings.
- Added the PDF-style home/profile switch, notification control and compact avatar account menu.
- Kept Favourites and Business access inside the account menu to prevent header clutter.
- Reworked the dashboard to match the PDF's black greeting strip, orange primary action, three summary cards, compact upcoming-trip list and dark AI Concierge preview.
- Reworked dashboard recommendations into the supplied four-card presentation.
- Preserved profile-completion and booking-payment next steps below the primary PDF content.
- Authenticated guests now enter Explore Stays without the public marketing hero.
- Added the PDF-style search area, category strip and compact dark AI Concierge preview.
- Discovery mode uses horizontally navigable property rows.
- An active search/filter changes the marketplace into the PDF-style four-column results grid with result totals and filter controls.
- Public visitors retain the existing marketing homepage and marketing sections.

## Phase 3 — listing, checkout and booking confirmation (implemented)

- Preserved the listing's live gallery, 18-month availability calendar, longer-stay pricing preview, guest capacity rules and iCal-backed blocked dates.
- Replaced the listing's direct-booking modal with the new UX's dedicated checkout step.
- Added a server-validated `GET /stays/{slug}/checkout` review page with trip dates, guest details, simulated payment choices and a sticky price summary.
- Availability and pricing are rechecked before checkout renders and again inside the transactional booking creation service.
- Successful simulated payment now opens a dedicated confirmation experience with booking details, receipt access, messaging and next steps.
- Property owners and active staff remain unable to book inventory belonging to their own business.
- Reworked Checkout against the supplied PDF: completed-step header, compact trip and guest panels, side-by-side payment methods, secure-payment notice, full-width payment action, listing preview and sticky price summary.
- Reworked Booking Confirmation against the supplied PDF: dark success banner, booking reference, property summary, check-in/check-out facts, payment receipt, host contact card, next-step timeline and AI-trip preview.
- Reworked Bookings against the supplied PDF: status statistics, working status/search/sort controls, visual upcoming-stay cards and a compact historical bookings table.
- Reworked Calendar against the supplied PDF: trip sidebar, month controls, status legend and full month grid with confirmed/pending stay ranges. The grid scrolls horizontally on small screens rather than crushing calendar cells.

## Backend behaviour retained

- Booking pricing and host-configured discounts.
- Availability and iCal collision protection.
- Server-side double-booking prevention.
- Guest booking ownership checks.
- Booking messages, reviews and favourites.
- Business membership, staff permission and property-scope enforcement.

## Phase 4 — remaining PDF pages and unified forms (implemented)

- Added a project-wide professional form-control baseline: visible neutral borders, consistent radii, readable placeholders, hover states, orange focus rings and disabled states.
- Corrected the checkout phone and special-request controls so their boundaries remain visible without relying on a missing border-width utility.
- Confirmed Calendar and the Bookings index are integrated with live booking data.
- Added the missing public property Reviews screen at `/stays/{slug}/reviews`, backed by approved verified-stay reviews and owner responses.
- Connected listing review summaries and “Read all reviews” to the standalone Reviews screen.
- Reworked booking Messages into the supplied conversation layout with real message history, composer, booking details and host context.
- Brought Settings and Booking Details onto the wide, light guest workspace used by the supplied UX.
- Preserved booking authorization, cancellation, support requests, payment summaries and verified-stay review submission.
- Rebuilt Settings to match the supplied information hierarchy: left navigation, profile editor, preference preview and compact security controls.
- Expanded the global form standard to include professional control height, padding, typography and textarea sizing, while preserving intentional compact/unstyled controls through low-specificity CSS.
- Rebuilt the View Listing first viewport around the supplied design: title/action header, multi-image gallery mosaic, property-fact strip, amenities and description cards, plus a sticky pricing and availability panel.

## Next guest UX phases

1. Complete final browser/mobile visual acceptance against the supplied PDFs.
2. Update the client presentation runbook with the final guest URLs and screenshots.

## Explicitly non-functional concepts

- AI Trip Concierge and AI Insights remain preview UI until an AI service is approved.
- Real Paystack/Flutterwave charging remains outside the simulated-payment MVP.
- Notification preferences beyond existing platform notifications require a later delivery-preference implementation.
## September 30 guest-flow QA additions

- Expanded the repeatable guest demo portfolio to cover confirmed upcoming, awaiting-payment, currently checked-in, completed and cancelled/refunded stays.
- Added simulated payment and refund records so payment summaries display credible paid, outstanding and refunded figures.
- Standardised visible guest language from “trips” to “stays”.
- Moved the full property availability calendar out of the narrow booking sidebar; the sidebar now holds only the compact date summary, guest controls and live estimate.
- Rebuilt booking details around the approved compact property summary, stay facts, host card, payment summary, management actions and booking timeline while preserving messaging, receipts, cancellation, service requests and reviews.
- Corrected the property booking sidebar guest controls: adult and child steppers now remain full-width rows inside the narrow sticky panel, preventing breakpoint wrapping; the estimate and secure-payment note were also consolidated into a cleaner reservation summary.
