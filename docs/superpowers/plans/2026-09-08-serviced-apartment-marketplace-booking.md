# Serviced-Apartment Marketplace and Booking Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a fully backend-powered serviced-apartment marketplace where guests register, search ten seeded apartments, book available dates through simulated payment, and manage bookings visible to the correct owner.

**Architecture:** Extend the existing Laravel property, marketplace listing, availability, booking, payment, and business-context models. Public controllers delegate eligibility, search, pricing, availability, checkout, and cancellation rules to focused services; checkout owns state transitions inside a database transaction and a configurable payment gateway boundary.

**Tech Stack:** PHP 8.2+, Laravel 12, Blade, Alpine.js, Tailwind/Vite, PHPUnit, MySQL 8, SQLite test fallback.

**Spec:** `docs/superpowers/specs/2026-09-08-serviced-apartment-marketplace-booking-design.md`

## Global Constraints

- This version supports serviced apartments rented only as entire places.
- Seed exactly two business operators with five apartments each.
- Public results contain only active, verified, published, ready, operational, eligible listings.
- All prices and availability decisions are recalculated on the backend.
- Confirmed date intervals are arrival-inclusive and departure-exclusive.
- Payment is simulated behind a replaceable gateway contract.
- Cancellations outside 48 hours are refundable; later cancellations are non-refundable.
- External channel connections remain pending.

---

### Task 1: Marketplace schema and model contracts

**Files:**
- Create: `database/migrations/2026_09_08_000100_extend_marketplace_for_guest_booking.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/PropertyMarketplaceListing.php`
- Modify: `database/factories/PropertyFactory.php`
- Test: `tests/Feature/Marketplace/MarketplaceSchemaTest.php`

**Interfaces:**
- Produces: `Property::$beds`, `PropertyMarketplaceListing::$stay_categories`, searchable indexes, and model casts used by all later tasks.

- [ ] **Step 1: Write the failing schema test**

```php
public function test_marketplace_inventory_fields_are_available(): void
{
    $property = Property::factory()->create(['beds' => 3]);
    $listing = PropertyMarketplaceListing::create([
        'id' => (string) Str::uuid(), 'business_id' => $property->business_id,
        'property_id' => $property->id, 'slug' => 'schema-test',
        'public_title' => 'Schema Test', 'stay_categories' => ['family', 'business'],
    ]);

    $this->assertSame(3, $property->fresh()->beds);
    $this->assertSame(['family', 'business'], $listing->fresh()->stay_categories);
}
```

- [ ] **Step 2: Run `php artisan test tests/Feature/Marketplace/MarketplaceSchemaTest.php` and verify it fails for missing columns.**
- [ ] **Step 3: Add nullable/default-safe columns and explicit MySQL-safe index names, then add fillable/casts and factory defaults.**

```php
Schema::table('properties', function (Blueprint $table): void {
    $table->unsignedSmallInteger('beds')->default(1)->after('bedrooms');
});
Schema::table('property_marketplace_listings', function (Blueprint $table): void {
    $table->json('stay_categories')->nullable()->after('public_description');
});
```

- [ ] **Step 4: Run the focused test on SQLite and an isolated MySQL migration smoke database.**
- [ ] **Step 5: Commit with `git commit -m "feat: extend serviced apartment marketplace schema"`.**

### Task 2: Ten-property idempotent serviced-apartment seed

**Files:**
- Create: `database/seeders/ServicedApartmentMarketplaceSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Modify: `database/factories/PropertyFactory.php`
- Test: `tests/Feature/Marketplace/ServicedApartmentMarketplaceSeederTest.php`

**Interfaces:**
- Produces: stable owner emails, business codes, ten property codes/slugs, listing tags, media, amenities, promotions, and rolling availability used by marketplace acceptance tests.

- [ ] **Step 1: Write a failing repeatability and tenant-distribution test.**

```php
$this->seed(ServicedApartmentMarketplaceSeeder::class);
$this->seed(ServicedApartmentMarketplaceSeeder::class);
$this->assertDatabaseCount('businesses', 2);
$this->assertDatabaseCount('properties', 10);
$this->assertSame([5, 5], Business::withCount('properties')->orderBy('name')->pluck('properties_count')->all());
```

- [ ] **Step 2: Verify the test fails because the seeder does not exist.**
- [ ] **Step 3: Implement stable `updateOrCreate` records for two owners and businesses, five Lekki/Ikoyi/Victoria Island apartments each, and deterministic associated rows.**
- [ ] **Step 4: Assert every property is serviced-apartment/entire-place, verified, published, operational, marketplace eligible, priced in NGN, and has beds, media, amenities, categories, and future availability.**
- [ ] **Step 5: Run the seeder twice in the test and local development database; confirm totals remain ten.**
- [ ] **Step 6: Commit with `git commit -m "feat: seed serviced apartment marketplace inventory"`.**

### Task 3: Public marketplace eligibility, search, filters, and pagination

**Files:**
- Create: `app/Services/Marketplace/MarketplacePropertyQuery.php`
- Create: `app/Http/Requests/MarketplaceSearchRequest.php`
- Create: `app/Http/Controllers/MarketplaceController.php`
- Create: `app/Http/Controllers/MarketplacePropertyController.php`
- Modify: `routes/web.php`
- Modify: `resources/views/welcome.blade.php`
- Modify: `resources/views/components/category-pills.blade.php`
- Modify: `resources/views/components/filter-bar.blade.php`
- Modify: `resources/views/components/property-card.blade.php`
- Create: `resources/views/marketplace/show.blade.php`
- Test: `tests/Feature/Marketplace/MarketplaceSearchTest.php`
- Test: `tests/Feature/Marketplace/MarketplaceVisibilityTest.php`

**Interfaces:**
- Produces: `MarketplacePropertyQuery::paginate(array $filters): LengthAwarePaginator` and `eligibleBySlug(string $slug): PropertyMarketplaceListing`.

- [ ] **Step 1: Write failing tests for text search, every category, price range, serviced-apartment type, minimum beds, guests, composed filters, pagination query retention, and ineligible-detail 404s.**
- [ ] **Step 2: Run the two feature files and verify they fail against the static landing page.**
- [ ] **Step 3: Implement validated query input.**

```php
return [
    'q' => ['nullable', 'string', 'max:120'],
    'category' => ['nullable', Rule::in(['all','lekki','ikoyi','victoria-island','beachfront','family','business'])],
    'min_price' => ['nullable', 'numeric', 'min:0'],
    'max_price' => ['nullable', 'numeric', 'gte:min_price'],
    'beds' => ['nullable', 'integer', 'min:1', 'max:20'],
    'guests' => ['nullable', 'integer', 'min:1', 'max:50'],
    'check_in' => ['nullable', 'date', 'after_or_equal:today', 'required_with:check_out'],
    'check_out' => ['nullable', 'date', 'after:check_in', 'required_with:check_in'],
];
```

- [ ] **Step 4: Implement one eligibility scope/query and reuse it for index and detail. Eager-load listing/media/amenities and paginate five per page with `withQueryString()`.**
- [ ] **Step 5: Bind the existing landing filters to GET parameters and render real cards, totals, empty state, and pagination; create the real property detail page.**
- [ ] **Step 6: Run focused tests, Blade compilation, and production build.**
- [ ] **Step 7: Commit with `git commit -m "feat: power serviced apartment marketplace search"`.**

### Task 4: Guest registration return path and guest dashboard authorization

**Files:**
- Modify: `app/Http/Controllers/Auth/RegisteredUserController.php`
- Modify: `app/Services/Auth/RegisterUserService.php`
- Modify: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- Create: `app/Http/Controllers/Guest/GuestBookingController.php`
- Create: `resources/views/guest/bookings/index.blade.php`
- Create: `resources/views/guest/bookings/show.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Guest/GuestRegistrationJourneyTest.php`
- Test: `tests/Feature/Guest/GuestBookingAuthorizationTest.php`

**Interfaces:**
- Produces: authenticated guest return-to-checkout behavior and guest-owned booking index/detail routes.

- [ ] **Step 1: Write failing tests that registration assigns only the guest role, preserves intended checkout state, and prevents one guest reading another guest's booking.**
- [ ] **Step 2: Run focused tests and verify the current verification redirect/static routing fails expectations.**
- [ ] **Step 3: Preserve Laravel's intended URL and session checkout payload through login/registration, without granting business membership.**
- [ ] **Step 4: Implement guest booking queries constrained by `guest_user_id = auth()->id()` and route-model 404 behavior.**
- [ ] **Step 5: Render database-backed guest booking list/detail views with state, amount, dates, and actions.**
- [ ] **Step 6: Run authentication and guest authorization regression tests.**
- [ ] **Step 7: Commit with `git commit -m "feat: add guest booking account journey"`.**

### Task 5: Availability and authoritative pricing services

**Files:**
- Create: `app/Data/BookingPrice.php`
- Create: `app/Services/Booking/PropertyAvailabilityService.php`
- Create: `app/Services/Booking/BookingPricingService.php`
- Create: `app/Exceptions/PropertyUnavailable.php`
- Test: `tests/Unit/Booking/PropertyAvailabilityServiceTest.php`
- Test: `tests/Unit/Booking/BookingPricingServiceTest.php`

**Interfaces:**
- Produces: `isAvailable(Property $property, CarbonImmutable $arrival, CarbonImmutable $departure): bool`, `lockAndAssertAvailable(...)`, and `quote(Property $property, CarbonImmutable $arrival, CarbonImmutable $departure): BookingPrice`.

- [ ] **Step 1: Write failing tests for half-open ranges, blocks, confirmed bookings, released/failed attempts, promotion eligibility, and the 5% service fee.**
- [ ] **Step 2: Verify failures because services/value object do not exist.**
- [ ] **Step 3: Implement immutable integer-minor-unit calculations, converting to schema decimals only at persistence boundaries.**

```php
$discountedSubtotal = $subtotal - $discount;
$serviceFee = (int) round($discountedSubtotal * 0.05);
return new BookingPrice('NGN', $nights, $subtotal, $discount, $serviceFee, $discountedSubtotal + $serviceFee);
```

- [ ] **Step 4: Implement overlap checks and `lockForUpdate()` over the property plus requested availability-day rows.**
- [ ] **Step 5: Run unit tests on SQLite, then repeat locking/conflict coverage on MySQL.**
- [ ] **Step 6: Commit with `git commit -m "feat: add booking availability and pricing rules"`.**

### Task 6: Simulated payment and atomic checkout

**Files:**
- Create: `app/Contracts/Payments/PaymentGateway.php`
- Create: `app/Data/PaymentRequest.php`
- Create: `app/Data/PaymentResult.php`
- Create: `app/Services/Payments/SimulatedPaymentGateway.php`
- Create: `app/Services/Booking/CreateMarketplaceBooking.php`
- Create: `app/Http/Requests/StoreMarketplaceBookingRequest.php`
- Create: `app/Http/Controllers/Guest/MarketplaceCheckoutController.php`
- Create: `resources/views/marketplace/checkout.blade.php`
- Create: `resources/views/guest/bookings/success.blade.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `config/services.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Booking/MarketplaceCheckoutTest.php`
- Test: `tests/Feature/Booking/MarketplaceCheckoutConcurrencyTest.php`

