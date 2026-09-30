# Reputation, Payments and Settlement Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Deliver property-time and pricing improvements, platform-controlled bidirectional reputation, real Paystack/Flutterwave payments, and auditable owner settlements through Verified Shortlet.

**Architecture:** Extend the existing Laravel domain through four ordered phases. Keep controllers thin, isolate quote/reputation/gateway/ledger decisions in focused services, snapshot all historical calculations, and confirm bookings only after authenticated provider verification inside a transaction.

**Tech Stack:** Laravel 13, PHP 8.4, Blade/Alpine.js, MySQL/MariaDB production, SQLite tests, Laravel HTTP client, queues/notifications, Vite.

**Spec:** `docs/superpowers/specs/2026-09-30-reputation-payments-and-settlement-design.md`

## Global Constraints

- Verified Shortlet owns Paystack and Flutterwave credentials; businesses never configure provider keys.
- All provider and ledger amounts use integer minor units; decimal formatting happens only at application boundaries.
- Browser callbacks never confirm bookings; only authenticated and directly verified provider results do.
- Every callback, webhook, payment confirmation, allocation and settlement mutation is idempotent.
- All new reviews and owner responses require platform approval before affecting reputation or public display.
- Published questionnaire versions, historical quote snapshots and financial ledger entries are immutable.
- Existing tenant/property scoping and direct-route permission checks remain mandatory.
- Simulated payments remain local/testing-only and never create settleable owner balances.
- Exact production fees, caps, gateway-charge treatment, tax rules and settlement timing remain unapproved; live charging stays feature-flagged until reviewed.
- Do not introduce property-owner gateway credentials, automated bank payouts, public guest profiles or AI text-based scoring.

## Review Focus

- **Timezone boundaries:** a configured local arrival time around daylight/date boundaries must produce the correct cancellation deadline and reminder timestamp; Task 2 pins this.
- **Stale quote/promotion expiry:** a quote created before a promotion change must be rejected or explicitly refreshed before payment; Task 3 pins this.
- **Review-version races:** a guest opening a retired questionnaire while a new version is published must submit against the version originally issued or receive a safe refresh; Task 7 pins this.
- **Paid concurrency loser:** two guests can pay near-simultaneously, but only one booking may confirm and the other must enter a refund exception; Task 12 pins this.
- **Rounding and partial reversal:** fee allocations must balance to the minor unit after discounts, odd percentages and refunds; Task 16 pins this.

---

## File and phase map

### Phase A — property and marketplace

- `app/Services/Booking/BookingPricingService.php`: authoritative accommodation quote calculation.
- `app/Data/BookingPrice.php`: immutable quote result/snapshot fields.
- `app/Services/Booking/OwnBusinessBookingGuard.php`: reusable owner/staff booking denial.
- `app/Support/CompactMoney.php`: compact dashboard presentation only.
- Existing property, marketplace, checkout, booking and dashboard controllers/views consume those services.

### Phase B — reputation

- New `app/Models/ReviewQuestionnaire*.php`, `ReviewSubmission.php`, `ReviewAnswer.php`, `ReviewModerationAction.php`: immutable versioned review records.
- `app/Services/Reviews/ReviewQuestionnaireService.php`: draft/publish/version lifecycle.
- `app/Services/Reviews/ReviewScoringService.php`: deterministic weighted scoring.
- `app/Services/Reviews/ReviewSubmissionService.php`: eligibility and snapshot submission.
- `app/Services/Reviews/ReviewModerationService.php`: platform-only transitions and rating publication.
- Existing review controllers/views become consumers and compatibility entry points.

### Phase C — payments

- `app/Contracts/Payments/PaymentGateway.php`: initialize/verify/refund contract.
- Provider adapters under `app/Services/Payments/Gateways/` perform provider-only mapping.
- `PaymentOrchestrator`, `PaymentVerificationService`, `WebhookProcessor` and `PaymentConfirmationService` coordinate provider-independent flow.
- New payment attempts/webhook/reconciliation models preserve idempotency and diagnostics.
- Existing marketplace booking creation is split into pending intent creation and verified confirmation.

### Phase D — fees and settlements

- Fee policies and snapshots are platform-controlled.
- `PaymentAllocationService` creates balanced platform/owner amounts.
- An immutable owner payable ledger backs owner finance and platform settlement views.
- `SettlementService` creates, approves, completes and reverses manual batches.

---

## Phase A — Property, pricing and presentation

### Task 1: Preserve and display property arrival/departure times

