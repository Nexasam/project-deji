# Verified Shortlet - Client Feedback Fixes Tracker

Date: 2026-09-22
Meeting target: 9:00 PM WAT
Environment: Local and staging, depending on the issue being reviewed

## Purpose

This note tracks every client-facing update, fix and verification completed before the 9:00 PM review. It is written so the same points can be used during the client presentation without needing to search through code or chat history.

## Presentation Summary

Use this section at the start of the meeting:

- We are using client feedback to polish the MVP before the review.
- Each reported issue is being checked against the actual browser flow, not only the code.
- Fixes are being documented with the affected area, expected behaviour and verification result.
- Social login remains intentionally out of scope for this review; email and password login is the supported demo path.

## Fix Log

### 1. Homepage Login Modal

Client/user feedback:
Confirm that the homepage login modal works like the normal `/login` page. Social logins are not part of this check.

Area affected:
Public homepage authentication.

Result:
Confirmed working.

What was verified:

- Homepage loads successfully at `http://127.0.0.1:8000/`.
- Standard login page loads successfully at `http://127.0.0.1:8000/login`.
- Homepage includes the login modal.
- Navbar "Log In" actions open the modal through the Alpine modal store.
- Modal email/password form posts to the same endpoint as the normal login page: `POST /login`.
- Modal form includes CSRF protection.
- Modal form includes email and password fields.
- Guest demo login succeeds from the modal.
- Guest demo login succeeds from `/login`.
- Both flows redirect to `/guest/bookings`.

Verification evidence:

- Modal login returned `302 -> /guest/bookings`.
- Normal `/login` returned `302 -> /guest/bookings`.
- Authenticated destination loaded with `200 OK`.

Demo wording:
The homepage login modal is not a separate or fake UI. It uses the same secure Laravel login route as the full `/login` page, including CSRF protection and the same authenticated redirect behaviour.

## Incoming Client Feedback

Add new client notes below this line as they come in.

### 2. Auth Screen Branding and Input Weight

Client/user feedback:
The login screen should say "Verified Shortlet", not "Project Nexus". The input fields should not look bold.

Area affected:
Authentication screens and shared form-control styling.

Decision:
Apply the brand correction across related auth pages, not only the visible login page. Keep labels and primary actions strong, but make typed input values and placeholders normal weight.

Fix made:

- Replaced remaining visible "Project Nexus" auth branding with "Verified Shortlet".
- Updated login page title, mobile header, desktop auth panel, support copy and "New to..." footer text.
- Updated register page title, header and desktop auth panel copy.
- Updated forgot-password page title, header and secure-access copy.
- Updated verify-email page title and brand header.
- Updated the business onboarding and owner dashboard browser titles.
- Set shared `.property-form-control` text to normal weight.
- Set shared `.property-form-control::placeholder` text to normal weight.

Verification:

- Auth resource scan shows no remaining "Project Nexus" or "Nexus" strings.
- PHP syntax checks passed for login, register, forgot-password and verify-email Blade templates.
- PHP syntax checks passed for the related owner onboarding and dashboard title templates.
- Rendered-page checks passed for `/login`, `/register` and `/forgot-password`.
- Production frontend build passed after the input-weight CSS change.

Demo wording:
We applied the client's brand correction across the authentication journey, not just the screenshot page. Login, account creation, password reset and email verification now consistently present the product as Verified Shortlet, and the input fields have been softened so the form feels cleaner on mobile.

### 3. Owner Dashboard Mobile Header and Density

Client/user feedback:
The owner dashboard top section looks dense on mobile. The logo/header area is not well positioned. The business/property name should show in full so different properties are easier to identify. The "Real view / Demo view" switch should not be visible for the client demo.

Area affected:
Owner dashboard header, KPI summary cards, live workboard and owner view-switch component.

Decision:
Keep the dashboard as the real workspace, but remove the demo toggle from the visible UI. Improve mobile spacing and allow the business name to wrap naturally instead of being truncated.

Fix made:

- Commented out the owner `Real view / Demo view` switch component so demo mode is no longer visible in the owner workspace.
- Updated the owner dashboard mobile header to use better spacing between menu, business name, user avatar and logout.
- Allowed the business name to show in full by replacing the truncated title with wrapping text.
- Reduced header crowding by shrinking the avatar slightly on mobile and using a smaller logout control.
- Improved KPI card spacing and readability.
- Changed the KPI grid to one column on very small screens, then two/four/eight columns as space allows.
- Expanded the "Today’s operations" workboard into clearer sections with more breathing room on mobile.

Verification:

- PHP syntax checks passed for `resources/views/dashboard.blade.php`.
- PHP syntax checks passed for the owner view-switch component.
- Production frontend build passed after the responsive dashboard changes.
- Local owner login succeeded and `/owner/dashboard` rendered with `200 OK`.
- Rendered dashboard shows the full business name in the header.
- Rendered dashboard no longer displays the visible "Real view / Demo view" switch.

Demo wording:
We simplified the owner dashboard for client-facing use. The workspace now presents one clear live view, the business name is easier to read on mobile, and the dashboard summary/workboard has more breathing room instead of feeling compressed.

### 4. Marketplace Mobile Simplicity and Stay Detail Stats

Client/user feedback:
The client referenced `nezt.com` as a cleaner and simpler marketplace direction, especially on mobile. A second client screenshot from `/stays/banana-island-harbour-flat` highlighted the loose "3 bedrooms 4 beds 6 guests" row.

Area affected:
Public marketplace homepage, property cards and stay detail pages.

