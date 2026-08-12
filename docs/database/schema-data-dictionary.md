# Project Nexus — Exhaustive Schema Data Dictionary

> Generated from a freshly migrated database by `scripts/generate_schema_dictionary.php`.

This document lists every active table and every field. SQLite type names shown here are the portable representation Laravel produced during verification; production database engines may render equivalent types differently.

Legend: **Required** means the column is not nullable. Defaults are database defaults, not application-level defaults. Foreign-key deletion behavior is shown in the field description.

Total active tables: **134**.

## Dashboards and AI

### `ai_business_settings`

Stores AI workspace records for business settings.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `ai_enabled` | `tinyint(1)` | Yes | `'1'` | Stores the ai enabled value for this record. |
| `enabled_capabilities` | `TEXT` | No | — | Stores the enabled capabilities value for this record. |
| `data_use_preferences` | `TEXT` | No | — | JSON or structured data containing data use preferences. |
| `automation_limits` | `TEXT` | No | — | Stores the automation limits value for this record. |
| `approval_thresholds` | `TEXT` | No | — | Stores the approval thresholds value for this record. |
| `permitted_autonomous_actions` | `TEXT` | No | — | Stores the permitted autonomous actions value for this record. |
| `conversation_retention_days` | `INTEGER` | No | — | Stores the conversation retention days value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `ai_business_settings_business_id_unique` on (`business_id`) — unique.

### `ai_conversation_messages`

Stores AI workspace records for conversation messages.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `ai_conversation_id` | `varchar` | Yes | — | Foreign key to `ai_conversations.id`; deletion behavior is RESTRICT |
| `parent_message_id` | `varchar` | No | — | Foreign key to `ai_conversation_messages.id`; deletion behavior is RESTRICT |
| `sequence` | `INTEGER` | Yes | — | Numeric value used for sequence. |
| `role` | `varchar` | Yes | — | Stores the role value for this record. |
| `content` | `TEXT` | No | — | Stores the content value for this record. |
| `tool_calls` | `TEXT` | No | — | Stores the tool calls value for this record. |
| `citations` | `TEXT` | No | — | Stores the citations value for this record. |
| `model_provider` | `varchar` | No | — | Stores the model provider value for this record. |
| `model_name` | `varchar` | No | — | Stores the model name value for this record. |
| `input_tokens` | `INTEGER` | No | — | Stores the input tokens value for this record. |
| `output_tokens` | `INTEGER` | No | — | Stores the output tokens value for this record. |
| `duration_ms` | `INTEGER` | No | — | Stores the duration ms value for this record. |
| `message_status` | `varchar` | Yes | `'completed'` | Domain-specific lifecycle or processing state for message status. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `ai_conversation_messages_ai_conversation_id_sequence_unique` on (`ai_conversation_id`, `sequence`) — unique.

### `ai_conversations`

Stores AI workspace records for conversations.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `workspace_id` | `varchar` | No | — | Foreign key to `workspaces.id`; deletion behavior is SET NULL |
| `title` | `varchar` | No | — | Short human-readable title. |
| `context_type` | `varchar` | No | — | Classification describing context type. |
| `context_id` | `varchar` | No | — | Stores the context id value for this record. |
| `conversation_status` | `varchar` | Yes | `'active'` | Domain-specific lifecycle or processing state for conversation status. |
| `last_message_at` | `datetime` | No | — | Timestamp recording when last message at. |
| `archived_at` | `datetime` | No | — | Timestamp recording when archived at. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `ai_conversations_context_type_context_id_index` on (`context_type`, `context_id`); `ai_conversations_business_id_user_id_last_message_at_index` on (`business_id`, `user_id`, `last_message_at`).

### `ai_knowledge_chunks`

Stores AI workspace records for knowledge chunks.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `ai_knowledge_source_id` | `varchar` | Yes | — | Foreign key to `ai_knowledge_sources.id`; deletion behavior is CASCADE |
| `chunk_index` | `INTEGER` | Yes | — | Stores the chunk index value for this record. |
| `content` | `TEXT` | Yes | — | Stores the content value for this record. |
| `content_hash` | `varchar` | Yes | — | Stores the content hash value for this record. |
| `vector_reference` | `varchar` | No | — | Stable identifier used for vector reference. |
| `token_count` | `INTEGER` | No | — | Numeric value used for token count. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `ai_knowledge_chunks_content_hash_index` on (`content_hash`); `ai_knowledge_chunks_ai_knowledge_source_id_chunk_index_unique` on (`ai_knowledge_source_id`, `chunk_index`) — unique.

### `ai_knowledge_sources`

Stores AI workspace records for knowledge sources.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `document_id` | `varchar` | No | — | Foreign key to `documents.id`; deletion behavior is RESTRICT |
| `source_type` | `varchar` | Yes | — | Classification describing source type. |
| `title` | `varchar` | Yes | — | Short human-readable title. |
| `source_uri` | `varchar` | No | — | Location used to access source uri; file contents normally live outside the relational database. |
| `access_scope` | `varchar` | Yes | `'business'` | Stores the access scope value for this record. |
| `content_hash` | `varchar` | No | — | Stores the content hash value for this record. |
| `indexing_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for indexing status. |
| `index_version` | `varchar` | No | — | Stores the index version value for this record. |
| `last_indexed_at` | `datetime` | No | — | Timestamp recording when last indexed at. |
| `indexing_error` | `TEXT` | No | — | Stores the indexing error value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `ai_knowledge_sources_business_id_indexing_status_status_index` on (`business_id`, `indexing_status`, `status`).

### `ai_prediction_snapshots`

Stores AI workspace records for prediction snapshots.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `prediction_type` | `varchar` | Yes | — | Classification describing prediction type. |
| `subject_type` | `varchar` | Yes | — | Classification describing subject type. |
| `subject_id` | `varchar` | Yes | — | Stores the subject id value for this record. |
| `predicted_value` | `numeric` | No | — | Numeric value representing predicted value; interpret it with the table's currency, scale, or scoring context. |
| `predicted_label` | `varchar` | No | — | Stores the predicted label value for this record. |
| `confidence_score` | `numeric` | No | — | Numeric value representing confidence score; interpret it with the table's currency, scale, or scoring context. |
| `factors` | `TEXT` | No | — | Stores the factors value for this record. |
| `evidence` | `TEXT` | No | — | JSON or structured data containing evidence. |
| `prediction_for` | `datetime` | Yes | — | Stores the prediction for value for this record. |
| `generated_at` | `datetime` | Yes | — | Timestamp recording when generated at. |
| `valid_until` | `datetime` | No | — | Stores the valid until value for this record. |
| `model_provider` | `varchar` | No | — | Stores the model provider value for this record. |
| `model_name` | `varchar` | No | — | Stores the model name value for this record. |
| `model_version` | `varchar` | No | — | Stores the model version value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `ai_prediction_snapshots_subject_type_subject_id_prediction_for_index` on (`subject_type`, `subject_id`, `prediction_for`); `ai_prediction_snapshots_business_id_prediction_type_prediction_for_index` on (`business_id`, `prediction_type`, `prediction_for`).

### `ai_recommendation_feedback`

Stores AI workspace records for recommendation feedback.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `ai_recommendation_id` | `varchar` | Yes | — | Foreign key to `ai_recommendations.id`; deletion behavior is RESTRICT |
| `user_id` | `varchar` | No | — | Related platform user UUID. |
| `feedback_type` | `varchar` | Yes | — | Classification describing feedback type. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `snoozed_until` | `datetime` | No | — | Stores the snoozed until value for this record. |
| `action_taken` | `TEXT` | No | — | Stores the action taken value for this record. |
| `outcome` | `TEXT` | No | — | Stores the outcome value for this record. |
| `outcome_score` | `numeric` | No | — | Numeric value representing outcome score; interpret it with the table's currency, scale, or scoring context. |
| `recorded_at` | `datetime` | Yes | — | Timestamp recording when recorded at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `ai_recommendation_feedback_business_id_feedback_type_recorded_at_index` on (`business_id`, `feedback_type`, `recorded_at`); `ai_recommendation_feedback_ai_recommendation_id_recorded_at_index` on (`ai_recommendation_id`, `recorded_at`).

### `ai_recommendations`

An explainable AI recommendation with evidence, lifecycle state, model provenance, and optional executable action.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `domain_event_id` | `varchar` | No | — | Foreign key to `domain_events.id`; deletion behavior is RESTRICT |
| `workflow_run_id` | `varchar` | No | — | Foreign key to `workflow_runs.id`; deletion behavior is RESTRICT |
| `recommendation_key` | `varchar` | Yes | — | Stable identifier used for recommendation key. |
| `category` | `varchar` | Yes | — | Stores the category value for this record. |
| `title` | `varchar` | Yes | — | Short human-readable title. |
| `summary` | `TEXT` | Yes | — | Stores the summary value for this record. |
| `rationale` | `TEXT` | No | — | Stores the rationale value for this record. |
| `evidence` | `TEXT` | No | — | JSON or structured data containing evidence. |
| `proposed_action` | `TEXT` | No | — | Stores the proposed action value for this record. |
| `priority` | `varchar` | Yes | `'normal'` | Stores the priority value for this record. |
| `recommendation_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for recommendation status. |
| `confidence_score` | `numeric` | No | — | Numeric value representing confidence score; interpret it with the table's currency, scale, or scoring context. |
| `model_provider` | `varchar` | No | — | Stores the model provider value for this record. |
| `model_name` | `varchar` | No | — | Stores the model name value for this record. |
| `model_version` | `varchar` | No | — | Stores the model version value for this record. |
| `generated_at` | `datetime` | Yes | — | Timestamp recording when generated at. |
| `valid_until` | `datetime` | No | — | Stores the valid until value for this record. |
| `accepted_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `accepted_at` | `datetime` | No | — | Timestamp recording when accepted at. |
| `dismissed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `dismissed_at` | `datetime` | No | — | Timestamp recording when dismissed at. |
| `dismissal_reason` | `TEXT` | No | — | Stores the dismissal reason value for this record. |
| `executed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `executed_at` | `datetime` | No | — | Timestamp recording when executed at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `risk_level` | `varchar` | No | — | Stores the risk level value for this record. |
| `execution_status` | `varchar` | No | — | Domain-specific lifecycle or processing state for execution status. |
| `snoozed_until` | `datetime` | No | — | Stores the snoozed until value for this record. |
| `superseded_by_id` | `varchar` | No | — | Foreign key to `ai_recommendations.id`; deletion behavior is RESTRICT |

**Indexes:** `ai_recommendations_business_id_snoozed_until_index` on (`business_id`, `snoozed_until`); `ai_recommendations_business_id_execution_status_index` on (`business_id`, `execution_status`); `ai_recommendations_property_id_recommendation_status_index` on (`property_id`, `recommendation_status`); `ai_recommendations_business_id_recommendation_status_priority_index` on (`business_id`, `recommendation_status`, `priority`); `ai_recommendations_business_id_recommendation_key_unique` on (`business_id`, `recommendation_key`) — unique; `ai_recommendations_business_id_category_generated_at_index` on (`business_id`, `category`, `generated_at`); `ai_recommendations_booking_id_recommendation_status_index` on (`booking_id`, `recommendation_status`).

### `dashboard_briefings`

Stores dashboard briefings records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `briefing_date` | `date` | Yes | — | Calendar date associated with briefing date. |
| `title` | `varchar` | No | — | Short human-readable title. |
| `content` | `TEXT` | No | — | Stores the content value for this record. |
| `summary_data` | `TEXT` | No | — | Stores the summary data value for this record. |
| `supporting_metrics` | `TEXT` | No | — | Stores the supporting metrics value for this record. |
| `generation_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for generation status. |
| `model_provider` | `varchar` | No | — | Stores the model provider value for this record. |
| `model_name` | `varchar` | No | — | Stores the model name value for this record. |
| `model_version` | `varchar` | No | — | Stores the model version value for this record. |
| `prompt_version` | `varchar` | No | — | Stores the prompt version value for this record. |
| `generation_duration_ms` | `INTEGER` | No | — | Stores the generation duration ms value for this record. |
| `generated_at` | `datetime` | No | — | Timestamp recording when generated at. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `dashboard_briefings_business_id_generation_status_briefing_date_index` on (`business_id`, `generation_status`, `briefing_date`); `dashboard_briefings_business_id_briefing_date_unique` on (`business_id`, `briefing_date`) — unique.

## Properties and marketplace

### `amenities`

Stores amenities records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `code` | `varchar` | Yes | — | Stable business-readable code used for searching and uniqueness. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `category` | `varchar` | No | — | Stores the category value for this record. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `amenities_name_status_index` on (`name`, `status`); `amenities_code_unique` on (`code`) — unique; `amenities_category_status_index` on (`category`, `status`).

### `asset_assignments`

Stores asset lifecycle records for assignments.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `asset_id` | `varchar` | Yes | — | Foreign key to `assets.id`; deletion behavior is RESTRICT |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `employee_id` | `varchar` | No | — | Related business employee UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `operational_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `assignment_type` | `varchar` | Yes | `'custody'` | Classification describing assignment type. |
| `assigned_at` | `datetime` | Yes | — | Timestamp recording when assigned at. |
| `expected_return_at` | `datetime` | No | — | Timestamp recording when expected return at. |
| `returned_at` | `datetime` | No | — | Timestamp recording when returned at. |
| `assigned_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `returned_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `asset_assignments_business_id_employee_id_status_index` on (`business_id`, `employee_id`, `status`); `asset_assignments_business_id_property_id_status_index` on (`business_id`, `property_id`, `status`); `asset_assignments_business_id_asset_id_returned_at_index` on (`business_id`, `asset_id`, `returned_at`).

### `asset_maintenance_records`

Stores asset lifecycle records for maintenance records.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `asset_id` | `varchar` | Yes | — | Foreign key to `assets.id`; deletion behavior is RESTRICT |
| `maintenance_schedule_id` | `varchar` | No | — | Foreign key to `asset_maintenance_schedules.id`; deletion behavior is RESTRICT |
| `operational_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `supplier_id` | `varchar` | No | — | Foreign key to `suppliers.id`; deletion behavior is RESTRICT |
| `status` | `varchar` | Yes | `'scheduled'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `scheduled_for` | `date` | No | — | Stores the scheduled for value for this record. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `work_performed` | `TEXT` | No | — | Stores the work performed value for this record. |
| `cost_amount` | `numeric` | No | — | Numeric value representing cost amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `asset_maintenance_records_business_id_scheduled_for_status_index` on (`business_id`, `scheduled_for`, `status`); `asset_maintenance_records_business_id_asset_id_status_index` on (`business_id`, `asset_id`, `status`).

### `asset_maintenance_schedules`

Stores asset lifecycle records for maintenance schedules.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `asset_id` | `varchar` | Yes | — | Foreign key to `assets.id`; deletion behavior is RESTRICT |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `frequency` | `varchar` | Yes | — | Stores the frequency value for this record. |
| `interval` | `INTEGER` | Yes | `'1'` | Stores the interval value for this record. |
| `starts_on` | `date` | No | — | Calendar date associated with starts on. |
| `next_due_on` | `date` | No | — | Calendar date associated with next due on. |
| `instructions` | `TEXT` | No | — | Stores the instructions value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `asset_maintenance_schedules_business_id_next_due_on_status_index` on (`business_id`, `next_due_on`, `status`).

### `asset_media`

Stores asset lifecycle records for media.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `asset_id` | `varchar` | Yes | — | Foreign key to `assets.id`; deletion behavior is RESTRICT |
| `media_type` | `varchar` | Yes | `'image'` | Classification describing media type. |
| `storage_disk` | `varchar` | No | — | Stores the storage disk value for this record. |
| `storage_path` | `varchar` | No | — | Stores the storage path value for this record. |
| `external_url` | `varchar` | No | — | Location used to access external url; file contents normally live outside the relational database. |
| `title` | `varchar` | No | — | Short human-readable title. |
| `alt_text` | `varchar` | No | — | Stores the alt text value for this record. |
| `caption` | `TEXT` | No | — | Stores the caption value for this record. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `is_primary` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is primary. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `asset_media_asset_id_is_primary_index` on (`asset_id`, `is_primary`); `asset_media_asset_id_sort_order_index` on (`asset_id`, `sort_order`); `asset_media_asset_lookup` on (`business_id`, `property_id`, `asset_id`, `status`).

### `assets`

A durable physical item belonging to a property and retained throughout its maintenance history.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `supplier_id` | `varchar` | No | — | Foreign key to `suppliers.id`; deletion behavior is RESTRICT |
| `asset_code` | `varchar` | Yes | — | Stable identifier used for asset code. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `category` | `varchar` | Yes | — | Stores the category value for this record. |
| `serial_number` | `varchar` | No | — | Numeric value used for serial number. |
| `qr_identifier` | `varchar` | Yes | — | Stores the qr identifier value for this record. |
| `qr_token_hash` | `varchar` | No | — | Stores the qr token hash value for this record. |
| `qr_token_rotated_at` | `datetime` | No | — | Timestamp recording when qr token rotated at. |
| `purchase_date` | `date` | No | — | Calendar date associated with purchase date. |
| `warranty_expires_on` | `date` | No | — | Calendar date associated with warranty expires on. |
| `condition` | `varchar` | Yes | `'good'` | Stores the condition value for this record. |
| `replacement_value` | `numeric` | No | — | Numeric value representing replacement value; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `assets_qr_identifier_unique` on (`qr_identifier`) — unique; `assets_warranty_expires_on_index` on (`warranty_expires_on`); `assets_serial_number_status_index` on (`serial_number`, `status`); `assets_business_id_condition_status_index` on (`business_id`, `condition`, `status`); `assets_business_id_property_id_category_status_index` on (`business_id`, `property_id`, `category`, `status`); `assets_business_id_property_id_id_unique` on (`business_id`, `property_id`, `id`) — unique; `assets_business_id_id_unique` on (`business_id`, `id`) — unique; `assets_business_id_asset_code_unique` on (`business_id`, `asset_code`) — unique.

### `properties`

The permanent digital profile of an accommodation owned by a business.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `code` | `varchar` | Yes | — | Stable business-readable code used for searching and uniqueness. |
| `address` | `TEXT` | No | — | Structured or serialized address information. |
| `latitude` | `numeric` | No | — | Stores the latitude value for this record. |
| `longitude` | `numeric` | No | — | Stores the longitude value for this record. |
| `property_type` | `varchar` | Yes | — | Classification describing property type. |
| `capacity` | `INTEGER` | Yes | `'1'` | Stores the capacity value for this record. |
| `bedrooms` | `INTEGER` | Yes | `'0'` | Stores the bedrooms value for this record. |
| `bathrooms` | `numeric` | Yes | `'0'` | Stores the bathrooms value for this record. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `default_nightly_price` | `numeric` | No | — | Numeric value representing default nightly price; interpret it with the table's currency, scale, or scoring context. |
| `pricing_currency` | `varchar` | No | — | Stores the pricing currency value for this record. |
| `verification_status` | `varchar` | Yes | `'unverified'` | Domain-specific lifecycle or processing state for verification status. |
| `maintenance_status` | `varchar` | Yes | `'not_required'` | Domain-specific lifecycle or processing state for maintenance status. |
| `publication_status` | `varchar` | Yes | `'draft'` | Domain-specific lifecycle or processing state for publication status. |
| `readiness_status` | `varchar` | Yes | `'not_ready'` | Domain-specific lifecycle or processing state for readiness status. |
| `operational_status` | `varchar` | Yes | `'unavailable'` | Domain-specific lifecycle or processing state for operational status. |
| `operational_status_updated_at` | `datetime` | No | — | Timestamp recording when operational status updated at. |
| `information_completed_at` | `datetime` | No | — | Timestamp recording when information completed at. |
| `media_completed_at` | `datetime` | No | — | Timestamp recording when media completed at. |
| `verification_submitted_at` | `datetime` | No | — | Timestamp recording when verification submitted at. |
| `verified_at` | `datetime` | No | — | Timestamp recording when verified at. |
| `verified_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `published_at` | `datetime` | No | — | Timestamp recording when published at. |
| `published_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `archived_at` | `datetime` | No | — | Timestamp recording when archived at. |
| `owner_name` | `varchar` | No | — | Stores the owner name value for this record. |
| `manager_name` | `varchar` | No | — | Stores the manager name value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `properties_manager_name_status_index` on (`manager_name`, `status`); `properties_latitude_longitude_index` on (`latitude`, `longitude`); `properties_business_id_verification_status_index` on (`business_id`, `verification_status`); `properties_business_id_status_index` on (`business_id`, `status`); `properties_business_id_readiness_status_status_index` on (`business_id`, `readiness_status`, `status`); `properties_business_id_publication_status_status_index` on (`business_id`, `publication_status`, `status`); `properties_business_id_property_type_status_index` on (`business_id`, `property_type`, `status`); `properties_business_id_operational_status_status_index` on (`business_id`, `operational_status`, `status`); `properties_business_id_maintenance_status_index` on (`business_id`, `maintenance_status`); `properties_business_id_id_unique` on (`business_id`, `id`) — unique; `properties_business_id_code_unique` on (`business_id`, `code`) — unique.

### `property_access_instructions`

Stores property workspace records for access instructions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `title` | `varchar` | Yes | — | Short human-readable title. |
| `access_method` | `varchar` | No | — | Stores the access method value for this record. |
| `instructions` | `TEXT` | Yes | — | Stores the instructions value for this record. |
| `secret_payload` | `TEXT` | No | — | JSON or structured data containing secret payload. |
| `effective_at` | `datetime` | No | — | Timestamp recording when effective at. |
| `expires_at` | `datetime` | No | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `version` | `INTEGER` | Yes | `'1'` | Stores the version value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `property_access_current_lookup` on (`business_id`, `property_id`, `status`, `effective_at`); `property_access_instructions_property_id_version_unique` on (`property_id`, `version`) — unique.

### `property_amenities`

Stores property workspace records for amenities.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `amenity_id` | `varchar` | Yes | — | Foreign key to `amenities.id`; deletion behavior is RESTRICT |
| `details` | `TEXT` | No | — | Stores the details value for this record. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `property_amenities_property_id_amenity_id_unique` on (`property_id`, `amenity_id`) — unique; `property_amenities_business_id_property_id_status_index` on (`business_id`, `property_id`, `status`).

### `property_availability_blocks`

Stores property workspace records for availability blocks.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `external_calendar_connection_id` | `varchar` | No | — | Foreign key to `external_calendar_connections.id`; deletion behavior is RESTRICT |
| `source_type` | `varchar` | Yes | — | Classification describing source type. |
| `source_reference` | `varchar` | No | — | Stable identifier used for source reference. |
| `blocks_booking` | `tinyint(1)` | Yes | `'1'` | Stores the blocks booking value for this record. |
| `starts_on` | `date` | Yes | — | Calendar date associated with starts on. |
| `ends_on` | `date` | Yes | — | Calendar date associated with ends on. |
| `validation_status` | `varchar` | Yes | `'validated'` | Domain-specific lifecycle or processing state for validation status. |
| `validated_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `validated_at` | `datetime` | No | — | Timestamp recording when validated at. |
| `block_state` | `varchar` | Yes | `'active'` | Stores the block state value for this record. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `released_at` | `datetime` | No | — | Timestamp recording when released at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `property_availability_blocks_booking_id_block_state_index` on (`booking_id`, `block_state`); `availability_block_validation_lookup` on (`business_id`, `property_id`, `blocks_booking`, `validation_status`); `availability_overlap_lookup` on (`business_id`, `property_id`, `block_state`, `starts_on`, `ends_on`); `property_availability_blocks_business_id_id_unique` on (`business_id`, `id`) — unique.

### `property_availability_days`

Stores property workspace records for availability days.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `availability_block_id` | `varchar` | No | — | Foreign key to `property_availability_blocks.id`; deletion behavior is RESTRICT |
| `availability_date` | `date` | Yes | — | Calendar date associated with availability date. |
| `availability_state` | `varchar` | Yes | `'held'` | Stores the availability state value for this record. |
| `source_type` | `varchar` | Yes | — | Classification describing source type. |
| `source_reference` | `varchar` | No | — | Stable identifier used for source reference. |
| `hold_token_hash` | `varchar` | No | — | Stores the hold token hash value for this record. |
| `held_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `hold_expires_at` | `datetime` | No | — | Timestamp recording when hold expires at. |
| `active_key` | `varchar` | No | `'active'` | Stable identifier used for active key. |
| `allocated_at` | `datetime` | Yes | — | Timestamp recording when allocated at. |
| `released_at` | `datetime` | No | — | Timestamp recording when released at. |
| `release_reason` | `TEXT` | No | — | Stores the release reason value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `property_availability_days_availability_block_id_active_key_index` on (`availability_block_id`, `active_key`); `property_availability_days_booking_id_active_key_index` on (`booking_id`, `active_key`); `availability_expired_hold_lookup` on (`availability_state`, `hold_expires_at`, `active_key`); `property_availability_calendar_lookup` on (`business_id`, `property_id`, `availability_date`, `availability_state`); `property_active_availability_day_unique` on (`property_id`, `availability_date`, `active_key`) — unique.

### `property_cleaning_schedules`

Stores property workspace records for cleaning schedules.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `frequency` | `varchar` | Yes | `'between_stays'` | Stores the frequency value for this record. |
| `days_of_week` | `TEXT` | No | — | Stores the days of week value for this record. |
| `preferred_start_time` | `time` | No | — | Stores the preferred start time value for this record. |
| `preferred_end_time` | `time` | No | — | Stores the preferred end time value for this record. |
| `instructions` | `TEXT` | No | — | Stores the instructions value for this record. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `property_cleaning_schedules_property_id_frequency_status_index` on (`property_id`, `frequency`, `status`); `property_cleaning_schedules_business_id_property_id_status_index` on (`business_id`, `property_id`, `status`).

### `property_health_snapshots`

Stores property workspace records for health snapshots.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `snapshot_date` | `date` | Yes | — | Calendar date associated with snapshot date. |
| `period_type` | `varchar` | Yes | `'daily'` | Classification describing period type. |
| `score` | `INTEGER` | Yes | — | Stores the score value for this record. |
| `occupancy_score` | `INTEGER` | No | — | Numeric value representing occupancy score; interpret it with the table's currency, scale, or scoring context. |
| `guest_rating_score` | `INTEGER` | No | — | Numeric value representing guest rating score; interpret it with the table's currency, scale, or scoring context. |
| `maintenance_score` | `INTEGER` | No | — | Numeric value representing maintenance score; interpret it with the table's currency, scale, or scoring context. |
| `cleaning_score` | `INTEGER` | No | — | Numeric value representing cleaning score; interpret it with the table's currency, scale, or scoring context. |
| `revenue_growth_score` | `INTEGER` | No | — | Numeric value representing revenue growth score; interpret it with the table's currency, scale, or scoring context. |
| `asset_condition_score` | `INTEGER` | No | — | Numeric value representing asset condition score; interpret it with the table's currency, scale, or scoring context. |
| `inspection_score` | `INTEGER` | No | — | Numeric value representing inspection score; interpret it with the table's currency, scale, or scoring context. |
| `booking_conversion_score` | `INTEGER` | No | — | Numeric value representing booking conversion score; interpret it with the table's currency, scale, or scoring context. |
| `component_details` | `TEXT` | No | — | Stores the component details value for this record. |
| `supporting_metrics` | `TEXT` | No | — | Stores the supporting metrics value for this record. |
| `ai_explanation` | `TEXT` | No | — | Stores the ai explanation value for this record. |
| `recommended_improvements` | `TEXT` | No | — | Stores the recommended improvements value for this record. |
| `calculation_version` | `varchar` | Yes | — | Stores the calculation version value for this record. |
| `calculated_at` | `datetime` | Yes | — | Timestamp recording when calculated at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `property_health_history_lookup` on (`business_id`, `property_id`, `snapshot_date`, `score`); `property_health_period_unique` on (`business_id`, `property_id`, `period_type`, `snapshot_date`) — unique.

### `property_house_rules`

Stores property workspace records for house rules.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `is_mandatory` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether is mandatory. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `property_house_rules_property_id_sort_order_index` on (`property_id`, `sort_order`); `property_house_rules_business_id_property_id_status_index` on (`business_id`, `property_id`, `status`).

### `property_lifecycle_events`

Stores property workspace records for lifecycle events.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `event_type` | `varchar` | Yes | — | Classification describing event type. |
| `previous_state` | `varchar` | No | — | Stores the previous state value for this record. |
| `new_state` | `varchar` | No | — | Stores the new state value for this record. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `property_lifecycle_events_event_type_occurred_at_index` on (`event_type`, `occurred_at`); `property_lifecycle_events_business_id_property_id_occurred_at_index` on (`business_id`, `property_id`, `occurred_at`).

### `property_marketplace_listings`

Stores property workspace records for marketplace listings.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `slug` | `varchar` | Yes | — | Stores the slug value for this record. |
| `public_title` | `varchar` | Yes | — | Stores the public title value for this record. |
| `short_summary` | `varchar` | No | — | Stores the short summary value for this record. |
| `public_description` | `TEXT` | No | — | Stores the public description value for this record. |
| `check_in_time` | `time` | No | — | Stores the check in time value for this record. |
| `check_out_time` | `time` | No | — | Stores the check out time value for this record. |
| `instant_booking_enabled` | `tinyint(1)` | Yes | `'0'` | Stores the instant booking enabled value for this record. |
| `minimum_advance_notice_hours` | `INTEGER` | Yes | `'0'` | Stores the minimum advance notice hours value for this record. |
| `maximum_advance_booking_days` | `INTEGER` | No | — | Stores the maximum advance booking days value for this record. |
| `seo_title` | `varchar` | No | — | Stores the seo title value for this record. |
| `seo_description` | `varchar` | No | — | Stores the seo description value for this record. |
| `seo_keywords` | `TEXT` | No | — | Stores the seo keywords value for this record. |
| `seo_score` | `INTEGER` | No | — | Numeric value representing seo score; interpret it with the table's currency, scale, or scoring context. |
| `seo_score_details` | `TEXT` | No | — | Stores the seo score details value for this record. |
| `listing_quality_score` | `INTEGER` | No | — | Numeric value representing listing quality score; interpret it with the table's currency, scale, or scoring context. |
| `listing_quality_details` | `TEXT` | No | — | Stores the listing quality details value for this record. |
| `publication_status` | `varchar` | Yes | `'draft'` | Domain-specific lifecycle or processing state for publication status. |
| `is_publication_eligible` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is publication eligible. |
| `publication_eligibility_details` | `TEXT` | No | — | Stores the publication eligibility details value for this record. |
| `eligibility_checked_at` | `datetime` | No | — | Timestamp recording when eligibility checked at. |
| `published_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `published_at` | `datetime` | No | — | Timestamp recording when published at. |
| `unpublished_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `unpublished_at` | `datetime` | No | — | Timestamp recording when unpublished at. |
| `suspension_reason` | `TEXT` | No | — | Stores the suspension reason value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `property_marketplace_listings_slug_unique` on (`slug`) — unique; `property_marketplace_listings_is_publication_eligible_publication_status_index` on (`is_publication_eligible`, `publication_status`); `property_marketplace_listings_business_id_publication_status_status_index` on (`business_id`, `publication_status`, `status`); `property_marketplace_listings_property_id_unique` on (`property_id`) — unique.

### `property_media`

Stores property workspace records for media.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `media_type` | `varchar` | Yes | — | Classification describing media type. |
| `storage_disk` | `varchar` | No | — | Stores the storage disk value for this record. |
| `storage_path` | `varchar` | No | — | Stores the storage path value for this record. |
| `external_url` | `varchar` | No | — | Location used to access external url; file contents normally live outside the relational database. |
| `title` | `varchar` | No | — | Short human-readable title. |
| `alt_text` | `varchar` | No | — | Stores the alt text value for this record. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `is_primary` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is primary. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `property_media_property_id_sort_order_index` on (`property_id`, `sort_order`); `property_media_property_id_is_primary_index` on (`property_id`, `is_primary`); `property_media_business_id_property_id_media_type_status_index` on (`business_id`, `property_id`, `media_type`, `status`).

### `property_pricing_rules`

Stores property workspace records for pricing rules.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `rule_type` | `varchar` | Yes | `'date_range'` | Classification describing rule type. |
| `adjustment_type` | `varchar` | Yes | `'fixed_price'` | Classification describing adjustment type. |
| `adjustment_value` | `numeric` | Yes | — | Numeric value representing adjustment value; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `starts_on` | `date` | No | — | Calendar date associated with starts on. |
| `ends_on` | `date` | No | — | Calendar date associated with ends on. |
| `applicable_weekdays` | `TEXT` | No | — | Stores the applicable weekdays value for this record. |
| `minimum_stay_nights` | `INTEGER` | No | — | Stores the minimum stay nights value for this record. |
| `maximum_stay_nights` | `INTEGER` | No | — | Stores the maximum stay nights value for this record. |
| `minimum_advance_booking_days` | `INTEGER` | No | — | Stores the minimum advance booking days value for this record. |
| `maximum_advance_booking_days` | `INTEGER` | No | — | Stores the maximum advance booking days value for this record. |
| `priority` | `INTEGER` | Yes | `'100'` | Stores the priority value for this record. |
| `is_stackable` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is stackable. |
| `effective_at` | `datetime` | No | — | Timestamp recording when effective at. |
| `expires_at` | `datetime` | No | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `property_pricing_rules_effective_at_expires_at_status_index` on (`effective_at`, `expires_at`, `status`); `property_pricing_rules_property_id_rule_type_priority_status_index` on (`property_id`, `rule_type`, `priority`, `status`); `property_pricing_date_lookup` on (`business_id`, `property_id`, `status`, `starts_on`, `ends_on`).

### `property_promotions`

Stores property workspace records for promotions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `public_description` | `TEXT` | No | — | Stores the public description value for this record. |
| `promotion_type` | `varchar` | Yes | `'manual'` | Classification describing promotion type. |
| `discount_type` | `varchar` | Yes | `'percentage'` | Classification describing discount type. |
| `discount_value` | `numeric` | Yes | — | Numeric value representing discount value; interpret it with the table's currency, scale, or scoring context. |
| `coupon_code` | `varchar` | No | — | Stable identifier used for coupon code. |
| `booking_window_starts_on` | `date` | No | — | Calendar date associated with booking window starts on. |
| `booking_window_ends_on` | `date` | No | — | Calendar date associated with booking window ends on. |
| `stay_window_starts_on` | `date` | No | — | Calendar date associated with stay window starts on. |
| `stay_window_ends_on` | `date` | No | — | Calendar date associated with stay window ends on. |
| `applicable_weekdays` | `TEXT` | No | — | Stores the applicable weekdays value for this record. |
| `minimum_stay_nights` | `INTEGER` | No | — | Stores the minimum stay nights value for this record. |
| `minimum_booking_amount` | `numeric` | No | — | Numeric value representing minimum booking amount; interpret it with the table's currency, scale, or scoring context. |
| `maximum_discount_amount` | `numeric` | No | — | Numeric value representing maximum discount amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `total_usage_limit` | `INTEGER` | No | — | Stores the total usage limit value for this record. |
| `per_guest_usage_limit` | `INTEGER` | No | — | Stores the per guest usage limit value for this record. |
| `is_stackable` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is stackable. |
| `publication_status` | `varchar` | Yes | `'draft'` | Domain-specific lifecycle or processing state for publication status. |
| `effective_at` | `datetime` | No | — | Timestamp recording when effective at. |
| `expires_at` | `datetime` | No | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `property_promotions_stay_window_starts_on_stay_window_ends_on_index` on (`stay_window_starts_on`, `stay_window_ends_on`); `property_promotions_booking_window_starts_on_booking_window_ends_on_index` on (`booking_window_starts_on`, `booking_window_ends_on`); `property_promotions_publication_lookup` on (`business_id`, `property_id`, `publication_status`, `status`); `property_promotions_business_id_coupon_code_unique` on (`business_id`, `coupon_code`) — unique.

### `property_staff_assignments`

Stores property workspace records for staff assignments.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `employee_id` | `varchar` | Yes | — | Related business employee UUID. |
| `assignment_role` | `varchar` | Yes | `'other'` | Stores the assignment role value for this record. |
| `responsibilities` | `TEXT` | No | — | Stores the responsibilities value for this record. |
| `is_primary` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is primary. |
| `starts_on` | `date` | No | — | Calendar date associated with starts on. |
| `ends_on` | `date` | No | — | Calendar date associated with ends on. |
| `assignment_status` | `varchar` | Yes | `'active'` | Domain-specific lifecycle or processing state for assignment status. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `property_staff_assignments_starts_on_ends_on_index` on (`starts_on`, `ends_on`); `property_primary_staff_lookup` on (`property_id`, `assignment_role`, `is_primary`, `assignment_status`); `property_staff_employee_lookup` on (`business_id`, `employee_id`, `assignment_status`); `property_staff_property_lookup` on (`business_id`, `property_id`, `assignment_role`, `assignment_status`).

### `suppliers`

Stores suppliers records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `email` | `varchar` | No | — | Email address associated with the record. |
| `phone_number` | `varchar` | No | — | Telephone number associated with the record. |
| `address` | `TEXT` | No | — | Structured or serialized address information. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `suppliers_business_id_id_unique` on (`business_id`, `id`) — unique; `suppliers_business_id_name_status_index` on (`business_id`, `name`, `status`).

## Events, workflows, and approvals

### `approval_actions`

Stores approval-engine records for actions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `approval_request_id` | `varchar` | Yes | — | Foreign key to `approval_requests.id`; deletion behavior is RESTRICT |
| `approval_request_step_id` | `varchar` | No | — | Foreign key to `approval_request_steps.id`; deletion behavior is RESTRICT |
| `approval_delegation_id` | `varchar` | No | — | Foreign key to `approval_delegations.id`; deletion behavior is RESTRICT |
| `actor_user_id` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `action_type` | `varchar` | Yes | — | Classification describing action type. |
| `comments` | `TEXT` | No | — | Stores the comments value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `acted_at` | `datetime` | Yes | — | Timestamp recording when acted at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `approval_actions_approval_request_id_acted_at_index` on (`approval_request_id`, `acted_at`).

### `approval_delegations`

Stores approval-engine records for delegations.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `approval_workflow_id` | `varchar` | No | — | Foreign key to `approval_workflows.id`; deletion behavior is RESTRICT |
| `delegator_user_id` | `varchar` | Yes | — | Foreign key to `users.id`; deletion behavior is RESTRICT |
| `delegate_user_id` | `varchar` | Yes | — | Foreign key to `users.id`; deletion behavior is RESTRICT |
| `starts_at` | `datetime` | Yes | — | Timestamp recording when starts at. |
| `ends_at` | `datetime` | Yes | — | Timestamp recording when ends at. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `revoked_at` | `datetime` | No | — | Timestamp recording when revoked at. |
| `revoked_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `approval_delegations_active_index` on (`business_id`, `delegator_user_id`, `starts_at`, `ends_at`).