**Files:**
- Create: `database/migrations/2026_09_30_000000_backfill_property_stay_times.php`
- Modify: `app/Http/Controllers/Owner/PropertySetupController.php`
- Modify: `app/Services/Property/PropertyPublicationReadinessService.php`
- Modify: `resources/views/owner/properties/setup-step.blade.php`
- Modify: `resources/views/marketplace/show.blade.php`
- Modify: `resources/views/marketplace/checkout.blade.php`
- Modify: `resources/views/guest/bookings/confirmation.blade.php`
- Modify: `resources/views/guest/bookings/show.blade.php`
- Modify: `resources/views/owner/bookings/show.blade.php`
- Test: `tests/Feature/Owner/PropertyWizardTest.php`
- Test: `tests/Feature/Marketplace/ServicedApartmentBookingJourneyTest.php`

**Interfaces:**
- Consumes: existing `PropertyMarketplaceListing::check_in_time` and `check_out_time`.
- Produces: consistently formatted local-time labels available on every booking-facing surface.

- [ ] **Step 1: Write failing feature tests** asserting an owner can save `08:00`/`12:00`, publication requires both, and public detail, checkout, confirmation, guest booking and owner booking render `8:00 AM`/`12:00 PM`.
- [ ] **Step 2: Run the focused tests** with `php artisan test tests/Feature/Owner/PropertyWizardTest.php tests/Feature/Marketplace/ServicedApartmentBookingJourneyTest.php`; expect the new rendering assertions to fail.
- [ ] **Step 3: Add an idempotent backfill migration** setting missing check-in/check-out values to `14:00`/`11:00` without changing already configured listings.
- [ ] **Step 4: Implement the minimal form and view changes** while keeping persisted values in `H:i` and formatting only in Blade/view data.
- [ ] **Step 5: Re-run the focused tests** and expect PASS.
- [ ] **Step 6: Commit** with `git commit -m "feat: surface property arrival and departure times"`.

### Task 2: Centralize property-local stay timestamps

**Files:**
- Create: `app/Services/Booking/PropertyStayTimeService.php`
- Modify: `app/Services/Booking/CancelMarketplaceBooking.php`
- Modify: `app/Services/Booking/BookingLifecycleService.php`
- Modify: `app/Services/Operations/BookingOperationsService.php`
- Modify: `routes/console.php`
- Test: `tests/Unit/Booking/PropertyStayTimeServiceTest.php`
- Test: `tests/Feature/Booking/GuestCancellationTest.php`
- Test: `tests/Feature/Operations/BookingOperationsTest.php`

**Interfaces:**
- Consumes: `Property`, arrival/departure dates and property/business timezone fallback.
- Produces: `checkInAt(Property $property, CarbonInterface $date): CarbonImmutable` and `checkOutAt(Property $property, CarbonInterface $date): CarbonImmutable`.

- [ ] **Step 1: Write failing unit tests** for `08:00`, `12:00`, timezone conversion and the 14:00/11:00 legacy fallback.
- [ ] **Step 2: Run** `php artisan test tests/Unit/Booking/PropertyStayTimeServiceTest.php`; expect class-not-found failure.
- [ ] **Step 3: Implement `PropertyStayTimeService`** and replace duplicated string/time composition in cancellation, lifecycle, operations and scheduled reminders.
- [ ] **Step 4: Add regression assertions** proving cancellation cutoff and operation due times use configured local time, including a UTC/date-boundary case.
- [ ] **Step 5: Run the three focused suites** and expect PASS.
- [ ] **Step 6: Commit** with `git commit -m "refactor: centralize property stay timestamps"`.

### Task 3: Expand the authoritative booking quote

**Files:**
- Modify: `app/Data/BookingPrice.php`
- Modify: `app/Services/Booking/BookingPricingService.php`
- Modify: `app/Http/Controllers/Guest/MarketplaceCheckoutController.php`
- Modify: `app/Services/Booking/CreateMarketplaceBooking.php`
- Modify: `app/Models/Booking.php`
- Create: `database/migrations/2026_09_30_000100_add_quote_snapshot_to_bookings.php`
- Test: `tests/Feature/Booking/BookingRulesTest.php`
- Test: `tests/Feature/Booking/MarketplaceCheckoutTest.php`

**Interfaces:**
- Produces: `quote(Property $property, CarbonImmutable $arrival, CarbonImmutable $departure, int $adults = 1, int $children = 0): BookingPrice`.
- `BookingPrice` exposes currency, nights, nightly/subtotal/discount/platform-fee/total minor amounts, applied promotion ID/name, date range, `fingerprint()` and `toSnapshot()`.

- [ ] **Step 1: Write failing tests** for no discount, qualifying percentage discount, non-qualifying minimum stay, deterministic fingerprint and persisted snapshot.
- [ ] **Step 2: Add the stale-quote test**: change/expire the promotion after quote generation and assert booking creation rejects the old fingerprint with a refreshed quote response.
- [ ] **Step 3: Run** `php artisan test tests/Feature/Booking/BookingRulesTest.php tests/Feature/Booking/MarketplaceCheckoutTest.php`; expect missing fields/signature failures.
- [ ] **Step 4: Add the migration and model casts/fillable fields** for `quote_snapshot` and `quote_fingerprint`.
- [ ] **Step 5: Expand `BookingPrice` and `BookingPricingService`** without adding commercial fees yet; `platformFeeMinor` remains zero until Phase D.
- [ ] **Step 6: Update quote and booking creation consumers** to compare fingerprints and persist the snapshot.
- [ ] **Step 7: Run focused tests** and expect PASS.
- [ ] **Step 8: Commit** with `git commit -m "feat: snapshot authoritative booking quotes"`.

