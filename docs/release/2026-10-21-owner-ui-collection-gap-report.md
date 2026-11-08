# Property Owner UI Collection — Coverage and Alignment Report

**Reference collection:** `Property Owner Manage Screens-20261021T151623Z-1-001.zip`  
**Assessment date:** 21 October 2026  
**Purpose:** Compare the supplied owner UI collection with the current application before implementing the confirmed change backlog.

## Executive summary

The collection contains 28 screen PDFs covering operations, finance, reports, documents, marketplace, AI and settings. The application currently has working owner routes for the dashboard, properties, bookings, messages, calendar, finance, operations and team/access. Several collection areas have no corresponding owner route or view yet. Those should be treated as missing product surfaces, not merely styling tasks.

The collection should be used as a visual reference. It should not be implemented screen-by-screen blindly: first establish shared owner layout components, navigation, typography, content width, page headers, cards, tables, filters, forms, empty states and modal patterns.

## Coverage matrix

| Collection screen | Current application coverage | Assessment | Next action |
|---|---|---|---|
| Marketplace | Public marketplace exists; owner marketplace management route is not evident | Partial / unclear | Define owner view versus guest marketplace and add only the approved owner actions |
| Operations | `/owner/operations` and task actions exist | Implemented, needs visual alignment | Compare against collection; consolidate task cards, filters and assignment UI |
| Operations — Assign work | Task assignment route exists | Implemented, needs coverage review | Verify single and multi-staff assignment, availability and permission states |
| Operations — Add | Task creation route exists | Implemented, needs coverage review | Verify maintenance, cleaning, inspection and evidence flows |
| Finances | `/owner/finance` exists | Partial | Align finance summary, transaction table, filters and empty states |
| Expenses | No dedicated `/owner/expenses` route/view found | Missing | Define expense model, list, filters and create/edit flow |
| Expenses — Add | Finance has expense posting endpoint | Partial / hidden inside Finance | Decide whether a dedicated screen is required; match collection if approved |
| Owners Statement | No dedicated route/view found | Missing | Define statement period, export, owner balance and audit requirements |
| Profit and Loss Statement | No dedicated route/view found | Missing | Define accounting calculations and reporting period behavior |
| Financial Reports | No dedicated route/view found | Missing | Define report types, filters, export and permissions |
| Occupancy Reports | No dedicated route/view found | Missing | Define occupancy calculation, date range and property filters |
| Reports | No dedicated route/view found | Missing | Establish reports hub and link the approved report types |
| Build Reports | No dedicated route/view found | Missing | Decide whether custom report builder is in scope for this release |
| Tax and Compliance | No dedicated route/view found | Missing | Define tax records, compliance documents, jurisdiction and permissions |
| Documents | No dedicated owner document hub found | Missing | Define document categories, upload, preview, expiry and access rules |
| Documents — Upload | Property document upload exists in the wizard | Partial | Reuse the upload component; decide whether owner-wide documents need a separate hub |
| Documents — Smart AI | No dedicated route/view found | Missing / future | Confirm AI scope, supported document types and launch dependency |
| AI Assistant | No dedicated owner assistant route found | Missing / feature-gated | Confirm whether this is launch scope; define data boundaries and permission model |
| AI Assistant (duplicate collection screen) | Same as AI Assistant | Duplicate reference | Treat as one requirement until UX confirms a distinct flow |
| My Apps | No dedicated route/view found | Missing | Define connected apps/integrations landing page |
| Add Integration | Calendar connection routes exist per property | Partial | Align integration setup and add provider status, reconnect and disconnect states |
| Settings — Business Profile | Business onboarding/edit data exists, no dedicated settings screen found | Partial | Create business profile settings or explicitly link onboarding data |
| Settings — Staffs and Permissions | `/owner/team` and role routes exist | Implemented, needs visual alignment | Compare role creation, templates, access matrix and deactivation states |
| Settings — Notifications | Notification preferences/configuration exist in separate areas | Partial | Separate owner notification preferences from platform email-provider settings |
| Settings — Security | Profile/password/MFA-related capabilities exist in separate areas | Partial | Consolidate security settings, sessions, password and MFA states |
| Settings — Billing and Subscription | No dedicated route/view found | Missing | Confirm subscription/billing is in product scope before implementation |

## Current owner foundation to standardize first

The following existing surfaces should become the shared visual foundation before adding missing pages:

- Owner dashboard
- Property portfolio and property setup wizard
- Bookings list and booking detail
- Messages
- Calendar and availability blocks
- Finance
- Operations and task assignment
- Team, roles and permissions

Shared components should cover:

- Owner navigation and responsive mobile navigation
- Page shell, content width and breadcrumbs
- Page header with title, description and primary action
- Cards, stat tiles, tables and filter bars
- Buttons, icon buttons, inputs, selects, checkboxes and radios
- Upload/dropzone and image ordering controls
- Empty, loading, error and forbidden states
- Confirmation dialogs and destructive actions
- Toast/status messages and pagination

## Recommended implementation sequence

### Phase 0 — UI audit and design system

1. Capture each existing owner route at desktop and mobile widths.
2. Compare against the collection and record visual differences separately from missing functionality.
3. Establish one owner layout and shared component set.
4. Remove duplicated navigation and inconsistent typography/content widths.

### Phase 1 — Complete existing operational surfaces

1. Align properties, bookings, calendar, finance, operations and team screens.
2. Verify permission-specific visibility and forbidden states.
3. Complete assignment, task evidence, maintenance and offline-booking entry points.

### Phase 2 — Add high-value missing surfaces

1. Expenses and owner statement.
2. Reports hub, occupancy and financial reports.
3. Documents hub and upload/expiry management.
4. Integrations/My Apps with calendar connection states.

### Phase 3 — Scope-dependent surfaces

1. Business profile settings.
2. Security and notification settings consolidation.
3. Tax and compliance.
4. Billing/subscription.
5. AI Assistant and Smart AI documents.

## Acceptance checklist

- Every collection screen is mapped to an existing route, a planned route, or explicitly deferred.
- Existing owner pages share the same navigation, typography, content width and component behavior.
- Missing screens have confirmed permissions, data sources, empty states and audit requirements before coding.
- Finance/reporting numbers are reconciled with booking, payment, expense and settlement records.
- Staff see only permitted owner surfaces and cannot access guest-only pages.
- Desktop and mobile screenshots are reviewed against the approved collection reference.
- Duplicate or exploratory collection screens are identified before implementation.

## Decisions required before building missing screens

- Which screens are required for the current launch and which are future roadmap items?
- Is subscription billing active scope or only a design concept?
- Is AI Assistant launch scope, and what data may it access?
- Should owner-wide documents be separate from property documents?
- Does “two-way iCal” mean availability synchronization only, or channel API booking synchronization?
- Which reports are legally/accounting-critical for the first release?