Decision:
Borrow the simple marketplace feel: lighter filter UI, clearer section heading, fewer dense visual blocks and more readable mobile cards. Do not remove our verification, availability or booking functionality.

Fix made:

- Reworked the secondary marketplace filter form into a cleaner card with labelled fields.
- Simplified filter labels and placeholders for mobile scanning.
- Renamed the marketplace section from "Live marketplace" to "Find the right place".
- Added short supporting copy: "Simple verified stays, clear pricing and live availability."
- Reduced the desktop grid density from five columns at large width to four columns, using five only at extra-wide width.
- Updated property card titles to support two-line wrapping instead of single-line truncation.
- Replaced the plain stay-detail stats row with three compact spec cards for Bedrooms, Beds and Guests.

Verification:

- Reviewed `nezt.com`; the relevant direction is its clean headline, search-first layout and lightweight category discovery.
- PHP syntax checks passed for the marketplace homepage, stay-detail page and property-card component.
- Production frontend build passed after the marketplace CSS/view changes.
- Local homepage render confirmed the new search placeholder, "Find the right place" heading and full property card names.
- Local `/stays/banana-island-harbour-flat` render confirmed the new Bedrooms, Beds and Guests spec cards.

Demo wording:
We responded to the marketplace feedback by simplifying the browsing experience rather than adding more UI. The marketplace now reads cleaner on mobile, property names are easier to identify, and the stay-detail facts are presented as clear cards instead of a cramped text row.

### 5. Font Standardization Across Marketplace and Owner Workspace

Client/user feedback:
Standardize the font across the marketplace and the property-owner dashboard.

Area affected:
Shared application CSS, public marketplace layout and owner dashboard/workspace pages.

Decision:
Use Manrope as the primary product font across public and owner-facing surfaces. Keep Inter and Instrument Sans as fallbacks only.

Fix made:

- Updated Tailwind's shared `font-sans` stack to start with Manrope.
- Added a global `html` and `body` font-family rule using the same Manrope-first stack.
- Kept `.public-surface` aligned with the same stack.
- Removed the one-off inline Manrope override from owner business onboarding so the page follows the shared system.
- Rebuilt frontend assets so both marketplace and owner dashboard load the same compiled CSS.

Verification:

- PHP syntax checks passed for owner onboarding, owner dashboard, marketplace homepage and stay-detail page.
- Production frontend build passed.
- Compiled manifest now points to the latest CSS asset.
- Local `/stays/banana-island-harbour-flat` rendered with `200 OK`.
- Local authenticated `/owner/dashboard` rendered with `200 OK`.

Demo wording:
The public marketplace and property-owner workspace now share one product font system. This makes the guest-facing and owner-facing sides feel like one platform instead of separate templates.

### 6. Marketplace Card Guest Line Consistency

Client/user feedback:
Make the guest count stay on the next line for consistency on the marketplace dashboard/listing cards.

Area affected:
Public marketplace property cards.

Decision:
Keep location and guest count as separate metadata lines on every card instead of allowing them to wrap unpredictably on one row.

Fix made:

- Removed the separator dot between location and guest count.
- Updated property-card markup so location and guest count are separate spans.
- Updated card metadata CSS to use a vertical layout.
- Kept icons aligned and prevented icon shrinking.

Verification:

- PHP syntax check passed for the property-card component.
- Production frontend build passed.
- Compiled manifest now points to the latest CSS asset.
- Source scan confirms `card-dot` is no longer rendered by the component.

Demo wording:
Marketplace cards now present location first and guest capacity on its own line, giving every card the same readable rhythm on mobile and desktop.

### 7. Stay Detail Spec Icons

Client/user feedback:
Add related icons to the stay-detail facts so Bedrooms, Beds and Guests are easier to understand at a glance, but keep the section compact.

Area affected:
Public stay detail page, including `/stays/banana-island-harbour-flat`.

Decision:
Keep the three-card layout, but display each fact as icon, value, then label. This keeps the cards compact and places the label to the right side of the value.

Fix made:

- Added a room/bedroom icon to the Bedrooms card.
- Added a bed icon to the Beds card.
- Added a people icon to the Guests card.
- Changed the cards from vertical icon stacks to compact icon-value-label rows.
- Reduced wrapper radius, card padding, icon size and internal spacing.

Verification:

- PHP syntax check passed for `resources/views/marketplace/show.blade.php`.
- Production frontend build passed.
- Source scan confirms all three compact stat icons and labels are present.

Demo wording:
The property detail facts now read as compact icon-value-label chips, so guests can scan bedrooms, beds and capacity quickly without the section taking over the page.

### 8. Marketplace Property Card Border

Client/user feedback:
A little border on the marketplace property cards would help. The hover is already okay.

Area affected:
Public marketplace property cards.

Decision:
Add a subtle default border while preserving the existing hover lift and shadow.

Fix made:

- Added a light `#e5e7eb` border to `.card-listing`.
- Added border-color transition to the existing card hover transition.
- Slightly deepened the border on hover without changing the existing hover movement or shadow.

Verification:

- Source scan confirms the card border and hover border color are present.
- Production frontend build passed.
- Compiled manifest now points to the latest CSS asset.

Demo wording:
Marketplace cards now have a light outline, making each listing easier to separate visually while keeping the existing hover polish.

### 9. Booking Calendar Checkout Change and Live Price Preview

Client/user feedback:
On the stay page, after a guest selects check-in and checkout, changing only the checkout date should not reset the check-in date. Example: if check-in is 22 September and checkout is 30 September, clicking 26 September should update checkout to 26 September while keeping 22 September. The amount to pay should also be visible in real time before clicking "Review booking".

