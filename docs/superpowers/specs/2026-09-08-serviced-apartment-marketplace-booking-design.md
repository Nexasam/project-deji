# Serviced-Apartment Marketplace and Booking Design

**Date:** 2026-09-08

## Goal

Deliver the guest-facing MVP for Verified Shortlet: a guest can register, search and filter real serviced apartments, inspect availability and backend-calculated pricing, complete a simulated payment, and receive a confirmed booking that appears in both the guest and correct business owner's workspace.

## Product Scope

- This release serves serviced apartments only.
- A `Property` is one self-contained apartment rented as an entire place. It is not a building and it does not expose separately bookable rooms.
- Multiple-room and parent-building inventory remain outside this version.
- Seed two serviced-apartment businesses with five published apartments each, for ten marketplace listings total.
- The public marketplace aggregates eligible apartments across businesses while every authenticated owner query remains business-scoped.
- External Airbnb and Booking.com connections remain pending and are not required for direct marketplace booking.

## Marketplace Eligibility

A property is publicly discoverable only when all of these conditions are true:

- the property is active;
- its type is serviced apartment and its booking mode is entire place;
- verification status is verified;
- publication status is published;
- operational/readiness state permits bookings;
- it has an active marketplace listing and usable nightly pricing.

Public detail routes apply the same eligibility query as search. Guessing the identifier of a draft, unpublished, unverified, archived, or foreign-only record must return 404 rather than reveal it.

## Guest Identity

The existing `users` table remains the authentication identity. Guest registration assigns the guest role and does not create a business membership. A guest profile is created or updated when booking details are supplied. Login, logout, password reset, email uniqueness, strong passwords, and terms acceptance use the existing authentication system.

If an unauthenticated visitor begins checkout, the intended property, dates, guest counts, and return URL are stored in the session. Registration or login returns the visitor to the same checkout. Marketplace browsing and property details remain public; creating, viewing, or cancelling a booking requires authentication.

## Search and Filters

Marketplace results come from a dedicated query object/service over eligible database records. Supported query-string inputs are:

- `q`: property name, public title, city, state, neighbourhood, or address text;
- `category`: `all`, `lekki`, `ikoyi`, `victoria-island`, `beachfront`, `family`, or `business`;
- `min_price` and `max_price`: nightly NGN bounds;
- `property_type`: serviced apartment for this version;
- `beds`: minimum physical bed count;
- `check_in` and `check_out`: exclude unavailable apartments;
- `guests`: exclude apartments below capacity;
- `page`: database pagination.

Filters compose with AND semantics, remain in pagination links, and are represented in the URL so result pages can be refreshed or shared. Invalid ranges return validation feedback. Search never filters an in-memory sample collection.

An additive migration introduces `properties.beds` as an unsigned small integer and `property_marketplace_listings.stay_categories` as nullable JSON. Location categories are matched from normalized address fields; `beachfront`, `family`, and `business` are matched from the persisted category array. Existing records remain valid through nullable/default-safe columns.

## Property Details and Availability

The detail page reads the real listing, media, amenities, location, capacity, bedrooms, bathrooms, nightly price, applicable promotion, and availability. The date interval is half-open: arrival is inclusive and departure is exclusive. A same-day arrival/departure is invalid.

Availability is denied when any requested night overlaps a confirmed booking, a booking-owned availability record, or an active owner/maintenance block. The final checkout operation repeats this test under database locks; UI availability is informative and cannot authorize a booking by itself.

## Pricing

A backend pricing service is the only authority for totals. It returns a value object containing currency, number of nights, nightly line items, subtotal, eligible promotion/discount, discount amount, service fee, and grand total. The MVP uses NGN, applies a 5% guest service fee after discounts, and persists monetary values in existing decimal money columns. Client-submitted totals are ignored.

The service applies active property pricing rules and promotions already represented in the schema. The checkout summary and final booking creation invoke the same service. A changed price is shown to the guest before charge rather than silently accepting a stale client total.

## Booking Transaction

The booking command receives the authenticated user, property identifier, arrival, departure, adult count, child count, and special requests. It performs the following sequence:

1. Load the eligible property and listing.
2. Validate the date interval, capacity, and booking rules.
3. Lock the relevant property availability rows and re-check overlapping bookings/blocks.
4. Calculate authoritative pricing.
5. Create a unique booking reference with status `pending_payment` and unpaid payment status.
6. Create the payment attempt through the configured payment gateway.
7. Process the signed/idempotent payment result.
8. Mark the payment paid, transition the booking to confirmed, append status history, and reserve each occupied night.
9. Commit the transaction and redirect to booking confirmation.