### Task 4: Show contextual totals and approved ratings on marketplace cards

**Files:**
- Modify: `app/Services/Marketplace/MarketplacePropertyQuery.php`
- Modify: `app/Http/Controllers/MarketplaceController.php`
- Modify: `resources/views/components/marketplace-property-tile.blade.php`
- Modify: `resources/views/marketplace/show.blade.php`
- Modify: `resources/css/components.css`
- Test: `tests/Feature/Marketplace/MarketplaceSearchTest.php`
- Test: `tests/Unit/LandingPagePresentationTest.php`

**Interfaces:**
- Consumes: Task 3 `BookingPricingService`/`BookingPrice`; approved review aggregate query.
- Produces: card presentation with nightly fallback or selected-date range, nights, original subtotal, savings and final total.

- [ ] **Step 1: Write failing feature/presentation tests** for `New` with zero approved reviews, `4.8 · 27 reviews` with approved reviews, exclusion of pending/hidden reviews, nightly fallback and selected-date discounted total.
- [ ] **Step 2: Run the two focused tests** and verify failure.
- [ ] **Step 3: Add batched quote/card view data** without per-card N+1 queries and update the Blade component/CSS.
- [ ] **Step 4: Run focused tests and `npm run build`**; expect PASS.
- [ ] **Step 5: Commit** with `git commit -m "feat: show contextual marketplace prices and ratings"`.

### Task 5: Enforce own-business booking protection through one guard

**Files:**
- Create: `app/Services/Booking/OwnBusinessBookingGuard.php`
- Modify: `app/Http/Controllers/Guest/MarketplaceCheckoutController.php`
- Modify: `app/Services/Booking/CreateMarketplaceBooking.php`
- Modify when created in Task 12: `app/Services/Payments/PaymentConfirmationService.php`
- Modify: `resources/views/marketplace/show.blade.php`
- Test: `tests/Feature/Booking/MarketplaceCheckoutTest.php`
- Test: `tests/Feature/Owner/OwnerMarketplaceBookingTest.php`

**Interfaces:**
- Produces: `assertBookableBy(User $user, Property $property): void`, throwing a validation/authorization exception for active business owners or members.

- [ ] **Step 1: Write failing tests** for owner, manager, cleaner/staff, inactive former member and unrelated guest at checkout and POST booking boundaries.
- [ ] **Step 2: Run focused tests** and verify duplicated/incomplete protection fails at least one new case.
- [ ] **Step 3: Implement and inject the guard**, replacing inline membership checks and rendering `Manage property`/`Open business` for denied users.
- [ ] **Step 4: Run focused tests** and expect PASS.
- [ ] **Step 5: Commit** with `git commit -m "refactor: centralize own business booking protection"`.

### Task 6: Compact and scroll owner dashboard metrics

**Files:**
- Create: `app/Support/CompactMoney.php`
- Modify: `app/Http/Controllers/Owner/OwnerDashboardController.php`
- Modify: `resources/views/owner/dashboard.blade.php`
- Modify: `resources/css/components.css`
- Test: `tests/Unit/Support/CompactMoneyTest.php`
- Test: `tests/Feature/Owner/OwnerDashboardTodayTest.php`

**Interfaces:**
- Produces: `CompactMoney::format(int $minor, string $currency): string` plus exact accessible label.

- [ ] **Step 1: Write failing unit tests** for zero, thousands, millions, billions, negatives and boundary rounding (`999_950` minor-unit equivalent must not misleadingly overflow a suffix).
- [ ] **Step 2: Run focused tests** and expect class-not-found failure.
- [ ] **Step 3: Implement formatter and dashboard view data**, preserving exact amount in `title`/screen-reader text.
- [ ] **Step 4: Add responsive snap-row CSS** for mobile and retain desktop grid.
- [ ] **Step 5: Run tests and `npm run build`**; expect PASS.
- [ ] **Step 6: Commit** with `git commit -m "feat: compact owner dashboard metrics"`.

## Phase B — Platform-controlled reputation

### Task 7: Add versioned questionnaire schema and publication service

**Files:**
- Create: `database/migrations/2026_09_30_000200_create_versioned_review_questionnaires.php`
- Create: `app/Models/ReviewQuestionnaire.php`
- Create: `app/Models/ReviewQuestionnaireVersion.php`
- Create: `app/Models/ReviewQuestion.php`
- Create: `app/Services/Reviews/ReviewQuestionnaireService.php`
- Modify: `database/seeders/AccessControlSeeder.php`
- Create: `database/seeders/ReviewQuestionnaireSeeder.php`
- Test: `tests/Feature/Admin/ReviewQuestionnaireManagementTest.php`