Area affected:
Public stay detail booking calendar and booking form.

Decision:
Preserve the selected check-in whenever the guest clicks a later valid date, even if checkout was already selected. Show a live estimated total once both dates are selected, while still rechecking secure pricing and availability during review/confirmation.

Fix made:

- Updated calendar date selection logic so clicking a later date updates checkout instead of resetting the full range.
- Kept the existing reset behavior when the guest clicks a date on or before the current check-in.
- Added a live "Estimated total" card before the Review booking button.
- The live estimate shows nights, guest count, subtotal and total.
- Automatic stay discount and service-fee display has now been removed because the client wants all fees/offers manually incorporated into the nightly price.
- Kept the backend quote call on Review booking as the secure final check.

Verification:

- PHP syntax check passed for `resources/views/marketplace/show.blade.php`.
- Focused presentation test passed: 14 tests, 59 assertions.
- Test coverage now confirms the live total text exists and the old reset-on-checkout-change condition is gone.
- Production frontend build passed.

Demo wording:
The booking calendar now behaves more naturally: guests can adjust checkout without losing their selected check-in. They also see the estimated amount immediately after selecting dates, while the system still performs a secure availability and price check before final confirmation. The shown price is the nightly rate multiplied by selected nights, less any eligible longer-stay discount. No separate service fee is added.

### 10. Service Fee, Automatic Discount and Premium Booking Form

Client/user feedback:
Service fee should be removed because it will be manually incorporated into the listing price. Longer-stay discounts should remain available from property setup and should be visible in marketplace pricing when a guest searches with dates. The guest details form should feel more premium and professional.

Area affected:
Public stay detail booking form, booking pricing service, guest booking detail and receipt pages.

Decision:
Make booking totals straightforward for MVP demo: nightly rate multiplied by nights, minus eligible longer-stay discount, with zero service fee. Improve the form presentation without changing the submitted fields.

Where the old discount came from:

- The longer-stay discount comes from active published `PropertyPromotion` records, especially the owner wizard's longer-stay discount setup.
- The backend pricing service selected an eligible percentage promotion by minimum stay and applied it automatically.
- That automatic discount logic has been restored for eligible longer stays only. The service-fee logic remains disabled.

Fix made:

- Removed the automatic 5% service-fee calculation from `BookingPricingService`.
- Restored longer-stay discount calculation in `BookingPricingService`.
- New booking total now equals: `(nightly price × number of nights) - eligible longer-stay discount`.
- Removed service-fee rows from the stay booking confirmation modal.
- Kept longer-stay discount rows in the stay booking estimate and confirmation modal when the discount applies.
- Removed service-fee wording from guest booking detail and receipt pages.
- Restored the owner wizard's longer-stay discount input so property owners can set discount percentage and minimum nights during property creation.
- Restored owner property-detail display for longer-stay offers with wording that no separate service fee is added.
- Marketplace cards now show selected-date total pricing when guests search with check-in/check-out dates, e.g. `Oct 7 - 15 · 8 nights`.
- Marketplace selected-date total also shows a longer-stay discount note when one applies.
- Updated live price preview copy to explain that no separate service fee is added.
- Added extra padding to guest phone-number and special-request fields.
- Adjusted the stay-detail calendar responsively: very small phones stack the months to avoid page overflow, while wider mobile/tablet screens show both months side-by-side.
- Redesigned the guest details booking form into a premium panel with:
  - selected check-in/checkout cards,
  - capacity badge,
  - plus/minus adults and children controls,
  - cleaner phone-number field,
  - cleaner special-requests field,
  - stronger primary Review booking button.
- Enlarged and aligned the Bedrooms/Beds/Guests stat chips as icon → value → label.

Verification:

- Pricing tests now expect eligible longer-stay discount and zero service fee.
- Marketplace checkout tests now use the corrected totals: ₦190,000 for 2 nights and ₦285,000 for 3 nights on the ₦95,000/night seeded property.
- Presentation tests now confirm service-fee text is absent and longer-stay discount logic is present on the stay page.
- Owner property wizard tests now confirm the price step can create a longer-stay discount promotion.

Demo wording:
Pricing is now easier to explain to the client: the customer pays the displayed nightly rate multiplied by the selected nights, with longer-stay discounts applied only when the property owner configured them and the stay meets the minimum-night rule. There is no separate service fee during checkout.

### 11. Marketplace Card AI Insights and Review Layout

Client/user feedback:
The Figma design shows an Ask AI / AI Insights icon on the property card, but it was missing in the live marketplace. The marketplace card layout and review/rating presentation should feel closer to the design. AI can remain a dead link for now.

Area affected:
Public marketplace property cards.

Decision:
Restore the AI Insights pill visually without enabling the AI flow yet. Make the star rating more visible and keep the card responsive on mobile.

Fix made:

- Added a black `AI Insights` pill button back to every marketplace property card.
- Added a lightbulb icon inside the pill to match the design direction.
- Kept the AI action as a dead/non-functional button for now with `aria-disabled="true"`.
- Increased the review star and rating size/weight so the card rating is easier to see.
- Adjusted the card footer so price and AI Insights align horizontally on normal screens and stack cleanly on very small phones.

Verification:

- Presentation test now confirms the property-card component contains `AI Insights`.
- Presentation test now confirms the AI button is disabled/dead for now.

Demo wording:
The marketplace cards now match the Figma direction better: guests see verified status, review rating and an AI Insights callout. The AI button is intentionally visual-only for this MVP review and will be wired to real insights later.

