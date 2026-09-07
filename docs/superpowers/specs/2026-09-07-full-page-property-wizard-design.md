# Backend-Powered Full-Page Property Wizard Design

## Objective

Replace the disconnected backend property setup interface with the existing full-page wizard UI at `/owner/properties/create/step1` through review and success. Each step must validate and persist real data, support draft recovery, and submit a complete property for marketplace verification.

The implementation preserves the project-wide migrated domain schema. Existing models, services, lifecycle states, and tables remain authoritative. Schema changes are additive and limited to fields that have no correct existing storage location.

## Scope

### Included

- Backend-power the brand-new-property path across all ten full-page steps.
- Create a draft on Step 1 and use property-scoped URLs thereafter.
- Persist, validate, revisit, skip where allowed, exit, and resume the wizard.
- Render the final review from database records.
- Submit the property for marketplace verification and show a guarded success screen.
- Retire the old `/owner/properties/create/wizard` UI by redirecting it into the new flow.
- Apply owner authentication, active-business context, and owner authorization middleware.
- Change `/owner/finance` from `#F4F4F4` to the established `#ECECEC` workspace background.

### Deferred

- “Another flat in a property” and parent-property/unit modelling.
- Real Airbnb, Booking.com, or WhatsApp authorization and synchronization.
- Google Places/geocoding and interactive map pinning.

Deferred controls must not claim to work. “Another flat” is disabled and labelled “Coming soon.” Channel records display “Connection pending.” The map displays an honest pending-verification state.

## Routes and Draft Lifecycle

`GET /owner/properties/create/step1` displays the property-kind screen. Submitting the enabled brand-new choice creates one minimal property draft for the active business and initializes its setup progress. It then redirects to:

`GET /owner/properties/{property}/create/step2`

Steps 2 through 10 use property-scoped GET and mutation routes. Each mutation validates, saves transactionally, marks its workflow step complete, and redirects to the next property-scoped GET route. Back and Edit links address the same draft. Save and exit returns to the property index without changing draft status.

Resume resolves the first required incomplete step, or the first optional step that has neither been completed nor explicitly skipped. Direct access to an impermissible future step redirects to the correct unfinished step. Completed earlier steps remain editable.

The existing route names used by dashboard and property-list CTAs continue to work. `/owner/properties/create/wizard` redirects to Step 1. Old unscoped Step 2–success URLs do not operate as independent fake flows; they redirect safely to Step 1 or require a scoped draft.

Draft creation is idempotent per submission. The initial draft may use an internal temporary display name and code because the public name is collected at Step 8. Temporary values are never presented as submitted listing data.

## Step-to-Domain Mapping

### Step 1: Property choice

- Accept only `brand_new`.
- Create a `properties` draft under the active business.
- Initialize ten setup-step records.
- Disable “Another flat in a property” and label it “Coming soon.”

### Step 2: Location

- Persist state, area/city, and street/estate in `properties.address`.
- Persist optional latitude and longitude on `properties`.
- Use an existing employee/property staff assignment if a manager is selected; do not store a display name when a real relationship is available.
- Live address search and map verification are deferred and represented honestly in the UI.

### Step 3: Property basics

- Persist property type, booking mode (`entire`, `private`, or `shared`), capacity, bedrooms, bathrooms, and optional floor area.
- Expand validation to the six types exposed by the UI: apartment, self-contained, duplex, bungalow, boutique suite, and villa.
- Add dedicated nullable/required property columns for booking mode and floor area because neither belongs in address or generic metadata.

### Step 4: Amenities

- Load active amenities from `amenities`, grouped according to the existing UI.
- Persist selections through `property_amenities` using the existing sync service.
- Seed any missing canonical amenity codes through a seeder, not runtime creation.

### Step 5: Photos and video

- Persist uploads in `property_media` through the existing media service.
- Enforce supported MIME types and size limits.
- Require at least one image before advancing.
- Support deletion, ordering, and primary-image selection with ownership checks.
- Store a video upload or external video URL using the existing media type and columns.

### Step 6: Assets

- Map preset and custom items into the existing asset/property assignment domain.
- Synchronize selected items without duplicating records on repeated submission.
- Preserve user-entered custom names.
- Treat this step as optional and explicitly skippable.

### Step 7: Documents

- Store optional uploads through the existing document domain and private storage.
- Enforce MIME, size, and ownership validation.
- Support removal before submission.
- Treat this step as optional and explicitly skippable.

### Step 8: Name and price

- Replace the temporary name with the validated property name.
- Persist description, default nightly price, and business currency on `properties`.
- Represent configured longer-stay discounts using existing property pricing/promotion records where their semantics match.
- Disable the multiple-bookable-rooms control with the deferred unit functionality; do not add a misleading boolean without the associated unit model.

### Step 9: Booking channels

- Add a property-level channel connection table because `booking_channel_links` belongs to individual bookings and must not be repurposed.
- Store provider, supplied external identifier/URL/phone, `pending` connection state, metadata, audit fields, and timestamps.
- Upsert by property and provider so retries do not duplicate requests.
- Skipping the optional step records workflow intent without creating channel rows.

### Step 10: Review and submission

- Render all sections from persisted property relations; remove hard-coded sample values.
- Link each Edit control to its property-scoped step.
- Run setup and marketplace readiness checks.
- In one transaction, mark setup complete, set property publication and verification statuses to `pending`, record `verification_submitted_at`, and create one lifecycle event.
- Make submission idempotent so repeated requests cannot create duplicate events or requests.

### Success

- Require a property identifier and load a property owned by the active business.
- Display only for a property whose verification submission is pending or later.
- Render the real property name and status.
- “Add another property” returns to Step 1.

## Workflow State

Update `PropertySetupWorkflow` to represent the full-page sequence:

1. property-kind
2. location
3. basics
4. amenities
5. media
6. assets
7. documents
8. name-price
9. channels
10. review

Required steps are property-kind, location, basics, media, name-price, and review. Amenities may be empty but must be deliberately submitted. Assets, documents, and channels are skippable. Existing draft setup rows are normalized by an additive migration or compatibility routine without rewriting historical migrations.

## Schema Changes

Create one additive migration that:

- Adds `booking_mode` to `properties`.
- Adds nullable `floor_area_sqm` to `properties`.
- Creates property channel connections with composite business/property integrity, provider uniqueness per property, connection state, external reference, metadata, audit fields, timestamps, and soft deletion where consistent with the domain.
- Normalizes setup-step keys only when needed for existing drafts.

No existing migration is edited. No duplicate property, media, amenity, asset, document, pricing, or lifecycle tables are introduced.

## Controllers, Requests, and Services

- Use a focused full-page wizard controller for rendering and transitions.
- Use dedicated Form Request classes per step rather than a single oversized validator.
- Reuse `CreatePropertyService`, amenity sync, media storage, setup save, readiness, and lifecycle services after adapting their interfaces where required.
- Keep business scoping in one draft resolver used by every route.
- Keep persistence logic out of Blade and Alpine code.
- Alpine may manage local interaction state, but server-rendered saved values are the source of truth.

## Authorization and Security

All owner workspace routes use `auth`, `business.context`, and `business.owner`. Draft lookup is constrained by active business, draft/submission state, and route property ID. Cross-business UUID access returns 404 or 403 consistently.

Uploads use server-side MIME and size validation, non-public document storage, generated storage paths, and ownership checks for deletion. User-provided channel identifiers are treated as data, never executable URLs. CSRF protection applies to every mutation.

The fallback first-business context must not authorize production owner routes. Tests verify that unauthenticated users and unrelated business members cannot read or mutate wizard drafts.

## Error Handling and Recovery

- Validation failures redirect back with errors and old input.
- Saved database values repopulate revisited pages.
- Transactions protect multi-record saves and final submission.
- Upload failures leave the prior saved state intact.
- Duplicate submissions and browser refreshes are idempotent.
- Optional-step skips are explicit workflow events and can be reversed by later completion.
- Missing or invalid drafts fail safely rather than falling back to sample data.

## Finance Background Consistency

Change only the `/owner/finance` page shell background from `bg-[#F4F4F4]` to `bg-[#ECECEC]`, matching the established Properties and Calendar workspace background. Finance cards, charts, and component styling remain unchanged.

## Testing

Feature tests cover:

- Authenticated draft creation from Step 1.
- Property-scoped routing and active-business ownership.
- Validation and persistence for each step.
- Saved-value repopulation, Back/Edit links, Save and exit, and resume.
- Required-step enforcement and optional-step skipping.
- Media/document upload, removal, storage, and ownership.
- Amenity and asset synchronization without duplicates.
- Pending channel upserts without fake external connection claims.
- Dynamic review output.
- Successful readiness transition to pending verification.
- Idempotent final submission and lifecycle events.
- Guarded success page.
- Unauthenticated and cross-business rejection.
- Redirect behavior for retired legacy routes.
- Finance background regression.

Unit tests cover workflow ordering, required/skippable rules, resume selection, and readiness mapping. The full relevant PHP test suite, frontend production build, and diff checks must pass before completion.

## Acceptance Criteria

- No Step 1–success page advances solely through a static link when data must be saved.
- Refreshing, leaving, resuming, going back, or editing does not lose persisted information.
- Final review contains no hard-coded property sample data.
- Final submission creates no duplicates and results in pending marketplace verification.
- Existing migrated domain tables remain authoritative.
- Deferred integrations are visibly honest and cannot enter false connected states.
- The old backend wizard UI is no longer a competing user flow.
- Finance uses the same `#ECECEC` page background as the referenced owner workspace pages.