**Interfaces:**
- Produces: `createDraft(string $direction, User $actor): ReviewQuestionnaireVersion`, `publish(ReviewQuestionnaireVersion $version, User $actor): ReviewQuestionnaireVersion`, and `active(string $direction): ReviewQuestionnaireVersion`.

- [ ] **Step 1: Write failing tests** for one active version per direction, immutable published versions, ordered typed questions, positive weights and audited publication.
- [ ] **Step 2: Add the review-version race test**: an invitation bound to version 1 remains on version 1 after version 2 is published.
- [ ] **Step 3: Run the new test file** and expect missing schema/classes.
- [ ] **Step 4: Add schema/models/service and seed the approved initial dimensions** without hard-coding them in scoring logic.
- [ ] **Step 5: Add platform permissions** for questionnaire view/draft/publish.
- [ ] **Step 6: Run the test file** and expect PASS.
- [ ] **Step 7: Commit** with `git commit -m "feat: add versioned review questionnaires"`.

### Task 8: Add deterministic review submissions and scoring

**Files:**
- Create: `database/migrations/2026_09_30_000300_create_review_submissions_and_answers.php`
- Create: `app/Models/ReviewSubmission.php`
- Create: `app/Models/ReviewAnswer.php`
- Create: `app/Services/Reviews/ReviewScoringService.php`
- Create: `app/Services/Reviews/ReviewSubmissionService.php`
- Modify: `app/Http/Controllers/Guest/GuestReviewController.php`
- Modify: `app/Http/Controllers/Owner/OwnerGuestStayReviewController.php`
- Test: `tests/Unit/Reviews/ReviewScoringServiceTest.php`
- Test: `tests/Feature/Booking/BidirectionalReviewSubmissionTest.php`

**Interfaces:**
- Produces: `score(ReviewQuestionnaireVersion $version, array $answers): ReviewScore`; `submit(Booking $booking, User $author, string $direction, array $answers): ReviewSubmission`.

- [ ] **Step 1: Write failing scoring tests** for weighted 1–5 values, yes/no and choice normalization, optional unanswered weight exclusion, configured penalty and deterministic rounding.
- [ ] **Step 2: Write failing submission tests** for completed-stay eligibility, one review per direction, author/booking ownership, questionnaire snapshot and default `pending` moderation.
- [ ] **Step 3: Run both test files** and verify failure.
- [ ] **Step 4: Add schema/models/value result and scoring service** using decimal/integer-safe arithmetic.
- [ ] **Step 5: Implement submission service and update both controllers** so messages/interactions are no longer the authoritative owner-review store.
- [ ] **Step 6: Run both test files** and expect PASS.
- [ ] **Step 7: Commit** with `git commit -m "feat: score bidirectional stay reviews"`.

### Task 9: Build platform questionnaire and unified moderation UI

**Files:**
- Create: `app/Http/Controllers/Admin/AdminReviewQuestionnaireController.php`
- Create: `app/Services/Reviews/ReviewModerationService.php`
- Modify: `app/Http/Controllers/Admin/AdminReviewController.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/reviews/questionnaires/index.blade.php`
- Create: `resources/views/admin/reviews/questionnaires/edit.blade.php`
- Modify: `resources/views/admin/reviews/index.blade.php`
- Modify: `resources/views/admin/reviews/show.blade.php`
- Test: `tests/Feature/Admin/ReviewQuestionnaireManagementTest.php`
- Modify: `tests/Feature/Admin/ReviewModerationTest.php`

**Interfaces:**
- Consumes: Tasks 7–8 questionnaire/submission models.
- Produces: `approve`, `reject`, `hide`, `restore` transitions with immutable moderation action/audit history.

- [ ] **Step 1: Extend failing feature tests** for questionnaire CRUD/publish permission, both review directions in the queue, required reasons, invalid transitions and inability of business users to moderate.
- [ ] **Step 2: Run admin review tests** and verify failure.
- [ ] **Step 3: Add controller routes/views and moderation service**, keeping content immutable.
- [ ] **Step 4: Update approved aggregate queries** so only approved active guest-to-property submissions affect marketplace rating and approved owner-to-guest submissions affect private guest reputation.
- [ ] **Step 5: Run admin and marketplace review tests** and expect PASS.
- [ ] **Step 6: Commit** with `git commit -m "feat: add platform review governance"`.

### Task 10: Migrate existing reviews and update guest/owner surfaces