### `approval_request_steps`

Stores approval-engine records for request steps.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `approval_request_id` | `varchar` | Yes | — | Foreign key to `approval_requests.id`; deletion behavior is RESTRICT |
| `approval_workflow_step_id` | `varchar` | No | — | Foreign key to `approval_workflow_steps.id`; deletion behavior is RESTRICT |
| `step_order` | `INTEGER` | Yes | — | Numeric value used for step order. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `role_id` | `varchar` | No | — | Foreign key to `roles.id`; deletion behavior is SET NULL |
| `permission_id` | `varchar` | No | — | Foreign key to `permissions.id`; deletion behavior is SET NULL |
| `required_approvals` | `INTEGER` | Yes | `'1'` | Stores the required approvals value for this record. |
| `approval_count` | `INTEGER` | Yes | `'0'` | Numeric value used for approval count. |
| `step_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for step status. |
| `due_at` | `datetime` | No | — | Timestamp recording when due at. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `definition_snapshot` | `TEXT` | No | — | JSON or structured data containing definition snapshot. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `approval_request_steps_approval_request_id_step_order_unique` on (`approval_request_id`, `step_order`) — unique.

### `approval_requests`

A particular approval lifecycle for a polymorphic business subject.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `approval_workflow_id` | `varchar` | Yes | — | Foreign key to `approval_workflows.id`; deletion behavior is RESTRICT |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `subject_type` | `varchar` | Yes | — | Classification describing subject type. |
| `subject_id` | `varchar` | Yes | — | Stores the subject id value for this record. |
| `requested_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `request_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for request status. |
| `current_step_order` | `INTEGER` | No | — | Numeric value used for current step order. |
| `subject_snapshot` | `TEXT` | Yes | — | JSON or structured data containing subject snapshot. |
| `request_reason` | `TEXT` | No | — | Stores the request reason value for this record. |
| `submitted_at` | `datetime` | Yes | — | Timestamp recording when submitted at. |
| `due_at` | `datetime` | No | — | Timestamp recording when due at. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `cancelled_at` | `datetime` | No | — | Timestamp recording when cancelled at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `approval_requests_business_id_request_status_due_at_index` on (`business_id`, `request_status`, `due_at`); `approval_requests_subject_type_subject_id_index` on (`subject_type`, `subject_id`); `approval_requests_business_id_reference_unique` on (`business_id`, `reference`) — unique.

### `approval_workflow_steps`

Stores approval-engine records for workflow steps.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `approval_workflow_id` | `varchar` | Yes | — | Foreign key to `approval_workflows.id`; deletion behavior is CASCADE |
| `step_order` | `INTEGER` | Yes | — | Numeric value used for step order. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `role_id` | `varchar` | No | — | Foreign key to `roles.id`; deletion behavior is SET NULL |
| `permission_id` | `varchar` | No | — | Foreign key to `permissions.id`; deletion behavior is SET NULL |
| `required_approvals` | `INTEGER` | Yes | `'1'` | Stores the required approvals value for this record. |
| `minimum_amount` | `numeric` | No | — | Numeric value representing minimum amount; interpret it with the table's currency, scale, or scoring context. |
| `maximum_amount` | `numeric` | No | — | Numeric value representing maximum amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `escalate_after_minutes` | `INTEGER` | No | — | Stores the escalate after minutes value for this record. |
| `conditions` | `TEXT` | No | — | Stores the conditions value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `approval_workflow_steps_approval_workflow_id_step_order_unique` on (`approval_workflow_id`, `step_order`) — unique.

### `approval_workflows`

A reusable, versioned approval definition for expenses, refunds, publication, AI actions, or other subjects.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `workflow_key` | `varchar` | Yes | — | Stable identifier used for workflow key. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `subject_type` | `varchar` | Yes | — | Classification describing subject type. |
| `version` | `INTEGER` | Yes | `'1'` | Stores the version value for this record. |
| `conditions` | `TEXT` | No | — | Stores the conditions value for this record. |
| `is_system` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is system. |
| `is_active` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether is active. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `approval_workflows_business_id_subject_type_is_active_index` on (`business_id`, `subject_type`, `is_active`); `approval_workflows_business_id_workflow_key_version_unique` on (`business_id`, `workflow_key`, `version`) — unique.

### `domain_events`

The durable event/outbox record used to coordinate event-driven behavior across modules.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `event_name` | `varchar` | Yes | — | Stores the event name value for this record. |
| `aggregate_type` | `varchar` | Yes | — | Classification describing aggregate type. |
| `aggregate_id` | `varchar` | Yes | — | Stores the aggregate id value for this record. |
| `correlation_id` | `varchar` | No | — | Stores the correlation id value for this record. |
| `causation_id` | `varchar` | No | — | Stores the causation id value for this record. |
| `idempotency_key` | `varchar` | Yes | — | Stable identifier used for idempotency key. |
| `payload` | `TEXT` | Yes | — | JSON or structured data containing payload. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `publication_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for publication status. |
| `attempt_count` | `INTEGER` | Yes | `'0'` | Numeric value used for attempt count. |
| `published_at` | `datetime` | No | — | Timestamp recording when published at. |
| `next_attempt_at` | `datetime` | No | — | Timestamp recording when next attempt at. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `domain_events_idempotency_key_unique` on (`idempotency_key`) — unique; `domain_events_business_id_event_name_occurred_at_index` on (`business_id`, `event_name`, `occurred_at`); `domain_events_aggregate_history` on (`aggregate_type`, `aggregate_id`, `occurred_at`); `domain_events_publication_queue` on (`publication_status`, `next_attempt_at`, `occurred_at`).

### `workflow_runs`

One historical execution of a workflow in response to a domain event.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `domain_event_id` | `varchar` | Yes | — | Foreign key to `domain_events.id`; deletion behavior is RESTRICT |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `workflow_key` | `varchar` | Yes | — | Stable identifier used for workflow key. |
| `workflow_version` | `INTEGER` | Yes | `'1'` | Stores the workflow version value for this record. |
| `idempotency_key` | `varchar` | Yes | — | Stable identifier used for idempotency key. |
| `execution_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for execution status. |
| `input` | `TEXT` | No | — | Stores the input value for this record. |
| `output` | `TEXT` | No | — | Stores the output value for this record. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `next_retry_at` | `datetime` | No | — | Timestamp recording when next retry at. |
| `error_message` | `TEXT` | No | — | Stores the error message value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `workflow_template_id` | `varchar` | No | — | Foreign key to `workflow_templates.id`; deletion behavior is RESTRICT |

**Indexes:** `workflow_runs_idempotency_key_unique` on (`idempotency_key`) — unique; `workflow_runs_execution_status_next_retry_at_index` on (`execution_status`, `next_retry_at`); `workflow_runs_business_id_workflow_key_created_at_index` on (`business_id`, `workflow_key`, `created_at`).

### `workflow_steps`

Stores workflow-engine records for steps.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `workflow_run_id` | `varchar` | Yes | — | Foreign key to `workflow_runs.id`; deletion behavior is RESTRICT |
| `step_order` | `INTEGER` | Yes | — | Numeric value used for step order. |
| `step_key` | `varchar` | Yes | — | Stable identifier used for step key. |
| `handler_key` | `varchar` | Yes | — | Stable identifier used for handler key. |
| `execution_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for execution status. |
| `input` | `TEXT` | No | — | Stores the input value for this record. |
| `output` | `TEXT` | No | — | Stores the output value for this record. |
| `attempt_count` | `INTEGER` | Yes | `'0'` | Numeric value used for attempt count. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `next_retry_at` | `datetime` | No | — | Timestamp recording when next retry at. |
| `error_message` | `TEXT` | No | — | Stores the error message value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `workflow_template_step_id` | `varchar` | No | — | Foreign key to `workflow_template_steps.id`; deletion behavior is RESTRICT |

**Indexes:** `workflow_steps_workflow_run_id_step_order_unique` on (`workflow_run_id`, `step_order`) — unique; `workflow_steps_execution_status_next_retry_at_index` on (`execution_status`, `next_retry_at`).

### `workflow_template_steps`

Stores workflow-engine records for template steps.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `workflow_template_id` | `varchar` | Yes | — | Foreign key to `workflow_templates.id`; deletion behavior is CASCADE |
| `step_key` | `varchar` | Yes | — | Stable identifier used for step key. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `step_order` | `INTEGER` | Yes | — | Numeric value used for step order. |
| `step_type` | `varchar` | Yes | `'task'` | Classification describing step type. |
| `task_type` | `varchar` | No | — | Classification describing task type. |
| `responsible_role_id` | `varchar` | No | — | Foreign key to `roles.id`; deletion behavior is SET NULL |
| `responsible_department_id` | `varchar` | No | — | Foreign key to `departments.id`; deletion behavior is SET NULL |
| `estimated_duration_minutes` | `INTEGER` | No | — | Stores the estimated duration minutes value for this record. |
| `sla_minutes` | `INTEGER` | No | — | Stores the sla minutes value for this record. |
| `requires_verification` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether requires verification. |
| `requires_approval` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether requires approval. |
| `checklist_template` | `TEXT` | No | — | Stores the checklist template value for this record. |
| `failure_configuration` | `TEXT` | No | — | JSON or structured data containing failure configuration. |
| `escalation_configuration` | `TEXT` | No | — | JSON or structured data containing escalation configuration. |
| `configuration` | `TEXT` | No | — | JSON or structured data containing configuration. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `workflow_template_steps_workflow_template_id_step_key_unique` on (`workflow_template_id`, `step_key`) — unique; `workflow_template_steps_workflow_template_id_step_order_unique` on (`workflow_template_id`, `step_order`) — unique.

### `workflow_templates`

A reusable, versioned system or business workflow definition.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `template_key` | `varchar` | Yes | — | Stable identifier used for template key. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `version` | `INTEGER` | Yes | `'1'` | Stores the version value for this record. |
| `trigger_type` | `varchar` | Yes | `'manual'` | Classification describing trigger type. |
| `trigger_event` | `varchar` | No | — | Stores the trigger event value for this record. |
| `trigger_configuration` | `TEXT` | No | — | JSON or structured data containing trigger configuration. |
| `is_system` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is system. |
| `is_active` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether is active. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `workflow_templates_trigger_event_is_active_index` on (`trigger_event`, `is_active`); `workflow_templates_business_id_is_active_status_index` on (`business_id`, `is_active`, `status`); `workflow_templates_business_id_template_key_version_unique` on (`business_id`, `template_key`, `version`) — unique.

## Governance and framework support

### `audit_events`

An immutable record of a significant action, its actor, source, affected entity, and before/after data.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `auditable_type` | `varchar` | No | — | Classification describing auditable type. |
| `auditable_id` | `varchar` | No | — | Stores the auditable id value for this record. |
| `event_type` | `varchar` | Yes | — | Classification describing event type. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `before_values` | `TEXT` | No | — | Numeric value representing before values; interpret it with the table's currency, scale, or scoring context. |
| `after_values` | `TEXT` | No | — | Numeric value representing after values; interpret it with the table's currency, scale, or scoring context. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `ip_address` | `varchar` | No | — | Stores the ip address value for this record. |
| `user_agent` | `TEXT` | No | — | Stores the user agent value for this record. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `source_channel` | `varchar` | No | — | Stores the source channel value for this record. |
| `actor_type` | `varchar` | Yes | `'user'` | Classification describing actor type. |
| `correlation_id` | `varchar` | No | — | Stores the correlation id value for this record. |
| `request_id` | `varchar` | No | — | Stores the request id value for this record. |
| `device_identifier` | `varchar` | No | — | Stores the device identifier value for this record. |

**Indexes:** `audit_events_source_channel_occurred_at_index` on (`source_channel`, `occurred_at`); `audit_events_correlation_id_occurred_at_index` on (`correlation_id`, `occurred_at`); `audit_events_created_by_occurred_at_index` on (`created_by`, `occurred_at`); `audit_events_business_id_event_type_occurred_at_index` on (`business_id`, `event_type`, `occurred_at`); `audit_events_auditable_type_auditable_id_index` on (`auditable_type`, `auditable_id`).

### `data_retention_executions`

Stores data-governance records for retention executions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `data_retention_policy_id` | `varchar` | Yes | — | Foreign key to `data_retention_policies.id`; deletion behavior is RESTRICT |
| `execution_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for execution status. |
| `cutoff_at` | `datetime` | Yes | — | Timestamp recording when cutoff at. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `records_examined` | `INTEGER` | Yes | `'0'` | Stores the records examined value for this record. |
| `records_affected` | `INTEGER` | Yes | `'0'` | Stores the records affected value for this record. |
| `records_held` | `INTEGER` | Yes | `'0'` | Stores the records held value for this record. |
| `errors` | `TEXT` | No | — | Stores the errors value for this record. |
| `executed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `data_retention_executions_execution_status_started_at_index` on (`execution_status`, `started_at`).

### `data_retention_policies`

Stores data-governance records for retention policies.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `policy_key` | `varchar` | Yes | — | Stable identifier used for policy key. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `data_category` | `varchar` | Yes | — | Stores the data category value for this record. |
| `retention_days` | `INTEGER` | No | — | Stores the retention days value for this record. |
| `retention_trigger` | `varchar` | Yes | `'record_created'` | Stores the retention trigger value for this record. |
| `terminal_action` | `varchar` | Yes | `'anonymize'` | Stores the terminal action value for this record. |
| `legal_basis` | `TEXT` | No | — | Stores the legal basis value for this record. |
| `allow_legal_hold` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether allow legal hold. |
| `is_active` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether is active. |
| `configuration` | `TEXT` | No | — | JSON or structured data containing configuration. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `data_retention_policies_data_category_is_active_index` on (`data_category`, `is_active`); `data_retention_policies_business_id_policy_key_unique` on (`business_id`, `policy_key`) — unique.

### `data_subject_requests`

Stores data-governance records for subject requests.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `user_id` | `varchar` | No | — | Related platform user UUID. |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `request_type` | `varchar` | Yes | — | Classification describing request type. |
| `request_status` | `varchar` | Yes | `'received'` | Domain-specific lifecycle or processing state for request status. |
| `requester_name` | `varchar` | Yes | — | Stores the requester name value for this record. |
| `requester_email` | `varchar` | Yes | — | Stores the requester email value for this record. |
| `identity_verified_at` | `datetime` | No | — | Timestamp recording when identity verified at. |
| `received_at` | `datetime` | Yes | — | Timestamp recording when received at. |
| `due_at` | `datetime` | No | — | Timestamp recording when due at. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `resolution_summary` | `TEXT` | No | — | Stores the resolution summary value for this record. |
| `legal_hold_reason` | `TEXT` | No | — | Stores the legal hold reason value for this record. |
| `request_data` | `TEXT` | No | — | Stores the request data value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `data_subject_requests_reference_unique` on (`reference`) — unique; `data_subject_requests_business_id_request_status_due_at_index` on (`business_id`, `request_status`, `due_at`).

### `migrations`

Stores migrations records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `INTEGER` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `migration` | `varchar` | Yes | — | Stores the migration value for this record. |
| `batch` | `INTEGER` | Yes | — | Stores the batch value for this record. |

## Bookings and guest stay

### `booking_cancellations`

Stores booking workspace records for cancellations.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `cancellation_policy_id` | `varchar` | No | — | Foreign key to `cancellation_policies.id`; deletion behavior is RESTRICT |
| `requested_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `requester_type` | `varchar` | Yes | — | Classification describing requester type. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `policy_snapshot` | `TEXT` | Yes | — | JSON or structured data containing policy snapshot. |
| `refund_amount` | `numeric` | Yes | `'0'` | Numeric value representing refund amount; interpret it with the table's currency, scale, or scoring context. |
| `cancellation_fee` | `numeric` | Yes | `'0'` | Stores the cancellation fee value for this record. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `refund_payment_id` | `varchar` | No | — | Foreign key to `payments.id`; deletion behavior is RESTRICT |
| `approved_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `requested_at` | `datetime` | Yes | — | Timestamp recording when requested at. |
| `approved_at` | `datetime` | No | — | Timestamp recording when approved at. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `status` | `varchar` | Yes | `'pending'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_cancellations_business_id_requested_at_status_index` on (`business_id`, `requested_at`, `status`); `booking_cancellations_business_id_booking_id_status_index` on (`business_id`, `booking_id`, `status`).

### `booking_channel_links`

Stores booking workspace records for channel links.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `provider` | `varchar` | Yes | — | Stores the provider value for this record. |
| `external_booking_id` | `varchar` | Yes | — | Stores the external booking id value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_channel_links_business_id_booking_id_index` on (`business_id`, `booking_id`); `booking_channel_links_provider_external_booking_id_unique` on (`provider`, `external_booking_id`) — unique.

### `booking_check_ins`

Stores booking workspace records for check ins.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `identity_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for identity status. |
| `balance_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for balance status. |
| `security_deposit_status` | `varchar` | Yes | `'not_required'` | Domain-specific lifecycle or processing state for security deposit status. |
| `access_method` | `varchar` | No | — | Stores the access method value for this record. |
| `access_reference` | `varchar` | No | — | Stable identifier used for access reference. |
| `checklist` | `TEXT` | No | — | Stores the checklist value for this record. |
| `blocking_issues` | `TEXT` | No | — | Stores the blocking issues value for this record. |
| `completed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `status` | `varchar` | Yes | `'pending'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_check_ins_business_id_status_index` on (`business_id`, `status`); `booking_check_ins_booking_id_unique` on (`booking_id`) — unique.

### `booking_check_outs`

