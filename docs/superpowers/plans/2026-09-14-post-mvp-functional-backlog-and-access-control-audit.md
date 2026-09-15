# Post-MVP Functional Backlog and Access-Control Audit

**Audit date:** 14 September 2026  
**Source:** `PROJECT NEXUS (2).pdf` (190 pages)  
**Repository baseline:** Owner-operated MVP 1 with simulated payments, verified-stay reviews, and the first runtime RBAC release

## Purpose and scope

This document answers two questions:

1. Which functional parts of the 190-page SRS are still not connected as usable product workflows?
2. How much of the SRS role-based access-control model is actually enforced for business users and platform administrators?

The current application already closes the core stay loop:

`Create property → verify/publish → discover → book → prepare → check in → support → check out → clean → inspect → ready → review`

This audit does not treat release rehearsals as missing functionality. Native mobile applications, native Google/Outlook/WhatsApp integrations, and production payment gateways are recorded separately as deliberately deferred work.

## Executive conclusion

The application is a coherent **multi-role pilot foundation**, but it is not yet the complete Hospitality Operating System described in Chapter 5.

The role and permission foundation is now connected to runtime authorization for the implemented owner workspaces. The application resolves role permissions and membership overrides, enforces property scope, exposes team administration, filters navigation/actions, and supports active-business switching. Capability management, multiple-role editing, approval/separation-of-duty execution, and complete audit coverage remain later work.

The highest-value remaining functional programme is therefore:

`RBAC hardening → platform administration → remaining business workspaces → advanced governance`

## Access control: what exists

### Implemented foundation

- System roles are seeded for guest, platform super administrator, business owner, property manager, reception officer, accountant, operations manager, cleaner, maintenance technician, inspector and customer support.
- A broad permission catalogue exists across properties, bookings, calendar, finance, operations, employees, documents, approvals, governance, AI and platform administration.
- Role-to-permission and role-to-workspace mappings are seeded.
- Business memberships and business-scoped role assignments exist.
- Active business context and tenant-scoped queries prevent known cross-business data access.
- Platform-admin login and a global super-admin role check exist.
- Sensitive document downloads, staff tasks, owner bookings and other implemented flows have tenant/role-focused tests.
- Cleaner, inspector and maintenance task execution is restricted through the staff task surface.

### Runtime enforcement delivered on 14 September 2026

- `BusinessPermissionService` resolves active role mappings plus membership allow/deny overrides, with explicit deny taking precedence.
- Owner routes now use permission-specific middleware instead of the blanket `business.owner` gate.
- Object-level authorization resolves properties from properties, bookings, tasks, reviews, service requests, availability blocks, and submitted property/booking identifiers.
- Property assignments scope property, booking, calendar, finance, dashboard and operations queries; crafted writes to unassigned properties are rejected.
- Navigation and primary workspace actions are permission-aware.
- Team & access supports inviting/provisioning a user, assigning one system business role, optional property scope, changing role/scope, and deactivating access with audit events.
- Users with multiple active memberships can switch their active business context.
- Cleaner, maintenance and inspector navigation converges on the assigned-task surface; `task.view_assigned` does not expose other employees' tasks.
- Feature coverage verifies role workspace boundaries, property-scoped reads/writes, overrides, team lifecycle, context switching and assigned-task isolation.

### Remaining enforcement and governance gaps

- The team UI assigns one active role at a time; custom-role creation and deliberate multi-role assignment are not exposed.
- Membership permission overrides are enforced but do not yet have an owner-facing management screen.
- Invitation resend/expiry and a dedicated invitation-acceptance page are not present; new users currently receive the secure password-setup flow.
- Staff skills, certifications, availability and capability management remain schema-first.
- Permission `requires_audit`, risk levels, approvals and separation-of-duty rules are not yet enforced uniformly across all sensitive actions.
- Customer-support incident/dispute tooling and richer role-specific reporting remain incomplete.

### Business-owner-side work still required

#### Delivered — permission enforcement baseline

- Central role/override resolver and granular permission middleware.
- Business, property and assignment scoping for implemented workspaces.
- Permission-aware navigation and core quick actions.
- Explicit deny coverage for representative sensitive mutations.

#### Partly delivered — team and role management

- Team workspace listing members, status, assigned properties and current role. **Delivered.**
- Invite/provision employee through password setup and deactivate/remove access. **Delivered; dedicated acceptance/resend remains.**
- Assign/change/revoke one active role and optional property scope. **Delivered.**
- Prevent invalid cross-business role/property assignments. **Delivered.**
- Support deliberate multiple-role assignment and custom roles. **Remaining.**
- Manage staff capabilities, skills and certifications where operationally relevant.
- Allow users belonging to multiple businesses to select and switch active business context. **Delivered.**