**Files:**
- Create: `database/migrations/2026_09_30_000400_backfill_versioned_review_submissions.php`
- Modify: `app/Services/Reviews/VerifiedStayReviewService.php`
- Modify: `app/Services/Booking/OwnerBookingWorkspace.php`
- Modify: `resources/views/guest/bookings/show.blade.php`
- Modify: `resources/views/owner/bookings/show.blade.php`
- Modify: `resources/views/marketplace/reviews.blade.php`
- Test: `tests/Feature/Booking/VerifiedStayReviewTest.php`
- Test: `tests/Feature/Owner/OwnerMessagesAndGuestReviewTest.php`

**Interfaces:**
- Consumes: Tasks 7–9 versioned reputation system.
- Produces: compatibility reads for current URLs plus migrated historical content/moderation state.

- [ ] **Step 1: Write failing migration/compatibility tests** proving existing approved/hidden guest reviews and owner guest-review interactions retain content, author, booking and visibility.
- [ ] **Step 2: Write UI assertions** for pending review state, approved property review, private guest reputation and moderated owner response.
- [ ] **Step 3: Run focused tests** and verify failure.
- [ ] **Step 4: Implement idempotent backfill and compatibility relationships/read models**; do not delete legacy records during this release.
- [ ] **Step 5: Update guest, owner and marketplace views** to use authoritative submissions.
- [ ] **Step 6: Run focused review suites** and expect PASS.
- [ ] **Step 7: Commit** with `git commit -m "migrate: preserve reviews in versioned reputation model"`.

## Phase C — Paystack and Flutterwave payments

### Task 11: Add encrypted platform gateway configuration and provider contract

**Files:**
- Create: `database/migrations/2026_09_30_000500_create_payment_gateway_configurations.php`
- Create: `app/Models/PaymentGatewayConfiguration.php`
- Modify: `app/Contracts/Payments/PaymentGateway.php`
- Create: `app/Data/PaymentInitialization.php`
- Create: `app/Data/VerifiedPayment.php`
- Create: `app/Data/RefundResult.php`
- Modify: `app/Services/Payments/SimulatedPaymentGateway.php`
- Create: `app/Http/Controllers/Admin/AdminPaymentGatewayController.php`
- Create: `resources/views/admin/payments/gateways.blade.php`
- Modify: `routes/web.php`
- Modify: `database/seeders/AccessControlSeeder.php`
- Test: `tests/Feature/Admin/PaymentGatewayConfigurationTest.php`

**Interfaces:**
- `PaymentGateway` produces `initialize(PaymentAttempt $attempt): PaymentInitialization`, `verify(string $providerReference): VerifiedPayment`, and `refund(Payment $payment, int $amountMinor): RefundResult`.

- [ ] **Step 1: Write failing tests** for permission, encrypted test/live secrets, masking after save, environment switch audit and absence from serialization/log output.
- [ ] **Step 2: Run the test** and expect missing schema/route failures.
- [ ] **Step 3: Add configuration model with encrypted casts**, platform permissions and masked admin forms.
- [ ] **Step 4: Expand the contract/value objects and adapt the simulated gateway** to the new interface without enabling it in production.
- [ ] **Step 5: Run tests** and expect PASS.
- [ ] **Step 6: Commit** with `git commit -m "feat: configure platform payment gateways securely"`.

### Task 12: Add payment attempts, webhook inbox and idempotent confirmation

**Files:**
- Create: `database/migrations/2026_09_30_000600_create_payment_attempts_and_webhook_events.php`
- Create: `app/Models/PaymentAttempt.php`
- Create: `app/Models/PaymentWebhookEvent.php`
- Create: `app/Models/PaymentReconciliationException.php`
- Create: `app/Services/Payments/PaymentOrchestrator.php`
- Create: `app/Services/Payments/PaymentVerificationService.php`
- Create: `app/Services/Payments/WebhookProcessor.php`
- Create: `app/Services/Payments/PaymentConfirmationService.php`
- Refactor: `app/Services/Booking/CreateMarketplaceBooking.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/Booking/MarketplaceCheckoutTest.php`
- Create: `tests/Feature/Payments/PaymentConfirmationTest.php`
- Modify: `tests/Concurrency/MarketplaceDoubleBookingMysqlTest.php`

**Interfaces:**
- Produces: `PaymentOrchestrator::initialize(Booking $booking, string $provider): PaymentInitialization`.
- Produces: `WebhookProcessor::process(string $provider, string $rawBody, array $headers): WebhookOutcome`.
- Produces: `PaymentConfirmationService::confirm(PaymentAttempt $attempt, VerifiedPayment $verified): Booking|PaymentReconciliationException`.