### 12. Dynamic Marketplace Pricing Presentation

Client/user feedback:
Pricing should be more dynamic than a flat nightly price. Guests should be able to see totals based on selected dates, discounts and host settings, then open a property and tweak dates to compare prices.

Area affected:
Public marketplace cards and stay-detail booking estimate.

Decision:
Use host-configured nightly pricing and longer-stay discounts to present a richer price story. Keep backend checkout as the final source of truth.

Fix made:

- Marketplace cards still show base nightly price when no dates are selected.
- When guests search with check-in/check-out dates, marketplace cards now show:
  - selected date-range total,
  - selected date range and number of nights,
  - base nightly price,
  - savings amount when longer-stay discount applies,
  - “Best value” / selected-total badge.
- Stay-detail estimate now highlights `Best value · save ₦...` when a longer-stay discount applies.
- Stay-detail estimate now shows a helper such as “Stay X more nights to unlock Y% longer-stay pricing” when dates are close to a configured discount threshold.
- Service fee remains removed.
- Checkout still recalculates pricing securely before final booking confirmation.

Verification:

- Presentation tests now confirm pricing badges, savings labels and the next-discount hint exist.

Demo wording:
The marketplace now gives guests a more useful pricing preview. Without dates, they see the nightly price. With dates, they see a calculated total and any longer-stay savings already configured by the host. They can then open the property and adjust dates to compare totals before confirming.

### 13. Host Strip on Selected Property and Filled AI Bulb

Client/user feedback:
After a property is selected, the detail page should show who hosts it, similar to Airbnb. However, it should avoid exposing the full company name where guests could research the company and book directly. The AI Insights bulb should also be filled white.

Area affected:
Public stay detail page and marketplace property cards.

Decision:
Show a privacy-safe host label using the host/contact first name when available, otherwise show “Verified host.” Do not expose the full business/company name in this guest-facing host strip.

Fix made:

- Added a “Hosted by …” section on the selected property detail page.
- The display name is derived from `business.primary_contact_name` first name where available.
- If no safe first name exists, it falls back to “Verified host.”
- Added Verified Shortlet host metadata and hosting duration where available.
- Updated the AI Insights bulb SVG from outline-only to filled.
- Forced the AI Insights icon fill/stroke to white inside the black pill.

Verification:

- Presentation tests now confirm the stay detail page includes the host strip logic.
- Presentation tests now confirm the AI Insights SVG uses `fill="currentColor"`.

Demo wording:
When guests open a selected property, they now see a trust-building host strip without exposing the full operator name. The AI Insights button also visually matches the Figma direction with a filled white bulb.

### 14. Horizontal Marketplace Sections and `/index2` Fallback

Client/user feedback:
The marketplace should show properties horizontally within each subsection, with arrows/swipe navigation, so guests can explore different sections without scrolling endlessly down one vertical grid.

Area affected:
Public landing page marketplace section.

Decision:
Preserve the current landing page first, then convert the active landing marketplace into Airbnb-style horizontal rows.

Fix made:

- Copied the previous landing page to `/index2`.
- Added route `GET /index2` as a preserved fallback for the previous layout.
- Extracted marketplace card/calendar logic into `x-marketplace-property-tile`.
- Replaced the main marketplace grid with horizontal sections:
  - Popular verified stays in Lagos.
  - Great stays for your next trip.
  - Best value longer stays.
  - Family and group stays.
- Each section supports horizontal swipe/scroll.
- Desktop users also get left/right arrow controls.
- Existing property card pricing, AI Insights pill and availability-calendar modal remain intact.

Verification:

- Presentation tests now confirm the landing page uses horizontal rails and the reusable marketplace tile.

Demo wording:
The landing page now feels more like a modern marketplace: guests can swipe through sections horizontally and compare categories quickly, while `/index2` preserves the previous vertical page if we need to compare or roll back.

### 15. Marketplace AI Insights Preview Popup

Client/user feedback:
AI Insights should provide useful property information directly from the homepage/marketplace before the guest clicks into the property. It can appear as a popup and later be connected to a real AI flow.

Area affected:
Marketplace property cards on `/` and `/index2`.

Decision:
Turn the AI Insights pill into a working preview popup using verified marketplace data already available on the card. Keep it framed as an AI preview until a full AI assistant is wired later.

Fix made:

- AI Insights pill now opens a property-specific popup.
- Popup includes:
  - privacy-safe host display,
  - verified-host metadata,
  - location/context insight,
  - booking/access convenience insight,
  - review/quality insight,
  - pricing/value insight.
- Insights are generated from existing property data, amenities, reviews, host metadata and selected date pricing.
- Added the same popup support to `/index2` so the preserved page works too.
- Removed the dead-link behaviour from the AI Insights button.

Verification:

- Presentation tests now confirm AI Insights dispatches a popup event.
- Presentation tests confirm the homepage includes the AI Insights preview modal.
- Presentation tests confirm pricing and quality insight payloads are built in the marketplace tile.

Demo wording:
Guests can now click AI Insights from the marketplace card and see a quick preview before opening the property. It is currently a smart, data-backed MVP preview and can later be connected to a full AI assistant.

### 16. Dynamic Hero Search Bar

Client/user feedback:
The hero search bar looked fixed/static and was not moving dynamically as discussed.

Area affected:
Public landing-page search bar.

Decision:
Make the top search control behave like a real marketplace filter: location, check-in/check-out and guest count should update inside the widget and submit into the marketplace results.

Fix made:

- Moved the search form and date picker into one shared Alpine state scope.
- The date picker is now connected to the visible "Check in / out" display.
- Selected dates update the search display immediately.
- Guest plus/minus controls update the guest count value submitted to search.
- Search submits to the marketplace section using the selected location, dates and guest count.
- Existing secondary filters are preserved when searching from the hero bar.
- Added date guards so checkout cannot be earlier than check-in.

Verification:

- Search-bar PHP syntax passed.
- Presentation tests now confirm the search bar is a live filter control.

Demo wording:
The top search bar is now an active booking search control. Guests can choose destination, dates and guests from the hero, then jump directly to matching marketplace results.

### 17. Compact Expandable Mobile Search

Client/user feedback:
On mobile, the full search section felt too large and would be better as an enclosed section that can expand only when needed.

Area affected:
Mobile landing-page search experience.

Decision:
Keep the full search controls, but collapse them into a premium summary card on mobile. Desktop remains unchanged.

Fix made:

- Added a mobile-only rounded search summary card.
- The summary shows destination, selected dates and guest count.
- Tapping the summary expands the full search form.
- The expanded form still supports location, check-in/check-out, guest controls and marketplace search.
- The chevron rotates to clearly show expanded/collapsed state.
- Existing search behavior and desktop layout remain unchanged.

Verification:

- Search-bar PHP syntax passed.
- Presentation tests confirm the mobile collapsed/expanded search behavior.
- Production frontend build passed.

Demo wording:
On mobile, the search area now starts as a compact enclosed card and expands only when the guest wants to search, giving the homepage more breathing room.

### 18. Guest Phone, Messages and Two-Sided Stay Reviews

Client/user feedback:
The guest "Add phone" action was not working from the profile flow. The client also asked for a Messages section, and the owner should be able to review the guest/stay experience as good, neutral or bad. Guest reviews of completed stays should also remain available.

Area affected:
Guest profile, guest dashboard next steps, guest booking details, owner booking details, guest messages and owner messages.

Decision:
Implement an MVP in-app messaging flow tied to bookings, because bookings already provide the safest context for guest/owner communication. Keep it non-realtime for MVP. Store messages in the existing booking interaction records and create in-app/email-intent notifications through the existing notification system. Guest reviews remain the existing verified-stay review flow; owner guest assessment is added as a new booking interaction.

Fix made:

- Added phone-number validation and normalization to the profile update flow.
- Added a phone-number field to the profile form.
- Guest dashboard now hides the "Add your phone number" next step once a phone has been saved.
- Added guest inbox at `/guest/messages`.
- Added guest booking message thread at `/guest/bookings/{booking}/messages`.
- Added owner inbox at `/owner/messages`.
- Added owner booking message thread at `/owner/bookings/{booking}/messages`.
- Added "Messages" links to guest navigation and owner workspace navigation.
- Added "Message property team" action on guest booking details.
- Added "Messages" quick action on owner booking details.
- Guest-created messages are stored as `booking_interactions` with `interaction_type = message`, `channel = platform`, `direction = inbound`.
- Owner-created replies are stored as `booking_interactions` with `interaction_type = message`, `channel = platform`, `direction = outbound`.
- Guest access is restricted to the guest's own bookings.
- Owner/staff access is restricted to the active business and permitted property scope.
- Message creation creates recipient-side in-app notifications and email-delivery intent records through the existing notification service.
- Added owner guest-stay assessment on owner booking details.
- Owner can record rating, outcome and notes for a guest stay.
- Owner guest-stay assessment is stored as `booking_interactions` with `interaction_type = owner_guest_review`.
- Existing guest verified-stay review flow remains available for completed stays from the guest dashboard/booking detail.

Verification:

- PHP syntax checks passed for the new message service, guest message controller, owner message controller, owner guest-review controller and related tests.
- Focused feature suite passed: 17 tests, 71 assertions.
- Test coverage confirms profile phone saving normalizes `+234 801 234 5678` to `+2348012345678`.
- Test coverage confirms invalid profile phone numbers are rejected.
- Test coverage confirms the guest dashboard hides "Add your phone number" after a phone is saved.
- Test coverage confirms guest inbox only lists the logged-in guest's own booking threads.
- Test coverage confirms guests cannot open or post to another guest's booking thread.
- Test coverage confirms guest messages create the correct `booking_interactions` record.
- Test coverage confirms owner inbox only lists active-business message threads.
- Test coverage confirms owner replies create the correct `booking_interactions` record and notify the guest.
- Test coverage confirms property-scoped staff cannot open out-of-scope booking messages.
- Test coverage confirms owner guest-stay reviews are recorded against the booking and guest.
- Production frontend build passed.

Demo wording:
The platform now supports booking-based messaging for both guests and property teams. Guests can message the property team from their booking, and owners/staff can reply from the owner workspace, with access still restricted by booking ownership, active business and property scope. Guests can still review completed stays, and owners can now also record a good, neutral or bad assessment of the guest stay for operational history.

### 19. Owner Self-Booking Protection, Dual Workspace Switch and Property Badge Polish

Client/user feedback:
Property owners should not be able to book their own listed properties. If a user has both guest and property-owner access, switching between workspaces should be smooth. The owner property portfolio should use clearer colours, and the “Verified” watermark/badge on images may not be necessary; a Superhost-style label is preferred.

Area affected:
Marketplace checkout, public/guest navigation, owner sidebar navigation, owner dashboard property cards and owner property portfolio cards.