#### P1 — role-specific workspace experiences

- Property manager: properties, bookings, calendar and operations without subscription/ownership control.
- Reception/reservations: bookings, guest communication, check-in/out and limited payment recording.
- Accountant: finance, payment verification, expenses, refunds and reports without booking mutation.
- Operations manager: cleaning, inspections, maintenance, staff assignment and operational reporting.
- Customer support: bookings, guest enquiries, service requests, incidents and disputes without finance mutation.
- Cleaner/maintenance/inspector: retain the current task surface but add only the property instructions, evidence and equipment history required for their work.

#### P1 — approvals and auditability

- Record immutable audit events for login, role changes, booking cancellation, payment verification, refunds, publication and other permission-sensitive actions.
- Activate approval workflows for configurable high-risk finance/operations actions.
- Enforce or visibly warn on separation-of-duty conflicts.
- Add owner-facing audit history and permission-change history.

### Platform-admin-side work still required

Current platform administration supports authentication and property review/publish/reject/unpublish. The SRS additionally assigns the platform administrator responsibility for:

#### P0/P1 — platform operations

- Platform dashboard with pending verifications, unresolved calendar conflicts, critical incidents and delivery/scheduler health.
- Business directory and business profile inspection.
- Business verification, rejection, suspension/reactivation and verification history.
- User lookup, account lock/unlock and security status inspection.
- Review/response moderation, reporting and takedown workflow.
- Booking/guest dispute and escalation oversight.
- Marketplace listing moderation beyond the initial property publication decision.

#### P1/P2 — platform management

- Subscription/plan administration and account-entitlement visibility.
- Platform analytics across businesses, properties, bookings and marketplace conversion.
- Platform configuration, notification defaults and policy management.
- Audited, time-limited business impersonation with a visible banner and explicit exit.
- Platform-wide audit log, security-event view and administrative action history.
- AI configuration/governance when the AI layer becomes operational.

## Remaining SRS functionality excluding the deferred integrations/payment/mobile work

The schema is broader than the active application. A model or table is not counted as implemented unless a user can complete the workflow through authorized routes and screens.

| Area | Working baseline | Remaining functional work | Priority |
|---|---|---|---|
| Roles and permissions | Runtime resolver, granular route/object authorization, property scope, team UI, role-aware navigation/actions, overrides and business switching | Override/custom-role UI, multi-role editing, universal sensitive-action audit, approvals and separation-of-duty enforcement | P1/P2 |
| Platform administration | Property verification and publication | Business/user management, review moderation, disputes, platform dashboard/configuration, analytics and audited impersonation | P0/P1 |
| Business settings | Initial business onboarding and profile data | Editable business settings, contacts, operating policies, notification preferences, branding and multi-business switching | P1 |
| Team workspace | Member directory, user provisioning, password setup, one-role assignment/change, property scope, deactivation and assigned staff task inbox | Invite resend/expiry, dedicated acceptance, multiple/custom roles, capability management, availability and performance view | P1 |
| Guest workspace/CRM | Guest account, own bookings, contact captured per booking and service requests | Owner guest directory, unified guest profile/history, preferences, notes, incidents, repeat-guest context and consent controls | P1 |
| Property workspace | Setup, media/documents, amenities, pricing, publication, channels and rich details | Dedicated lifecycle/history, staff assignments, asset maintenance history, access instructions governance, health metrics and property-level analytics | P1 |
| Booking workspace | Booking case file, payments, lifecycle, operations, requests, reviews and history | Structured communication log, booking documents, guest-party management, incidents/disputes, approval paths and configurable automations | P1 |
| Marketplace | Search/filter, listings, media, availability, booking and verified reviews | Favourites, comparison, interactive map, verified business profile, review reporting/moderation and ranking/recommendation logic | P1/P2 |
| Calendar | Bookings, blocks, operational tasks and iCal visibility | Rich filters/views, drag/reschedule controls, recurring internal events, resource/staff planning and conflict-resolution workflow | P1/P2 |
| Operations | Automated preparation/turnover/inspection, maintenance correction and staff tasks | Recurring housekeeping, preventive maintenance schedules, reusable workflow library/builder, dependencies, SLA/escalation rules and richer operational reporting | P1/P2 |
| Inventory/procurement | Inventory, stock, supplier and movement schema | Inventory workspace, stock counts, consumption, low-stock alerts, purchase/replenishment and supplier workflows | P2 |
| Assets | Asset capture and task/maintenance relationships | Asset register UI, assignments, preventive schedules, service history, warranty/condition tracking and retirement | P2 |
| Finance | Booking totals, payment/refund/expense records, receipt and basic report | Accounts/ledgers, reconciliation, expense approvals, cash flow, profitability, budgets, recurring expenses, tax/invoice/credit-note and settlement workflows | P1/P2 |
| Reports | Basic revenue/expense reporting and dashboard KPIs | Saved report definitions, exports, scheduled reports, drill-downs, occupancy/ADR/RevPAR and role-scoped reporting | P2 |
| Documents | Private property verification files and scoped previews | Central document workspace, categories, expiry reminders, version/history UI, sharing rules and archive/retention workflow | P1/P2 |
| Notifications | In-app and queued email for core booking/operations/calendar/review events | User preferences, per-role routing, configurable channels, escalation rules, retry visibility and complete SRS event coverage | P1 |
| Reviews | Verified completed-stay review and one owner response | Moderation queue, report/takedown, invitation resend, response editing policy, rating breakdown analytics and owner follow-up workflow | P1/P2 |
| AI Phase 1 | Presentation-only concierge components and AI-oriented schema | Real daily executive briefing, basic explainable recommendations, grounded conversational assistant and simple forecasting | P2 unless required for branded V1 |
| Approvals/governance | Extensive schema and seed rules | Usable approval inbox, actions/delegation, retention execution, privacy requests and governance screens | P2 |
| Public website/support | Marketplace-led landing page and authentication | Dedicated features/pricing/about/contact/help/legal content, support intake and operational help centre | P2 |