- [ ] **Step 1: Write failing tests** for pending booking/attempt creation, callback remaining processing, exact amount/currency/reference/environment verification, duplicate events and atomic confirmation.
- [ ] **Step 2: Add the paid-concurrency-loser test**: two verified attempts overlap; exactly one confirms and the other creates a paid-unallocated refund exception.
- [ ] **Step 3: Run payment and MySQL concurrency tests** and verify failure.
- [ ] **Step 4: Add schema/models with unique provider/event/reference constraints** and immutable diagnostic fields.
- [ ] **Step 5: Split `CreateMarketplaceBooking`** into pending booking creation; move successful lifecycle/availability/payment mutation to `PaymentConfirmationService` and re-run Task 5's own-business guard at confirmation.
- [ ] **Step 6: Implement orchestrator, verification and webhook inbox**, preserving redacted raw payload and retry-safe outcomes.
- [ ] **Step 7: Run focused SQLite tests and MySQL concurrency proof** and expect PASS.
- [ ] **Step 8: Commit** with `git commit -m "feat: confirm bookings from verified payments"`.

### Task 13: Implement Paystack and Flutterwave adapters/webhooks

**Files:**
- Create: `app/Services/Payments/Gateways/PaystackGateway.php`
- Create: `app/Services/Payments/Gateways/FlutterwaveGateway.php`
- Create: `app/Services/Payments/PaymentGatewayManager.php`
- Create: `app/Http/Controllers/Webhooks/PaystackWebhookController.php`
- Create: `app/Http/Controllers/Webhooks/FlutterwaveWebhookController.php`
- Create: `app/Http/Controllers/Guest/PaymentCallbackController.php`
- Modify: `app/Http/Controllers/Guest/MarketplaceCheckoutController.php`
- Modify: `resources/views/marketplace/checkout.blade.php`
- Modify: `routes/web.php`
- Modify: `bootstrap/app.php`
- Modify: `config/services.php`
- Create: `tests/Feature/Payments/PaystackPaymentTest.php`
- Create: `tests/Feature/Payments/FlutterwavePaymentTest.php`

**Interfaces:**
- Consumes: Task 11 `PaymentGateway`; Task 12 `WebhookProcessor`.
- Produces: provider adapters selected by `PaymentGatewayManager::for(string $provider): PaymentGateway`.

- [ ] **Step 1: Write HTTP-faked Paystack tests** for initialization, `x-paystack-signature` HMAC SHA-512 validation, direct verification, mismatch rejection and duplicate success event.
- [ ] **Step 2: Write HTTP-faked Flutterwave tests** for initialization, current official signature validation, direct verification, mismatch rejection and duplicate event.
- [ ] **Step 3: Run both test files** and verify missing adapter failures.
- [ ] **Step 4: Implement adapters and manager** using Laravel HTTP timeouts/retries and redacted exception context.
- [ ] **Step 5: Add CSRF-exempt webhook endpoints in `bootstrap/app.php`, provider choice at checkout, and processing-only callback pages**; acknowledge valid duplicate events without duplicating work.
- [ ] **Step 6: Run provider tests** and expect PASS.
- [ ] **Step 7: Commit** with `git commit -m "feat: integrate paystack and flutterwave payments"`.

### Task 14: Add payment receipts, notifications and reconciliation workspace

**Files:**
- Create: `app/Events/PaymentConfirmed.php`
- Create: `app/Listeners/NotifyPaymentParticipants.php`
- Create: `app/Listeners/RecordPaymentOperations.php`
- Modify: `app/Services/Notifications/ProductNotificationService.php`
- Create: `app/Http/Controllers/Admin/AdminPaymentController.php`
- Create: `resources/views/admin/payments/index.blade.php`
- Create: `resources/views/admin/payments/show.blade.php`
- Modify: `resources/views/guest/bookings/confirmation.blade.php`
- Modify: `resources/views/guest/bookings/receipt.blade.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/Payments/PaymentNotificationTest.php`
- Create: `tests/Feature/Admin/PaymentReconciliationTest.php`

**Interfaces:**
- Consumes: Task 12 confirmed payment/exception records.
- Produces: one queued notification set per domain event and admin reconciliation actions with audit notes.

- [ ] **Step 1: Write failing tests** asserting one guest receipt, one owner/authorized-finance notification and one platform-finance notification per confirmation despite webhook replay.
- [ ] **Step 2: Write failing reconciliation tests** for mismatch, missing allocation, stale pending attempt and paid-unavailable exception visibility/permission.
- [ ] **Step 3: Run both tests** and verify failure.
- [ ] **Step 4: Add domain event/listeners**, ensuring notification failure cannot roll back confirmation.
- [ ] **Step 5: Add receipt and platform reconciliation routes/controllers/views** with permission and audit enforcement.
- [ ] **Step 6: Run tests and `npm run build`**; expect PASS.
- [ ] **Step 7: Commit** with `git commit -m "feat: notify and reconcile verified payments"`.

### Task 15: Add verified refund processing and financial reversal events

**Files:**
- Create: `app/Services/Payments/RefundService.php`
- Create: `app/Events/PaymentRefunded.php`
- Create: `app/Listeners/NotifyRefundParticipants.php`
- Modify: `app/Models/RefundRequest.php`
- Modify: `app/Http/Controllers/Admin/AdminPaymentController.php`
- Modify: `resources/views/admin/payments/show.blade.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/Payments/PaymentRefundTest.php`

