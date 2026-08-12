<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->string('name');
            $table->string('code', 80);
            $table->json('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('property_type', 80);
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->unsignedSmallInteger('bedrooms')->default(0);
            $table->decimal('bathrooms', 4, 1)->default(0);
            $table->text('description')->nullable();
            $table->decimal('default_nightly_price', 19, 4)->nullable();
            $table->char('pricing_currency', 3)->nullable();
            $table->string('verification_status', 40)->default('unverified');
            $table->string('publication_status', 40)->default('draft');
            $table->string('readiness_status', 40)->default('not_ready');
            $table->timestamp('information_completed_at')->nullable();
            $table->timestamp('media_completed_at')->nullable();
            $table->timestamp('verification_submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignUuid('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('published_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->json('cleaning_schedule')->nullable();
            $table->string('maintenance_status', 40)->default('not_required');
            $table->foreignUuid('owner_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('manager_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'id']);
            $table->unique(['business_id', 'code']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'publication_status', 'status']);
            $table->index(['business_id', 'readiness_status', 'status']);
            $table->index(['business_id', 'published_at']);
            $table->index(['business_id', 'property_type', 'status']);
            $table->index(['business_id', 'verification_status']);
            $table->index(['manager_user_id', 'status']);
            $table->index(['latitude', 'longitude']);
        });

        Schema::create('amenities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 80)->unique();
            $table->string('name');
            $table->string('category', 80)->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('amenity_property', function (Blueprint $table) {
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();
            $table->foreignUuid('amenity_id')
                ->constrained('amenities')
                ->restrictOnDelete();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'amenity_id']);
            $table->index(['business_id', 'property_id']);
        });

        Schema::create('property_house_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['business_id', 'property_id', 'status']);
        });

        Schema::create('property_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();
            $table->string('media_type', 20);
            $table->string('storage_disk', 40)->nullable();
            $table->string('storage_path', 2048)->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'property_id', 'media_type', 'status']);
            $table->index(['property_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_media');
        Schema::dropIfExists('property_house_rules');
        Schema::dropIfExists('amenity_property');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('properties');
    }
};
