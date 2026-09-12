<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_business_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->boolean('ai_enabled')->default(true);
            $table->json('enabled_capabilities')->nullable();
            $table->json('data_use_preferences')->nullable();
            $table->json('automation_limits')->nullable();
            $table->json('approval_thresholds')->nullable();
            $table->json('permitted_autonomous_actions')->nullable();
            $table->unsignedInteger('conversation_retention_days')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique('business_id');
        });
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('workspace_id')->nullable()->constrained('workspaces')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('context_type')->nullable();
            $table->uuid('context_id')->nullable();
            $table->string('conversation_status', 40)->default('active');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['business_id', 'user_id', 'last_message_at']);
            $table->index(['context_type', 'context_id']);
        });
        Schema::create('ai_conversation_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ai_conversation_id')->constrained('ai_conversations')->restrictOnDelete();
            $table->uuid('parent_message_id')->nullable();
            $table->unsignedInteger('sequence');
            $table->string('role', 30);
            $table->longText('content')->nullable();
            $table->json('tool_calls')->nullable();
            $table->json('citations')->nullable();
            $table->string('model_provider', 80)->nullable();
            $table->string('model_name', 120)->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('message_status', 40)->default('completed');
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign('parent_message_id')->references('id')->on('ai_conversation_messages')->restrictOnDelete();
            $table->unique(['ai_conversation_id', 'sequence']);
        });
        Schema::create('ai_recommendation_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('ai_recommendation_id')->constrained('ai_recommendations')->restrictOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('feedback_type', 40);
            $table->text('reason')->nullable();
            $table->timestamp('snoozed_until')->nullable();
            $table->json('action_taken')->nullable();
            $table->json('outcome')->nullable();
            $table->decimal('outcome_score', 5, 4)->nullable();
            $table->dateTime('recorded_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['ai_recommendation_id', 'recorded_at'], 'ai_recommendation_feedback_date_idx');
            $table->index(['business_id', 'feedback_type', 'recorded_at'], 'ai_recommendation_feedback_type_idx');
        });
        Schema::create('ai_prediction_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->restrictOnDelete();
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings')->restrictOnDelete();
            $table->string('prediction_type', 100);
            $table->string('subject_type');
            $table->uuid('subject_id');
            $table->decimal('predicted_value', 18, 6)->nullable();
            $table->string('predicted_label', 120)->nullable();
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->json('factors')->nullable();
            $table->json('evidence')->nullable();
            $table->dateTime('prediction_for');
            $table->dateTime('generated_at');
            $table->timestamp('valid_until')->nullable();
            $table->string('model_provider', 80)->nullable();
            $table->string('model_name', 120)->nullable();
            $table->string('model_version', 80)->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['business_id', 'prediction_type', 'prediction_for'], 'ai_predictions_business_type_idx');
            $table->index(['subject_type', 'subject_id', 'prediction_for'], 'ai_predictions_subject_idx');
        });
        Schema::create('ai_knowledge_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('document_id')->nullable()->constrained('documents')->restrictOnDelete();
            $table->string('source_type', 40);
            $table->string('title');
            $table->string('source_uri', 2048)->nullable();
            $table->string('access_scope', 40)->default('business');
            $table->string('content_hash', 128)->nullable();
            $table->string('indexing_status', 40)->default('pending');
            $table->string('index_version', 80)->nullable();
            $table->timestamp('last_indexed_at')->nullable();
            $table->text('indexing_error')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['business_id', 'indexing_status', 'status']);
        });
        Schema::create('ai_knowledge_chunks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ai_knowledge_source_id')->constrained('ai_knowledge_sources')->cascadeOnDelete();
            $table->unsignedInteger('chunk_index');
            $table->longText('content');
            $table->string('content_hash', 128);
            $table->string('vector_reference', 512)->nullable();
            $table->unsignedInteger('token_count')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['ai_knowledge_source_id', 'chunk_index']);
            $table->index('content_hash');
        });
        Schema::table('ai_recommendations', function (Blueprint $table) {
            $table->string('risk_level', 30)->nullable()->after('priority');
            $table->string('execution_status', 40)->nullable()->after('recommendation_status');
            $table->timestamp('snoozed_until')->nullable()->after('valid_until');
            $table->foreignUuid('superseded_by_id')->nullable()->after('snoozed_until')->constrained('ai_recommendations')->restrictOnDelete();
            $table->index(['business_id', 'execution_status']);
            $table->index(['business_id', 'snoozed_until']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_recommendations', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'execution_status']);
            $table->dropIndex(['business_id', 'snoozed_until']);
            $table->dropForeign(['superseded_by_id']);
            $table->dropColumn(['risk_level', 'execution_status', 'snoozed_until', 'superseded_by_id']);
        });
        Schema::dropIfExists('ai_knowledge_chunks');
        Schema::dropIfExists('ai_knowledge_sources');
        Schema::dropIfExists('ai_prediction_snapshots');
        Schema::dropIfExists('ai_recommendation_feedback');
        Schema::dropIfExists('ai_conversation_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_business_settings');
    }
};
