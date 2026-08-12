# Project Nexus Operations, Governance, and AI Schema Design

## Purpose

This specification defines the remaining MVP-oriented schema work derived from Chapters 12 through 17. It extends the existing Operations, Workflow, Governance, and AI foundations without redesigning established Business, Property, Booking, Calendar, Finance, RBAC, document, notification, or audit schemas.

## Scope and boundaries

The implementation is divided into four ordered migration stages:

1. Operations workflow and task orchestration.
2. Inventory, employee capabilities, and asset assignment.
3. Generic approvals and data governance.
4. AI conversations, feedback, predictions, and knowledge sources.

The work also includes the corresponding enums, Eloquent models, relationships, permissions, and Chapter 17 seed records. Automated application tests remain deferred at the user's request. Verification will use PHP linting, formatting checks, migration rollback/rebuild in a disposable SQLite database, and seed execution.

Infrastructure concerns from Chapter 15 and post-MVP roadmap items from Chapter 16 are excluded. This includes IoT, smart locks, feature-flag infrastructure, observability storage, offline synchronization, franchise management, vendor marketplaces, and enterprise portfolio structures.

## Stage 1: Operations workflow and task orchestration

### Workflow definitions

`workflow_templates` defines reusable system or business workflows. Each definition is business-scoped when customized, may be triggered manually or by a domain event, is versioned, and may be activated or archived without destroying execution history.

`workflow_template_steps` defines the ordered task-producing or approval-producing steps in a template. A step can specify an operational task type, responsible role or department, expected duration, SLA, verification requirement, failure behavior, and escalation configuration.

Existing `workflow_runs` and `workflow_steps` remain execution records. They will reference their definitions where applicable while retaining snapshots needed to preserve historical meaning after a template changes.

### Operational task extensions

`operational_tasks` will gain only fields needed for orchestration and lifecycle reporting: workflow-run linkage, parent-task linkage, estimated duration, start time, verification requirements and outcome, manual-creation reason, SLA deadline, and escalation time.

`operational_task_dependencies` records prerequisite relationships between tasks. It prevents dependency data from being hidden in JSON and enables querying blocked work.

`operational_task_attachments` records task evidence and file metadata. Binary files remain outside the relational database.

`operational_task_assignments` supports primary and supporting staff, assignment history, acceptance or rejection, and reassignment. The existing direct assignee remains available as the task's current primary-assignee convenience field during this phase.

Completed operational records remain historical and use soft deletion where supported rather than cascading physical deletion.

## Stage 2: Inventory, employee capabilities, and asset assignment

`employee_skills` records searchable business-scoped employee skills and proficiency information.

`employee_certifications` records certification authority, reference, validity dates, verification, and optional document evidence. Certifications are separate from skills because they expire and require proof.

`inventory_items` represents consumable or countable stock and remains separate from durable assets. It includes unit of measure, SKU, supplier, reorder defaults, and lifecycle status.

`inventory_locations` represents warehouses, stores, property storage, vehicles, or other controlled stock locations.

`inventory_stock_levels` stores the current quantity and reorder thresholds for an item at a location. The item-location combination is unique within a business.

`inventory_movements` is the immutable stock ledger for receipts, consumption, transfers, adjustments, damage, and returns. Movements may link to a property, operational task, booking, supplier, employee, and paired source or destination locations as appropriate.

`asset_assignments` preserves the assignment and return history of durable assets. An assignment can target a property, employee, booking, or operational task using explicit nullable foreign keys with a rule that exactly one target is populated.

## Stage 3: Generic approvals and data governance

### Approval engine

`approval_workflows` defines business-configurable approval processes for subjects such as expenses, refunds, invoices, property publication, and AI actions.

`approval_workflow_steps` defines ordered approval levels, eligible roles or permissions, monetary thresholds, quorum, escalation timing, and step configuration.