**Interfaces:**
- Produces: `PaymentGateway::charge(PaymentRequest): PaymentResult` and `CreateMarketplaceBooking::handle(User $guest, Property $property, array $data, string $idempotencyKey): Booking`.

- [ ] **Step 1: Write failing tests for backend price authority, successful confirmation, failed payment without blocked nights, duplicate idempotency key, duplicate provider result, capacity rejection, and unavailable dates.**
- [ ] **Step 2: Run focused tests and verify missing checkout behavior.**
- [ ] **Step 3: Bind `PaymentGateway` to `SimulatedPaymentGateway`, with deterministic test failure through an injected fake/fake control rather than request flags.**
- [ ] **Step 4: Implement the database transaction: eligible property, locks, quote, pending booking/payment, charge result, paid/confirmed transition, history, and allocated availability days.**
- [ ] **Step 5: Add checkout GET/POST routes behind auth and preserve request state for unauthenticated visitors. Never accept totals/status/business IDs from the request.**
- [ ] **Step 6: Render quote, apartment summary, guest counts, simulated-payment disclosure, validation conflicts, and success page.**
- [ ] **Step 7: Run the concurrency test on isolated MySQL and all focused booking tests.**
- [ ] **Step 8: Commit with `git commit -m "feat: confirm marketplace bookings with simulated payments"`.**