Decision:
Block marketplace checkout when the logged-in user is an active member of the business that owns the selected property. Add workspace switch controls only when the user actually has owner/business access. Replace the extra image-level “Verified” badge with Superhost-style language and colour-coded publication/review states.

Fix made:

- Added a self-booking guard before marketplace booking creation.
- Repeated the same self-booking guard inside the transactional booking lock so it remains safe under concurrent requests.
- The error message explains: “You cannot book a property that belongs to your own business workspace.”
- Added “Business” to the guest/public navbar for users with active business membership.
- Added “Guest dashboard” to the business workspace sidebar.
- Kept switching conditional on real access, so ordinary guests do not see business workspace controls.
- Owner property portfolio cards now use stronger colour-coded publication badges:
  - Published: green.
  - Pending/In review: amber.
  - Unpublished: red.
  - Draft/Setup: slate.
- Removed the plain “Verified” image badge from owner property cards.
- Verified properties now show “Superhost” / “Superhost eligible” styling instead.
- Pending/rejected verification states now show clearer operational wording such as “Reviewing” and “Needs fixes.”
- Dashboard property cards now use the same badge language.

Verification:

- PHP syntax checks passed for checkout service, navbar, owner sidebar, owner properties page, dashboard page and updated tests.
- Focused checkout/presentation suite passed: 21 tests, 153 assertions.
- Test coverage confirms the seeded marketplace owner cannot book their own property.
- Test coverage confirms no booking is created when the owner self-booking rule is triggered.
- Test coverage confirms the dual-access switch labels exist in the correct guest/owner surfaces.
- Test coverage confirms owner property portfolio contains Superhost/review/fix language and no longer renders a simple `Verified</span>` badge.
- Production frontend build passed.

Demo wording:
We now prevent self-booking: a business member cannot use the guest checkout flow to reserve a property owned by their own business. If one person legitimately has both guest and business-role access, they can switch between the guest dashboard and business workspace without logging out. This works whether the business role is owner, cleaner, accountant, receptionist or another permitted role. We also cleaned up the owner property cards so statuses are colour-coded and the previous Verified watermark is replaced with Superhost-style trust language.

### 18. Mobile Advanced Filters Collapsed

Client/user feedback:
The secondary search fields — apartment/city search, stay type, minimum price, maximum price and beds — should also be part of the hidden mobile search experience instead of taking up space beneath the hero search.

Area affected:
Mobile marketplace filter area on `/` and preserved `/index2`.

Decision:
Keep advanced filters visible on desktop, but collapse them into a mobile-only disclosure so guests can open them only when needed.

Fix made:

- Wrapped the advanced marketplace filter form in a `More filters` disclosure.
- Mobile now shows a compact enclosed summary row for advanced filters.
- Tapping `More filters` reveals search, stay type, price and beds.
- Desktop continues to show the full advanced filter form immediately.
- Applied the same behaviour to `/index2`.

Verification:

- Landing and `/index2` Blade syntax passed.
- Presentation tests confirm both pages use collapsible advanced filters.
- Production frontend build passed.

Demo wording:
On mobile, both the main search and the advanced filters now stay compact until the guest chooses to expand them, keeping the marketplace cleaner and easier to browse.

### 19. Mobile Property Row Navigation Icons

Client/user feedback:
The horizontal property sections need visible navigation icons on mobile so guests understand they can swipe or move through each row.

Area affected:
Mobile marketplace horizontal property sections.

Decision:
Expose the existing row navigation controls on mobile and style them as compact circular arrow buttons beside each section title.

Fix made:

- Removed the mobile-hidden behaviour from marketplace row controls.
- Replaced text arrow characters with proper SVG chevrons.
- Added reusable styling for small circular row navigation buttons.
- Buttons remain accessible with row-specific labels.
- Horizontal swipe still works; the icons now make the interaction obvious.

Verification:

- Landing Blade syntax passed.
- Presentation tests confirm the row controls are no longer hidden on mobile.
- Production frontend build passed.

Demo wording:
Each horizontal marketplace row now has visible mobile arrow controls, so guests can either swipe naturally or tap the arrows to explore more properties in that section.

### 20. Uniform Mobile Marketplace Card Alignment

Client/user feedback:
Some marketplace sections/cards did not align uniformly on mobile, especially rows with fewer properties or different image/card content.

Area affected:
Mobile marketplace horizontal property sections.

Decision:
Normalize the marketplace section rhythm so every row uses the same header, card width, image area and card height on mobile.

Fix made:

- Added shared marketplace row header, title and rail classes.
- Standardized horizontal rail alignment and hidden rail scrollbars.
- Normalized mobile card width to a consistent footprint.
- Normalized marketplace image height inside each card.
- Ensured card bodies stretch to the same height within horizontal rows.
- Kept swipe and arrow navigation behaviour intact.

Verification:

- Landing Blade syntax passed.
- Presentation tests confirm marketplace row/card alignment classes and mobile sizing rules.
- Production frontend build passed.

Demo wording:
Marketplace sections now use a uniform mobile card rhythm: images, cards and row starts align consistently across every section, including rows with fewer properties.

### 21. Guest Home / Resume Dashboard

Client/user feedback:
The client asked whether guests have a simple page where they can pick up from where they left off or complete final registration/onboarding steps, using another accommodation site as a reference.

Area affected:
Guest workspace after login.

Decision:
Add a dedicated guest home page instead of sending guests directly to the bookings list. The page should summarize reservations, surface next steps and help guests resume their active booking journey.

Fix made:

- Added `/guest` as the new guest dashboard route.
- Guest login now falls back to `/guest` instead of `/guest/bookings`.
- Navbar authenticated guest link now points to `Dashboard`.
- Added a mobile-friendly guest dashboard with:
  - personalized welcome,
  - continue/resume card,
  - reservation status counts,
  - recent/upcoming reservations,
  - next-step prompts for ID/profile completion, phone number, unpaid booking and pending review.
- Kept `/guest/bookings` as the full booking directory.
- Dashboard data is scoped to the authenticated guest only.

Verification:

- Guest dashboard controller and Blade syntax passed.
- Guest feature tests passed: 5 tests, 24 assertions.
- Production frontend build passed.

Demo wording:
Guests now have a proper home page after login. It helps them resume a booking, see reservation status, complete profile steps and jump into receipts, reviews or stay support from one simple place.

Follow-up refinement:

- Updated the greeting from `Welcome home [Name]` to `Welcome [Name]`.
- Converted the reservation pills into real filter tabs.
- Tabs now filter the dashboard reservation list by all, arriving soon, checking out and currently staying.
- Renamed the guest active-stay tab from `Currently hosting` to `Currently staying`.

Design defense note:
The supplied Guest Dashboard PDF/reference was useful for the product concept — a simple guest landing page with reservations and next steps — but its visual system does not fully align with the current Verified Shortlet marketplace direction we have already established. The MVP therefore follows the current Verified Shortlet guest/marketplace UI language: orange actions, rounded marketplace cards, light surfaces, existing navbar, reservation cards and the same typography. This keeps the guest dashboard consistent with the live marketplace and avoids introducing a separate visual language shortly before client testing.

### 22. Guest Favourites / Saved Stays

Client/user feedback:
Guests should be able to see properties they have saved as favourites and pick a property from there later.

Area affected:
Marketplace cards, authenticated guest navigation and guest saved-stays workspace.

Decision:
Make the existing heart icon a real persisted guest feature instead of a temporary UI animation. Favourites are property-based, while Messages remain booking-based for the MVP.

Fix made:

- Added a `guest_favourites` table linking guests to saved properties.
- Added a `GuestFavourite` model and guest relationship.
- Added `/guest/favourites` for guests to see saved stays.
- Added save/remove routes for marketplace property hearts.
- Marketplace hearts now save to the database for logged-in guests.
- Logged-out users are redirected to login when they try to save a stay.
- Guest navigation now includes `Favourites`.
- The favourites page shows property image, title, location, guests, nightly price, saved time, remove action and link back to the property details page.
- Ineligible/unpublished properties cannot be saved.

Verification:

- Guest favourites feature tests added for save, remove, visibility, unpublished-property rejection and guest navigation.

Demo wording:
Guests can now shortlist properties while browsing, then return to their Favourites page to compare saved stays and continue to the property details/booking flow.

Follow-up refinement:

- Added a live favourites count badge in the guest navigation.
- The count updates immediately when a guest saves or removes a property without requiring a page refresh.
- Separated navigation by context to reduce clutter:
  - Marketplace pages show marketplace/discovery navigation plus Favourites.
  - Guest workspace pages show guest-account links such as Dashboard, Bookings, Favourites and Messages.
  - Business switch remains only in the guest workspace context for dual-access users.

Demo wording:
The navigation now adapts to the user journey: marketplace pages stay focused on browsing and saved stays, while guest dashboard pages show account and booking tools.

### 23. Business Owner Role Permissions and Property Scope

Client/user feedback:
The business/property owner should behave like the super admin of their business. Staff roles such as accountant, cleaner, operations manager and reception should only access the functions and properties assigned to them.

Area affected:
Property-owner team management, permission enforcement, owner dashboard entry flow and staff/business access.

Decision:
Implement this as two separate access layers:

1. Functional access by role:
   - Hidden
   - Read only
   - Read & write

2. Property access by team member:
   - Business-wide access when no property is selected
   - Assigned-property-only access when properties are selected

Fix made:

- Added business-specific role permission settings, so one business owner can configure role access without changing global seeded roles for every business.
- Added a Role capability matrix to `/owner/team`.
- Business owner role is locked as full access and treated as the business super admin.
- Dashboard analytics is hidden by default for non-owner roles unless explicitly enabled.
- Added modules for:
  - Dashboard analytics
  - Properties
  - Bookings & messages
  - Calendar & iCal
  - Finance
  - Operations
  - Team administration
- Each module supports Hidden, Read only and Read & write access.
- Existing property assignment controls remain per team member.
- Permission resolver now checks:
  - business owner super-admin access,
  - business-specific role permission settings,
  - existing seeded role permissions,
  - member-specific overrides,
  - assigned property scope.
- `/owner` entry now redirects staff to the first workspace they are actually allowed to access instead of always sending them to dashboard.
- Non-owners cannot change role capability settings.

Verification:

- Added focused owner role-permission tests.
- Confirmed owner can configure module access for a role.
- Confirmed read-only Finance allows viewing Finance but blocks expense creation.
- Confirmed hidden Dashboard blocks non-owner dashboard analytics.
- Confirmed non-owner staff cannot edit the role capability matrix.
- Broader owner/property and presentation checks passed: 27 tests, 190 assertions.
- Production frontend build passed.

Demo wording:
Business owners now have two-level access control. First, they decide what each role can do using the Role capability matrix. Second, they decide which properties each team member can access. So an accountant can be Finance-only, a cleaner can be Operations-only, and two cleaners with the same role can still be limited to different properties.

Follow-up refinement:

- Collapsed the Role capability matrix by default so `/owner/team` is not visually overwhelming.
- Added a dedicated team-member access page at `/owner/team/{membership}`.
- The Team list now links each member to `View access` instead of forcing all details onto one page.
- The dedicated access page shows:
  - user identity and status,
  - current role and job title,
  - assigned property scope,
  - editable role/property assignment,
  - functional access inherited from the role.
- This makes the demo easier to explain: role capabilities are configured globally for the business role, while each staff member’s personal access page shows where that role applies.

Verification:

- Focused owner role-permission tests passed: 5 tests, 29 assertions.
- Broader owner/property and presentation checks passed: 28 tests, 201 assertions.
- Production frontend build passed.

## Local demo seed recovery and credential verification

Client/user feedback:

The documented owner demo login stopped working locally:

- `owner@coastlineresidences.test`
- `Password123!`

Area affected:

Local development/demo database only.

Decision:

Restore the repeatable demo data without using destructive `migrate:fresh`, then verify every documented demo credential by checking the stored password hashes.

Fix made:

- Confirmed the local demo database had no users, businesses or properties.
- Re-ran the normal migrations and repeatable presentation seeders.
- Re-ran the platform-admin seeder for the generic admin account.
- Confirmed the owner, guest, staff and platform-admin credentials now verify locally.
- Confirmed the local database now contains:
  - 14 demo users
  - 2 demo businesses
  - 11 properties
  - 1 seeded booking

Verified credentials:

- `owner@coastlineresidences.test / Password123!`
- `owner@lagoonstays.test / Password123!`
- `guest.demo@verifiedshortlet.test / DemoPassword123!`
- `manager.demo@verifiedshortlet.test / DemoPassword123!`
- `reception.demo@verifiedshortlet.test / DemoPassword123!`
- `accountant.demo@verifiedshortlet.test / DemoPassword123!`
- `operations.demo@verifiedshortlet.test / DemoPassword123!`
- `cleaner.demo@verifiedshortlet.test / DemoPassword123!`
- `maintenance.demo@verifiedshortlet.test / DemoPassword123!`
- `inspector.demo@verifiedshortlet.test / DemoPassword123!`
- `support.demo@verifiedshortlet.test / DemoPassword123!`
- `admin@verifiedshortlet.test / AdminPassword123!`
- `verification.admin@verifiedshortlet.test / DemoPassword123!`
- `disputes.admin@verifiedshortlet.test / DemoPassword123!`

Demo wording:

The local demo data was restored from the repeatable seeders and the documented credentials were revalidated. This was a local development data issue, not a product-login issue.

## Team roles: collapsible matrix and custom business roles

Client/user feedback:

The role capability matrix was still too heavy visually, and property owners should not be boxed into only the default roles.

Area affected:

Property owner `/owner/team` access-control workspace.

Decision:

Keep the two-level access model:

1. Role capability access controls what a role can see or edit.
2. Property assignment controls which property records an individual staff member can access.

Then add custom role creation so each business can define its own operational roles.

Fix made:

- The main Role capability matrix remains collapsed by default.
- Each individual role inside the matrix is now also collapsible.
- Added a `Create role` action for business owners.
- Business owners can create custom roles such as Night Supervisor, Inventory Officer or Senior Cleaner.
- Custom roles are scoped to the current business only.
- Custom roles appear in Add/Edit team-member role dropdowns.
- Custom roles use the same Hidden / Read only / Read & write permission matrix.
- Business Owner remains locked as the business super-admin role and cannot be restricted.
- Non-owner staff cannot create roles or change role capabilities.

Verification:

- PHP syntax checks passed.
- Production frontend build passed before this UI-only Blade change.
- Focused owner role-permission tests passed after the change: 6 tests, 40 assertions.
- New test confirms an owner can create a custom business role and configure its permissions.

Demo wording:

The property owner is the super admin of their business. They can either use the default roles or create custom roles, then decide what each role can see or edit. After that, each staff member can still be limited to only assigned properties.

## Team access reactivation

Client/user feedback:

Deactivated users should be recoverable. A business owner should be able to reactivate a former team member instead of creating a duplicate user.

Area affected:

Property owner `/owner/team` and dedicated team-member access pages.

Decision:

Reactivation should be intentional and should require selecting the role and property scope again. This is safer than silently restoring old access, because a team member's previous role or property access may no longer be appropriate.

Fix made:

- Added a Reactivate action for inactive team members.
- Reactivate is available from the Team list and the dedicated team-member access page.
- Reactivation requires:
  - role selection,
  - optional job title,
  - optional property scope.
- Reactivation restores:
  - business membership status,
  - active business context,
  - active role assignment,
  - employee/staff record,
  - property staff assignments where selected.
- Business owners cannot reactivate blindly into the old access state; they must confirm the access being restored.
- Reactivation is audited as `team.member_reactivated`.

Verification:

- PHP syntax checks passed.
- Production frontend build passed.
- Fixed a duplicate-role reactivation edge case where reactivating a user into a previously held role could violate the `user_roles` unique key.
- Focused owner role/access tests passed: 7 tests, 53 assertions.

Demo wording:

If a staff member is deactivated, the owner can bring them back later without recreating the user. During reactivation, the owner must confirm the correct role and property scope, so access remains deliberate and secure.

Follow-up polish:

- Premium-styled the Create role form.
- Premium-styled the Add team member form.
- Added stronger input padding, filled surfaces, rounded corners, shadowed focus states and clearer helper copy.
- Role creation now visually explains that permissions are configured after creation from the matrix.

### Template

Client/user feedback:

Area affected:

Decision:

Fix made:

Verification:

Demo wording:
