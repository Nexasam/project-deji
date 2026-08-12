# Operations, Governance, and AI Schema Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add the remaining MVP schema for operations orchestration, inventory, governance, approvals, and AI while extending the existing Project Nexus migrations rather than duplicating them.

**Architecture:** Four ordered migrations own four bounded domains. Existing runtime workflow tables remain executions, new template tables remain definitions, generic approvals use immutable request snapshots, stock uses an append-only movement ledger, and AI knowledge storage remains provider-neutral.

**Tech Stack:** PHP 8.3, Laravel 13, Eloquent, Laravel migrations and seeders, UUID primary keys, SQLite-compatible schema verification.

## Global Constraints

- Do not add automated application tests yet; the user explicitly deferred them until all migrations are complete.
- Preserve all existing user changes and the archived `database/migrations_old` directory.
- Use UUID primary keys and existing audit-column conventions.
- Preserve historical records with restrictive foreign keys, soft deletion where appropriate, and append-only application semantics for ledgers and actions.
- Keep business relationships relational; reserve JSON for variable configurations and snapshots.
- Do not implement Chapter 15 infrastructure or Chapter 16 post-MVP roadmap features.

---

### Task 1: Operations workflow and task orchestration

**Files:**
- Create: `database/migrations/2026_08_08_002200_create_operations_workflow_orchestration_tables.php`
- Create: `app/Models/WorkflowTemplate.php`
- Create: `app/Models/WorkflowTemplateStep.php`
- Create: `app/Models/OperationalTaskDependency.php`
- Create: `app/Models/OperationalTaskAttachment.php`
- Create: `app/Models/OperationalTaskAssignment.php`
- Modify: `app/Models/OperationalTask.php`
- Modify: `app/Models/WorkflowRun.php`
- Modify: `app/Models/WorkflowStep.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/Employee.php`

**Interfaces:**
- Consumes: existing `businesses`, `roles`, `departments`, `domain_events`, `workflow_runs`, `workflow_steps`, `operational_tasks`, `employees`, and `users` tables.
- Produces: reusable workflow definitions, task dependency/evidence/assignment history, and definition-to-execution references.

- [ ] Create `workflow_templates` and ordered `workflow_template_steps` with business scope, versioning, trigger configuration, SLA, approval, verification, and escalation data.
- [ ] Extend `workflow_runs`, `workflow_steps`, and `operational_tasks` with nullable definition and orchestration references that preserve existing records.
- [ ] Create task dependencies with a unique predecessor-successor pair and a self-dependency guard in application design.
- [ ] Create task attachments with storage metadata and task assignments with employee, assignment role, lifecycle timestamps, and assignment actor.
- [ ] Add casts and Eloquent relationships for all Stage 1 records.
- [ ] Run PHP lint and `php artisan migrate:fresh --seed` against disposable SQLite.

### Task 2: Inventory, employee capability, and asset assignment

**Files:**
- Create: `database/migrations/2026_08_08_002300_create_inventory_and_workforce_capability_tables.php`
- Create: `app/Enums/InventoryMovementType.php`
- Create: `app/Models/EmployeeSkill.php`
- Create: `app/Models/EmployeeCertification.php`
- Create: `app/Models/InventoryItem.php`
- Create: `app/Models/InventoryLocation.php`
- Create: `app/Models/InventoryStockLevel.php`
- Create: `app/Models/InventoryMovement.php`
- Create: `app/Models/AssetAssignment.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/Employee.php`
- Modify: `app/Models/Asset.php`
- Modify: `app/Models/Property.php`
- Modify: `app/Models/OperationalTask.php`

**Interfaces:**
- Consumes: businesses, employees, properties, bookings, suppliers, assets, operational tasks, documents, and users.
- Produces: searchable workforce capability, certification validity, current inventory levels, immutable stock movements, and durable asset assignment history.

- [ ] Create employee skills and certifications, including certificate-document linkage and validity indexes.
- [ ] Create inventory item, location, and stock-level tables with unique business SKU/code and item-location stock rows.
- [ ] Create append-only inventory movements with source/destination locations and optional operational context.
- [ ] Create asset assignments with one explicit target among property, employee, booking, or task.
- [ ] Add enums, casts, and Eloquent relationships.
- [ ] Run PHP lint and disposable SQLite migration/seed verification.

### Task 3: Generic approvals and data governance

**Files:**
- Create: `database/migrations/2026_08_08_002400_create_approval_and_data_governance_tables.php`
- Create: `app/Enums/ApprovalRequestStatus.php`
- Create: `app/Enums/ApprovalActionType.php`
- Create: `app/Enums/DataSubjectRequestType.php`
- Create: `app/Enums/DataSubjectRequestStatus.php`
- Create: `app/Models/ApprovalWorkflow.php`
- Create: `app/Models/ApprovalWorkflowStep.php`
- Create: `app/Models/ApprovalRequest.php`
- Create: `app/Models/ApprovalRequestStep.php`
- Create: `app/Models/ApprovalAction.php`
- Create: `app/Models/ApprovalDelegation.php`
- Create: `app/Models/DataRetentionPolicy.php`
- Create: `app/Models/DataRetentionExecution.php`
- Create: `app/Models/DataSubjectRequest.php`
- Modify: `app/Models/AuditEvent.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/User.php`

**Interfaces:**
- Consumes: businesses, roles, permissions, users, audit events, and arbitrary UUID subjects.
- Produces: reusable approval definitions, immutable approval actions, delegation, retention execution history, and privacy request tracking.

- [ ] Create approval workflow definitions and ordered step definitions.
- [ ] Create approval requests, snapshotted request steps, append-only actions, and time-limited delegations.
- [ ] Create retention policies, execution records, and data-subject requests with legal-hold outcomes.
- [ ] Extend audit events with source, actor, correlation, request, device, and occurrence fields.
- [ ] Add enums, casts, and Eloquent relationships.
- [ ] Run PHP lint and disposable SQLite migration/seed verification.

### Task 4: AI conversations, learning, prediction, and knowledge

**Files:**
- Create: `database/migrations/2026_08_08_002500_create_ai_interaction_and_knowledge_tables.php`
- Create: `app/Enums/AiMessageRole.php`
- Create: `app/Enums/AiRecommendationFeedbackType.php`
- Create: `app/Models/AiConversation.php`
- Create: `app/Models/AiConversationMessage.php`
- Create: `app/Models/AiRecommendationFeedback.php`
- Create: `app/Models/AiPredictionSnapshot.php`
- Create: `app/Models/AiKnowledgeSource.php`
- Create: `app/Models/AiKnowledgeChunk.php`
- Create: `app/Models/AiBusinessSetting.php`
- Modify: `app/Models/AiRecommendation.php`
- Modify: `app/Models/Business.php`
- Modify: `app/Models/User.php`
- Modify: `app/Models/Document.php`

**Interfaces:**
- Consumes: businesses, users, workspaces, documents, AI recommendations, properties, bookings, domain events, workflows, and approvals.
- Produces: retained AI chat, immutable feedback, generalized predictions, provider-neutral indexed knowledge, and business automation boundaries.

- [ ] Create conversation and ordered-message tables with model and usage metadata.
- [ ] Create recommendation feedback and general prediction snapshots.
- [ ] Create knowledge source/chunk metadata without coupling the schema to a vector provider.
- [ ] Create one AI settings record per business and extend recommendation lifecycle fields only where indexed filtering is useful.
- [ ] Add enums, casts, and Eloquent relationships.
- [ ] Run PHP lint and disposable SQLite migration/seed verification.

### Task 5: Permissions and Chapter 17 workflow catalogue

**Files:**
- Modify: `database/seeders/AccessControlSeeder.php`
- Create: `database/seeders/WorkflowTemplateSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Consumes: existing workspaces, permissions, roles, role-permission mappings, workflow templates, and workflow template steps.
- Produces: idempotent permissions and standard configurable system workflow definitions.

- [ ] Add fine-grained operations, inventory, approvals, governance, and AI permissions and map them to existing system roles.
- [ ] Seed the Chapter 17 guest journey, cleaning, maintenance, property lifecycle, finance, and compliance workflows with stable keys and versions.
- [ ] Register the workflow seeder after access-control seeding.
- [ ] Run the seeders twice on disposable SQLite to verify idempotency.

### Task 6: Final non-test verification

**Files:**
- Verify all files created or modified by Tasks 1–5.

**Interfaces:**
- Consumes: complete Stage 1–4 implementation.
- Produces: evidence that syntax, formatting, migration order, rollback, and seeds work without running the deferred automated test suite.

- [ ] Run `php -l` for all changed PHP files.
- [ ] Run `vendor/bin/pint --dirty` and re-run linting.
- [ ] Run `php artisan migrate:fresh --seed` with a disposable SQLite database.
- [ ] Run `php artisan migrate:rollback` for the four new migration batches where practical, then rebuild.
- [ ] Run `git diff --check` and inspect `git status --short` to ensure unrelated user changes were preserved.
