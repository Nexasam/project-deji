# Platform Administration MVP Design

## Goal

Expand the existing property-publishing console into a defensible platform-management workspace for the simulated-payment MVP. The workspace must support platform-level roles, actionable oversight, business and user controls, property review, review moderation, internally created booking disputes, configuration and immutable audit visibility.

## Approved scope

The release includes:

- platform dashboard and operational health indicators;
- Platform Super Admin, Verification & Moderation Admin, and Support & Disputes Admin roles;
- business directory, detail, verification, rejection, suspension and reactivation;
- user directory, security detail, account lock and unlock with active-session termination;
- existing property publishing within the unified admin shell;
- guest-review and owner-response hide/restore moderation;
- platform-admin-created booking disputes with assignment and controlled status transitions;
- existing platform operational settings exposed through a validated UI;
- platform-wide immutable audit-history browsing.

Subscriptions, production refunds, administrator impersonation, advanced analytics, administrator invitation, provider-secret management and complete infrastructure observability are deferred.

## Information architecture

The authenticated `/admin` workspace uses a dedicated layout based on the established property-owner visual language: responsive sidebar, white/slate surfaces, orange primary actions, clear status colour, restrained cards and table-first data views.

Navigation:

1. Dashboard
2. Businesses
3. Users
4. Properties
5. Reviews
6. Disputes
7. Platform configuration
8. Audit history

Navigation and actions are permission-aware. Direct routes are protected by platform-permission middleware.

## Platform permissions and roles

Platform permissions are resolved from active global user-role assignments. Business context is never required for platform administration.

### Platform Super Admin

Receives every platform permission. Can manage configuration and lock ordinary users, but cannot lock their own currently authenticated account.

### Verification & Moderation Admin

Can view the dashboard, users, businesses, properties, reviews and contextual disputes; verify or reject businesses; suspend/reactivate businesses; publish/reject/unpublish properties; and hide/restore reviews and responses. Cannot lock users, manage disputes or change configuration.

### Support & Disputes Admin

Can view the dashboard, users, businesses, properties, bookings, reviews and disputes; lock/unlock non-platform users; create, assign and transition disputes. Cannot verify businesses, publish properties, moderate reviews or change configuration.

## Dashboard

The dashboard presents actionable counts and recent activity:

- businesses awaiting verification;
- suspended businesses;
- properties awaiting review;
- hidden/pending reviews and responses;
- open and overdue disputes;
- locked or suspended users;
- failed/stale calendar connections;
- failed queued jobs;
- overdue operational tasks;
- recent sensitive platform actions.

Every metric links to its filtered workspace when the administrator has access.

## Business administration

The directory supports search by name, email or registration number and filters by verification status, account status, country and business type. Detail pages show profile data, ownership/team, properties, bookings, revenue summary, disputes, review activity, calendar health and administrative history.

Actions use mandatory reasons and database transactions:

- verify: verification status becomes `verified`;
- reject: verification status becomes `rejected`;
- suspend: account status becomes `suspended`;
- reactivate: account status becomes `active`.

Suspension hides the business from all new marketplace discovery and booking. Existing property publication states remain unchanged. Existing guest bookings remain readable and essential stay completion continues: check-in, in-stay support, checkout, linked cleaning and inspection. New supply, manual bookings, listing/calendar/team changes and finance mutations are blocked. Reactivation restores eligibility based on each property's existing state.

## User security administration

The directory supports search and filters for account status, platform/business role, business membership and lock state. Detail pages show identity state, membership/role/property scope, failed-login count, lock status, last login/activity and recent administrative/security events.

Locking requires a reason, changes the account to `suspended`, terminates database-backed sessions and prevents authentication. Unlocking changes it to `active`, clears `locked_until` and resets failed-login attempts. An administrator cannot lock themselves. Only the Platform Super Admin may lock another platform administrator.

## Property administration

The existing property queue and publish/reject/unpublish behaviour moves into the unified admin shell and uses platform permissions. Property actions remain transactional and gain immutable audit events with reasons and state snapshots.

## Review moderation

Verified-stay reviews continue to publish automatically when `reviews.auto_publish_verified` is enabled. Administrators may hide or restore a guest review or owner response. Hiding requires a reason, changes `moderation_status` to `hidden`, and preserves original content and publication timestamps. Restoring changes the status to `approved`.

Administrators cannot edit or delete review content. Marketplace queries continue to show only active, approved, published reviews/responses. Every moderation action is audited and displayed in the review case history.

## Dispute management

Disputes are created only by platform administrators from an existing booking. Creation records dispute type, description, priority, optional disputed amount, currency, assigned platform administrator and due date.

Allowed state flow:

`open → under_review → awaiting_guest | awaiting_business → under_review → resolved | rejected → closed`

The dispute page presents the booking, guest, business, property, payments, cancellations, service requests and immutable action history. Assignment and every state transition require a case note. Resolution stores a decision narrative and optional approved amount; it never triggers a real payment or refund in this MVP.

## Configuration

Platform Super Admin may edit the existing definitions only:

- `reviews.auto_publish_verified`;
- `reviews.invitation_window_days`;
- `bookings.free_cancellation_hours`;
- `notifications.email_delivery_enabled`;
- `calendar.stale_after_minutes`;
- `marketplace.require_verified_business`;
- `support.contact_email`.

Inputs are validated by declared type plus safe ranges. Provider secrets remain environment-managed. Each changed setting records old/new value and actor in the audit log.

## Audit and integrity

All sensitive actions use `PlatformAudit` with actor, subject, event name, human description, before/after values, reason metadata, IP address, user agent and occurrence time. Audit events are immutable and cannot be deleted.

The audit page filters by event, administrator, business and date. Historical business, user, review and dispute records are never deleted by platform actions.

## Error handling

- Invalid state transitions return validation errors without partial mutation.
- Destructive or restrictive actions use confirmation modals and mandatory reasons.
- Cross-role direct access returns HTTP 403.
- Missing records return HTTP 404 without leaking foreign context.
- Database mutations and matching audits commit atomically.
- Empty dashboards and queues explain the next action rather than showing blank tables.

## Verification

Feature tests cover role permissions, navigation/action visibility, business transitions, suspended-business marketplace protection, essential existing-stay continuity, user session termination, self/platform-admin lock protection, property audit records, review/response hide and restore, dispute creation/transitions/assignment, configuration validation/audit and audit immutability. Final verification includes SQLite full regression, MySQL migrations/focused tests, Blade compilation, route cache, production frontend build and browser rehearsal.
