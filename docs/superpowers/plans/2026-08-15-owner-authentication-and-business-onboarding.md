# Property Owner Authentication and Business Onboarding Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add real Breeze-based authentication and a transaction-safe business onboarding flow that unlocks protected property-owner routes without changing the existing Project Nexus schema architecture.

**Architecture:** Laravel Breeze provides the Blade authentication scaffold, but the existing UUID `users`, RBAC, memberships, business context, onboarding, and soft-deletion design remains authoritative. Focused actions/services own registration and business creation; middleware resolves owner context; controllers render business-scoped Blade screens; route names replace hard-coded static URLs.

**Tech Stack:** PHP 8.3, Laravel Framework 13.24, Laravel Breeze Blade stack, Blade, Alpine.js, Tailwind CSS 4, Vite 8, PHPUnit 12, MySQL verification.

## Global Constraints

- Do not create or retain duplicate migrations for `users`, `password_reset_tokens`, or `sessions`.
- Preserve all existing uncommitted user work, including the landing page, owner dashboard, property wizard, migrations, models, and documentation.
- Use the existing UUID Eloquent models and enum casts.
- Registration creates a global guest role assignment but does not create a business.
- Business onboarding creates the business, active membership, owner role assignment, active context, and onboarding steps atomically.
- Basic business onboarding unlocks draft property creation while the business remains `in_progress` and `unverified`.
- Business verification blocks property publication, not draft creation or editing.
- Do not expose Breeze's hard user deletion behavior.
- Use Manrope only for the public landing/auth surface; do not redesign the owner dashboard typography in this slice.
- Use named routes and URL generation instead of new hard-coded owner URLs.
- Do not commit, merge, or discard files unless the user separately authorizes repository-history changes; the worktree already contains extensive uncommitted work.

---

## File map

### Package/scaffold files

- Modify `composer.json` and `composer.lock`: install `laravel/breeze` as a development dependency.
- Modify `package.json` and lockfile only if Breeze adds required frontend packages not already available.
- Create `routes/auth.php`: Breeze authentication routes.
- Modify `routes/web.php`: public, auth include, owner route group, and legacy redirects.
- Modify `bootstrap/app.php`: owner middleware aliases.

### Authentication domain

- Modify `app/Models/User.php`: implement `MustVerifyEmail`; add role/context query helpers.
- Create `app/Services/Auth/RegisterUserService.php`: atomic user plus guest-role creation.
- Create or adapt `app/Http/Controllers/Auth/RegisteredUserController.php`: delegate registration to the service.
- Create or adapt `app/Http/Requests/Auth/RegisterUserRequest.php`: Project Nexus registration validation.
- Modify Breeze `app/Http/Requests/Auth/LoginRequest.php`: user lifecycle/lock checks and counters.
- Keep/adapt the Breeze authentication controllers for login, logout, reset, verification, and password confirmation.
- Create `app/Support/PasswordPolicyRules.php`: reusable validation rules sourced from the seeded platform policy with secure fallback.

### Owner context and onboarding

- Create `app/Services/Business/BusinessOnboardingService.php`: atomic business-owner aggregate creation.
- Create `app/Http/Requests/Owner/StoreBusinessOnboardingRequest.php`: normalized business validation.
- Create `app/Http/Middleware/EnsureActiveBusinessContext.php`: validate and bind tenant context.
- Create `app/Http/Middleware/EnsureBusinessOwner.php`: require the active `business_owner` assignment.
- Create `app/Support/ActiveBusinessContext.php`: immutable request-scoped context object.
- Create `app/Http/Controllers/Owner/OwnerEntryController.php`.
- Create `app/Http/Controllers/Owner/BusinessOnboardingController.php`.
- Create `app/Http/Controllers/Owner/OwnerDashboardController.php`.
- Create `app/Http/Controllers/Owner/OwnerPropertyController.php`.
- Create `app/Http/Controllers/Owner/OwnerCalendarController.php`.
- Create `app/Http/Controllers/Owner/OwnerFinanceController.php`.
- Create `app/Http/Controllers/Owner/OwnerOperationsController.php`.
- Create `app/Policies/BusinessPolicy.php` and `app/Policies/PropertyPolicy.php`.

### Views/frontend

