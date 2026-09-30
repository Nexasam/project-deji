# Reputation, Payments and Settlement Design

## Status

Approved conversational design, written for final review before implementation planning.

This specification covers four connected workstreams:

1. property configuration, marketplace pricing and owner dashboard presentation;
2. bidirectional, platform-moderated reputation;
3. real Paystack and Flutterwave payment processing through Verified Shortlet;
4. platform fees, owner payable balances and manual settlements.

The fee engine and accounting structure are in scope. The commercial fee values and policies are deliberately provisional and must be reviewed separately before live activation.

## Intended outcome

Verified Shortlet should support the complete commercial loop from an owner configuring a stay through guest payment and platform settlement. Guests must see accurate property times, availability, prices, discounts and approved reputation. Owners must receive auditable balances without controlling the platform's payment credentials or moderation rules. Platform administrators must control payment configuration, review questionnaires, moderation, fee policy and settlement approval.

Success means:

- the same backend services calculate every public quote and final charge;
- an owner or staff member cannot book a property belonging to their own business;
- no callback, duplicate webhook or concurrent request can confirm a booking twice;
- only platform-approved reviews affect public or guest reputation;
- payment, platform revenue and owner payable amounts are traceable from gateway event to settlement;
- provider secrets never reach business users or browser code;
- future fee-policy changes do not rewrite historical bookings or balances.

## Existing foundations

The implementation extends the current application rather than replacing it:

- `PropertyMarketplaceListing` already stores `check_in_time` and `check_out_time`;
- the property workflow validates both times and publication readiness requires them;
- cancellation and operational services already use the listing check-in time;
- marketplace queries already calculate approved review count and average rating;
- verified guest reviews, owner responses and platform hide/restore moderation already exist;
- owner-to-guest review data currently exists as booking interactions;
- promotions, booking discounts and server-side quote protection already exist;
- `Payment` is immutable, supports provider metadata and links to financial allocations;
- a `PaymentGateway` abstraction and simulated gateway already exist;
- notifications, platform audits, booking history, refund requests and business/property authorization already exist.

The new work must preserve these behaviours and migrate existing records safely.

## Scope and non-goals

### In scope

- property-specific arrival and departure times;
- selected-date totals and longer-stay discounts on marketplace cards and checkout;
- compact and mobile-scrollable owner metrics;
- owner-business booking prevention;
- versioned platform questionnaires for both review directions;
- approval-first moderation for guest and owner reviews;
- real Paystack and Flutterwave initialization, callbacks, webhooks and verification;
- platform-owned gateway configuration;
- configurable fee-policy infrastructure;
- immutable owner payable ledger and administrator-approved manual settlement;
- payment, refund and settlement notifications and audit records.

### Deferred

- automated bank payouts or gateway transfer APIs;
- property-owner gateway credentials;
- public guest reputation profiles;
- free-form pre-booking reputation reports;
- automatic AI-generated review scoring from written text;
- exact live fee percentages, fixed charges, caps, gateway-charge ownership and settlement timetable;
- multi-currency conversion;
- a personal-booking exception for owners using a separate consumer identity.

## Architectural principles

- Controllers validate input and delegate; business decisions belong in services.
- Gateway-specific code is isolated behind contracts.
- Prices are integer minor units inside payment and ledger services.
- Booking confirmation is driven by verified payment state, never by browser callback state.
- Every externally retried operation is idempotent.
- Historical quotes, questionnaires, fee policies and allocations are snapshots, not live references only.
- Platform-wide rules and secrets are controlled through platform permissions.
- Business and property scope remains enforced in queries, policies and mutations, not only navigation.
- Financial records are reversed or adjusted, never edited away.

## Workstream 1: property and marketplace

### Arrival and departure times

Each published property must have a valid local `check_in_time` and `check_out_time`. The owner may select any valid time, including 08:00 check-in and 12:00 checkout. These values remain property settings rather than global defaults.

The times must appear consistently on:

- public property details;
- availability/date selection and checkout;
- booking review and confirmation;
- guest booking details and reminders;
- owner booking details;
- operations, arrival, checkout and task schedules.

Date-only availability retains half-open intervals: a checkout date may be another guest's check-in date. Time-sensitive cancellation deadlines and operational schedules combine the property timezone, booking date and stored property time. Existing records missing a time receive the current 14:00/11:00 defaults during migration.

### Unified pricing presentation

