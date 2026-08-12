<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->uuidMorphs('owner');
            $table->string('title');
            $table->string('category', 80);
            $table->string('document_number', 191)->nullable();
            $table->string('issuer')->nullable();
            $table->text('description')->nullable();
            $table->text('searchable_text')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('effective_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->unsignedSmallInteger('reminder_days_before_expiry')->nullable();
            $table->timestamp('last_expiry_reminder_at')->nullable();
            $table->string('confidentiality', 40)->default('internal');
            $table->string('verification_status', 40)->default('unverified');
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('current_version_number')->default(1);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'category', 'status']);
            $table->index(['business_id', 'title']);
            $table->index(['business_id', 'expires_on', 'status']);
            $table->index(['business_id', 'verification_status', 'status']);
            $table->index(['owner_type', 'owner_id', 'category']);
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('document_id')->constrained('documents')->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('storage_disk', 40);
            $table->string('storage_path', 2048);
            $table->string('original_name');
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('size_bytes');
            $table->string('checksum', 128)->nullable();
            $table->text('change_summary')->nullable();
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['document_id', 'version_number']);
            $table->index(['business_id', 'created_at']);
            $table->index('checksum');
        });

        Schema::create('document_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('user_role_id')->nullable()->constrained('user_roles')->cascadeOnDelete();
            $table->string('access_level', 20);
            $table->timestamp('expires_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['document_id', 'user_id', 'access_level']);
            $table->unique(['document_id', 'user_role_id', 'access_level']);
            $table->index(['business_id', 'status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_permissions');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
    }
};