Stores booking workspace records for check outs.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `actual_departure_at` | `datetime` | No | — | Timestamp recording when actual departure at. |
| `access_return_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for access return status. |
| `room_condition` | `varchar` | No | — | Stores the room condition value for this record. |
| `damage_status` | `varchar` | Yes | `'none'` | Domain-specific lifecycle or processing state for damage status. |
| `damage_notes` | `TEXT` | No | — | Stores the damage notes value for this record. |
| `deposit_release_status` | `varchar` | Yes | `'not_required'` | Domain-specific lifecycle or processing state for deposit release status. |
| `deposit_release_amount` | `numeric` | No | — | Numeric value representing deposit release amount; interpret it with the table's currency, scale, or scoring context. |
| `handover_notes` | `TEXT` | No | — | Stores the handover notes value for this record. |
| `completed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `status` | `varchar` | Yes | `'pending'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_check_outs_business_id_status_index` on (`business_id`, `status`); `booking_check_outs_booking_id_unique` on (`booking_id`) — unique.

### `booking_date_changes`

Stores booking workspace records for date changes.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `previous_arrival_date` | `date` | Yes | — | Calendar date associated with previous arrival date. |
| `previous_departure_date` | `date` | Yes | — | Calendar date associated with previous departure date. |
| `new_arrival_date` | `date` | Yes | — | Calendar date associated with new arrival date. |
| `new_departure_date` | `date` | Yes | — | Calendar date associated with new departure date. |
| `change_type` | `varchar` | Yes | `'reschedule'` | Classification describing change type. |
| `availability_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for availability status. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `approved_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `approved_at` | `datetime` | No | — | Timestamp recording when approved at. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_date_changes_business_id_booking_id_occurred_at_index` on (`business_id`, `booking_id`, `occurred_at`).

### `booking_disputes`

Stores booking workspace records for disputes.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `booking_incident_id` | `varchar` | No | — | Foreign key to `booking_incidents.id`; deletion behavior is RESTRICT |
| `financial_document_id` | `varchar` | No | — | Foreign key to `booking_financial_documents.id`; deletion behavior is RESTRICT |
| `payment_id` | `varchar` | No | — | Foreign key to `payments.id`; deletion behavior is RESTRICT |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `dispute_type` | `varchar` | Yes | — | Classification describing dispute type. |
| `opened_by_type` | `varchar` | Yes | — | Classification describing opened by type. |
| `opened_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `description` | `TEXT` | Yes | — | Longer human-readable explanation of the record. |
| `disputed_amount` | `numeric` | No | — | Numeric value representing disputed amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `dispute_status` | `varchar` | Yes | `'open'` | Domain-specific lifecycle or processing state for dispute status. |
| `resolution` | `TEXT` | No | — | Stores the resolution value for this record. |
| `resolved_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `opened_at` | `datetime` | Yes | — | Timestamp recording when opened at. |
| `resolved_at` | `datetime` | No | — | Timestamp recording when resolved at. |
| `closed_at` | `datetime` | No | — | Timestamp recording when closed at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_disputes_business_id_dispute_status_opened_at_index` on (`business_id`, `dispute_status`, `opened_at`); `booking_disputes_business_id_booking_id_dispute_status_index` on (`business_id`, `booking_id`, `dispute_status`); `booking_disputes_business_id_reference_unique` on (`business_id`, `reference`) — unique.

### `booking_financial_allocations`

Stores booking workspace records for financial allocations.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `payment_id` | `varchar` | No | — | Foreign key to `payments.id`; deletion behavior is RESTRICT |
| `expense_id` | `varchar` | No | — | Foreign key to `expenses.id`; deletion behavior is RESTRICT |
| `allocation_type` | `varchar` | Yes | — | Classification describing allocation type. |
| `direction` | `varchar` | Yes | — | Stores the direction value for this record. |
| `amount` | `numeric` | Yes | — | Numeric value representing amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `recognized_on` | `date` | Yes | — | Calendar date associated with recognized on. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_financial_allocations_business_id_recognized_on_direction_index` on (`business_id`, `recognized_on`, `direction`); `booking_financial_allocations_business_id_booking_id_allocation_type_index` on (`business_id`, `booking_id`, `allocation_type`).

### `booking_financial_document_items`

Stores booking workspace records for financial document items.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `financial_document_id` | `varchar` | Yes | — | Foreign key to `booking_financial_documents.id`; deletion behavior is RESTRICT |
| `item_type` | `varchar` | Yes | `'accommodation'` | Classification describing item type. |
| `description` | `varchar` | Yes | — | Longer human-readable explanation of the record. |
| `quantity` | `numeric` | Yes | `'1'` | Stores the quantity value for this record. |
| `unit_amount` | `numeric` | Yes | — | Numeric value representing unit amount; interpret it with the table's currency, scale, or scoring context. |
| `subtotal_amount` | `numeric` | Yes | — | Numeric value representing subtotal amount; interpret it with the table's currency, scale, or scoring context. |
| `discount_amount` | `numeric` | Yes | `'0'` | Numeric value representing discount amount; interpret it with the table's currency, scale, or scoring context. |
| `tax_rate` | `numeric` | Yes | `'0'` | Numeric value representing tax rate; interpret it with the table's currency, scale, or scoring context. |
| `tax_amount` | `numeric` | Yes | `'0'` | Numeric value representing tax amount; interpret it with the table's currency, scale, or scoring context. |
| `total_amount` | `numeric` | Yes | — | Numeric value representing total amount; interpret it with the table's currency, scale, or scoring context. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_financial_document_items_business_id_item_type_created_at_index` on (`business_id`, `item_type`, `created_at`); `booking_financial_document_items_financial_document_id_sort_order_index` on (`financial_document_id`, `sort_order`).

### `booking_financial_documents`

Stores booking workspace records for financial documents.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `source_document_id` | `varchar` | No | — | Foreign key to `booking_financial_documents.id`; deletion behavior is RESTRICT |
| `payment_id` | `varchar` | No | — | Foreign key to `payments.id`; deletion behavior is RESTRICT |
| `document_number` | `varchar` | Yes | — | Numeric value used for document number. |
| `document_type` | `varchar` | Yes | `'quotation'` | Classification describing document type. |
| `document_status` | `varchar` | Yes | `'draft'` | Domain-specific lifecycle or processing state for document status. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `subtotal_amount` | `numeric` | Yes | `'0'` | Numeric value representing subtotal amount; interpret it with the table's currency, scale, or scoring context. |
| `discount_amount` | `numeric` | Yes | `'0'` | Numeric value representing discount amount; interpret it with the table's currency, scale, or scoring context. |
| `tax_amount` | `numeric` | Yes | `'0'` | Numeric value representing tax amount; interpret it with the table's currency, scale, or scoring context. |
| `total_amount` | `numeric` | Yes | `'0'` | Numeric value representing total amount; interpret it with the table's currency, scale, or scoring context. |
| `paid_amount` | `numeric` | Yes | `'0'` | Numeric value representing paid amount; interpret it with the table's currency, scale, or scoring context. |
| `balance_amount` | `numeric` | Yes | `'0'` | Numeric value representing balance amount; interpret it with the table's currency, scale, or scoring context. |
| `issuer_snapshot` | `TEXT` | Yes | — | JSON or structured data containing issuer snapshot. |
| `recipient_snapshot` | `TEXT` | Yes | — | JSON or structured data containing recipient snapshot. |
| `booking_snapshot` | `TEXT` | Yes | — | JSON or structured data containing booking snapshot. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `terms` | `TEXT` | No | — | Stores the terms value for this record. |
| `issued_on` | `date` | No | — | Calendar date on which the document, certificate, or item was issued. |
| `due_on` | `date` | No | — | Calendar date associated with due on. |
| `sent_at` | `datetime` | No | — | Timestamp recording when sent at. |
| `paid_at` | `datetime` | No | — | Timestamp recording when paid at. |
| `voided_at` | `datetime` | No | — | Timestamp recording when voided at. |
| `voided_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `void_reason` | `TEXT` | No | — | Stores the void reason value for this record. |
| `storage_disk` | `varchar` | No | — | Stores the storage disk value for this record. |
| `storage_path` | `varchar` | No | — | Stores the storage path value for this record. |
| `checksum` | `varchar` | No | — | Stores the checksum value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_financial_documents_business_id_due_on_document_status_index` on (`business_id`, `due_on`, `document_status`); `booking_financial_document_lookup` on (`business_id`, `booking_id`, `document_type`, `document_status`); `booking_financial_documents_business_id_id_unique` on (`business_id`, `id`) — unique; `booking_financial_documents_business_id_document_number_unique` on (`business_id`, `document_number`) — unique.

### `booking_guests`

Stores booking workspace records for guests.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `user_id` | `varchar` | No | — | Related platform user UUID. |
| `guest_type` | `varchar` | Yes | `'adult'` | Classification describing guest type. |
| `is_primary` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is primary. |
| `full_name` | `varchar` | Yes | — | Stores the full name value for this record. |
| `email` | `varchar` | No | — | Email address associated with the record. |
| `phone_number` | `varchar` | No | — | Telephone number associated with the record. |
| `nationality` | `varchar` | No | — | Stores the nationality value for this record. |
| `date_of_birth` | `date` | No | — | Stores the date of birth value for this record. |
| `identity_verification_status` | `varchar` | Yes | `'unverified'` | Domain-specific lifecycle or processing state for identity verification status. |
| `preferences` | `TEXT` | No | — | JSON or structured data containing preferences. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `booking_guests_email_phone_number_index` on (`email`, `phone_number`); `booking_guests_business_id_booking_id_is_primary_status_index` on (`business_id`, `booking_id`, `is_primary`, `status`); `booking_guests_booking_id_user_id_unique` on (`booking_id`, `user_id`) — unique.

### `booking_incidents`

Stores booking workspace records for incidents.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `reported_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `operational_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `document_id` | `varchar` | No | — | Foreign key to `documents.id`; deletion behavior is RESTRICT |
| `incident_type` | `varchar` | Yes | — | Classification describing incident type. |
| `severity` | `varchar` | Yes | `'normal'` | Stores the severity value for this record. |
| `description` | `TEXT` | Yes | — | Longer human-readable explanation of the record. |
| `financial_impact` | `numeric` | No | — | Stores the financial impact value for this record. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `resolution` | `TEXT` | No | — | Stores the resolution value for this record. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `resolved_at` | `datetime` | No | — | Timestamp recording when resolved at. |
| `status` | `varchar` | Yes | `'open'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_incidents_severity_status_index` on (`severity`, `status`); `booking_incidents_business_id_booking_id_status_index` on (`business_id`, `booking_id`, `status`); `booking_incidents_business_id_id_unique` on (`business_id`, `id`) — unique.

### `booking_interactions`

Stores booking workspace records for interactions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `user_id` | `varchar` | No | — | Related platform user UUID. |
| `recipient_user_id` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `interaction_type` | `varchar` | Yes | — | Classification describing interaction type. |
| `direction` | `varchar` | No | — | Stores the direction value for this record. |
| `channel` | `varchar` | No | — | Stores the channel value for this record. |
| `recipient_name` | `varchar` | No | — | Stores the recipient name value for this record. |
| `recipient_address` | `varchar` | No | — | Stores the recipient address value for this record. |
| `summary` | `varchar` | No | — | Stores the summary value for this record. |
| `content` | `TEXT` | No | — | Stores the content value for this record. |
| `is_internal` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is internal. |
| `external_thread_id` | `varchar` | No | — | Stores the external thread id value for this record. |
| `external_message_id` | `varchar` | No | — | Stores the external message id value for this record. |
| `delivery_status` | `varchar` | No | — | Domain-specific lifecycle or processing state for delivery status. |
| `sent_at` | `datetime` | No | — | Timestamp recording when sent at. |
| `delivered_at` | `datetime` | No | — | Timestamp recording when delivered at. |
| `read_at` | `datetime` | No | — | Timestamp recording when read at. |
| `failed_at` | `datetime` | No | — | Timestamp recording when failed at. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_interactions_channel_external_message_id_unique` on (`channel`, `external_message_id`) — unique; `booking_interactions_delivery_status_failed_at_index` on (`delivery_status`, `failed_at`); `booking_interaction_visibility_lookup` on (`business_id`, `booking_id`, `is_internal`, `occurred_at`); `booking_interactions_interaction_type_channel_index` on (`interaction_type`, `channel`); `booking_interactions_business_id_booking_id_occurred_at_index` on (`business_id`, `booking_id`, `occurred_at`).

### `booking_payment_installments`

Stores booking workspace records for payment installments.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `financial_document_id` | `varchar` | No | — | Foreign key to `booking_financial_documents.id`; deletion behavior is RESTRICT |
| `sequence` | `INTEGER` | Yes | — | Numeric value used for sequence. |
| `purpose` | `varchar` | Yes | — | Stores the purpose value for this record. |
| `amount` | `numeric` | Yes | — | Numeric value representing amount; interpret it with the table's currency, scale, or scoring context. |
| `paid_amount` | `numeric` | Yes | `'0'` | Numeric value representing paid amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `due_at` | `datetime` | Yes | — | Timestamp recording when due at. |
| `paid_at` | `datetime` | No | — | Timestamp recording when paid at. |
| `payment_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for payment status. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_payment_installments_business_id_payment_status_due_at_index` on (`business_id`, `payment_status`, `due_at`); `booking_payment_installments_booking_id_sequence_unique` on (`booking_id`, `sequence`) — unique.

### `booking_staff_assignments`

Stores booking workspace records for staff assignments.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `employee_id` | `varchar` | Yes | — | Related business employee UUID. |
| `assignment_role` | `varchar` | Yes | `'booking_manager'` | Stores the assignment role value for this record. |
| `responsibilities` | `TEXT` | No | — | Stores the responsibilities value for this record. |
| `is_primary` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is primary. |
| `assigned_at` | `datetime` | Yes | — | Timestamp recording when assigned at. |
| `ended_at` | `datetime` | No | — | Timestamp recording when ended at. |
| `assignment_status` | `varchar` | Yes | `'active'` | Domain-specific lifecycle or processing state for assignment status. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `booking_primary_staff_lookup` on (`booking_id`, `assignment_role`, `is_primary`, `assignment_status`); `booking_staff_employee_lookup` on (`business_id`, `employee_id`, `assignment_status`); `booking_staff_booking_lookup` on (`business_id`, `booking_id`, `assignment_role`, `assignment_status`).

### `booking_status_history`

Stores booking workspace records for status history.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `previous_status` | `varchar` | No | — | Domain-specific lifecycle or processing state for previous status. |
| `new_status` | `varchar` | Yes | — | Domain-specific lifecycle or processing state for new status. |
| `source` | `varchar` | Yes | `'user'` | Stores the source value for this record. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `booking_status_history_business_id_booking_id_occurred_at_index` on (`business_id`, `booking_id`, `occurred_at`).

### `bookings`

The central reservation record connecting a business, property, guest, dates, occupancy, pricing, and booking/payment state.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `guest_user_id` | `varchar` | Yes | — | Foreign key to `users.id`; deletion behavior is RESTRICT |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `arrival_date` | `date` | Yes | — | Calendar date associated with arrival date. |
| `departure_date` | `date` | Yes | — | Calendar date associated with departure date. |
| `number_of_guests` | `INTEGER` | Yes | `'1'` | Stores the number of guests value for this record. |
| `adult_count` | `INTEGER` | Yes | `'1'` | Numeric value used for adult count. |
| `child_count` | `INTEGER` | Yes | `'0'` | Numeric value used for child count. |
| `pet_count` | `INTEGER` | Yes | `'0'` | Numeric value used for pet count. |
| `source` | `varchar` | Yes | `'marketplace'` | Stores the source value for this record. |
| `status` | `varchar` | Yes | `'enquiry'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `payment_status` | `varchar` | Yes | `'unpaid'` | Domain-specific lifecycle or processing state for payment status. |
| `special_requests` | `TEXT` | No | — | Stores the special requests value for this record. |
| `discount_type` | `varchar` | No | — | Classification describing discount type. |
| `discount_value` | `numeric` | Yes | `'0'` | Numeric value representing discount value; interpret it with the table's currency, scale, or scoring context. |
| `discount_amount` | `numeric` | Yes | `'0'` | Numeric value representing discount amount; interpret it with the table's currency, scale, or scoring context. |
| `coupon_code` | `varchar` | No | — | Stable identifier used for coupon code. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `subtotal_amount` | `numeric` | Yes | — | Numeric value representing subtotal amount; interpret it with the table's currency, scale, or scoring context. |
| `total_amount` | `numeric` | Yes | — | Numeric value representing total amount; interpret it with the table's currency, scale, or scoring context. |
| `external_reference` | `varchar` | No | — | Stable identifier used for external reference. |
| `source_metadata` | `TEXT` | No | — | Stores the source metadata value for this record. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `bookings_source_external_reference_index` on (`source`, `external_reference`); `bookings_property_availability_lookup` on (`business_id`, `property_id`, `status`, `arrival_date`, `departure_date`); `bookings_business_id_status_arrival_date_index` on (`business_id`, `status`, `arrival_date`); `bookings_business_id_source_created_at_index` on (`business_id`, `source`, `created_at`); `bookings_business_id_reference_unique` on (`business_id`, `reference`) — unique; `bookings_business_id_payment_status_index` on (`business_id`, `payment_status`); `bookings_business_id_id_unique` on (`business_id`, `id`) — unique; `bookings_business_id_guest_user_id_created_at_index` on (`business_id`, `guest_user_id`, `created_at`).

### `cancellation_policies`

Stores cancellation policies records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `policy_type` | `varchar` | Yes | — | Classification describing policy type. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `rules` | `TEXT` | Yes | — | JSON or structured data containing rules. |
| `is_default` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is default. |
| `effective_from` | `date` | Yes | — | Stores the effective from value for this record. |
| `effective_until` | `date` | No | — | Stores the effective until value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `cancellation_policies_business_id_is_default_effective_from_index` on (`business_id`, `is_default`, `effective_from`); `cancellation_policies_business_id_property_id_status_index` on (`business_id`, `property_id`, `status`).

### `guest_service_requests`

Stores guest service requests records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `guest_user_id` | `varchar` | Yes | — | Foreign key to `users.id`; deletion behavior is RESTRICT |
| `operational_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `assigned_employee_id` | `varchar` | No | — | Foreign key to `employees.id`; deletion behavior is RESTRICT |
| `request_type` | `varchar` | Yes | — | Classification describing request type. |
| `priority` | `varchar` | Yes | `'normal'` | Stores the priority value for this record. |
| `description` | `TEXT` | Yes | — | Longer human-readable explanation of the record. |
| `resolution` | `TEXT` | No | — | Stores the resolution value for this record. |
| `requested_at` | `datetime` | Yes | — | Timestamp recording when requested at. |
| `resolved_at` | `datetime` | No | — | Timestamp recording when resolved at. |
| `status` | `varchar` | Yes | `'open'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `guest_service_requests_assigned_employee_id_status_index` on (`assigned_employee_id`, `status`); `guest_service_requests_business_id_booking_id_status_index` on (`business_id`, `booking_id`, `status`).

## Business and onboarding