**Interfaces:**
- Consumes: Task 11 gateway refund operation and Task 12 verified payment/exception records.
- Produces: `RefundService::request(Payment $payment, int $amountMinor, string $reason, User $actor): RefundRequest` and `process(RefundRequest $request, User $actor): Payment`.

- [ ] **Step 1: Write failing tests** for full/partial eligible refunds, cumulative amount ceiling, provider failure, idempotent retry, paid-unavailable exception handoff and permission/audit checks.
- [ ] **Step 2: Write notification assertions** for guest, business finance users and platform finance administrators on requested/completed/failed refund states.
- [ ] **Step 3: Run the new test** and verify missing service/route failures.
- [ ] **Step 4: Implement `RefundService`** so a provider-confirmed refund creates an immutable refund `Payment`, links the original and emits `PaymentRefunded`; do not perform Phase D ledger reversal until Task 17.
- [ ] **Step 5: Add platform refund controls** to the payment case view with mandatory reasons and safe retry status.
- [ ] **Step 6: Run the test** and expect PASS.
- [ ] **Step 7: Commit** with `git commit -m "feat: process verified payment refunds"`.

## Phase D — Provisional fees and manual settlement

### Task 16: Add versioned fee policies and balanced allocation

**Files:**
- Create: `database/migrations/2026_09_30_000700_create_fee_policies_and_payment_allocations.php`
- Create: `app/Models/FeePolicy.php`
- Create: `app/Models/FeePolicyOverride.php`
- Create: `app/Models/BookingFeeSnapshot.php`
- Create: `app/Data/PaymentAllocation.php`
- Create: `app/Services/Finance/FeePolicyResolver.php`
- Create: `app/Services/Finance/PaymentAllocationService.php`
- Create: `app/Http/Controllers/Admin/AdminFeePolicyController.php`
- Create: `resources/views/admin/finance/fee-policies.blade.php`
- Modify: `routes/web.php`
- Modify: `app/Services/Booking/BookingPricingService.php`
- Modify: `app/Services/Payments/PaymentConfirmationService.php`
- Test: `tests/Unit/Finance/PaymentAllocationServiceTest.php`
- Create: `tests/Feature/Admin/FeePolicyTest.php`

**Interfaces:**
- Produces: `FeePolicyResolver::for(Business $business, CarbonInterface $at): FeePolicySnapshot`.
- Produces: `PaymentAllocationService::calculate(BookingPrice $quote, FeePolicySnapshot $policy, int $gatewayChargeMinor = 0): PaymentAllocation` and `record(Payment $payment, PaymentAllocation $allocation): void`.

- [ ] **Step 1: Write failing allocation tests** for guest-pays, owner-pays, fixed/percentage/combined fee, minimum/maximum and zero unapproved default.
- [ ] **Step 2: Add rounding/partial reversal tests** using odd totals and percentages; assert guest total equals captured amount and platform plus owner allocations balance to the minor unit.
- [ ] **Step 3: Write failing policy tests** for global default, time-bound per-business override, owner read-only visibility and historical snapshot stability.
- [ ] **Step 4: Run focused tests** and verify missing schema/services.
- [ ] **Step 5: Add schema/models/resolver/calculator and platform-admin policy UI** with no inferred production fee values and a live-payment commercial-approval flag.
- [ ] **Step 6: Integrate quote display and verified confirmation allocation** while preserving zero-fee test policy until configured.
- [ ] **Step 7: Run focused tests** and expect PASS.
- [ ] **Step 8: Commit** with `git commit -m "feat: calculate versioned platform fee allocations"`.

### Task 17: Add immutable owner payable ledger

**Files:**
- Create: `database/migrations/2026_09_30_000800_create_owner_payable_ledger.php`
- Create: `app/Models/OwnerPayableEntry.php`
- Create: `app/Services/Finance/OwnerPayableLedger.php`
- Modify: `app/Services/Payments/RefundService.php`
- Modify: `app/Services/Finance/OwnerFinanceWorkspace.php`
- Modify: `resources/views/owner/finance.blade.php`
- Create: `tests/Feature/Owner/OwnerPayableLedgerTest.php`

**Interfaces:**
- Produces: `credit(Payment $payment, PaymentAllocation $allocation): OwnerPayableEntry`, `reverse(OwnerPayableEntry $entry, int $amountMinor, string $reason, User $actor): OwnerPayableEntry`, and scoped balance queries.

- [ ] **Step 1: Write failing tests** for pending/available/settled balances, immutable entries, linked reversal/adjustment, tenant/property scope and exclusion of simulated payments.
- [ ] **Step 2: Run the new test** and verify failure.
- [ ] **Step 3: Add schema/model/service** with append-only enforcement and unique source allocation constraint; connect verified full/partial refunds to proportional linked reversal entries.
- [ ] **Step 4: Update owner finance workspace/UI** to show gross, discounts, platform fee, gateway treatment, refund/adjustment and precise pending/available/settled values.
- [ ] **Step 5: Run tests and `npm run build`**; expect PASS.
- [ ] **Step 6: Commit** with `git commit -m "feat: add owner payable ledger"`.