- Modify `resources/views/layouts/app.blade.php`: public Manrope font and auth error/modal state.
- Preserve/adapt Breeze guest/auth layouts rather than replacing the public landing layout.
- Modify `resources/css/app.css`: public font token and scoped landing typography.
- Modify `resources/views/components/auth/signup-modal.blade.php`: real registration POST.
- Modify `resources/views/components/auth/login-modal.blade.php`: real login POST.
- Modify `resources/views/components/navbar.blade.php`, `host-cta.blade.php`, and `final-cta.blade.php`: named auth/owner entry links.
- Create `resources/views/owner/onboarding/business.blade.php`.
- Modify `resources/views/layouts/dashboard.blade.php`: authenticated owner context/navigation support.
- Modify/adapt `resources/views/dashboard.blade.php`, `properties.blade.php`, and `calendar.blade.php` for controller-provided data and named routes.
- Create owner finance/operations placeholder workspace views only if no existing view is available; they must use the dashboard layout and clearly identify their current capability rather than faking data.

### Tests

- Create `tests/Feature/Auth/RegistrationTest.php`.
- Create/adapt Breeze auth tests for login, logout, password reset, and email verification.
- Create `tests/Feature/Owner/OwnerEntryTest.php`.
- Create `tests/Feature/Owner/BusinessOnboardingTest.php`.
- Create `tests/Feature/Owner/OwnerAuthorizationTest.php`.
- Modify `tests/TestCase.php` only if deterministic seeding helpers are needed.

---

### Task 1: Install Breeze safely and audit generated changes

**Files:**
- Modify: `composer.json`
- Modify: `composer.lock`
- Potentially modify: `package.json`, package lockfile
- Create/adapt: Breeze-generated authentication controllers, requests, routes, views, and components
- Preserve: all current migrations and frontend pages

**Interfaces:**
- Consumes: Laravel 13.24 application, existing Blade/Tailwind/Alpine frontend.
- Produces: Breeze Blade authentication scaffold available for domain adaptation.

- [ ] **Step 1: Capture the pre-install file state**

Run:

```bash
git status --short
git diff -- routes/web.php resources/css/app.css resources/js/app.js resources/views/layouts/app.blade.php
```

Record which files already contain user work. Do not restore, reset, or overwrite them.

- [ ] **Step 2: Install the compatible Breeze package**

Run:

```bash
composer require laravel/breeze --dev
```

Expected: Composer selects a Breeze release whose `illuminate/support` constraint includes `^13.0`.

- [ ] **Step 3: Generate the Blade scaffold**

Run:

```bash
php artisan breeze:install blade
```

Do not run migrations yet. Do not accept a duplicate business migration.

- [ ] **Step 4: Audit every generated or overwritten file**

Run:

```bash
git status --short
git diff --stat
git diff -- routes/web.php resources/css/app.css resources/js/app.js resources/views/layouts/app.blade.php package.json composer.json
```

Classify changes as authentication scaffold, required dependency, or accidental overwrite. Reconcile accidental overwrites with the pre-install content using `apply_patch`.

- [ ] **Step 5: Verify migration authority**

Run:

```bash
rg -n "Schema::create\('(users|password_reset_tokens|sessions)'" database/migrations
```

Expected: exactly one active definition for each table, all in `2026_08_07_000300_create_users_memberships_and_roles_tables.php`.

### Task 2: Align the User model and password policy with Breeze

**Files:**
- Modify: `app/Models/User.php`
- Create: `app/Support/PasswordPolicyRules.php`
- Test: `tests/Unit/Support/PasswordPolicyRulesTest.php`

**Interfaces:**
- Consumes: `PasswordPolicy` model and seeded `platform-default` policy.
- Produces: `PasswordPolicyRules::rules(): array` and email-verifiable UUID users.

- [ ] **Step 1: Write password-rule tests**

Test exact fallback behavior when no database policy exists and seeded behavior when it does:

```php
$rules = app(PasswordPolicyRules::class)->rules();

$validator = Validator::make(
    ['password' => 'SecureOwner12'],
    ['password' => $rules],
);

$this->assertFalse($validator->fails());
```

Also assert rejection of fewer than 12 characters, missing uppercase, missing lowercase, and missing number.

- [ ] **Step 2: Run the focused test and confirm failure**

Run:

```bash
php artisan test tests/Unit/Support/PasswordPolicyRulesTest.php
```

Expected: failure because `PasswordPolicyRules` does not exist.

- [ ] **Step 3: Implement the rules provider**

Create a final class with this public interface:

```php
final class PasswordPolicyRules
{
    /** @return array<int, mixed> */
    public function rules(): array;
}
```