### Task 7: Cancellation/refund and real owner booking data

**Files:**
- Create: `app/Services/Booking/CancelMarketplaceBooking.php`
- Create: `app/Http/Controllers/Guest/GuestBookingCancellationController.php`
- Modify: `app/Http/Controllers/Owner/OwnerBookingsController.php`
- Modify: `resources/views/guest/bookings/show.blade.php`
- Modify: `resources/views/owner/bookings.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Booking/GuestCancellationTest.php`
- Test: `tests/Feature/Owner/OwnerMarketplaceBookingTest.php`

**Interfaces:**
- Produces: `CancelMarketplaceBooking::handle(User $guest, Booking $booking, CarbonImmutable $now): BookingCancellation` and real business-scoped owner booking presentation.

- [ ] **Step 1: Write failing tests for refundable >48-hour cancellation, non-refundable <=48-hour cancellation, idempotency, ownership denial, availability release, and cross-business owner isolation.**
- [ ] **Step 2: Verify failures against absent cancellation service and preview owner arrays.**
- [ ] **Step 3: Implement cancellation under transaction lock, using listing check-in time or 14:00 Africa/Lagos, recording refund payment only when eligible.**
- [ ] **Step 4: Release booking-owned availability days without deleting history and append status history.**
- [ ] **Step 5: Replace owner preview data with active-business-scoped booking queries and calculated KPIs; update Blade to consume model/view-model data.**
- [ ] **Step 6: Run guest cancellation, owner isolation, finance, and booking page tests.**
- [ ] **Step 7: Commit with `git commit -m "feat: manage guest cancellations and owner bookings"`.**

### Task 8: Acceptance journey, migration safety, and release verification

**Files:**
- Create: `tests/Feature/Marketplace/ServicedApartmentBookingJourneyTest.php`
- Modify: migration files only if clean MySQL validation exposes explicit identifier/dependency defects
- Modify: `docs/superpowers/plans/2026-09-08-serviced-apartment-marketplace-booking.md` to check completed steps

**Interfaces:**
- Consumes: all prior routes and services.
- Produces: executable acceptance evidence for the full guest-to-owner journey.

- [ ] **Step 1: Write the acceptance test that seeds inventory, registers a guest, filters listings, opens detail, quotes dates, pays, confirms the guest view, confirms correct owner visibility, and denies the other owner.**
- [ ] **Step 2: Run the acceptance test on SQLite and repair only product defects it exposes.**
- [ ] **Step 3: Create a new isolated MySQL database, run the full migration chain, seed the ten apartments twice, and assert stable row totals. Do not reset local/business databases.**
- [ ] **Step 4: Run transactional checkout and overlap smoke tests on MySQL.**
- [ ] **Step 5: Run `php artisan test`, `npm run build`, `php artisan view:cache`, `php artisan route:list`, and `git diff --check`.**
- [ ] **Step 6: Manually verify desktop/mobile marketplace filters, detail, checkout, confirmation, guest bookings, and owner bookings using browser tooling when available; otherwise use Playwright and record that fallback.**
- [ ] **Step 7: Confirm no static sample booking/property arrays remain in active marketplace, guest, or owner booking pages.**
- [ ] **Step 8: Commit with `git commit -m "test: verify serviced apartment booking journey"`.**

## Completion Criteria

- Exactly ten repeatably seeded serviced apartments belong five each to two businesses.
- Guest signup, login return, search, all filters, details, dates, quote, simulated payment, confirmation, dashboard, and cancellation operate on persisted backend records.
- The correct owner sees a direct booking; the other owner cannot access it.
- Concurrent requests cannot double-book an apartment night.
- Clean MySQL migrations and full automated/build verification pass.