### Task 18: Add manual settlement batches and reversal

**Files:**
- Create: `database/migrations/2026_09_30_000900_create_manual_settlements.php`
- Create: `app/Models/SettlementBatch.php`
- Create: `app/Models/SettlementItem.php`
- Create: `app/Models/BusinessSettlementAccount.php`
- Create: `app/Services/Finance/SettlementService.php`
- Create: `app/Http/Controllers/Admin/AdminSettlementController.php`
- Create: `resources/views/admin/settlements/index.blade.php`
- Create: `resources/views/admin/settlements/create.blade.php`
- Create: `resources/views/admin/settlements/show.blade.php`
- Modify: `routes/web.php`
- Modify: `database/seeders/AccessControlSeeder.php`
- Create: `tests/Feature/Admin/SettlementManagementTest.php`

**Interfaces:**
- Produces: encrypted/masked `BusinessSettlementAccount`; `create(Business $business, Collection $entries, User $creator): SettlementBatch`, `approve(SettlementBatch $batch, User $approver): SettlementBatch`, `complete(SettlementBatch $batch, string $transferReference, ?UploadedFile $proof, User $actor): SettlementBatch`, and `reverse(SettlementBatch $batch, string $reason, User $actor): SettlementBatch`.

- [ ] **Step 1: Write failing tests** for encrypted/masked destination accounts, eligibility, one-business batches, double-selection protection, creator/approver separation, transfer reference/proof, atomic settlement and audited reversal.
- [ ] **Step 2: Run the new test** and verify missing schema/service failures.
- [ ] **Step 3: Add settlement permissions, schema/models/service** with row locks and idempotent transitions.
- [ ] **Step 4: Add platform settlement controllers/routes/views** and owner-visible settlement statement references.
- [ ] **Step 5: Add notifications** for approved/completed/reversed settlements without coupling delivery to the ledger transaction.
- [ ] **Step 6: Run tests and `npm run build`**; expect PASS.
- [ ] **Step 7: Commit** with `git commit -m "feat: add audited manual owner settlements"`.

## Programme verification and rollout

### Task 19: Complete cross-workstream regression and staging controls

**Files:**
- Modify: `.env.example`
- Modify: `config/services.php`
- Modify: `docs/release/mvp1-production-runbook.md`
- Modify: `docs/release/2026-09-14-client-presentation-browser-runbook.md`
- Create: `tests/Feature/Payments/CommercialActivationGuardTest.php`
- Modify: `database/seeders/PresentationDemoSeeder.php`

**Interfaces:**
- Consumes: all prior tasks.
- Produces: safe staging defaults, repeatable review/payment/settlement demonstration records and deploy/recovery instructions.

- [ ] **Step 1: Write the activation-guard test** proving live payment initialization fails unless live credentials, HTTPS webhook URLs and explicit commercial fee approval are all present.
- [ ] **Step 2: Extend presentation seeding** with pending/approved reviews in both directions, a test payment/reconciliation case and an unsettled owner payable entry; keep it idempotent.
- [ ] **Step 3: Run focused defence suites** for marketplace, booking, reviews, admin permissions, payments, finance and settlement.
- [ ] **Step 4: Run full SQLite regression** with `php artisan test`; expect zero failures.
- [ ] **Step 5: Run MySQL migrations and concurrency proof** in the existing testing environment; expect all migrations and double-booking/payment races to pass.
- [ ] **Step 6: Run framework/build verification**: `php artisan route:cache`, `php artisan config:cache`, `php artisan view:cache`, `npm run build`, then clear generated caches needed for local development.
- [ ] **Step 7: Update release/browser runbooks** with provider sandbox setup, webhook URLs, reconciliation, review moderation, manual settlement, rollback and the explicit unresolved fee-review gate.
- [ ] **Step 8: Perform browser acceptance** for guest, owner, moderator and finance admin on desktop/mobile; verify no console or missing-resource errors.
- [ ] **Step 9: Commit** with `git commit -m "test: verify reputation payment and settlement programme"`.

## Execution order and release gates

Tasks are intentionally sequential by phase. Phase A may ship without the later phases. Phase B may ship after review migration acceptance. Phase C remains sandbox-only until gateway/security/reconciliation acceptance passes. Phase D remains staging-only until the business approves fee policy and settlement operations.

Before production payment activation, obtain written approval for:

1. fee percentage/fixed formula and caps;
2. guest-pays versus owner-pays default and override eligibility;
3. gateway-charge and tax responsibility;
4. refund allocation and dispute rules;
5. payable reserve/delay and settlement schedule;
6. live Paystack/Flutterwave account ownership and finance contacts.