Two concurrent requests for the same apartment and nights cannot both confirm. Database constraints plus row locking provide the invariant; a conflict produces a friendly availability response. Repeated form submissions and repeated gateway callbacks return the existing result instead of creating a second booking or payment.

## Payment Boundary

Define a small payment gateway contract and a simulated implementation selected by configuration. The contract accepts an immutable payment request and returns a provider reference plus outcome. The simulated gateway must support deterministic success and test-controlled failure without network access.

The application owns booking state transitions; the gateway does not mutate models directly. Payment callbacks are verified and processed by an idempotent application service. UI copy must label the development/test payment as simulated. A future provider can replace the implementation without changing checkout controllers or booking rules.

On payment failure, the booking records the failure and does not own calendar nights. It may remain as a failed/pending historical attempt, but it must not appear as confirmed or block another guest.

## Cancellation

A guest may cancel only their own eligible booking. Cancellation more than 48 hours before the property-local check-in time is refundable; cancellation at or inside 48 hours is non-refundable. The listing check-in time is used when present and 14:00 Africa/Lagos is the MVP fallback. The backend calculates the outcome and persists a cancellation record, booking status history, refund status/amount, and released availability. Duplicate cancellation requests are idempotent.

The simulated payment gateway records a simulated refund for refundable cancellations. Historical booking/payment/cancellation records are retained and never hard-deleted.

## Dashboards

The guest dashboard lists only bookings belonging to the authenticated guest, with reference, apartment, dates, amount, payment state, booking state, and permitted actions. The detail page exposes cancellation policy and outcome.

The owner bookings page replaces preview arrays with database queries scoped through `ActiveBusinessContext`. A business owner sees only bookings whose `business_id` matches the active business. A newly confirmed direct booking appears with the marketplace/direct source and real guest/property information.

## Seed Data

Create an idempotent serviced-apartment marketplace seeder with stable emails, property codes, slugs, and natural keys:

- two business-owner users and two active businesses;
- five serviced apartments per business;
- locations distributed across Lekki, Ikoyi, and Victoria Island;
- beachfront, family, and business-friendly listing attributes;
- varied NGN nightly prices, capacities, bedrooms, bathrooms, amenities, and descriptions;
- multiple media rows per listing using stable local/public seed assets or stable external placeholder URLs supported by the media model;
- active marketplace listings with verified, published, ready, and operational properties;
- availability for a rolling future window plus deliberate blocked dates for conflict testing;
- selected active promotions to verify discounted searches and checkout totals.

Repeated seeding updates the same ten records and must not create additional businesses, users, properties, listings, media, or availability duplicates.

## Error Handling and Security

- Validate every public query and checkout payload with form-request objects.
- Scope guest booking reads and cancellation through the authenticated guest.
- Scope all owner booking reads through the active business context.
- Return 404 for ineligible marketplace properties and 409-style validation feedback for lost availability races.
- Never trust client prices, publication flags, business identifiers, payment status, or booking status.
- Avoid exposing encrypted guest identity data in marketplace or owner list payloads.
- Record booking and payment transitions in existing history/audit structures where available.

## Test Strategy

Feature tests cover guest registration and role assignment, intended checkout redirects, eligibility privacy, text search, every category, price/type/bed filters, composed filters, pagination preservation, date/capacity filtering, and property details.

Service tests cover half-open date overlap, capacity, authoritative pricing, promotions, price tampering, successful simulated payment, payment failure, callback idempotency, duplicate form submission, concurrent availability protection, refundable cancellation, non-refundable cancellation, and availability release.

End-to-end feature coverage proves this acceptance path:

1. seed ten apartments for two businesses;
2. register a guest;
3. search and combine filters;
4. open an eligible apartment;
5. select available dates and guests;
6. receive backend pricing;
7. complete simulated payment;
8. see a confirmed guest booking;
9. see the same booking in the correct owner's workspace;
10. confirm the other owner cannot access it.

Run the test suite on SQLite for fast regression and run the booking transaction smoke test on an isolated MySQL database because locking and migration identifier behavior are database-specific. The production asset build and Blade compilation must pass before completion.

## Delivery Boundaries

Included: serviced-apartment marketplace, ten-property seed data, guest authentication integration, search/filtering, real availability, authoritative pricing, direct booking, simulated payment/refund, guest booking views, owner booking visibility, cancellation, and automated tests.

Excluded: separately bookable rooms, building/unit hierarchy, live payment-provider credentials, external channel synchronization, messaging, loyalty, reviews submission, dynamic tax remittance, multi-currency conversion, and native mobile applications.