## Recommended delivery order after the current release gates

### Programme 1 — Make RBAC real (baseline delivered)

1. Permission resolver and middleware/policies. **Delivered.**
2. Convert owner routes from owner-only to permission-based access. **Delivered.**
3. Permission-aware navigation and primary actions. **Delivered.**
4. Team invitation/provisioning and role-assignment workspace. **Baseline delivered.**
5. Multi-business context switcher. **Delivered.**
6. Sensitive-action audit events and separation-of-duty tests. **Still required for full programme completion.**

**Exit criterion:** property manager, reception, accountant, operations manager and task staff can each complete only their intended work, while every forbidden action is denied and tenant boundaries remain intact.

### Programme 2 — Complete platform administration

1. Platform dashboard and business directory.
2. Business verification/suspension workflow.
3. Review moderation and dispute/escalation queue.
4. User/security administration.
5. Audited impersonation.
6. Platform configuration and audit history.

**Exit criterion:** routine platform governance no longer requires direct database access.

### Programme 3 — Complete daily operating workspaces

1. Guest CRM and communication history.
2. Recurring housekeeping and preventive maintenance.
3. Booking incidents/disputes/documents.
4. Document expiry and central document workspace.
5. Finance reconciliation/profitability and reports.
6. Inventory, procurement and asset lifecycle.

### Programme 4 — Intelligence and optimisation

1. Real rules-based daily briefing.
2. Basic explainable recommendations.
3. Saved/scheduled reporting.
4. Forecasting and later optimisation features.

## Explicitly deferred and documented for later

These are not part of the next functional programme unless the product scope changes:

### Payment productionisation

- Replace the simulated gateway with real Paystack/Stripe/Flutterwave initialization.
- Verify signed webhooks and make webhook processing idempotent.
- Reconcile gateway transactions, settlements, chargebacks and refunds.
- Add production credentials, failure recovery, provider monitoring and finance closeout.

### Native integrations

- Google Calendar API.
- Microsoft Outlook Calendar API.
- Native WhatsApp messaging/API.
- Additional channel-manager and accounting integrations.

The existing iCal import/export remains the supported calendar interoperability path until then.

### Native mobile applications

- Dedicated guest and staff applications.
- Push notifications, biometric login, camera-first evidence capture and offline task execution.

Responsive browser workflows remain the supported mobile experience until then.

## Readiness interpretation

- **Owner-operated pilot:** application functionality is coherent, subject to the existing release gates.
- **Multi-role business pilot:** baseline is ready for controlled role acceptance testing; advanced governance items in Programme 1 remain before a broader rollout.
- **Self-service platform administration:** not ready until the essential parts of Programme 2 are complete.
- **Full 190-page product:** remains a multi-phase roadmap; schema coverage is much wider than active workflow coverage.