`BookingQuoteService` becomes the only public source for a booking quote. It accepts property, check-in, checkout, guest composition and currency, then returns a serializable quote containing:

- date range and number of nights;
- nightly base amount and accommodation subtotal;
- applied promotion and discount explanation;
- discount amount;
- provisional platform fee, when applicable;
- guest total;
- owner gross basis;
- currency and quote expiry/fingerprint.

With no dates, a property card shows the normal per-night price. With valid selected dates, it shows the date range, nights, original subtotal when discounted, savings, and final stay total. Checkout and the booking command recalculate the quote server-side; a stale or altered client quote is rejected and the updated amount is shown for confirmation.

Long-stay benefits come from owner-configured property promotions already supported by the application. The marketplace must not invent discounts independently.

### Rating presentation

Only active, platform-approved guest-to-property reviews contribute to public rating and count. A property with no approved review shows `New`; otherwise it shows the rounded average and count, for example `4.8 · 27 reviews`. Pending, rejected or hidden reviews never affect either value.

### Own-business booking protection

An authenticated user may not create a marketplace booking when they are the owner or an active staff/member of the property's business. The protection runs in the booking authorization/service layer before payment initialization and is repeated during final confirmation. The UI replaces the booking action with `Manage property` or `Open business`, but hiding the button is not the security boundary.

This rule intentionally applies to all active business members in the first release. A separate consumer identity or explicit personal-booking policy is deferred.

### Owner dashboard metrics

Large currency values use a shared compact formatter: `11,000` → `11K`, `11,000,000` → `11M`, and `10,000,000,000` → `10B`. The accessible label, tooltip or detail view retains the precise amount and currency.

On mobile, summary metrics use a horizontally scrollable snap row with visible continuation affordance. Desktop retains the grid. Dashboard analytics remain owner-only unless an explicit analytics permission is later granted.

## Workstream 2: platform-controlled reputation

### Review directions

Two independent review directions exist:

- guest reviews the property/host after a completed stay;
- an authorized property owner or booking manager reviews the guest after a completed stay.

Each booking permits at most one submitted review in each direction. Messages, support interactions and reviews remain separate concepts and storage flows.

Neither the reviewed guest nor business may approve, reject, hide or restore a review. Moderation belongs only to the platform.

### Versioned questionnaires

The platform owns one active questionnaire per direction. Publishing a questionnaire creates an immutable version; administrators edit a draft and publish a new version rather than changing a version already used by submissions.

Supported question types are:

- 1–5 rating;
- yes/no;
- single-choice;
- optional written response.

Scored questions carry a positive numeric weight. Choice answers define their normalized score. Questions may be required or optional and may be marked as a risk/integrity question. The sum of weights need not be exactly 100 because the scoring service normalizes by the weights applicable to answered scored questions.

Suggested initial guest-to-property scored dimensions are cleanliness 25, accuracy 20, communication 15, check-in 10, location 10, amenities 10 and value 10. This is seed content controlled by the platform, not hard-coded calculation logic.

### Scoring

`ReviewScoringService` converts each scored answer to a 0–1 value, multiplies it by its configured weight, divides the weighted total by applicable weight, and converts the result to a 1–5 display score. Defined risk answers may apply an explicit versioned penalty after weighted scoring. Optional text never changes the numeric score automatically in this MVP.

Every submission snapshots:

- questionnaire/version identity;
- question wording and direction;
- answer and normalized answer score;
- weight and penalty rule;
- calculated raw and final score;
- scoring algorithm version.

Historical scores therefore remain reproducible after the platform publishes new questions.

### Moderation lifecycle

New submissions enter `pending` and are invisible outside the submitting party, relevant booking operators and platform administrators. The platform may:

- approve and publish;
- reject with reason;
- hide a previously approved review with reason;
- restore a hidden review.

The moderator sees the booking, property/business, guest, verified payment, questionnaire answers, calculated score, written response and available evidence. The platform cannot silently edit the author's answers. All transitions record actor, reason, timestamps and before/after state in `PlatformAudit`.

An owner response to an approved property review also enters platform moderation before publication.

### Visibility

Approved guest-to-property reviews appear on the marketplace and determine public property rating. Approved owner-to-guest reviews appear to the reviewed guest, relevant authorized operators on future/linked bookings where policy permits, and platform administrators. Guest conduct reviews are not publicly searchable.

### Data model

Add focused models/tables:

