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
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->string('owner_type', 100);
            $table->string('owner_id', 191);
            $table->string('document_type', 80);
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('searchable_text')->nullable();
            $table->unsignedInteger('current_version_number')->default(0);
            $table->string('access_level', 40)->default('restricted');
            $table->date('expires_on')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'owner_type', 'owner_id']);
            $table->index(['business_id', 'document_type', 'status']);
            $table->index(['business_id', 'expires_on', 'status']);
            $table->index(['business_id', 'title']);
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('document_id')->constrained('documents')->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('storage_disk', 40);
            $table->string('storage_path', 2048);
            $table->string('original_name');
            $table->string('mime_type', 191);
            $table->unsignedBigInteger('file_size');
            $table->string('checksum', 128);
            $table->text('change_notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['document_id', 'version_number']);
            $table->index(['business_id', 'document_id', 'status']);
            $table->index(['business_id', 'checksum']);
        });

        Schema::create('document_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('document_id')->constrained('documents')->restrictOnDelete();
            $table->string('grantee_type', 100);
            $table->string('grantee_id', 191);
            $table->string('permission', 40);
            $table->string('status', 40)->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['document_id', 'grantee_type', 'grantee_id', 'permission'],
                'document_grantee_permission_unique'
            );
            $table->index(['business_id', 'grantee_type', 'grantee_id', 'status'], 'document_grantee_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_permissions');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
    }
};
