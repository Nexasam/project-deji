# Staff Role Workflow Audit

This audit compares the 19 October operational workflow proposal with the current application implementation. It is an audit only; no permission changes are made until each gap is approved and implemented.

## Cleaner / Housekeeping

**Implemented:** assigned task list, automatic cleaning assignment, property scope, start/complete transitions, checklist completion, photo evidence and restricted access.

**Partial:** explicit task acceptance and daily recurring/in-stay cleaning generation are now implemented. Remaining Cleaner work is tracked below.

**Deferred Cleaner work:**

- Capture structured linen and supplies quantities used per task.
- Allow a cleaner to create a formal damage/fault issue ticket with category, priority, description and photos.
- Route the issue to a suggested recipient with permitted manual reassignment.
- Add manager approve/return-for-correction actions for completed cleaning.
- Add absence replacement and reassignment notifications.

## Property / Operations Manager

**Implemented:** task creation, one-or-many assignment, reassignment, scheduling, inspection workflows, corrective cleaning/maintenance tasks, manual blocks, offline bookings and operational calendar visibility.

**Partial:** occupancy/readiness dashboard detail, absence routing, delegated expense approval limits and owner-report preparation need a dedicated review.

## Maintenance Manager / Technician

**Implemented:** maintenance task types, automatic maintenance assignment, task notes, priority/due date, image evidence, completion and inspection-triggered corrective work.

The Maintenance step is now under implementation review. The current UI can execute an assigned maintenance task, but it does not yet provide a complete issue-management workspace.

**Partial:** contractor assignment, quotations/funding requests, structured materials/labour/receipt capture, preventive servicing and explicit reopen/unresolved-ticket workflow need completion.

**Maintenance core completed:** assigned technicians can now report a categorized, prioritized issue with description, due date and photo evidence. The issue is linked to the business, property, booking and operational task and is protected by the maintenance permission.

**Deferred Maintenance enhancements:**

- Contractor/supplier selection and assignment.
- Quotation upload and comparison.
- Funding request and approval before spending.
- Structured labour, materials, electricity/fuel and receipt entries.
- Preventive servicing schedules and reminders.
- Temporary booking restriction from an active maintenance issue.
- Reopen, verify and close issue actions with separate approval.

**Maintenance handoff:** The core technician workflow is complete for this pass: a scoped technician can work an assigned maintenance task, report a categorized and prioritized issue, attach photo evidence, set a due date and link the report to the relevant property, booking and task. The deferred items above are enhancements for a later maintenance pass; they do not block moving to the Operations Manager workflow.

## Accountant / Finance Officer

**Implemented:** finance visibility, payment and expense recording, expense categories, receipts, reports/statements, reconciliation permissions and property-scoped access.

**Partial:** funding requests, reimbursements, supplier invoices, approval/disbursement/reconciliation state separation and owner escalation require a dedicated workflow review.

**Accountant core handoff:** The current Finance workspace supports the core daily flow: an authorized finance user can review scoped transactions, record manual booking income, record categorized property or business expenses with supporting evidence, and use reports/statements and reconciliation controls. The finance feature test suite was run successfully. Funding, reimbursement and multi-stage approval automation remain deferred enhancements.

## Permission risks to review before implementation

- Finance write permissions currently include several powerful actions and must be separated by role.
- Operations permissions include assignment, completion, verification and maintenance creation; each needs explicit role defaults.
- Guest information must remain hidden unless the role has the relevant booking/communication permission.
- Any new route must enforce both business membership and property scope server-side, not only hide navigation.

## Operations Manager status

**Core implemented:** operations managers can view scoped operational work, create tasks, assign one or multiple staff members, reassign work, schedule priorities/due dates, inspect cleaning, trigger corrective work, create manual blocks and create offline bookings.

**Operations Manager handoff:** The core daily workflow is complete for this pass. The existing operations workspace and server-side business/property scope support task allocation, reassignment, scheduling, inspection and corrective work without changing the existing booking or finance rules. Relevant Operations workspace tests were run successfully.

**Deferred Operations Manager enhancements:** occupancy/readiness dashboard states, absence replacement routing, delegated expense limits and a dedicated owner-report preparation workflow.

## Recommended implementation order

1. Cleaner task acceptance, supplies/linen notes, issue tickets and manager review.
2. Maintenance issue lifecycle, quotes/funding, preventive service and reopen flow.
3. Manager occupancy/readiness, absence routing and delegated expense limits.
4. Accountant funding/reimbursement/invoice workflow and separated approvals.
5. Role defaults, permission matrix and direct-URL tests for every new action.