- `review_questionnaires`: direction, status and active version reference;
- `review_questionnaire_versions`: version number, published metadata and algorithm version;
- `review_questions`: version, type, wording, ordering, required flag, weight and scoring/risk configuration;
- `review_submissions`: booking, author, subject type/id, direction, version, scores, moderation and publication fields;
- `review_answers`: submission, question snapshot, answer payload, normalized score, weight and penalty;
- `review_moderation_actions`: immutable action history.

Existing guest `reviews` and owner `owner_guest_review` interactions are migrated or adapted behind a common read model. The implementation plan must choose a safe staged migration that preserves current URLs and history while making the new submission model authoritative.

## Workstream 3: real payments through Verified Shortlet

### Gateway ownership and configuration

Verified Shortlet is merchant of record for this flow. Platform administrators configure Paystack and Flutterwave credentials for test and live environments. Property owners never enter or view provider keys.

Secret keys and webhook secrets are encrypted at rest, masked after save, excluded from logs/serialization and never sent to browser code. Only users with a dedicated platform payment-configuration permission may update them. Environment/provider changes require confirmation and an immutable audit record. Public keys may be exposed only where a provider's client integration requires them.

### Service boundaries

Extend the current payment abstraction into:

- `PaymentGatewayContract`: initialize, verify and refund provider transactions;
- `PaystackGateway` and `FlutterwaveGateway`: provider HTTP/signature mapping only;
- `PaymentOrchestrator`: provider selection and payment-attempt lifecycle;
- `PaymentVerificationService`: compares provider status, reference, amount and currency to the attempt;
- `WebhookProcessor`: authenticates, deduplicates and dispatches webhook events;
- `PaymentConfirmationService`: atomically performs final availability and booking confirmation;
- `PaymentAllocationService`: creates fee and owner-payable allocations from the booking snapshot;
- `RefundService`: refund request, provider processing and financial reversal;
- `SettlementService`: eligible payable selection and manual settlement transitions.

The simulated gateway remains selectable only in local/testing or behind an explicit non-production feature flag.

### Payment flow

1. Guest selects dates and guests.
2. Server checks authorization, availability and a fresh quote.
3. The system creates a pending booking hold/intent and immutable payment attempt with a unique platform reference.
4. `PaymentOrchestrator` initializes the selected platform gateway.
5. The guest completes payment using the provider-hosted/approved interface.
6. The browser callback displays `processing` and may trigger server verification, but never confirms the booking by itself.
7. The signed webhook is authenticated, persisted and deduplicated. The server verifies the transaction directly with the provider.
8. Verification must match successful status, provider reference, platform reference, exact expected amount and currency.
9. Inside a transaction, the system locks the booking/property availability scope and repeats the overlap check.
10. If available, the payment becomes successful, the booking becomes confirmed, occupied dates are committed, allocations are written and status history is recorded.
11. A `PaymentConfirmed` domain event sends receipts/notifications and starts relevant operational follow-up.

If payment succeeds after the dates became unavailable, the system must not double-book. It records a paid-but-unallocated exception and opens a refund/resolution case for platform action. It does not silently move dates or confirm a conflict.

### Webhook security and idempotency

Webhook processing must:

- read the raw request body before parsing;
- validate the provider signature with constant-time comparison;
- retain provider event ID/hash and reject duplicate processing;
- return an appropriate acknowledgement quickly and perform retry-safe follow-up;
- verify transactions using the provider API before delivering booking value;
- compare amount, currency, reference and environment;
- use unique database constraints on provider/reference/event identities;
- retain redacted raw payload, headers needed for diagnosis, result and failure reason;
- never log credentials, full card data or unredacted sensitive personal data.

Paystack signs the raw payload with HMAC SHA-512 in `x-paystack-signature`; Flutterwave webhooks likewise require signature/authentication and server-side transaction verification. The implementation follows the current official provider documentation linked in References.

### Payment and event data

Add or extend:

- `payment_attempts`: expected amount/currency, provider, platform/provider references, environment, status and expiry;
- `payment_webhook_events`: provider event identity/hash, redacted payload, signature result, processing status and timestamps;
- existing `payments`: verified successful/refund financial records;
- booking status history and platform audit metadata;
- reconciliation exception records for mismatches and unresolved events.

All amounts used for provider requests and comparisons are integer minor units. Display formatting occurs only at the application edge.

### Notifications

`PaymentConfirmed` listeners notify:

- the guest with booking confirmation and receipt;
- the property owner and authorized finance/booking staff with booking and net-allocation context;
- platform finance administrators with gross payment, fee and owner payable values.

Delivery uses existing in-app notifications and queued email notifications. Equivalent events cover payment failure, payment exception, refund requested/completed/failed, settlement approved/completed/reversed and reconciliation attention. Notification failure must not roll back a valid confirmed payment; it is retried and surfaced operationally.

## Workstream 4: fees, owner payable and settlement

### Provisional commercial policy

The architecture supports a platform-wide default and platform-admin-controlled per-business override. Property owners can view the policy applied to their business but cannot edit it.

Supported policy components are percentage, fixed amount, optional minimum/maximum, and responsibility mode:

- `guest_pays`: platform fee is added to the accommodation amount;
- `owner_pays`: platform fee is deducted from the owner's gross accommodation amount.

However, the following values are not approved by this specification and must remain configuration/review items before production activation:

- actual percentages and fixed amounts;
- minimums and caps;
- whether gateway charges are absorbed by the platform, guest or owner;
- tax treatment;
- refund allocation rules beyond full reversal;
- settlement reserve, delay and frequency;
- which businesses receive overrides.

No production default may be inferred from examples in this document. Until commercial approval, real-payment rollout uses an explicitly reviewed test policy and a feature flag preventing accidental live charges.

### Policy snapshots and calculation

Every initialized booking stores a fee-policy snapshot and calculated explanation. A later policy change affects only new quotes/attempts.

Illustrative arithmetic only, using a hypothetical 5% fee on a 100,000 accommodation amount:

- guest pays: guest total 105,000; owner payable 100,000; platform fee 5,000;
- owner pays: guest total 100,000; owner payable 95,000; platform fee 5,000.

The authoritative calculation records accommodation gross, discount, taxable/fee basis, platform fee, gateway charge, guest total, refund/reversal, owner payable, currency, policy version and human-readable explanation.

### Owner payable ledger

Successful verified payments create immutable double-entry-style payable ledger entries for the property business. Ledger states include `pending`, `available`, `reserved`, `settled`, `reversed` and `adjusted`. Availability follows the reviewed settlement policy, not the payment callback.

Financial corrections append reversal or adjustment entries linked to the original; they never update or delete settled history. The owner finance UI shows gross booking value, discounts, platform fee, gateway charge treatment, refunds/adjustments, pending balance, available balance and settled balance.

### Manual settlement

For the first release, authorized platform finance administrators settle owners manually:

1. choose one business and eligible available ledger entries;
2. create a settlement batch with calculated total;
3. review bank/account destination and risk/exceptions;
4. record external transfer reference and optional proof;
5. require a separate confirmation action before completion;
6. mark linked entries settled atomically;
7. create owner statement, notifications and platform audit.

A failed or reversed external transfer creates a settlement reversal and returns eligible value through ledger entries. It does not delete the settlement.

### Reconciliation

The platform finance workspace flags:

- provider success with no processed webhook;
- amount, currency, reference or environment mismatch;
- duplicate provider events or transactions;
- successful payment with unavailable dates;
- payment without balanced allocation;
- refund/payment mismatch;
- stale pending attempts;
- available owner balances omitted from expected settlement windows.

Reconciliation actions require notes and audit records.

## Authorization

New platform permissions are separated by responsibility:

- payment configuration view/manage;
- payment and reconciliation view/manage;
- fee policy view/manage;
- settlement view/create/approve/reverse;
- questionnaire view/draft/publish;
- review moderation view/manage.

Platform Super Admin receives all permissions. Finance and moderation roles receive only their relevant permissions. A settlement creator cannot perform the final approval when a second eligible administrator is available; the system records an explicit exception when the demonstration environment has only one authorized administrator.

Business permissions remain property-scoped. Owners and authorized finance users may view their own allocations/statements; they cannot access provider secrets, another business's ledger, platform fee controls or moderation controls.

## User interfaces

### Guest

- Property cards and detail pages show approved rating/count and contextual date totals.
- Property detail and checkout show arrival/departure times prominently.
- Checkout offers enabled provider choices and returns to a processing/confirmation state.
- Booking detail shows payment receipt, fee explanation appropriate to the guest, review invitation/status and property times.
- Guest reputation shows only approved owner reviews and pending-submission status without exposing platform-only notes.

### Property owner and staff

