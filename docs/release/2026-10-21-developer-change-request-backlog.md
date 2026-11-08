# Verified Shortlet — Developer Change Request Backlog

**Source:** 18 October 2026 review meeting and confirmed written requirements  
**Status:** Current implementation backlog  
**Rule:** Preserve existing working flows unless a requirement below explicitly changes them.

## 1. Confirmed product rules

These decisions are explicit and should be treated as acceptance criteria:

- Public marketplace pages must not expose a property's exact address.
- Currency remains NGN. Country selection must not change currency, country code, or timezone; those remain configured platform values.
- The property type label must read **“A room in a flat”**, not “Another flat in a property”.
- “Things the Host Knows” is optional.
- A selected availability option makes the property available; an unselected option makes it unavailable and visually greyed out.
- AI and administrator-generated property titles must never contain the property's exact address.
- NIN must use normal field styling and must not receive unusual visual emphasis.
- Business owners can create and assign custom roles, while optionally starting from a global role template list.

## 2. Listing creation and editing

### 2.1 General interface

**LIST-01 — Password visibility control**  
Replace “Show/Hide” text controls with an accessible eye icon. The icon must have an accessible label and visible focus state.

**LIST-02 — Fixed regional settings**  
Keep NGN, country code and timezone fixed to the platform configuration. Country selection may update the displayed country, but must not silently alter these operational values.

**LIST-03 — Property-type wording**  
Replace “Another flat in a property” with “A room in a flat” everywhere: forms, validation, summaries, marketplace content and seed/demo data.

### 2.2 Wizard changes

**LIST-04 — Remove exact address from public marketplace**  
Hosts may provide the operational address privately, but marketplace cards, public detail pages, metadata, AI answers and public titles must use an area/location summary instead of the exact address. Authorized owner/admin views may retain the full address.

**LIST-05 — Reorder Step 8**  
Move Step 8 to immediately after property/flat type selection. Update step numbering, progress indicators, save/resume state and validation accordingly.

**LIST-06 — Availability behavior**  
When availability is selected, mark the property available. When it is not selected, mark it unavailable and grey out the relevant UI. Persist the state and enforce it in marketplace search and booking validation.

**LIST-07 — Step 9 review**  
Grey out Step 9 where the preceding state makes it unavailable. Audit its validation, save behavior, mobile layout and submission behavior; record and fix each reproducible defect.

**LIST-08 — Optional host information**  
Make “Things the Host Knows” optional. Draft save and submission must work when it is empty.

### 2.3 Media

**LIST-09 — Upload target**  
Both the “Click to Upload” label and its icon must activate the same file input and keyboard interaction.

**LIST-10 — Reorder and cover image**  
Allow hosts to drag/rearrange uploaded images and choose or change the cover image. Persist ordering and cover selection. Provide a keyboard/mobile fallback where drag-and-drop is unavailable.

## 3. Privacy, titles and location

**PRIV-01 — Address redaction boundary**  
Define one reusable public-location formatter and use it across marketplace cards, detail pages, search results, sharing previews, structured metadata, notifications and AI context.

**PRIV-02 — Title safety**  
Reject or rewrite generated titles containing the exact address. Add a regression test for AI-generated, admin-generated and manually entered public titles where applicable.

**PRIV-03 — Map privacy**  
Confirm the approved UX for hiding or fuzzing a property on the public map. Exact coordinates must remain owner/admin-only until this decision is implemented.

## 4. Calendar and channel synchronization

**CAL-01 — Two-way iCalendar synchronization**  
Implement import and export for connected channels. Native Verified Shortlet bookings and blocked dates must be exported; external events must be imported as unavailable dates. Define polling, retries, stale-feed handling, conflict rules, cancellation handling and privacy guarantees.

**CAL-02 — Manual blocks across channels**  
Manual/offline blocks must prevent local double-booking and be included in outbound calendar feeds.

**CAL-03 — Channel limitations**  
Document that iCalendar is availability synchronization, not full booking/payment synchronization. If Airbnb or another channel requires API integration for two-way booking updates, track that as a separate integration project.

## 5. Staff roles and permissions

**STAFF-01 — Manager task control**  
Managers can create maintenance/operational tasks and assign them to a Maintenance Manager, one staff member or multiple staff members.

**STAFF-02 — Manual date blocks**  
Authorized managers can block dates for offline/manual reservations with a reason, source and audit trail.

**STAFF-03 — Offline bookings**  
Property Managers can create an offline booking for a customer. The booking must block dates, carry the correct payment/status metadata and remain visible in operational calendars.

**STAFF-04 — Guest records for offline bookings**  
Allow creation or selection of a guest record when an offline booking requires one. Prevent duplicate guest records where an existing verified user matches.

**STAFF-05 — Role creation model**  
Do not force businesses to use pre-created roles. Provide “start from a global template” and “create custom role”; permissions must be editable, auditable and scoped by business/property.

**STAFF-06 — Permission-based visibility**  
Hide navigation items, pages, direct actions and sensitive guest information when the current role lacks permission. Return forbidden responses for direct unauthorized URLs.

## 6. UI/UX consistency

**UX-01 — Approved design source**  
Implement the UX designer's approved update once received. Do not introduce speculative redesigns before approval.

**UX-02 — Component consistency**  
Use shared components for buttons, inputs, icon inputs, selects, checkboxes, radios, upload controls, typography, spacing, content width and responsive navigation. Fix inconsistent sizing, font weight and repeated layouts across owner, guest, staff and admin surfaces.

**UX-03 — Icon controls**  
Use professional, consistent icons with accessible labels, tooltips where needed and consistent hover/focus/disabled states.

## 7. Implementation order

1. Privacy boundaries: exact-address removal, map visibility and title safety.
2. Listing wizard correctness: Step 8 reorder, availability state, Step 9 and optional host information.
3. Media upload and cover-image ordering.
4. Staff permissions, custom roles, task assignment and guest-data restrictions.
5. Manual blocks and offline booking workflow.
6. Two-way calendar/channel synchronization.
7. Shared UI component cleanup and approved UX changes.

## 8. Acceptance evidence required

For each item, record **Pass**, **Fail**, **Not Tested** or **Deferred**, with a screenshot/video and test account role.

- Exact address is absent from public marketplace pages and generated public content.
- NGN/country-code/timezone behavior remains fixed as configured.
- Wizard order and availability states persist across draft save, reload and submission.
- Upload label/icon, image ordering and cover selection work on desktop and mobile.
- Empty optional host-information fields do not block save or submission.
- Unauthorized staff cannot view guest records or hidden modules, including by direct URL.
- Managers can create/assign tasks, manual blocks and offline bookings with audit records.
- Offline bookings and manual blocks prevent overlapping bookings and synchronize through configured calendar feeds.
- Public titles and AI/admin output contain no exact address.
- Shared UI components produce consistent controls across all workspaces.

## 9. Open decisions before implementation

- Which country/location value is fixed for launch, and where may a host override it, if anywhere?
- Should public maps show an approximate area, neighborhood centroid or no marker?
- Is Airbnb integration limited to iCal, or is a channel API required for true two-way booking synchronization?
- What global role templates should be offered initially?
- What exact UX changes has the designer approved?