### `business_automation_settings`

Stores business workspace records for automation settings.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `scope_key` | `varchar` | Yes | `'business'` | Stable identifier used for scope key. |
| `automation_key` | `varchar` | Yes | — | Stable identifier used for automation key. |
| `trigger_event` | `varchar` | Yes | — | Stores the trigger event value for this record. |
| `workflow_key` | `varchar` | Yes | — | Stable identifier used for workflow key. |
| `is_enabled` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether is enabled. |
| `configuration` | `TEXT` | No | — | JSON or structured data containing configuration. |
| `workflow_version` | `INTEGER` | Yes | `'1'` | Stores the workflow version value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `business_automation_trigger_lookup` on (`business_id`, `trigger_event`, `is_enabled`, `status`); `business_automation_scope_unique` on (`business_id`, `scope_key`, `automation_key`) — unique.

### `business_health_snapshots`

Stores business workspace records for health snapshots.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `snapshot_date` | `date` | Yes | — | Calendar date associated with snapshot date. |
| `period_type` | `varchar` | Yes | `'daily'` | Classification describing period type. |
| `score` | `INTEGER` | Yes | — | Stores the score value for this record. |
| `component_scores` | `TEXT` | Yes | — | Stores the component scores value for this record. |
| `explanations` | `TEXT` | No | — | Stores the explanations value for this record. |
| `supporting_metrics` | `TEXT` | No | — | Stores the supporting metrics value for this record. |
| `calculation_version` | `varchar` | Yes | — | Stores the calculation version value for this record. |
| `calculated_at` | `datetime` | Yes | — | Timestamp recording when calculated at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `business_health_snapshots_business_id_snapshot_date_score_index` on (`business_id`, `snapshot_date`, `score`); `business_health_snapshots_business_id_period_type_snapshot_date_unique` on (`business_id`, `period_type`, `snapshot_date`) — unique.

### `business_memberships`

Connects a user to a business and establishes their tenant membership lifecycle.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `job_title` | `varchar` | No | — | Stores the job title value for this record. |
| `status` | `varchar` | Yes | `'invited'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `invited_at` | `datetime` | No | — | Timestamp recording when invited at. |
| `joined_at` | `datetime` | No | — | Timestamp recording when joined at. |
| `ended_at` | `datetime` | No | — | Timestamp recording when ended at. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `business_memberships_user_id_status_index` on (`user_id`, `status`); `business_memberships_business_id_user_id_unique` on (`business_id`, `user_id`) — unique; `business_memberships_business_id_status_index` on (`business_id`, `status`); `business_memberships_business_id_id_unique` on (`business_id`, `id`) — unique.

### `business_onboarding_steps`

Stores business workspace records for onboarding steps.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `step_key` | `varchar` | Yes | — | Stable identifier used for step key. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `state` | `varchar` | Yes | `'pending'` | Stores the state value for this record. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `completed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `business_onboarding_steps_business_id_state_sort_order_index` on (`business_id`, `state`, `sort_order`); `business_onboarding_steps_business_id_step_key_unique` on (`business_id`, `step_key`) — unique.

### `business_subscriptions`

Stores business workspace records for subscriptions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `plan_key` | `varchar` | Yes | — | Stable identifier used for plan key. |
| `provider` | `varchar` | No | — | Stores the provider value for this record. |
| `provider_subscription_id` | `varchar` | No | — | Stores the provider subscription id value for this record. |
| `subscription_status` | `varchar` | Yes | — | Domain-specific lifecycle or processing state for subscription status. |
| `trial_ends_at` | `datetime` | No | — | Timestamp recording when trial ends at. |
| `current_period_starts_at` | `datetime` | No | — | Timestamp recording when current period starts at. |
| `current_period_ends_at` | `datetime` | No | — | Timestamp recording when current period ends at. |
| `cancelled_at` | `datetime` | No | — | Timestamp recording when cancelled at. |
| `ended_at` | `datetime` | No | — | Timestamp recording when ended at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `business_subscriptions_provider_provider_subscription_id_index` on (`provider`, `provider_subscription_id`); `business_subscriptions_business_id_subscription_status_created_at_index` on (`business_id`, `subscription_status`, `created_at`).

### `businesses`

The tenant and legal/operational organization that owns properties, bookings, employees, finance, operations, and settings.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `logo_disk` | `varchar` | No | — | Stores the logo disk value for this record. |
| `logo_path` | `varchar` | No | — | Stores the logo path value for this record. |
| `website_url` | `varchar` | No | — | Location used to access website url; file contents normally live outside the relational database. |
| `social_links` | `TEXT` | No | — | Stores the social links value for this record. |
| `registration_number` | `varchar` | No | — | Numeric value used for registration number. |
| `country_code` | `varchar` | Yes | — | Stable identifier used for country code. |
| `address` | `TEXT` | No | — | Structured or serialized address information. |
| `primary_contact_name` | `varchar` | No | — | Stores the primary contact name value for this record. |
| `email` | `varchar` | No | — | Email address associated with the record. |
| `phone_number` | `varchar` | No | — | Telephone number associated with the record. |
| `tax_information` | `TEXT` | No | — | Stores the tax information value for this record. |
| `business_type` | `varchar` | Yes | — | Classification describing business type. |
| `timezone` | `varchar` | Yes | `'UTC'` | Stores the timezone value for this record. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `subscription_plan` | `varchar` | No | — | Stores the subscription plan value for this record. |
| `onboarding_status` | `varchar` | Yes | `'registered'` | Domain-specific lifecycle or processing state for onboarding status. |
| `onboarding_started_at` | `datetime` | No | — | Timestamp recording when onboarding started at. |
| `onboarding_completed_at` | `datetime` | No | — | Timestamp recording when onboarding completed at. |
| `verification_status` | `varchar` | Yes | `'unverified'` | Domain-specific lifecycle or processing state for verification status. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `businesses_subscription_plan_status_index` on (`subscription_plan`, `status`); `businesses_status_verification_status_index` on (`status`, `verification_status`); `businesses_onboarding_status_status_index` on (`onboarding_status`, `status`); `businesses_email_index` on (`email`); `businesses_country_registration_unique` on (`country_code`, `registration_number`) — unique; `businesses_country_code_business_type_index` on (`country_code`, `business_type`).

### `departments`

Stores departments records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `code` | `varchar` | Yes | — | Stable business-readable code used for searching and uniqueness. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `departments_business_id_status_index` on (`business_id`, `status`); `departments_business_id_code_unique` on (`business_id`, `code`) — unique.

## Payments and finance

### `cost_centres`

Stores cost centres records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `code` | `varchar` | Yes | — | Stable business-readable code used for searching and uniqueness. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `cost_centres_business_id_property_id_status_index` on (`business_id`, `property_id`, `status`); `cost_centres_business_id_id_unique` on (`business_id`, `id`) — unique; `cost_centres_business_id_code_unique` on (`business_id`, `code`) — unique.

### `currency_conversions`

Stores currency conversions records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `exchange_rate_id` | `varchar` | Yes | — | Foreign key to `currency_exchange_rates.id`; deletion behavior is RESTRICT |
| `financial_transaction_id` | `varchar` | No | — | Foreign key to `financial_transactions.id`; deletion behavior is RESTRICT |
| `source_type` | `varchar` | Yes | — | Classification describing source type. |
| `source_id` | `varchar` | Yes | — | Stores the source id value for this record. |
| `source_amount` | `numeric` | Yes | — | Numeric value representing source amount; interpret it with the table's currency, scale, or scoring context. |
| `source_currency` | `varchar` | Yes | — | Stores the source currency value for this record. |
| `rate` | `numeric` | Yes | — | Stores the rate value for this record. |
| `converted_amount` | `numeric` | Yes | — | Numeric value representing converted amount; interpret it with the table's currency, scale, or scoring context. |
| `target_currency` | `varchar` | Yes | — | Stores the target currency value for this record. |
| `converted_at` | `datetime` | Yes | — | Timestamp recording when converted at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `currency_conversion_history_lookup` on (`business_id`, `source_currency`, `target_currency`, `converted_at`); `currency_conversions_source_type_source_id_index` on (`source_type`, `source_id`).

### `currency_exchange_rates`

Stores currency exchange rates records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `base_currency` | `varchar` | Yes | — | Stores the base currency value for this record. |
| `quote_currency` | `varchar` | Yes | — | Stores the quote currency value for this record. |
| `rate` | `numeric` | Yes | — | Stores the rate value for this record. |
| `provider` | `varchar` | Yes | — | Stores the provider value for this record. |
| `effective_at` | `datetime` | Yes | — | Timestamp recording when effective at. |
| `expires_at` | `datetime` | No | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `provider_metadata` | `TEXT` | No | — | Stores the provider metadata value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `currency_exchange_rates_base_currency_quote_currency_effective_at_index` on (`base_currency`, `quote_currency`, `effective_at`); `exchange_rate_source_unique` on (`business_id`, `base_currency`, `quote_currency`, `provider`, `effective_at`) — unique.

### `expense_approval_events`

Stores expense approval events records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `expense_id` | `varchar` | Yes | — | Foreign key to `expenses.id`; deletion behavior is RESTRICT |
| `action` | `varchar` | Yes | — | Stores the action value for this record. |
| `previous_status` | `varchar` | No | — | Domain-specific lifecycle or processing state for previous status. |
| `new_status` | `varchar` | Yes | — | Domain-specific lifecycle or processing state for new status. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `expense_approval_events_business_id_expense_id_occurred_at_index` on (`business_id`, `expense_id`, `occurred_at`).

### `expense_recurring_schedules`

Stores expense recurring schedules records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `supplier_id` | `varchar` | No | — | Foreign key to `suppliers.id`; deletion behavior is RESTRICT |
| `cost_centre_id` | `varchar` | No | — | Foreign key to `cost_centres.id`; deletion behavior is RESTRICT |
| `tax_category_id` | `varchar` | No | — | Foreign key to `tax_categories.id`; deletion behavior is RESTRICT |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `category` | `varchar` | Yes | — | Stores the category value for this record. |
| `amount` | `numeric` | Yes | — | Numeric value representing amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `frequency` | `varchar` | Yes | — | Stores the frequency value for this record. |
| `interval` | `INTEGER` | Yes | `'1'` | Stores the interval value for this record. |
| `starts_on` | `date` | Yes | — | Calendar date associated with starts on. |
| `ends_on` | `date` | No | — | Calendar date associated with ends on. |
| `next_occurrence_on` | `date` | No | — | Calendar date associated with next occurrence on. |
| `recurrence_rule` | `TEXT` | No | — | Stores the recurrence rule value for this record. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `auto_submit_for_approval` | `tinyint(1)` | Yes | `'1'` | Stores the auto submit for approval value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `expense_recurring_schedules_business_id_next_occurrence_on_status_index` on (`business_id`, `next_occurrence_on`, `status`); `expense_recurring_schedules_business_id_id_unique` on (`business_id`, `id`) — unique.

### `expenses`

Stores expenses records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `operational_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `maintenance_issue_id` | `varchar` | No | — | Foreign key to `maintenance_issues.id`; deletion behavior is RESTRICT |
| `supplier_id` | `varchar` | No | — | Foreign key to `suppliers.id`; deletion behavior is RESTRICT |
| `employee_id` | `varchar` | No | — | Related business employee UUID. |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `category` | `varchar` | Yes | — | Stores the category value for this record. |
| `amount` | `numeric` | Yes | — | Numeric value representing amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `payee` | `varchar` | No | — | Stores the payee value for this record. |
| `provider` | `varchar` | No | — | Stores the provider value for this record. |
| `provider_reference` | `varchar` | No | — | Stable identifier used for provider reference. |
| `incurred_on` | `date` | Yes | — | Calendar date associated with incurred on. |
| `due_on` | `date` | No | — | Calendar date associated with due on. |
| `paid_on` | `date` | No | — | Calendar date associated with paid on. |
| `approval_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for approval status. |
| `approved_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `verified_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `cost_centre_id` | `varchar` | No | — | Foreign key to `cost_centres.id`; deletion behavior is RESTRICT |
| `tax_category_id` | `varchar` | No | — | Foreign key to `tax_categories.id`; deletion behavior is RESTRICT |
| `recurring_expense_schedule_id` | `varchar` | No | — | Foreign key to `expense_recurring_schedules.id`; deletion behavior is RESTRICT |
| `subtotal_amount` | `numeric` | No | — | Numeric value representing subtotal amount; interpret it with the table's currency, scale, or scoring context. |
| `tax_amount` | `numeric` | Yes | `'0'` | Numeric value representing tax amount; interpret it with the table's currency, scale, or scoring context. |
| `submitted_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `submitted_at` | `datetime` | No | — | Timestamp recording when submitted at. |
| `approved_at` | `datetime` | No | — | Timestamp recording when approved at. |
| `rejection_reason` | `TEXT` | No | — | Stores the rejection reason value for this record. |

**Indexes:** `expenses_recurring_expense_schedule_id_incurred_on_index` on (`recurring_expense_schedule_id`, `incurred_on`); `expenses_business_id_cost_centre_id_incurred_on_index` on (`business_id`, `cost_centre_id`, `incurred_on`); `expenses_business_id_reference_unique` on (`business_id`, `reference`) — unique; `expenses_business_id_id_unique` on (`business_id`, `id`) — unique; `expenses_business_id_category_incurred_on_index` on (`business_id`, `category`, `incurred_on`); `expenses_business_id_approval_status_due_on_index` on (`business_id`, `approval_status`, `due_on`).

### `financial_accounts`

Stores finance workspace records for accounts.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `code` | `varchar` | Yes | — | Stable business-readable code used for searching and uniqueness. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `account_type` | `varchar` | Yes | `'bank'` | Classification describing account type. |
| `provider` | `varchar` | No | — | Stores the provider value for this record. |
| `external_account_reference` | `varchar` | No | — | Stable identifier used for external account reference. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `opening_balance` | `numeric` | Yes | `'0'` | Stores the opening balance value for this record. |
| `opening_balance_date` | `date` | No | — | Calendar date associated with opening balance date. |
| `is_default` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is default. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `financial_accounts_provider_external_account_reference_index` on (`provider`, `external_account_reference`); `financial_accounts_business_id_account_type_currency_status_index` on (`business_id`, `account_type`, `currency`, `status`); `financial_accounts_business_id_id_unique` on (`business_id`, `id`) — unique; `financial_accounts_business_id_code_unique` on (`business_id`, `code`) — unique.

### `financial_forecast_snapshots`

Stores finance workspace records for forecast snapshots.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `forecast_type` | `varchar` | Yes | — | Classification describing forecast type. |
| `horizon_days` | `INTEGER` | Yes | — | Stores the horizon days value for this record. |
| `forecast_starts_on` | `date` | Yes | — | Calendar date associated with forecast starts on. |
| `forecast_ends_on` | `date` | Yes | — | Calendar date associated with forecast ends on. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `forecast_values` | `TEXT` | Yes | — | Numeric value representing forecast values; interpret it with the table's currency, scale, or scoring context. |
| `assumptions` | `TEXT` | No | — | Stores the assumptions value for this record. |
| `supporting_metrics` | `TEXT` | No | — | Stores the supporting metrics value for this record. |
| `model_provider` | `varchar` | No | — | Stores the model provider value for this record. |
| `model_name` | `varchar` | No | — | Stores the model name value for this record. |
| `model_version` | `varchar` | No | — | Stores the model version value for this record. |
| `calculation_version` | `varchar` | Yes | — | Stores the calculation version value for this record. |
| `generated_at` | `datetime` | Yes | — | Timestamp recording when generated at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `financial_forecast_scope_lookup` on (`business_id`, `property_id`, `forecast_starts_on`, `forecast_ends_on`); `financial_forecast_snapshots_business_id_forecast_type_generated_at_index` on (`business_id`, `forecast_type`, `generated_at`).

### `financial_reconciliation_items`

Stores finance workspace records for reconciliation items.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `financial_reconciliation_id` | `varchar` | Yes | — | Foreign key to `financial_reconciliations.id`; deletion behavior is RESTRICT |
| `financial_transaction_id` | `varchar` | No | — | Foreign key to `financial_transactions.id`; deletion behavior is RESTRICT |
| `external_reference` | `varchar` | No | — | Stable identifier used for external reference. |
| `statement_date` | `date` | Yes | — | Calendar date associated with statement date. |
| `statement_amount` | `numeric` | Yes | — | Numeric value representing statement amount; interpret it with the table's currency, scale, or scoring context. |
| `transaction_amount` | `numeric` | No | — | Numeric value representing transaction amount; interpret it with the table's currency, scale, or scoring context. |
| `difference_amount` | `numeric` | No | — | Numeric value representing difference amount; interpret it with the table's currency, scale, or scoring context. |
| `match_status` | `varchar` | Yes | `'unmatched'` | Domain-specific lifecycle or processing state for match status. |
| `matched_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `matched_at` | `datetime` | No | — | Timestamp recording when matched at. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `financial_reconciliation_items_business_id_external_reference_index` on (`business_id`, `external_reference`); `financial_reconciliation_items_financial_reconciliation_id_match_status_index` on (`financial_reconciliation_id`, `match_status`).

### `financial_reconciliations`

Stores finance workspace records for reconciliations.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `financial_account_id` | `varchar` | Yes | — | Foreign key to `financial_accounts.id`; deletion behavior is RESTRICT |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `period_starts_on` | `date` | Yes | — | Calendar date associated with period starts on. |
| `period_ends_on` | `date` | Yes | — | Calendar date associated with period ends on. |
| `opening_balance` | `numeric` | Yes | — | Stores the opening balance value for this record. |
| `closing_balance` | `numeric` | Yes | — | Stores the closing balance value for this record. |
| `calculated_balance` | `numeric` | No | — | Stores the calculated balance value for this record. |
| `difference_amount` | `numeric` | No | — | Numeric value representing difference amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `reconciliation_status` | `varchar` | Yes | `'draft'` | Domain-specific lifecycle or processing state for reconciliation status. |
| `completed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `financial_reconciliation_account_lookup` on (`business_id`, `financial_account_id`, `period_ends_on`); `financial_reconciliations_business_id_id_unique` on (`business_id`, `id`) — unique; `financial_reconciliations_business_id_reference_unique` on (`business_id`, `reference`) — unique.

### `financial_report_definitions`

Stores finance workspace records for report definitions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `report_type` | `varchar` | Yes | — | Classification describing report type. |
| `configuration` | `TEXT` | No | — | JSON or structured data containing configuration. |
| `is_system` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is system. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `financial_report_definitions_business_id_report_type_status_index` on (`business_id`, `report_type`, `status`); `financial_report_definitions_business_id_id_unique` on (`business_id`, `id`) — unique; `financial_report_definitions_business_id_name_unique` on (`business_id`, `name`) — unique.

### `financial_report_runs`

Stores finance workspace records for report runs.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `financial_report_definition_id` | `varchar` | Yes | — | Foreign key to `financial_report_definitions.id`; deletion behavior is RESTRICT |
| `financial_report_schedule_id` | `varchar` | No | — | Foreign key to `financial_report_schedules.id`; deletion behavior is RESTRICT |
| `run_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for run status. |
| `parameters` | `TEXT` | Yes | — | Stores the parameters value for this record. |
| `period_starts_on` | `date` | No | — | Calendar date associated with period starts on. |
| `period_ends_on` | `date` | No | — | Calendar date associated with period ends on. |
| `summary` | `TEXT` | No | — | Stores the summary value for this record. |
| `storage_disk` | `varchar` | No | — | Stores the storage disk value for this record. |
| `storage_path` | `varchar` | No | — | Stores the storage path value for this record. |
| `format` | `varchar` | Yes | — | Stores the format value for this record. |
| `checksum` | `varchar` | No | — | Stores the checksum value for this record. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `delivered_at` | `datetime` | No | — | Timestamp recording when delivered at. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `financial_report_period_lookup` on (`financial_report_definition_id`, `period_starts_on`, `period_ends_on`); `financial_report_runs_business_id_run_status_created_at_index` on (`business_id`, `run_status`, `created_at`).

