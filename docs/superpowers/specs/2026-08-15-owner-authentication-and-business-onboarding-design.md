# Property Owner Authentication and Business Onboarding Design

## Purpose

This specification defines the first real property-owner vertical slice for Project Nexus. It replaces static authentication redirects and owner route closures with Laravel Breeze authentication, guest-role assignment, email verification, business onboarding, active business context, protected owner routes, and a cleaner public landing-page typeface.

The existing database migrations remain authoritative. Breeze supplies authentication scaffolding and must be adapted to the existing UUID user, RBAC, membership, onboarding, audit, and soft-deletion design.

## Scope

This slice includes:

- Install Laravel Breeze as a development dependency using the Blade stack.
- Preserve and integrate the current Blade/Tailwind/Alpine frontend.
- Implement registration, login, logout, password reset, and email verification.
- Connect the existing landing-page login and signup modals to real authentication endpoints.
- Assign every new user the seeded `guest` role.
- Add the property-owner business onboarding screen and persistence flow.
- Create the business, active owner membership, owner role, active business context, and onboarding records in one transaction.
- Protect and group owner routes under `/owner`.
- Convert the current owner dashboard and properties entry points from route closures to controllers.
- Change only the public landing-page typography from Inter/Barlow to Manrope.
- Establish policy and middleware boundaries required by the later property-persistence slice.

This slice does not yet persist the existing nine-step property wizard. That is the next vertical slice after authentication and business context are stable.

## Existing schema authority

No Breeze migration may replace or duplicate the following existing tables:

- `users`
- `password_reset_tokens`
- `sessions`
- `roles`
- `user_roles`
- `businesses`
- `business_memberships`
- `user_business_contexts`
- `business_onboarding_steps`

User UUIDs remain generated through the existing Eloquent `HasUuids` behavior. Registration must use existing fields and defaults, including `status`, `identity_verification_status`, `preferred_language`, `timezone`, security counters, timestamps, and soft deletion.

The User model will implement Laravel's email-verification contract. Breeze account hard deletion will not be exposed; future account closure must use the platform's soft-deletion and privacy-governance process.

## Public landing typography

The public layout will load Manrope from Google Fonts and set Manrope as its landing-page sans family. Existing hard-coded Inter declarations inside public authentication modals will be removed or changed to inherit the public font.

The owner dashboard retains its current restrained interface typography for now. This avoids unintentionally restyling every existing operational screen while satisfying the landing-page request.

## Authentication behavior

### Registration

The existing landing signup modal posts to Breeze's named `register` route with:

- Full name
- Email
- Phone number
- Password
- Password confirmation
- Terms acceptance
- Preferred timezone inferred from the application default or request context

The country-code selector and local phone input will submit one normalized `phone_number` value. Social-provider buttons remain visually available only if clearly marked unavailable or disabled; they must not impersonate working Google, Apple, or Facebook authentication in this slice.

Registration validation uses a dedicated request or registration action aligned with the existing schema. Password rules initially follow the seeded platform-default password policy: minimum 12 characters, uppercase, lowercase, and number; symbol is optional.

Registration executes atomically:

1. Create the UUID user.
2. Resolve the seeded `guest` role by `system_key`.
3. Create an active global `user_roles` assignment for that role.
4. Record assignment timestamps and self/system audit actors where possible.
5. Dispatch Laravel's registered event.
6. Authenticate the new user.
7. Redirect to the email verification notice.

If the guest role is missing, registration must fail without leaving a partially created user.

### Login

The landing login modal posts to Breeze's named `login` route and supports remember-me. Login rejects users whose broad lifecycle status is not active or whose `locked_until` time is still in the future.

Successful login updates `last_login_at`, resets `failed_login_attempts`, and follows the intended destination when present. Otherwise:

- A user with an active owner business context goes to `owner.dashboard`.
- A user without an owner context returns to the public landing page.

Failed-login counter and lockout behavior will respect the platform-default password policy. Breeze rate limiting remains active as an additional request-level control.

### Password and verification

Breeze provides password reset and email verification using the existing reset-token and user verification columns. Authentication views will be restyled to align with the landing page instead of shipping Breeze's generic visual identity unchanged.

## Owner entry behavior

Every public “List your property” or “Register your property” action targets an owner-entry route.

The route resolves state as follows:

1. Guest users are redirected to login/registration with the owner-entry destination preserved.
2. Authenticated but unverified users are redirected to email verification.
3. Verified users without an active owner business are redirected to `owner.onboarding.business.create`.
4. Verified users with an active owner business context are redirected to `owner.properties.create`.

Basic registration never creates a business or owner membership automatically.

## Business onboarding form

Route: `GET /owner/onboarding/business`

The screen follows the visual language of the existing dashboard and property wizard. It collects the minimum business profile required by the migration:

Required:

- Business name
- Country
- Business type
- Timezone
- Currency
- Primary contact name
- Contact email
- Phone number

Optional:

- Registration number
- Address details
- Tax information
- Website URL

The authenticated user's name, email, phone, and timezone prefill compatible fields. The form uses a dedicated Form Request and preserves validation errors and old input.

## Business onboarding transaction

A focused `BusinessOnboardingService` performs the following in one database transaction:

1. Create the business with UUID identity, `registered` onboarding state, `unverified` verification state, and active lifecycle status.
2. Create an active `business_memberships` row for the authenticated user with `joined_at` set immediately.
3. Resolve the seeded `business_owner` role.
4. Create an active `user_roles` assignment connected to the membership. The existing `UserRole` model derives `scope_key` from the membership UUID, preserving its established business-role invariant.
5. Create or update the user's single `user_business_contexts` record with the business, membership, and active owner-role assignment.
6. Seed business onboarding steps:
   - `business_profile`: completed.
   - `business_verification`: pending.
   - `first_property`: pending.
   - `subscription_setup`: pending.
7. Mark the business onboarding state `in_progress` and set `onboarding_started_at`.
8. Create the relevant audit/domain event records where the existing services permit this without coupling the onboarding transaction to UI code.

If the owner role is missing or any relationship cannot be created, the entire transaction rolls back.

## Approved verification rule

Completing the basic business profile immediately unlocks:

- The owner dashboard.
- Draft property creation.
- Owner workspace navigation appropriate to an early-stage business.

The business remains `in_progress` and `unverified`. Verification is asynchronous. It blocks public property publication, not creation or editing of a property draft.

The `first_property` onboarding step is completed only after the later property-creation flow successfully persists its first property. `business_verification` and `subscription_setup` are completed by their corresponding future workflows.

## Owner routes

Owner routes use named routes under the `/owner` prefix:

```text
GET  /owner                         owner.entry
GET  /owner/onboarding/business     owner.onboarding.business.create
POST /owner/onboarding/business     owner.onboarding.business.store
GET  /owner/dashboard               owner.dashboard
GET  /owner/properties              owner.properties.index
GET  /owner/properties/create/...   owner.properties.create.*
GET  /owner/calendar                owner.calendar.index
GET  /owner/finance                 owner.finance.index
GET  /owner/operations              owner.operations.index
```

The onboarding routes require `auth` and `verified` but must allow a user who does not yet have a business context. Dashboard/workspace routes additionally require active business context and an active owner-capable role.

Legacy routes such as `/dashboard`, `/properties`, `/calendar`, and `/property/add/...` will redirect to their named `/owner` equivalents during transition rather than serving independent static screens.

## Controllers and services

Route closures will be replaced with focused controllers:

- `OwnerEntryController`: redirects based on authentication, verification, role, membership, and context.
- `BusinessOnboardingController`: displays and submits the business form.
- `OwnerDashboardController`: loads dashboard data for the active business.
- `OwnerPropertyController`: lists properties for the active business and becomes the resource boundary for the next slice.
- `OwnerCalendarController`, `OwnerFinanceController`, and `OwnerOperationsController`: protected workspace entry controllers, initially rendering existing screens where available.

Business creation belongs in `BusinessOnboardingService`, not the controller or Blade template. Registration guest-role assignment belongs in a focused registration action/service integrated with Breeze's generated controller.

## Middleware and authorization

Custom middleware responsibilities:

- `EnsureActiveBusinessContext`: resolve and validate the user's current business, membership, and active role assignment.
- `EnsureBusinessOwner`: require an active `business_owner` role for the resolved context.

The resolved business context is made available to controllers without trusting a business UUID supplied by the browser. Later property policies will require the property's `business_id` to match this resolved context.

Policies provide record-level authorization. Middleware provides broad route/context gates. Neither is replaced by hiding navigation links.

## Views and frontend integration

The current landing page remains `/`. Its Alpine modal store remains usable, but modal forms become ordinary POST forms with CSRF protection, named routes, error rendering, and old-input restoration.

Authentication validation failures rendered from `/login` or `/register` must reopen the corresponding landing modal when the request originated there, or render a dedicated responsive authentication page when accessed directly.

Owner pages use the shared dashboard layout. The standalone property-wizard shells will be consolidated during the next property persistence slice.

## Error handling

- Validation errors return to the form with field-level messages and preserved input.
- Missing seeded roles produce a controlled domain exception and transaction rollback.
- Duplicate registration email uses validation rather than exposing a database exception.
- Duplicate business registration number within a country uses validation and the existing database uniqueness constraint.
- Unauthorized tenant or role access returns HTTP 403; missing authentication redirects to login.
- Expired or inactive memberships/context redirect to owner onboarding or context selection rather than leaking another business's data.

## Testing and verification

This slice adds focused feature tests despite the earlier migration-only test deferral because authentication and tenant authorization are security-sensitive runtime behavior. Coverage includes:

- UUID user registration.
- Guest role assignment.
- Atomic rollback when the guest role is unavailable.
- Login and logout.
- Email-verification route protection.
- Owner-entry redirect states.
- Business onboarding transaction and created relationships.
- Duplicate business validation.
- Owner middleware rejects missing, inactive, or foreign business context.
- Existing public landing page remains accessible.
- Legacy owner routes redirect to `/owner` routes.

Verification also includes Pint, PHPUnit, `npm run build`, route inspection, fresh migration/seeding on disposable SQLite, and browser-level desktop/mobile checks when browser tooling is available. If the browser integration is unavailable, Playwright is the documented fallback.

## Next slice

After this specification is implemented and verified, the nine-step property wizard will be converted to a business-scoped persistent flow. It will save a property draft first, then incrementally persist address/location, capacity, amenities, media, assets, documents, price/listing information, and final verification submission through the existing property-related migrations.