Resolve the active `platform-default` policy and build Laravel `Password` rules. If the table is unavailable during early setup, use the secure 12-character uppercase/lowercase/number fallback.

- [ ] **Step 4: Make User email-verifiable**

Implement:

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
```

Retain `HasUuids`, enum casts, soft deletion, hidden password/token fields, and all existing relationships.

- [ ] **Step 5: Add role/context query helpers**

Add focused methods with these signatures:

```php
public function hasActiveGlobalRole(string $systemKey): bool;
public function hasActiveBusinessRole(string $systemKey, string $membershipId): bool;
public function resolvedBusinessContext(): ?UserBusinessContext;
```

They must filter inactive, revoked, expired, and soft-deleted relationships.

- [ ] **Step 6: Run focused tests**

Run:

```bash
php artisan test tests/Unit/Support/PasswordPolicyRulesTest.php
```

Expected: all tests pass.

### Task 3: Implement atomic registration and guest-role assignment

**Files:**
- Create: `app/Services/Auth/RegisterUserService.php`
- Create/adapt: `app/Http/Requests/Auth/RegisterUserRequest.php`
- Modify: `app/Http/Controllers/Auth/RegisteredUserController.php`
- Modify: `resources/views/auth/register.blade.php` if Breeze creates the dedicated page
- Test: `tests/Feature/Auth/RegistrationTest.php`

**Interfaces:**
- Consumes: validated registration data and seeded `roles.system_key = guest`.
- Produces: `RegisterUserService::register(array $attributes): User`.

- [ ] **Step 1: Write registration feature tests**

Cover:

```php
$response = $this->post(route('register'), [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone_number' => '+2348012345678',
    'password' => 'SecureOwner12',
    'password_confirmation' => 'SecureOwner12',
    'terms' => '1',
]);

$user = User::query()->where('email', 'john@example.com')->firstOrFail();
$this->assertTrue(Str::isUuid($user->id));
$this->assertTrue($user->hasActiveGlobalRole('guest'));
$response->assertRedirect(route('verification.notice', absolute: false));
```

Also test duplicate email, invalid password, missing terms, normalized phone storage, and full rollback when the guest role is absent.

- [ ] **Step 2: Run registration tests and confirm failure**

Run:

```bash
php artisan test tests/Feature/Auth/RegistrationTest.php
```

- [ ] **Step 3: Implement request validation**

`RegisterUserRequest` validates name, unique lowercase email, normalized phone, confirmed password using `PasswordPolicyRules`, and accepted terms. Its `validatedForRegistration(): array` method returns only User-fillable registration fields.

- [ ] **Step 4: Implement the registration transaction**

Create:

```php
final class RegisterUserService
{
    public function register(array $attributes): User;
}
```

Inside `DB::transaction`, resolve the active public guest role, create the User, then create `UserRole` with `status = active`, `assigned_at = now()`, and global scope derived by the existing model invariant. Throw a domain exception if the role is missing.

- [ ] **Step 5: Adapt Breeze's controller**

`RegisteredUserController::store(RegisterUserRequest $request, RegisterUserService $service)` calls the service, dispatches `Registered`, authenticates the user, regenerates the session, and redirects to the verification notice.

- [ ] **Step 6: Run registration tests**

Run:

```bash
php artisan test tests/Feature/Auth/RegistrationTest.php
```

Expected: all registration and rollback cases pass.

### Task 4: Implement lifecycle-aware login and standard auth flows

**Files:**
- Modify: `app/Http/Requests/Auth/LoginRequest.php`
- Modify/adapt: Breeze authentication controllers under `app/Http/Controllers/Auth/`
- Modify/adapt: authentication views under `resources/views/auth/`
- Test: Breeze-generated tests under `tests/Feature/Auth/`

**Interfaces:**
- Consumes: active User, `locked_until`, failed-attempt policy, and intended URL.
- Produces: secure login/logout/reset/verification flows with Project Nexus redirect behavior.

- [ ] **Step 1: Extend authentication tests**

Add assertions that inactive users and currently locked users cannot authenticate, successful login resets counters and sets `last_login_at`, failed authentication increments counters, and the threshold sets `locked_until`.

- [ ] **Step 2: Run auth tests and confirm the new cases fail**

Run:

```bash
php artisan test tests/Feature/Auth
```

- [ ] **Step 3: Adapt LoginRequest**

Keep Breeze throttling. Authenticate only active, non-deleted, non-locked users. Update counters transactionally enough to avoid negative or stale values. Do not reveal whether an email belongs to an inactive account.

- [ ] **Step 4: Implement destination selection**

After authentication:

```php
return redirect()->intended(
    $request->user()->resolvedBusinessContext()
        ? route('owner.dashboard', absolute: false)
        : route('home', absolute: false),
);
```

- [ ] **Step 5: Remove hard account deletion**

Do not register Breeze's profile destroy route. If `ProfileController::destroy` is generated, remove its route and do not expose a deletion form.

- [ ] **Step 6: Run auth tests**

Run:

```bash
php artisan test tests/Feature/Auth
```

Expected: login, logout, password reset, email verification, password confirmation, and lifecycle cases pass.

### Task 5: Connect public landing authentication and typography

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/css/app.css`
- Modify: `resources/views/components/auth/signup-modal.blade.php`
- Modify: `resources/views/components/auth/login-modal.blade.php`
- Modify: `resources/views/components/navbar.blade.php`
- Modify: `resources/views/components/host-cta.blade.php`
- Modify: `resources/views/components/final-cta.blade.php`
- Modify: `resources/views/welcome.blade.php`
- Test: `tests/Feature/PublicLandingAuthenticationTest.php`