### `financial_report_schedules`

Stores finance workspace records for report schedules.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `financial_report_definition_id` | `varchar` | Yes | — | Foreign key to `financial_report_definitions.id`; deletion behavior is RESTRICT |
| `frequency` | `varchar` | Yes | — | Stores the frequency value for this record. |
| `schedule_rule` | `TEXT` | Yes | — | Stores the schedule rule value for this record. |
| `formats` | `TEXT` | Yes | — | Stores the formats value for this record. |
| `delivery_channels` | `TEXT` | Yes | — | Stores the delivery channels value for this record. |
| `recipients` | `TEXT` | Yes | — | Stores the recipients value for this record. |
| `next_run_at` | `datetime` | No | — | Timestamp recording when next run at. |
| `last_run_at` | `datetime` | No | — | Timestamp recording when last run at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `financial_report_schedules_business_id_next_run_at_status_index` on (`business_id`, `next_run_at`, `status`); `financial_report_schedules_business_id_id_unique` on (`business_id`, `id`) — unique.

### `financial_transactions`

The immutable business money ledger used for auditable financial reporting.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `financial_account_id` | `varchar` | No | — | Foreign key to `financial_accounts.id`; deletion behavior is RESTRICT |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `payment_id` | `varchar` | No | — | Foreign key to `payments.id`; deletion behavior is RESTRICT |
| `expense_id` | `varchar` | No | — | Foreign key to `expenses.id`; deletion behavior is RESTRICT |
| `tax_category_id` | `varchar` | No | — | Foreign key to `tax_categories.id`; deletion behavior is RESTRICT |
| `exchange_rate_id` | `varchar` | No | — | Foreign key to `currency_exchange_rates.id`; deletion behavior is RESTRICT |
| `reversed_transaction_id` | `varchar` | No | — | Foreign key to `financial_transactions.id`; deletion behavior is RESTRICT |
| `transaction_group_id` | `varchar` | No | — | Stores the transaction group id value for this record. |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `transaction_type` | `varchar` | Yes | `'adjustment'` | Classification describing transaction type. |
| `direction` | `varchar` | Yes | `'non_cash'` | Stores the direction value for this record. |
| `economic_category` | `varchar` | Yes | — | Stores the economic category value for this record. |
| `source_type` | `varchar` | Yes | — | Classification describing source type. |
| `source_id` | `varchar` | Yes | — | Stores the source id value for this record. |
| `amount` | `numeric` | Yes | — | Numeric value representing amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `base_amount` | `numeric` | No | — | Numeric value representing base amount; interpret it with the table's currency, scale, or scoring context. |
| `base_currency` | `varchar` | No | — | Stores the base currency value for this record. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `effective_on` | `date` | Yes | — | Calendar date from which the record becomes effective. |
| `due_at` | `datetime` | No | — | Timestamp recording when due at. |
| `settled_at` | `datetime` | No | — | Timestamp recording when settled at. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `transaction_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for transaction status. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `financial_transactions_transaction_group_id_index` on (`transaction_group_id`); `financial_transactions_source_type_source_id_index` on (`source_type`, `source_id`); `financial_transactions_business_id_booking_id_occurred_at_index` on (`business_id`, `booking_id`, `occurred_at`); `financial_transaction_property_lookup` on (`business_id`, `property_id`, `effective_on`, `economic_category`); `financial_transaction_cashflow_lookup` on (`business_id`, `effective_on`, `direction`, `transaction_status`); `financial_transactions_business_id_reference_unique` on (`business_id`, `reference`) — unique; `financial_transactions_business_id_id_unique` on (`business_id`, `id`) — unique.

### `payments`

An immutable auditable money-transfer record related to a booking, including separate refund transactions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `original_payment_id` | `varchar` | No | — | Foreign key to `payments.id`; deletion behavior is RESTRICT |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `purpose` | `varchar` | Yes | — | Stores the purpose value for this record. |
| `amount` | `numeric` | Yes | — | Numeric value representing amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `method` | `varchar` | Yes | — | Stores the method value for this record. |
| `provider` | `varchar` | No | — | Stores the provider value for this record. |
| `provider_reference` | `varchar` | No | — | Stable identifier used for provider reference. |
| `status` | `varchar` | Yes | `'pending'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `transaction_at` | `datetime` | Yes | — | Timestamp recording when transaction at. |
| `verified_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `verified_at` | `datetime` | No | — | Timestamp recording when verified at. |
| `receipt_disk` | `varchar` | No | — | Stores the receipt disk value for this record. |
| `receipt_path` | `varchar` | No | — | Stores the receipt path value for this record. |
| `receipt_url` | `varchar` | No | — | Location used to access receipt url; file contents normally live outside the relational database. |
| `provider_metadata` | `TEXT` | No | — | Stores the provider metadata value for this record. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `payments_verified_by_verified_at_index` on (`verified_by`, `verified_at`); `payments_provider_provider_reference_unique` on (`provider`, `provider_reference`) — unique; `payments_original_payment_id_status_index` on (`original_payment_id`, `status`); `payments_business_id_status_transaction_at_index` on (`business_id`, `status`, `transaction_at`); `payments_business_id_reference_unique` on (`business_id`, `reference`) — unique; `payments_business_id_purpose_transaction_at_index` on (`business_id`, `purpose`, `transaction_at`); `payments_business_id_id_unique` on (`business_id`, `id`) — unique; `payments_business_id_booking_id_status_index` on (`business_id`, `booking_id`, `status`).

### `refund_requests`

Stores refund requests records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `original_payment_id` | `varchar` | Yes | — | Foreign key to `payments.id`; deletion behavior is RESTRICT |
| `refund_payment_id` | `varchar` | No | — | Foreign key to `payments.id`; deletion behavior is RESTRICT |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `reason_type` | `varchar` | Yes | — | Classification describing reason type. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `requested_amount` | `numeric` | Yes | — | Numeric value representing requested amount; interpret it with the table's currency, scale, or scoring context. |
| `approved_amount` | `numeric` | No | — | Numeric value representing approved amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `requested_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `reviewed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `approved_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `processed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `requested_at` | `datetime` | Yes | — | Timestamp recording when requested at. |
| `reviewed_at` | `datetime` | No | — | Timestamp recording when reviewed at. |
| `approved_at` | `datetime` | No | — | Timestamp recording when approved at. |
| `processed_at` | `datetime` | No | — | Timestamp recording when processed at. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `rejection_reason` | `TEXT` | No | — | Stores the rejection reason value for this record. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `refund_status` | `varchar` | Yes | `'requested'` | Domain-specific lifecycle or processing state for refund status. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `refund_requests_business_id_booking_id_refund_status_index` on (`business_id`, `booking_id`, `refund_status`); `refund_requests_business_id_refund_status_requested_at_index` on (`business_id`, `refund_status`, `requested_at`); `refund_requests_business_id_reference_unique` on (`business_id`, `reference`) — unique.

### `revenue_entries`

Stores revenue entries records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `revenue_recognition_policy_id` | `varchar` | Yes | — | Foreign key to `revenue_recognition_policies.id`; deletion behavior is RESTRICT |
| `financial_transaction_id` | `varchar` | No | — | Foreign key to `financial_transactions.id`; deletion behavior is RESTRICT |
| `financial_document_id` | `varchar` | No | — | Foreign key to `booking_financial_documents.id`; deletion behavior is RESTRICT |
| `revenue_category` | `varchar` | Yes | — | Stores the revenue category value for this record. |
| `gross_amount` | `numeric` | Yes | — | Numeric value representing gross amount; interpret it with the table's currency, scale, or scoring context. |
| `deduction_amount` | `numeric` | Yes | `'0'` | Numeric value representing deduction amount; interpret it with the table's currency, scale, or scoring context. |
| `net_amount` | `numeric` | Yes | — | Numeric value representing net amount; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | Yes | — | ISO 4217 currency code used by the monetary fields on the record. |
| `recognized_on` | `date` | Yes | — | Calendar date associated with recognized on. |
| `recognition_status` | `varchar` | Yes | `'recognized'` | Domain-specific lifecycle or processing state for recognition status. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `revenue_entries_business_id_booking_id_recognized_on_index` on (`business_id`, `booking_id`, `recognized_on`); `revenue_entries_business_id_property_id_recognized_on_index` on (`business_id`, `property_id`, `recognized_on`); `revenue_entries_business_id_recognized_on_revenue_category_index` on (`business_id`, `recognized_on`, `revenue_category`).

### `revenue_recognition_policies`

Stores revenue recognition policies records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `recognition_basis` | `varchar` | Yes | `'check_out'` | Stores the recognition basis value for this record. |
| `rules` | `TEXT` | No | — | JSON or structured data containing rules. |
| `is_default` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is default. |
| `effective_from` | `date` | Yes | — | Stores the effective from value for this record. |
| `effective_until` | `date` | No | — | Stores the effective until value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `revenue_policy_lookup` on (`business_id`, `property_id`, `is_default`, `effective_from`); `revenue_recognition_policies_business_id_id_unique` on (`business_id`, `id`) — unique.

### `tax_categories`

Stores tax categories records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `code` | `varchar` | Yes | — | Stable business-readable code used for searching and uniqueness. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `tax_type` | `varchar` | Yes | — | Classification describing tax type. |
| `rate` | `numeric` | Yes | `'0'` | Stores the rate value for this record. |
| `is_inclusive` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is inclusive. |
| `effective_from` | `date` | Yes | — | Stores the effective from value for this record. |
| `effective_until` | `date` | No | — | Stores the effective until value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `tax_categories_business_id_tax_type_status_index` on (`business_id`, `tax_type`, `status`); `tax_categories_business_id_id_unique` on (`business_id`, `id`) — unique; `tax_categories_business_id_code_effective_from_unique` on (`business_id`, `code`, `effective_from`) — unique.

## Documents, notifications, and reviews

### `document_permissions`

Stores document-management records for permissions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `document_id` | `varchar` | Yes | — | Foreign key to `documents.id`; deletion behavior is CASCADE |
| `user_id` | `varchar` | No | — | Related platform user UUID. |
| `user_role_id` | `varchar` | No | — | Foreign key to `user_roles.id`; deletion behavior is CASCADE |
| `access_level` | `varchar` | Yes | — | Stores the access level value for this record. |
| `expires_at` | `datetime` | No | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `document_permissions_business_id_status_expires_at_index` on (`business_id`, `status`, `expires_at`); `document_permissions_document_id_user_role_id_access_level_unique` on (`document_id`, `user_role_id`, `access_level`) — unique; `document_permissions_document_id_user_id_access_level_unique` on (`document_id`, `user_id`, `access_level`) — unique.

### `document_versions`

Stores document-management records for versions.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `document_id` | `varchar` | Yes | — | Foreign key to `documents.id`; deletion behavior is RESTRICT |
| `version_number` | `INTEGER` | Yes | — | Numeric value used for version number. |
| `storage_disk` | `varchar` | Yes | — | Stores the storage disk value for this record. |
| `storage_path` | `varchar` | Yes | — | Stores the storage path value for this record. |
| `original_name` | `varchar` | Yes | — | Stores the original name value for this record. |
| `mime_type` | `varchar` | Yes | — | Classification describing mime type. |
| `size_bytes` | `INTEGER` | Yes | — | Stores the size bytes value for this record. |
| `checksum` | `varchar` | No | — | Stores the checksum value for this record. |
| `change_summary` | `TEXT` | No | — | Stores the change summary value for this record. |
| `uploaded_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `document_versions_checksum_index` on (`checksum`); `document_versions_business_id_created_at_index` on (`business_id`, `created_at`); `document_versions_document_id_version_number_unique` on (`document_id`, `version_number`) — unique.

### `documents`

Searchable metadata for a business document owned by another platform entity.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `owner_type` | `varchar` | Yes | — | Classification describing owner type. |
| `owner_id` | `varchar` | Yes | — | Stores the owner id value for this record. |
| `title` | `varchar` | Yes | — | Short human-readable title. |
| `category` | `varchar` | Yes | — | Stores the category value for this record. |
| `document_number` | `varchar` | No | — | Numeric value used for document number. |
| `issuer` | `varchar` | No | — | Stores the issuer value for this record. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `searchable_text` | `TEXT` | No | — | Stores the searchable text value for this record. |
| `issued_on` | `date` | No | — | Calendar date on which the document, certificate, or item was issued. |
| `effective_on` | `date` | No | — | Calendar date from which the record becomes effective. |
| `expires_on` | `date` | No | — | Calendar date on which the record expires. |
| `reminder_days_before_expiry` | `INTEGER` | No | — | Stores the reminder days before expiry value for this record. |
| `last_expiry_reminder_at` | `datetime` | No | — | Timestamp recording when last expiry reminder at. |
| `confidentiality` | `varchar` | Yes | `'internal'` | Stores the confidentiality value for this record. |
| `verification_status` | `varchar` | Yes | `'unverified'` | Domain-specific lifecycle or processing state for verification status. |
| `verified_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `verified_at` | `datetime` | No | — | Timestamp recording when verified at. |
| `current_version_number` | `INTEGER` | Yes | `'1'` | Numeric value used for current version number. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `documents_owner_type_owner_id_category_index` on (`owner_type`, `owner_id`, `category`); `documents_business_id_verification_status_status_index` on (`business_id`, `verification_status`, `status`); `documents_business_id_expires_on_status_index` on (`business_id`, `expires_on`, `status`); `documents_business_id_title_index` on (`business_id`, `title`); `documents_business_id_category_status_index` on (`business_id`, `category`, `status`); `documents_owner_type_owner_id_index` on (`owner_type`, `owner_id`).

### `notification_deliveries`

Stores notification delivery records for deliveries.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `notification_id` | `varchar` | Yes | — | Foreign key to `notifications.id`; deletion behavior is CASCADE |
| `channel` | `varchar` | Yes | — | Stores the channel value for this record. |
| `destination` | `varchar` | No | — | Stores the destination value for this record. |
| `provider` | `varchar` | No | — | Stores the provider value for this record. |
| `provider_reference` | `varchar` | No | — | Stable identifier used for provider reference. |
| `status` | `varchar` | Yes | `'pending'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `attempt_count` | `INTEGER` | Yes | `'0'` | Numeric value used for attempt count. |
| `last_attempted_at` | `datetime` | No | — | Timestamp recording when last attempted at. |
| `delivered_at` | `datetime` | No | — | Timestamp recording when delivered at. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `provider_metadata` | `TEXT` | No | — | Stores the provider metadata value for this record. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `notification_deliveries_provider_provider_reference_index` on (`provider`, `provider_reference`); `notification_deliveries_status_last_attempted_at_index` on (`status`, `last_attempted_at`); `notification_deliveries_notification_id_channel_destination_unique` on (`notification_id`, `channel`, `destination`) — unique.

### `notifications`

A logical notification addressed to a user and generated by a platform event.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `type` | `varchar` | Yes | — | Classification describing type. |
| `title` | `varchar` | Yes | — | Short human-readable title. |
| `message` | `TEXT` | Yes | — | Stores the message value for this record. |
| `data` | `TEXT` | No | — | Stores the data value for this record. |
| `read_at` | `datetime` | No | — | Timestamp recording when read at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `notifications_business_id_type_created_at_index` on (`business_id`, `type`, `created_at`); `notifications_user_id_read_at_created_at_index` on (`user_id`, `read_at`, `created_at`).

### `review_analyses`

Stores guest-review records for analyses.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `review_id` | `varchar` | Yes | — | Foreign key to `reviews.id`; deletion behavior is RESTRICT |
| `sentiment` | `varchar` | No | — | Stores the sentiment value for this record. |
| `sentiment_score` | `numeric` | No | — | Numeric value representing sentiment score; interpret it with the table's currency, scale, or scoring context. |
| `summary` | `TEXT` | No | — | Stores the summary value for this record. |
| `positive_themes` | `TEXT` | No | — | Stores the positive themes value for this record. |
| `complaint_themes` | `TEXT` | No | — | Stores the complaint themes value for this record. |
| `keywords` | `TEXT` | No | — | Stores the keywords value for this record. |
| `follow_up_priority` | `varchar` | No | — | Stores the follow up priority value for this record. |
| `follow_up_recommended` | `tinyint(1)` | Yes | `'0'` | Stores the follow up recommended value for this record. |
| `model_provider` | `varchar` | No | — | Stores the model provider value for this record. |
| `model_name` | `varchar` | No | — | Stores the model name value for this record. |
| `model_version` | `varchar` | No | — | Stores the model version value for this record. |
| `analysis_version` | `varchar` | Yes | — | Stores the analysis version value for this record. |
| `analysed_at` | `datetime` | Yes | — | Timestamp recording when analysed at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `review_analyses_sentiment_follow_up_priority_status_index` on (`sentiment`, `follow_up_priority`, `status`); `review_analysis_property_lookup` on (`business_id`, `property_id`, `analysed_at`); `review_analyses_review_id_analysis_version_unique` on (`review_id`, `analysis_version`) — unique.

### `review_invitations`