- Property settings expose arrival/departure time controls.
- Booking detail provides owner-to-guest questionnaire after completed stays.
- Finance pages show gross, deductions, owner payable and settlement statements.
- The business sees its effective fee policy read-only.
- Compact dashboard metrics preserve exact-value access.

### Platform administration

- Questionnaire drafts, previews, version publication and retirement.
- Unified review moderation queue for both directions and owner responses.
- Encrypted gateway configuration with test/live status and webhook-health information.
- Payment/reconciliation workspace with event history and exceptions.
- Global fee policy and per-business override management.
- Owner payable ledger and manual settlement batches.
- Audit history for every privileged transition.

## Error handling and recovery

- Provider timeout during initialization leaves a retryable attempt, never a confirmed booking.
- Invalid signatures are recorded safely and rejected without mutation.
- Provider verification mismatch creates a reconciliation exception.
- Duplicate callbacks/webhooks return the prior outcome without duplicate payment, booking, allocation or notification.
- Transaction failure rolls back booking confirmation and allocations together.
- Notification failure is retried independently.
- A questionnaire version with submissions cannot be edited or deleted.
- Invalid moderation/state transitions return validation errors without partial mutation.
- Cross-business and missing-permission access returns 403; foreign scoped records do not leak through lists or errors.

## Migration and rollout

1. Add new tables and nullable compatibility links without changing current runtime behaviour.
2. Seed draft questionnaires and migrate existing review history into snapshot-compatible records.
3. Introduce the unified quote and compact-display services behind tests.
4. Add gateway adapters in test mode and webhook endpoints with payment confirmation disabled.
5. Run provider test transactions and reconciliation checks.
6. Enable approval-first review submission and updated public rating reads.
7. Enable payment confirmation for internal staging accounts.
8. Add owner payable ledger and manual settlement in staging.
9. Obtain explicit commercial approval for the fee configuration.
10. Complete live-key, webhook, refund, notification and settlement rehearsal before enabling production payments.

Existing simulated-payment records stay identifiable as simulated and must never be included in real owner settlement balances.

## Testing and acceptance

### Property and pricing

- arbitrary valid arrival/departure times save and render in every required surface;
- cancellation and operations use the configured local time;
- selected-date cards, checkout and booking command return the same quote;
- longer-stay discounts use the correct promotion and historical snapshot;
- concurrent booking tests still permit only one overlapping confirmation;
- owner and all active business members receive 403/validation failure when booking their business property;
- compact numbers and mobile metric scrolling remain accessible.

### Reputation

- only completed, eligible bookings can create one review per direction;
- published questionnaire versions are immutable;
- weighted scoring and penalties are reproducible from snapshots;
- all new reviews/responses remain pending until platform approval;
- pending/rejected/hidden reviews never affect public averages/counts;
- only authorized platform roles moderate;
- owner-to-guest reviews remain private to authorized audiences;
- migrated existing reviews retain content and moderation history.

### Payments

- provider adapters initialize and verify test transactions;
- invalid signatures, amount/currency/reference mismatches and test/live mismatch are rejected;
- callback alone cannot confirm a booking;
- duplicated and out-of-order events are idempotent;
- successful verification confirms booking, allocation and status history atomically;
- a paid concurrency loser creates an exception/refund case, not a double booking;
- secrets are encrypted, masked and absent from logs/browser responses;
- guest, owner/staff and platform finance notifications are emitted once.

### Fees and settlement

- global defaults and per-business overrides resolve predictably;
- booking policy snapshots remain unchanged after policy edits;
- guest-pays and owner-pays arithmetic balances in minor units;
- every verified payment has balanced platform and owner allocations;
- refunds and corrections append reversals;
- only eligible available entries enter a settlement batch;
- approval, reversal, statement and audit history are complete;
- simulated payments cannot be settled.

Final verification includes focused feature/unit tests, full SQLite regression, MySQL migration and concurrency tests, route/config/view cache checks, production asset build, Paystack and Flutterwave sandbox webhook tests, and browser acceptance for guest, owner, moderator and finance-admin roles.

## References

- Paystack, [Webhooks](https://paystack.com/docs/payments/webhooks/)
- Paystack, [Verify Payments](https://paystack.com/docs/payments/verify-payments/)
- Flutterwave, [Webhooks](https://developer.flutterwave.com/docs/webhooks)
- Flutterwave, [Transaction Verification](https://developer.flutterwave.com/docs/transaction-verification)