**Interfaces:**
- Consumes: named Breeze routes and Alpine modal store.
- Produces: real public authentication forms and owner-entry calls to action.

- [ ] **Step 1: Write landing integration tests**

Assert `/` returns 200 and contains form actions for named login/register routes, CSRF fields, password confirmation, field-error rendering, and the named owner-entry URL. Assert the landing layout contains Manrope and no public inline `font-family:'Inter'` declarations.

- [ ] **Step 2: Run the landing test and confirm failure**

Run:

```bash
php artisan test tests/Feature/PublicLandingAuthenticationTest.php
```

- [ ] **Step 3: Scope Manrope to the public layout**

Load Manrope weights 400–800 in `layouts/app.blade.php`. Add a public root class such as `public-site` and CSS:

```css
.public-site {
    font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
}
```

Remove hard-coded Inter from the public auth modals. Do not change dashboard typography.

- [ ] **Step 4: Convert modal controls to real forms**

Use `method="POST"`, `action="{{ route('register') }}"` / `route('login')`, `@csrf`, semantic submit buttons, named inputs, `old(...)`, and `@error`. Add password confirmation and remember-me. Replace the fake dashboard JavaScript redirects.

- [ ] **Step 5: Handle modal reopening**

Expose a server-derived Alpine initial state based on validation error bag or a flashed `auth_modal` key so failed landing registration/login reopens the correct modal.

- [ ] **Step 6: Fix owner-entry CTAs**

All “List your property” and “Register your property” controls use `route('owner.entry')`. Ordinary sign-in/register controls use Breeze named routes or modal triggers.

- [ ] **Step 7: Run landing tests and build**

Run:

```bash
php artisan test tests/Feature/PublicLandingAuthenticationTest.php
npm run build
```

Expected: test passes and Vite build succeeds.

### Task 6: Implement atomic business onboarding

**Files:**
- Create: `app/Http/Requests/Owner/StoreBusinessOnboardingRequest.php`
- Create: `app/Services/Business/BusinessOnboardingService.php`
- Create: `app/Http/Controllers/Owner/BusinessOnboardingController.php`
- Create: `resources/views/owner/onboarding/business.blade.php`
- Test: `tests/Feature/Owner/BusinessOnboardingTest.php`

**Interfaces:**
- Consumes: authenticated verified User and validated business form data.
- Produces: `BusinessOnboardingService::onboardOwner(User $user, array $attributes): UserBusinessContext`.

- [ ] **Step 1: Write onboarding transaction tests**

Assert one submission creates:

```php
$this->assertDatabaseHas('businesses', [
    'name' => 'John Stays Limited',
    'onboarding_status' => 'in_progress',
    'verification_status' => 'unverified',
]);
$this->assertDatabaseHas('business_memberships', [
    'user_id' => $user->id,
    'status' => 'active',
]);
$this->assertDatabaseHas('user_business_contexts', [
    'user_id' => $user->id,
    'status' => 'active',
]);
```

Assert owner role assignment references the membership, `scope_key` equals the membership UUID, profile step is completed, remaining steps are pending, and no rows remain if `business_owner` is missing.

- [ ] **Step 2: Run onboarding tests and confirm failure**

Run:

```bash
php artisan test tests/Feature/Owner/BusinessOnboardingTest.php
```

- [ ] **Step 3: Implement request normalization**

Validate required business fields and unique `(country_code, registration_number)`. Normalize country/currency uppercase, email lowercase, trim contact fields, and serialize structured address/tax fields in the format expected by Business casts.

- [ ] **Step 4: Implement the service transaction**

Create:

```php
final class BusinessOnboardingService
{
    public function onboardOwner(User $user, array $attributes): UserBusinessContext;
}
```

The service creates Business, active membership, owner UserRole, active UserBusinessContext, and the four onboarding steps. Use existing enums, model UUID generation, `joined_at`, `assigned_at`, `onboarding_started_at`, and audit actors. Return a loaded context only after transaction success.

- [ ] **Step 5: Implement controller and view**

`create()` prefills contact fields from the User. `store()` authorizes, delegates to the service, flashes success, and redirects to `owner.dashboard`. The Blade form uses the dashboard visual language and displays verification/publishing expectations clearly.

- [ ] **Step 6: Run onboarding tests**

Run:

```bash
php artisan test tests/Feature/Owner/BusinessOnboardingTest.php
```

Expected: transaction, uniqueness, verification-state, and rollback cases pass.

### Task 7: Resolve and authorize active owner context

**Files:**
- Create: `app/Support/ActiveBusinessContext.php`
- Create: `app/Http/Middleware/EnsureActiveBusinessContext.php`
- Create: `app/Http/Middleware/EnsureBusinessOwner.php`
- Modify: `bootstrap/app.php`
- Create: `app/Policies/BusinessPolicy.php`
- Create: `app/Policies/PropertyPolicy.php`
- Test: `tests/Feature/Owner/OwnerAuthorizationTest.php`

**Interfaces:**
- Produces: request-scoped `ActiveBusinessContext` with `User`, `Business`, `BusinessMembership`, and `UserRole`.
- Middleware aliases: `business.context`, `business.owner`.

- [ ] **Step 1: Write middleware authorization tests**

Cover unauthenticated redirect, unverified redirect, no-context redirect to onboarding, inactive membership rejection, revoked/expired owner role rejection, mismatched context rejection, and valid owner access.

- [ ] **Step 2: Run the tests and confirm failure**

Run:

```bash
php artisan test tests/Feature/Owner/OwnerAuthorizationTest.php
```

- [ ] **Step 3: Implement the context value object**

Use readonly promoted properties:

```php
final readonly class ActiveBusinessContext
{
    public function __construct(
        public User $user,
        public Business $business,
        public BusinessMembership $membership,
        public UserRole $roleAssignment,
    ) {}
}
```

- [ ] **Step 4: Implement context middleware**

Resolve the user's single context with business, membership, active role assignment, and role. Confirm all UUID relationships agree and lifecycle states are active. Bind `ActiveBusinessContext::class` into the request container for downstream dependency injection.

- [ ] **Step 5: Implement owner middleware**

Require related role `system_key = business_owner`, active assignment, no expiry/revocation, and matching membership. Return 403 for role denial; redirect to onboarding only when no context exists.

- [ ] **Step 6: Register aliases**

In `bootstrap/app.php`:

```php
$middleware->alias([
    'business.context' => EnsureActiveBusinessContext::class,
    'business.owner' => EnsureBusinessOwner::class,
]);
```

- [ ] **Step 7: Implement policies**

`BusinessPolicy::view(User $user, Business $business)` and `PropertyPolicy::view/update/create` must use resolved active context and compare tenant UUIDs. Draft creation is allowed for `in_progress`/`unverified` businesses; publication is not.

- [ ] **Step 8: Run authorization tests**

Run:

```bash
php artisan test tests/Feature/Owner/OwnerAuthorizationTest.php
```

Expected: all tenant and role boundary cases pass.

### Task 8: Replace static routes with protected owner controllers

**Files:**
- Modify: `routes/web.php`
- Create/adapt: `routes/auth.php`
- Create: owner controllers listed in the file map
- Modify: `resources/views/layouts/dashboard.blade.php`
- Modify: `resources/views/dashboard.blade.php`
- Modify: `resources/views/properties.blade.php`
- Modify: `resources/views/calendar.blade.php`
- Create: `resources/views/owner/finance/index.blade.php`
- Create: `resources/views/owner/operations/index.blade.php`
- Test: `tests/Feature/Owner/OwnerEntryTest.php`