Stores guest-review records for invitations.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `guest_user_id` | `varchar` | Yes | — | Foreign key to `users.id`; deletion behavior is RESTRICT |
| `token_hash` | `varchar` | Yes | — | Stores the token hash value for this record. |
| `sent_at` | `datetime` | No | — | Timestamp recording when sent at. |
| `expires_at` | `datetime` | Yes | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `used_at` | `datetime` | No | — | Timestamp recording when used at. |
| `status` | `varchar` | Yes | `'pending'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `review_invitations_token_hash_unique` on (`token_hash`) — unique; `review_invitations_status_expires_at_index` on (`status`, `expires_at`); `review_invitations_booking_id_guest_user_id_unique` on (`booking_id`, `guest_user_id`) — unique.

### `review_responses`

Stores guest-review records for responses.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `review_id` | `varchar` | Yes | — | Foreign key to `reviews.id`; deletion behavior is RESTRICT |
| `responded_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `content` | `TEXT` | Yes | — | Stores the content value for this record. |
| `moderation_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for moderation status. |
| `submitted_at` | `datetime` | Yes | — | Timestamp recording when submitted at. |
| `published_at` | `datetime` | No | — | Timestamp recording when published at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `review_responses_business_id_moderation_status_published_at_index` on (`business_id`, `moderation_status`, `published_at`); `review_responses_review_id_unique` on (`review_id`) — unique.

### `reviews`

Stores reviews records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | Yes | — | Related booking UUID. |
| `guest_user_id` | `varchar` | Yes | — | Foreign key to `users.id`; deletion behavior is RESTRICT |
| `rating` | `INTEGER` | Yes | — | Stores the rating value for this record. |
| `cleanliness_rating` | `INTEGER` | No | — | Stores the cleanliness rating value for this record. |
| `communication_rating` | `INTEGER` | No | — | Stores the communication rating value for this record. |
| `location_rating` | `INTEGER` | No | — | Stores the location rating value for this record. |
| `value_rating` | `INTEGER` | No | — | Numeric value representing value rating; interpret it with the table's currency, scale, or scoring context. |
| `accuracy_rating` | `INTEGER` | No | — | Stores the accuracy rating value for this record. |
| `title` | `varchar` | No | — | Short human-readable title. |
| `content` | `TEXT` | No | — | Stores the content value for this record. |
| `is_verified_stay` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is verified stay. |
| `moderation_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for moderation status. |
| `sentiment` | `varchar` | No | — | Stores the sentiment value for this record. |
| `sentiment_score` | `numeric` | No | — | Numeric value representing sentiment score; interpret it with the table's currency, scale, or scoring context. |
| `submitted_at` | `datetime` | Yes | — | Timestamp recording when submitted at. |
| `published_at` | `datetime` | No | — | Timestamp recording when published at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `reviews_property_publication_index` on (`business_id`, `property_id`, `moderation_status`, `published_at`); `reviews_business_id_property_id_id_unique` on (`business_id`, `property_id`, `id`) — unique; `reviews_booking_id_guest_user_id_unique` on (`booking_id`, `guest_user_id`) — unique.

## Operations and inventory

### `employee_certifications`

Stores workforce records for certifications.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `employee_id` | `varchar` | Yes | — | Related business employee UUID. |
| `document_id` | `varchar` | No | — | Foreign key to `documents.id`; deletion behavior is RESTRICT |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `issuing_organization` | `varchar` | No | — | Stores the issuing organization value for this record. |
| `certificate_number` | `varchar` | No | — | Numeric value used for certificate number. |
| `issued_on` | `date` | No | — | Calendar date on which the document, certificate, or item was issued. |
| `expires_on` | `date` | No | — | Calendar date on which the record expires. |
| `verification_status` | `varchar` | Yes | `'unverified'` | Domain-specific lifecycle or processing state for verification status. |
| `verified_at` | `datetime` | No | — | Timestamp recording when verified at. |
| `verified_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `employee_certifications_expiry_index` on (`business_id`, `expires_on`, `verification_status`).

### `employee_skills`

Stores workforce records for skills.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `employee_id` | `varchar` | Yes | — | Related business employee UUID. |
| `skill_key` | `varchar` | Yes | — | Stable identifier used for skill key. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `proficiency_level` | `varchar` | No | — | Stores the proficiency level value for this record. |
| `years_experience` | `INTEGER` | No | — | Stores the years experience value for this record. |
| `is_primary` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is primary. |
| `verified_at` | `datetime` | No | — | Timestamp recording when verified at. |
| `verified_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `employee_skills_business_id_skill_key_status_index` on (`business_id`, `skill_key`, `status`); `employee_skills_employee_id_skill_key_unique` on (`employee_id`, `skill_key`) — unique.

### `employees`

Business-specific employment information layered on top of a user business membership.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `business_membership_id` | `varchar` | Yes | — | Foreign key to `business_memberships.id`; deletion behavior is RESTRICT |
| `department_id` | `varchar` | No | — | Foreign key to `departments.id`; deletion behavior is SET NULL |
| `employee_code` | `varchar` | Yes | — | Stable identifier used for employee code. |
| `employment_status` | `varchar` | Yes | `'invited'` | Domain-specific lifecycle or processing state for employment status. |
| `availability` | `TEXT` | No | — | Stores the availability value for this record. |
| `emergency_contact` | `TEXT` | No | — | Stores the emergency contact value for this record. |
| `started_on` | `date` | No | — | Calendar date associated with started on. |
| `ended_on` | `date` | No | — | Calendar date associated with ended on. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `employees_business_id_department_id_employment_status_index` on (`business_id`, `department_id`, `employment_status`); `employees_business_id_id_unique` on (`business_id`, `id`) — unique; `employees_business_id_employee_code_unique` on (`business_id`, `employee_code`) — unique; `employees_business_membership_id_unique` on (`business_membership_id`) — unique.

### `inspection_items`

Stores inspection items records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `inspection_id` | `varchar` | Yes | — | Foreign key to `inspections.id`; deletion behavior is RESTRICT |
| `corrective_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `category` | `varchar` | Yes | — | Stores the category value for this record. |
| `item_name` | `varchar` | Yes | — | Stores the item name value for this record. |
| `expected_value` | `TEXT` | No | — | Numeric value representing expected value; interpret it with the table's currency, scale, or scoring context. |
| `observed_value` | `TEXT` | No | — | Numeric value representing observed value; interpret it with the table's currency, scale, or scoring context. |
| `result` | `varchar` | Yes | — | Stores the result value for this record. |
| `severity` | `varchar` | No | — | Stores the severity value for this record. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `evidence` | `TEXT` | No | — | JSON or structured data containing evidence. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `inspection_items_business_id_result_severity_index` on (`business_id`, `result`, `severity`); `inspection_items_inspection_id_sort_order_index` on (`inspection_id`, `sort_order`).

### `inspections`

Stores inspections records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `operational_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `inspector_employee_id` | `varchar` | No | — | Foreign key to `employees.id`; deletion behavior is RESTRICT |
| `preceding_inspection_id` | `varchar` | No | — | Foreign key to `inspections.id`; deletion behavior is RESTRICT |
| `inspection_type` | `varchar` | Yes | — | Classification describing inspection type. |
| `result` | `varchar` | Yes | `'pending'` | Stores the result value for this record. |
| `score` | `numeric` | No | — | Stores the score value for this record. |
| `findings` | `TEXT` | No | — | Stores the findings value for this record. |
| `recommendations` | `TEXT` | No | — | Stores the recommendations value for this record. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `approved_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `approved_at` | `datetime` | No | — | Timestamp recording when approved at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `inspections_inspector_employee_id_status_index` on (`inspector_employee_id`, `status`); `inspections_business_id_property_id_result_completed_at_index` on (`business_id`, `property_id`, `result`, `completed_at`); `inspections_business_id_id_unique` on (`business_id`, `id`) — unique.

### `inventory_items`

Stores inventory-management records for items.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `supplier_id` | `varchar` | No | — | Foreign key to `suppliers.id`; deletion behavior is RESTRICT |
| `sku` | `varchar` | Yes | — | Stores the sku value for this record. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `category` | `varchar` | No | — | Stores the category value for this record. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `unit_of_measure` | `varchar` | Yes | `'unit'` | Stores the unit of measure value for this record. |
| `default_reorder_level` | `numeric` | No | — | Stores the default reorder level value for this record. |
| `default_reorder_quantity` | `numeric` | No | — | Stores the default reorder quantity value for this record. |
| `unit_cost` | `numeric` | No | — | Numeric value representing unit cost; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `is_trackable` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether is trackable. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `inventory_items_business_id_category_status_index` on (`business_id`, `category`, `status`); `inventory_items_business_id_id_unique` on (`business_id`, `id`) — unique; `inventory_items_business_id_sku_unique` on (`business_id`, `sku`) — unique.

### `inventory_locations`

Stores inventory-management records for locations.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `code` | `varchar` | Yes | — | Stable business-readable code used for searching and uniqueness. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `location_type` | `varchar` | Yes | `'store'` | Classification describing location type. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `address` | `TEXT` | No | — | Structured or serialized address information. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `inventory_locations_business_id_property_id_status_index` on (`business_id`, `property_id`, `status`); `inventory_locations_business_id_id_unique` on (`business_id`, `id`) — unique; `inventory_locations_business_id_code_unique` on (`business_id`, `code`) — unique.

### `inventory_movements`

The immutable stock ledger for receipts, consumption, transfers, returns, damage, and adjustments.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `inventory_item_id` | `varchar` | Yes | — | Foreign key to `inventory_items.id`; deletion behavior is RESTRICT |
| `source_location_id` | `varchar` | No | — | Foreign key to `inventory_locations.id`; deletion behavior is RESTRICT |
| `destination_location_id` | `varchar` | No | — | Foreign key to `inventory_locations.id`; deletion behavior is RESTRICT |
| `property_id` | `varchar` | No | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `operational_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `employee_id` | `varchar` | No | — | Related business employee UUID. |
| `supplier_id` | `varchar` | No | — | Foreign key to `suppliers.id`; deletion behavior is RESTRICT |
| `movement_type` | `varchar` | Yes | — | Classification describing movement type. |
| `quantity` | `numeric` | Yes | — | Stores the quantity value for this record. |
| `unit_cost` | `numeric` | No | — | Numeric value representing unit cost; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `reference` | `varchar` | No | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `reason` | `TEXT` | No | — | Stores the reason value for this record. |
| `occurred_at` | `datetime` | Yes | — | Business timestamp when the represented event actually occurred. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `inventory_movements_business_id_movement_type_occurred_at_index` on (`business_id`, `movement_type`, `occurred_at`); `inventory_movements_item_time_index` on (`business_id`, `inventory_item_id`, `occurred_at`).

### `inventory_stock_levels`

Stores inventory-management records for stock levels.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `inventory_item_id` | `varchar` | Yes | — | Foreign key to `inventory_items.id`; deletion behavior is RESTRICT |
| `inventory_location_id` | `varchar` | Yes | — | Foreign key to `inventory_locations.id`; deletion behavior is RESTRICT |
| `quantity_on_hand` | `numeric` | Yes | `'0'` | Stores the quantity on hand value for this record. |
| `quantity_reserved` | `numeric` | Yes | `'0'` | Stores the quantity reserved value for this record. |
| `reorder_level` | `numeric` | No | — | Stores the reorder level value for this record. |
| `reorder_quantity` | `numeric` | No | — | Stores the reorder quantity value for this record. |
| `last_counted_at` | `datetime` | No | — | Timestamp recording when last counted at. |
| `last_counted_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `inventory_stock_levels_business_id_quantity_on_hand_index` on (`business_id`, `quantity_on_hand`); `inventory_stock_levels_inventory_item_id_inventory_location_id_unique` on (`inventory_item_id`, `inventory_location_id`) — unique.

### `maintenance_issues`

Stores maintenance issues records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `asset_id` | `varchar` | No | — | Foreign key to `assets.id`; deletion behavior is RESTRICT |
| `inspection_id` | `varchar` | No | — | Foreign key to `inspections.id`; deletion behavior is RESTRICT |
| `operational_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `reported_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `assigned_employee_id` | `varchar` | No | — | Foreign key to `employees.id`; deletion behavior is RESTRICT |
| `verified_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `origin` | `varchar` | Yes | — | Stores the origin value for this record. |
| `category` | `varchar` | Yes | — | Stores the category value for this record. |
| `priority` | `varchar` | Yes | `'normal'` | Stores the priority value for this record. |
| `description` | `TEXT` | Yes | — | Longer human-readable explanation of the record. |
| `diagnosis` | `TEXT` | No | — | Stores the diagnosis value for this record. |
| `resolution` | `TEXT` | No | — | Stores the resolution value for this record. |
| `estimated_cost` | `numeric` | No | — | Numeric value representing estimated cost; interpret it with the table's currency, scale, or scoring context. |
| `actual_cost` | `numeric` | No | — | Numeric value representing actual cost; interpret it with the table's currency, scale, or scoring context. |
| `currency` | `varchar` | No | — | ISO 4217 currency code used by the monetary fields on the record. |
| `due_at` | `datetime` | No | — | Timestamp recording when due at. |
| `accepted_at` | `datetime` | No | — | Timestamp recording when accepted at. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `verified_at` | `datetime` | No | — | Timestamp recording when verified at. |
| `closed_at` | `datetime` | No | — | Timestamp recording when closed at. |
| `status` | `varchar` | Yes | `'reported'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `maintenance_issues_assigned_employee_id_status_due_at_index` on (`assigned_employee_id`, `status`, `due_at`); `maintenance_issues_business_id_property_id_status_priority_index` on (`business_id`, `property_id`, `status`, `priority`); `maintenance_issues_business_id_id_unique` on (`business_id`, `id`) — unique.

### `operational_task_assignments`

Stores operational workflow records for task assignments.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `operational_task_id` | `varchar` | Yes | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `employee_id` | `varchar` | Yes | — | Related business employee UUID. |
| `assignment_role` | `varchar` | Yes | `'primary'` | Stores the assignment role value for this record. |
| `assignment_status` | `varchar` | Yes | `'assigned'` | Domain-specific lifecycle or processing state for assignment status. |
| `assigned_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `assigned_at` | `datetime` | Yes | — | Timestamp recording when assigned at. |
| `accepted_at` | `datetime` | No | — | Timestamp recording when accepted at. |
| `rejected_at` | `datetime` | No | — | Timestamp recording when rejected at. |
| `rejection_reason` | `TEXT` | No | — | Stores the rejection reason value for this record. |
| `released_at` | `datetime` | No | — | Timestamp recording when released at. |
| `release_reason` | `TEXT` | No | — | Stores the release reason value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `task_assignments_employee_status_index` on (`business_id`, `employee_id`, `assignment_status`); `operational_task_assignments_operational_task_id_assignment_status_index` on (`operational_task_id`, `assignment_status`).

### `operational_task_attachments`

Stores operational workflow records for task attachments.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `operational_task_id` | `varchar` | Yes | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `attachment_type` | `varchar` | Yes | `'general'` | Classification describing attachment type. |
| `disk` | `varchar` | Yes | `'private'` | Stores the disk value for this record. |
| `path` | `varchar` | Yes | — | Location used to access path; file contents normally live outside the relational database. |
| `original_name` | `varchar` | No | — | Stores the original name value for this record. |
| `mime_type` | `varchar` | No | — | Classification describing mime type. |
| `size_bytes` | `INTEGER` | No | — | Stores the size bytes value for this record. |
| `checksum` | `varchar` | No | — | Stores the checksum value for this record. |
| `caption` | `TEXT` | No | — | Stores the caption value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `task_attachments_type_status_index` on (`operational_task_id`, `attachment_type`, `status`).

### `operational_task_checklist_items`

Stores operational workflow records for task checklist items.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `operational_task_id` | `varchar` | Yes | — | Foreign key to `operational_tasks.id`; deletion behavior is CASCADE |
| `title` | `varchar` | Yes | — | Short human-readable title. |
| `instructions` | `TEXT` | No | — | Stores the instructions value for this record. |
| `is_required` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether is required. |
| `is_completed` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is completed. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `completed_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `operational_task_checklist_items_business_id_is_completed_index` on (`business_id`, `is_completed`); `operational_task_checklist_items_operational_task_id_sort_order_index` on (`operational_task_id`, `sort_order`).

### `operational_task_dependencies`

Stores operational workflow records for task dependencies.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `operational_task_id` | `varchar` | Yes | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `depends_on_task_id` | `varchar` | Yes | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `dependency_type` | `varchar` | Yes | `'finish_to_start'` | Classification describing dependency type. |
| `lag_minutes` | `INTEGER` | Yes | `'0'` | Stores the lag minutes value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `task_dependencies_predecessor_index` on (`business_id`, `depends_on_task_id`, `status`); `operational_task_dependencies_operational_task_id_depends_on_task_id_unique` on (`operational_task_id`, `depends_on_task_id`) — unique.

### `operational_tasks`

A unit of operational work for a property, optionally generated by a booking or workflow.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `booking_id` | `varchar` | No | — | Related booking UUID. |
| `assigned_employee_id` | `varchar` | No | — | Foreign key to `employees.id`; deletion behavior is RESTRICT |
| `parent_task_id` | `varchar` | No | — | Foreign key to `operational_tasks.id`; deletion behavior is RESTRICT |
| `reference` | `varchar` | Yes | — | Human-facing or provider-facing reference used to locate and reconcile the record. |
| `title` | `varchar` | Yes | — | Short human-readable title. |
| `task_type` | `varchar` | Yes | — | Classification describing task type. |
| `priority` | `varchar` | Yes | `'normal'` | Stores the priority value for this record. |
| `status` | `varchar` | Yes | `'pending'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `due_at` | `datetime` | No | — | Timestamp recording when due at. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `notes` | `TEXT` | No | — | Internal free-form operational notes. |
| `generation_source` | `varchar` | Yes | `'manual'` | Stores the generation source value for this record. |
| `is_recurring` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is recurring. |
| `recurrence_rule` | `TEXT` | No | — | Stores the recurrence rule value for this record. |
| `next_recurrence_at` | `datetime` | No | — | Timestamp recording when next recurrence at. |
| `generation_metadata` | `TEXT` | No | — | Stores the generation metadata value for this record. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `workflow_run_id` | `varchar` | No | — | Foreign key to `workflow_runs.id`; deletion behavior is RESTRICT |
| `workflow_step_id` | `varchar` | No | — | Foreign key to `workflow_steps.id`; deletion behavior is RESTRICT |
| `estimated_duration_minutes` | `INTEGER` | No | — | Stores the estimated duration minutes value for this record. |
| `sla_due_at` | `datetime` | No | — | Timestamp recording when sla due at. |
| `requires_verification` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether requires verification. |
| `verified_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `verified_at` | `datetime` | No | — | Timestamp recording when verified at. |
| `verification_notes` | `TEXT` | No | — | Stores the verification notes value for this record. |
| `manual_creation_reason` | `TEXT` | No | — | Stores the manual creation reason value for this record. |
| `escalated_at` | `datetime` | No | — | Timestamp recording when escalated at. |

**Indexes:** `operational_tasks_business_id_sla_due_at_status_index` on (`business_id`, `sla_due_at`, `status`); `operational_tasks_business_id_workflow_run_id_index` on (`business_id`, `workflow_run_id`); `tasks_assignee_status_due_index` on (`business_id`, `assigned_employee_id`, `status`, `due_at`); `operational_tasks_is_recurring_next_recurrence_at_status_index` on (`is_recurring`, `next_recurrence_at`, `status`); `operational_tasks_business_id_reference_unique` on (`business_id`, `reference`) — unique; `operational_tasks_business_id_property_id_status_due_at_index` on (`business_id`, `property_id`, `status`, `due_at`); `operational_tasks_business_id_id_unique` on (`business_id`, `id`) — unique; `operational_tasks_business_id_booking_id_index` on (`business_id`, `booking_id`).

## Calendar and availability

### `external_calendar_connections`

Stores external calendar integration records for connections.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `property_id` | `varchar` | Yes | — | Related property UUID. |
| `provider` | `varchar` | Yes | — | Stores the provider value for this record. |
| `external_calendar_id` | `varchar` | No | — | Stores the external calendar id value for this record. |
| `sync_direction` | `varchar` | Yes | `'bidirectional'` | Stores the sync direction value for this record. |
| `feed_url` | `varchar` | No | — | Location used to access feed url; file contents normally live outside the relational database. |
| `credentials` | `TEXT` | No | — | Stores the credentials value for this record. |
| `sync_cursor` | `varchar` | No | — | Stores the sync cursor value for this record. |
| `sync_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for sync status. |
| `last_synced_at` | `datetime` | No | — | Timestamp recording when last synced at. |
| `last_imported_at` | `datetime` | No | — | Timestamp recording when last imported at. |
| `last_exported_at` | `datetime` | No | — | Timestamp recording when last exported at. |
| `consecutive_failure_count` | `INTEGER` | Yes | `'0'` | Numeric value used for consecutive failure count. |
| `last_error` | `TEXT` | No | — | Stores the last error value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `external_calendar_connections_business_id_property_id_status_index` on (`business_id`, `property_id`, `status`); `external_calendar_connections_business_id_id_unique` on (`business_id`, `id`) — unique; `external_calendar_connections_provider_external_calendar_id_unique` on (`provider`, `external_calendar_id`) — unique.