`approval_requests` represents one approval lifecycle for a polymorphic subject. It stores subject type and UUID, requester, status, current step, request snapshot, and completion or cancellation information.

`approval_request_steps` materializes the definition applicable to a request so later definition changes cannot rewrite history.

`approval_actions` is an append-only record of approvals, rejections, returns, escalations, cancellations, and delegation use.

`approval_delegations` records time-limited delegation from one user to another, restricted by business and optional workflow scope.

Specialized existing approval records remain intact for compatibility. New application services may progressively use the common approval engine without deleting historical specialized records.

### Data governance

`data_retention_policies` defines business or platform retention behavior by data category, including retention duration, terminal action, legal basis, and activation state.

`data_retention_executions` records policy runs, their time range, outcome counts, errors, and execution actor.

`data_subject_requests` records privacy access, correction, export, restriction, and erasure requests. A request can be fulfilled through anonymization or restricted retention when financial, audit, or legal duties prohibit physical erasure.

`audit_events` will gain indexed source channel, actor type, correlation identifier, request identifier, device identifier, and occurrence time fields. Existing IP address, user agent, metadata, entity, and actor fields remain authoritative.

Optimistic locking, encryption, API errors, rate limiting, retries, monitoring, and tracing remain application or infrastructure concerns in this phase.

## Stage 4: AI interaction, feedback, predictions, and knowledge

`ai_conversations` records a persistent user conversation scoped to a business and optional workspace or contextual entity.

`ai_conversation_messages` stores ordered user, assistant, system, and tool messages, including model and token metadata where available. Conversation retention is controlled through governance policies.

`ai_recommendation_feedback` captures acceptance, rejection, dismissal, snoozing, partial application, user reasoning, and observed outcome. This is an immutable learning signal rather than a replacement for the recommendation's current display status.

`ai_prediction_snapshots` stores non-financial predictions such as cancellation risk, maintenance risk, staffing demand, readiness, and guest satisfaction. Existing financial forecast tables remain specialized and unchanged.

`ai_knowledge_sources` registers approved documents, policies, manuals, and other sources available to the AI, including indexing state and access scope.

`ai_knowledge_chunks` stores source segments and indexing metadata. It does not prescribe an embedding engine; vector storage may later use PostgreSQL extensions or an external vector service.

`ai_business_settings` controls enabled capabilities, data use, automation boundaries, approval thresholds, autonomous actions, and retention behavior for each business.

`ai_recommendations` may receive only frequently filtered lifecycle fields that are not already represented cleanly: snooze and expiry times, confidence and risk, execution state, and supersession linkage.

## Permissions and seed data

New permissions will follow the existing workspace-aware RBAC system. They will distinguish viewing, managing, assigning, approving, governing, and configuring rather than granting broad access through user categories.

The Chapter 17 catalogue will seed configurable system workflow templates for guest journey, cleaning, maintenance, property lifecycle, finance approval, and compliance renewal processes. Existing role and permission records will be extended rather than replaced. Seed operations must be idempotent.

## Integrity and tenancy rules

- Every business-owned record includes `business_id` and an appropriate business-leading index.
- Every new entity uses a UUID primary key, timestamps, lifecycle status where meaningful, and creator/modifier audit references consistent with the existing schema.
- Cross-business relationships are rejected by application rules and reinforced by composite constraints where practical and portable.
- Historical execution, approval action, inventory movement, recommendation feedback, and audit records are append-only in application behavior.
- Foreign-key deletion behavior preserves history; destructive cascades are reserved for definition children that cannot exist independently and have no historical execution.
- JSON is used for variable configuration and snapshots, not for relationships that must be searched, ordered, authorized, or constrained.

## Verification criteria

The schema work is complete when all new migrations can run from an empty database in order, roll back safely, and seed the access-control and standard-workflow records without errors. Every created table has a corresponding model where application access is expected, casts and relationships are defined, PHP files pass syntax checks and formatting, and the migration set remains compatible with disposable SQLite verification.