**Interfaces:**
- Produces named `/owner` route family and legacy redirects.

- [ ] **Step 1: Write owner-entry routing tests**

Test guest, unverified, verified-without-business, and active-owner redirects. Assert route names and exact `/owner` prefixes. Assert legacy `/dashboard`, `/properties`, `/calendar`, and property-add URLs redirect rather than render independent pages.

- [ ] **Step 2: Run routing tests and confirm failure**

Run:

```bash
php artisan test tests/Feature/Owner/OwnerEntryTest.php
```

- [ ] **Step 3: Implement the named routes**

Use:

```php
Route::get('/', WelcomeController::class)->name('home');

Route::middleware('auth')->prefix('owner')->name('owner.')->group(function () {
    Route::get('/', OwnerEntryController::class)->name('entry');

    Route::middleware('verified')->group(function () {
        Route::get('onboarding/business', [BusinessOnboardingController::class, 'create'])
            ->name('onboarding.business.create');
        Route::post('onboarding/business', [BusinessOnboardingController::class, 'store'])
            ->name('onboarding.business.store');

        Route::middleware(['business.context', 'business.owner'])->group(function () {
            Route::get('dashboard', OwnerDashboardController::class)->name('dashboard');
            Route::resource('properties', OwnerPropertyController::class)->only(['index', 'create']);
            Route::get('calendar', OwnerCalendarController::class)->name('calendar.index');
            Route::get('finance', OwnerFinanceController::class)->name('finance.index');
            Route::get('operations', OwnerOperationsController::class)->name('operations.index');
        });
    });
});
```

If the current root view requires no controller data, `Route::view('/', 'welcome')->name('home')` is acceptable; do not introduce an empty controller solely for style.

- [ ] **Step 4: Implement controllers**

Controllers resolve `ActiveBusinessContext` from the container and query only the active business. Dashboard counts and property lists must be real database queries, not hard-coded tenant data. Finance/operations pages may show honest empty states.

- [ ] **Step 5: Update dashboard navigation**

Use named routes and active-route states. Display active business name and authenticated user. Do not expose links the current owner context cannot authorize.

- [ ] **Step 6: Implement legacy redirects**

Legacy routes redirect through `owner.entry` or their named owner equivalent. Property wizard URLs can temporarily redirect to the protected owner create entry until the persistence slice assigns final step route names.

- [ ] **Step 7: Run routing tests and inspect routes**

Run:

```bash
php artisan test tests/Feature/Owner/OwnerEntryTest.php
php artisan route:list --path=owner
php artisan route:list --path=login
php artisan route:list --path=register
```

Expected: no duplicate auth routes and all owner workspace routes have the intended middleware.

### Task 9: Full verification and visual QA

**Files:**
- Verify all files touched in Tasks 1–8.

**Interfaces:**
- Produces evidence that the complete vertical slice works and preserves the existing schema/UI foundation.

- [ ] **Step 1: Format changed PHP files**

Run Pint only against files touched by this slice; do not format unrelated dirty files.

- [ ] **Step 2: Run the full PHP test suite**

Run:

```bash
composer test
```

Expected: zero failing tests.

- [ ] **Step 3: Verify a fresh dedicated MySQL test database**

Run only against a separately configured MySQL test database. Never run `migrate:fresh` against the development database:

```bash
APP_ENV=testing DB_CONNECTION=mysql DB_DATABASE=project_nexa_26_test php artisan migrate:fresh --seed --force
APP_ENV=testing DB_CONNECTION=mysql DB_DATABASE=project_nexa_26_test php artisan test
```

Expected: all active migrations, access-control seeds, workflow seeds, and tests succeed without a duplicate Breeze user/session/reset migration.

- [ ] **Step 4: Build frontend assets**

Run:

```bash
npm run build
```

Expected: Vite production build succeeds.

- [ ] **Step 5: Verify the real browser workflow**

Check desktop and mobile:

```text
/ → register → verification notice → verify → owner entry
→ business onboarding → owner dashboard → properties
→ logout → login → owner dashboard
→ forgot password → reset password
```

Confirm landing typography, modal validation, focus states, responsive layout, dashboard route protection, business name display, and honest empty states. Browser integration is preferred; if unavailable, use Playwright and record that fallback.

- [ ] **Step 6: Verify repository hygiene**

Run:

```bash
git diff --check
git status --short
```

Confirm no unrelated user change was reset, no duplicate migration exists, no temporary browser artifact remains, and no fake social-login redirect remains.