### `external_calendar_sync_items`

Stores external calendar integration records for sync items.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `sync_run_id` | `varchar` | Yes | — | Foreign key to `external_calendar_sync_runs.id`; deletion behavior is RESTRICT |
| `external_event_id` | `varchar` | No | — | Stores the external event id value for this record. |
| `operation` | `varchar` | Yes | — | Stores the operation value for this record. |
| `validation_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for validation status. |
| `result_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for result status. |
| `source_type` | `varchar` | No | — | Classification describing source type. |
| `source_id` | `varchar` | No | — | Stores the source id value for this record. |
| `payload_hash` | `varchar` | No | — | JSON or structured data containing payload hash. |
| `payload` | `TEXT` | No | — | JSON or structured data containing payload. |
| `validation_errors` | `TEXT` | No | — | Stores the validation errors value for this record. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `processed_at` | `datetime` | No | — | Timestamp recording when processed at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `external_calendar_sync_items_source_type_source_id_index` on (`source_type`, `source_id`); `external_calendar_sync_items_business_id_external_event_id_index` on (`business_id`, `external_event_id`); `external_calendar_sync_items_sync_run_id_result_status_index` on (`sync_run_id`, `result_status`).

### `external_calendar_sync_runs`

Stores external calendar integration records for sync runs.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `external_calendar_connection_id` | `varchar` | Yes | — | Foreign key to `external_calendar_connections.id`; deletion behavior is RESTRICT |
| `direction` | `varchar` | Yes | — | Stores the direction value for this record. |
| `trigger_type` | `varchar` | Yes | `'scheduled'` | Classification describing trigger type. |
| `sync_status` | `varchar` | Yes | `'pending'` | Domain-specific lifecycle or processing state for sync status. |
| `started_at` | `datetime` | No | — | Timestamp when processing or work began. |
| `completed_at` | `datetime` | No | — | Timestamp when processing or work completed. |
| `received_count` | `INTEGER` | Yes | `'0'` | Numeric value used for received count. |
| `created_count` | `INTEGER` | Yes | `'0'` | Numeric value used for created count. |
| `updated_count` | `INTEGER` | Yes | `'0'` | Numeric value used for updated count. |
| `skipped_count` | `INTEGER` | Yes | `'0'` | Numeric value used for skipped count. |
| `conflict_count` | `INTEGER` | Yes | `'0'` | Numeric value used for conflict count. |
| `failed_count` | `INTEGER` | Yes | `'0'` | Numeric value used for failed count. |
| `cursor_before` | `varchar` | No | — | Stores the cursor before value for this record. |
| `cursor_after` | `varchar` | No | — | Stores the cursor after value for this record. |
| `failure_reason` | `TEXT` | No | — | Stores the failure reason value for this record. |
| `metadata` | `TEXT` | No | — | Extensible JSON metadata that does not replace normalized relationships. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `external_calendar_sync_runs_sync_status_started_at_index` on (`sync_status`, `started_at`); `external_sync_connection_history` on (`business_id`, `external_calendar_connection_id`, `created_at`); `external_calendar_sync_runs_business_id_id_unique` on (`business_id`, `id`) — unique.

## Platform and access control

### `impersonation_sessions`

Stores impersonation sessions records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `platform_user_id` | `varchar` | Yes | — | Foreign key to `users.id`; deletion behavior is RESTRICT |
| `authorized_by` | `varchar` | Yes | — | Foreign key to `users.id`; deletion behavior is RESTRICT |
| `reason` | `TEXT` | Yes | — | Stores the reason value for this record. |
| `ip_address` | `varchar` | No | — | Stores the ip address value for this record. |
| `user_agent` | `TEXT` | No | — | Stores the user agent value for this record. |
| `started_at` | `datetime` | Yes | — | Timestamp when processing or work began. |
| `authorized_at` | `datetime` | Yes | — | Timestamp recording when authorized at. |
| `expires_at` | `datetime` | Yes | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `ended_at` | `datetime` | No | — | Timestamp recording when ended at. |
| `termination_reason` | `TEXT` | No | — | Stores the termination reason value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `impersonation_sessions_business_id_started_at_index` on (`business_id`, `started_at`); `impersonation_sessions_platform_user_id_status_started_at_index` on (`platform_user_id`, `status`, `started_at`).

### `password_policies`

Stores password policies records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `system_key` | `varchar` | No | — | Stable identifier used for system key. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `minimum_length` | `INTEGER` | Yes | `'12'` | Stores the minimum length value for this record. |
| `requires_uppercase` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether requires uppercase. |
| `requires_lowercase` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether requires lowercase. |
| `requires_number` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether requires number. |
| `requires_symbol` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether requires symbol. |
| `password_history_count` | `INTEGER` | Yes | `'5'` | Numeric value used for password history count. |
| `maximum_age_days` | `INTEGER` | No | — | Stores the maximum age days value for this record. |
| `maximum_failed_attempts` | `INTEGER` | Yes | `'5'` | Stores the maximum failed attempts value for this record. |
| `lockout_minutes` | `INTEGER` | Yes | `'15'` | Stores the lockout minutes value for this record. |
| `session_timeout_minutes` | `INTEGER` | Yes | `'60'` | Stores the session timeout minutes value for this record. |
| `requires_mfa` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether requires mfa. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `password_policies_system_key_unique` on (`system_key`) — unique.

### `password_reset_tokens`

Stores password reset tokens records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `email` | `varchar` | Yes | — | Email address associated with the record. |
| `token` | `varchar` | Yes | — | Stores the token value for this record. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |

### `permission_separation_rules`

Stores permission separation rules records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `system_key` | `varchar` | No | — | Stable identifier used for system key. |
| `permission_id` | `varchar` | Yes | — | Foreign key to `permissions.id`; deletion behavior is RESTRICT |
| `conflicting_permission_id` | `varchar` | Yes | — | Foreign key to `permissions.id`; deletion behavior is RESTRICT |
| `enforcement` | `varchar` | Yes | `'block'` | Stores the enforcement value for this record. |
| `description` | `TEXT` | Yes | — | Longer human-readable explanation of the record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `permission_separation_rules_system_key_unique` on (`system_key`) — unique; `permission_separation_unique` on (`business_id`, `permission_id`, `conflicting_permission_id`) — unique.

### `permissions`

Defines atomic actions that roles may be allowed to perform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `workspace_id` | `varchar` | No | — | Foreign key to `workspaces.id`; deletion behavior is RESTRICT |
| `key` | `varchar` | Yes | — | Stores the key value for this record. |
| `category` | `varchar` | Yes | — | Stores the category value for this record. |
| `action` | `varchar` | Yes | — | Stores the action value for this record. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `risk_level` | `varchar` | Yes | `'normal'` | Stores the risk level value for this record. |
| `requires_audit` | `tinyint(1)` | Yes | `'1'` | Boolean flag indicating whether requires audit. |
| `is_sensitive` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is sensitive. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `permissions_key_unique` on (`key`) — unique; `permissions_workspace_id_status_index` on (`workspace_id`, `status`); `permissions_category_action_status_index` on (`category`, `action`, `status`).

### `role_permissions`

Stores role permissions records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `role_id` | `varchar` | Yes | — | Foreign key to `roles.id`; deletion behavior is RESTRICT |
| `permission_id` | `varchar` | Yes | — | Foreign key to `permissions.id`; deletion behavior is RESTRICT |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `role_permissions_permission_id_status_index` on (`permission_id`, `status`); `role_permissions_role_id_permission_id_unique` on (`role_id`, `permission_id`) — unique.

### `role_workspaces`

Stores role workspaces records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `role_id` | `varchar` | Yes | — | Foreign key to `roles.id`; deletion behavior is RESTRICT |
| `workspace_id` | `varchar` | Yes | — | Foreign key to `workspaces.id`; deletion behavior is RESTRICT |
| `access_level` | `varchar` | Yes | `'full'` | Stores the access level value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `role_workspaces_workspace_id_status_index` on (`workspace_id`, `status`); `role_workspaces_role_id_workspace_id_unique` on (`role_id`, `workspace_id`) — unique.

### `roles`

Defines system or business-specific roles at public, business, or platform scope.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `parent_role_id` | `varchar` | No | — | Foreign key to `roles.id`; deletion behavior is RESTRICT |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `slug` | `varchar` | Yes | — | Stores the slug value for this record. |
| `system_key` | `varchar` | No | — | Stable identifier used for system key. |
| `scope` | `varchar` | Yes | — | Stores the scope value for this record. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `hierarchy_level` | `INTEGER` | Yes | `'0'` | Stores the hierarchy level value for this record. |
| `is_system` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is system. |
| `is_template` | `tinyint(1)` | Yes | `'0'` | Boolean flag indicating whether is template. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `roles_system_key_unique` on (`system_key`) — unique; `roles_business_id_hierarchy_level_status_index` on (`business_id`, `hierarchy_level`, `status`); `roles_scope_status_index` on (`scope`, `status`); `roles_business_id_slug_unique` on (`business_id`, `slug`) — unique.

### `sessions`

Stores sessions records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `user_id` | `varchar` | No | — | Related platform user UUID. |
| `ip_address` | `varchar` | No | — | Stores the ip address value for this record. |
| `user_agent` | `TEXT` | No | — | Stores the user agent value for this record. |
| `payload` | `TEXT` | Yes | — | JSON or structured data containing payload. |
| `last_activity` | `INTEGER` | Yes | — | Stores the last activity value for this record. |

**Indexes:** `sessions_last_activity_index` on (`last_activity`).

### `user_business_contexts`

Stores user business contexts records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `business_id` | `varchar` | No | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `business_membership_id` | `varchar` | No | — | Foreign key to `business_memberships.id`; deletion behavior is RESTRICT |
| `active_user_role_id` | `varchar` | No | — | Foreign key to `user_roles.id`; deletion behavior is RESTRICT |
| `switched_at` | `datetime` | No | — | Timestamp recording when switched at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `user_business_contexts_business_id_status_index` on (`business_id`, `status`); `user_business_contexts_user_id_unique` on (`user_id`) — unique.

### `user_identities`

Stores user identities records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `provider` | `varchar` | Yes | — | Stores the provider value for this record. |
| `provider_user_id` | `varchar` | Yes | — | Stores the provider user id value for this record. |
| `provider_email` | `varchar` | No | — | Stores the provider email value for this record. |
| `access_token` | `TEXT` | No | — | Stores the access token value for this record. |
| `refresh_token` | `TEXT` | No | — | Stores the refresh token value for this record. |
| `token_expires_at` | `datetime` | No | — | Timestamp recording when token expires at. |
| `last_used_at` | `datetime` | No | — | Timestamp recording when last used at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `user_identities_user_id_status_index` on (`user_id`, `status`); `user_identities_provider_provider_user_id_unique` on (`provider`, `provider_user_id`) — unique.

### `user_mfa_methods`

Stores user mfa methods records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `method_type` | `varchar` | Yes | — | Classification describing method type. |
| `label` | `varchar` | No | — | Stores the label value for this record. |
| `secret` | `TEXT` | No | — | Stores the secret value for this record. |
| `recovery_codes` | `TEXT` | No | — | Stores the recovery codes value for this record. |
| `confirmed_at` | `datetime` | No | — | Timestamp recording when confirmed at. |
| `last_used_at` | `datetime` | No | — | Timestamp recording when last used at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `user_mfa_methods_user_id_method_type_status_index` on (`user_id`, `method_type`, `status`).

### `user_refresh_tokens`

Stores user refresh tokens records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `token_hash` | `varchar` | Yes | — | Stores the token hash value for this record. |
| `device_name` | `varchar` | No | — | Stores the device name value for this record. |
| `ip_address` | `varchar` | No | — | Stores the ip address value for this record. |
| `last_used_at` | `datetime` | No | — | Timestamp recording when last used at. |
| `expires_at` | `datetime` | Yes | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `revoked_at` | `datetime` | No | — | Timestamp recording when revoked at. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `user_refresh_tokens_token_hash_unique` on (`token_hash`) — unique; `user_refresh_tokens_user_id_status_expires_at_index` on (`user_id`, `status`, `expires_at`).

### `user_roles`

Assigns one or more roles to a user, optionally through a particular business membership.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `role_id` | `varchar` | Yes | — | Foreign key to `roles.id`; deletion behavior is RESTRICT |
| `business_membership_id` | `varchar` | No | — | Foreign key to `business_memberships.id`; deletion behavior is RESTRICT |
| `scope_key` | `varchar` | Yes | `'global'` | Stable identifier used for scope key. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `assigned_by` | `varchar` | No | — | Foreign key to `users.id`; deletion behavior is SET NULL |
| `assigned_at` | `datetime` | No | — | Timestamp recording when assigned at. |
| `expires_at` | `datetime` | No | — | Timestamp after which the record, token, authority, or action is no longer valid. |
| `revoked_at` | `datetime` | No | — | Timestamp recording when revoked at. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `user_roles_business_membership_id_status_index` on (`business_membership_id`, `status`); `user_roles_user_id_status_expires_at_index` on (`user_id`, `status`, `expires_at`); `user_roles_user_id_role_id_scope_key_unique` on (`user_id`, `role_id`, `scope_key`) — unique.

### `user_workspace_preferences`

Stores user workspace preferences records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `business_id` | `varchar` | Yes | — | Owning business/tenant. It is the primary boundary for authorization and data isolation. |
| `user_id` | `varchar` | Yes | — | Related platform user UUID. |
| `default_workspace` | `varchar` | Yes | `'dashboard'` | Stores the default workspace value for this record. |
| `theme` | `varchar` | Yes | `'system'` | Stores the theme value for this record. |
| `language` | `varchar` | Yes | `'en'` | Stores the language value for this record. |
| `timezone` | `varchar` | Yes | `'UTC'` | Stores the timezone value for this record. |
| `currency_display` | `varchar` | No | — | Stores the currency display value for this record. |
| `kpi_configuration` | `TEXT` | No | — | JSON or structured data containing kpi configuration. |
| `widget_configuration` | `TEXT` | No | — | JSON or structured data containing widget configuration. |
| `notification_preferences` | `TEXT` | No | — | JSON or structured data containing notification preferences. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |

**Indexes:** `user_workspace_preferences_user_id_status_index` on (`user_id`, `status`); `user_workspace_preferences_business_id_user_id_unique` on (`business_id`, `user_id`) — unique.

### `users`

The single identity and authentication record for every person using the platform, regardless of the roles they later adopt.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `email` | `varchar` | Yes | — | Email address associated with the record. |
| `phone_number` | `varchar` | No | — | Telephone number associated with the record. |
| `email_verified_at` | `datetime` | No | — | Timestamp recording when email verified at. |
| `phone_verified_at` | `datetime` | No | — | Timestamp recording when phone verified at. |
| `password` | `varchar` | No | — | Stores the password value for this record. |
| `nationality` | `varchar` | No | — | Stores the nationality value for this record. |
| `identity_verification_status` | `varchar` | Yes | `'unverified'` | Domain-specific lifecycle or processing state for identity verification status. |
| `preferred_language` | `varchar` | Yes | `'en'` | Stores the preferred language value for this record. |
| `emergency_contact` | `TEXT` | No | — | Stores the emergency contact value for this record. |
| `marketing_preferences` | `TEXT` | No | — | JSON or structured data containing marketing preferences. |
| `guest_notes` | `TEXT` | No | — | Stores the guest notes value for this record. |
| `timezone` | `varchar` | Yes | `'UTC'` | Stores the timezone value for this record. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `failed_login_attempts` | `INTEGER` | Yes | `'0'` | Stores the failed login attempts value for this record. |
| `locked_until` | `datetime` | No | — | Stores the locked until value for this record. |
| `password_changed_at` | `datetime` | No | — | Timestamp recording when password changed at. |
| `last_login_at` | `datetime` | No | — | Timestamp recording when last login at. |
| `last_seen_at` | `datetime` | No | — | Timestamp recording when last seen at. |
| `remember_token` | `varchar` | No | — | Stores the remember token value for this record. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |
| `deleted_at` | `datetime` | No | — | Soft-deletion timestamp. A value hides the record operationally without physically destroying its history. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |

**Indexes:** `users_status_created_at_index` on (`status`, `created_at`); `users_phone_number_index` on (`phone_number`); `users_identity_verification_status_status_index` on (`identity_verification_status`, `status`); `users_email_unique` on (`email`) — unique.

### `workspaces`

Stores workspaces records used by the platform.

| Field | Database type | Required | Default | Meaning |
|---|---|---:|---|---|
| `id` | `varchar` | Yes | — | Globally unique UUID primary key. It is permanent and must not be reused. |
| `key` | `varchar` | Yes | — | Stores the key value for this record. |
| `name` | `varchar` | Yes | — | Human-readable name. |
| `description` | `TEXT` | No | — | Longer human-readable explanation of the record. |
| `sort_order` | `INTEGER` | Yes | `'0'` | Numeric value used for sort order. |
| `status` | `varchar` | Yes | `'active'` | General lifecycle state, normally active, inactive, or archived. Domain-specific state is stored separately where needed. |
| `created_by` | `varchar` | No | — | User who created the record. Null is allowed for imports, seeds, and system-generated records. |
| `updated_by` | `varchar` | No | — | User who last modified the record. Null is allowed for system changes or when the user is no longer retained. |
| `created_at` | `datetime` | No | — | Timestamp when the database record was created. |
| `updated_at` | `datetime` | No | — | Timestamp when the database record was last modified. |

**Indexes:** `workspaces_key_unique` on (`key`) — unique.

