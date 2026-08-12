<?php

use App\Enums\CleaningScheduleFrequency;
use App\Enums\PropertyMaintenanceStatus;
use App\Enums\PropertyOperationalStatus;
use App\Enums\PropertyPublicationStatus;
use App\Enums\PropertyReadinessStatus;
use App\Enums\PropertyStatus;
use App\Enums\PropertyVerificationStatus;
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
            $table->string('verification_status', 40)
                ->default(PropertyVerificationStatus::Unverified->value);
            $table->string('maintenance_status', 40)
                ->default(PropertyMaintenanceStatus::NotRequired->value);
            $table->string('publication_status', 40)
                ->default(PropertyPublicationStatus::Draft->value);
            $table->string('readiness_status', 40)
                ->default(PropertyReadinessStatus::NotReady->value);
            $table->string('operational_status', 40)
                ->default(PropertyOperationalStatus::Unavailable->value);
            $table->timestamp('operational_status_updated_at')->nullable();
            $table->timestamp('information_completed_at')->nullable();
            $table->timestamp('media_completed_at')->nullable();
            $table->timestamp('verification_submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->uuid('verified_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->uuid('published_by')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('status', 40)->default(PropertyStatus::Active->value);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'code']);
            $table->unique(['business_id', 'id']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'property_type', 'status']);
            $table->index(['business_id', 'verification_status']);
            $table->index(['business_id', 'maintenance_status']);
            $table->index(['business_id', 'publication_status', 'status']);
            $table->index(['business_id', 'readiness_status', 'status']);
            $table->index(['business_id', 'operational_status', 'status']);
            $table->index(['latitude', 'longitude']);
            $table->index(['manager_name', 'status']);
        });

        Schema::create('amenities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 80)->unique();
            $table->string('name');
            $table->string('category', 80)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 40)->default('active');
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index(['name', 'status']);
        });

        Schema::create('property_amenities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('property_id')
                ->constrained('properties')
                ->restrictOnDelete();
            $table->foreignUuid('amenity_id')
                ->constrained('amenities')
                ->restrictOnDelete();
            $table->text('details')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 40)->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['property_id', 'amenity_id']);
            $table->index(['business_id', 'property_id', 'status']);
        });

        Schema::create('property_house_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('property_id')
                ->constrained('properties')
                ->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 40)->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'property_id', 'status']);
            $table->index(['property_id', 'sort_order']);
        });

        Schema::create('property_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('property_id')
                ->constrained('properties')
                ->restrictOnDelete();
            $table->string('media_type', 20);
            $table->string('storage_disk', 40)->nullable();
            $table->string('storage_path', 2048)->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->string('status', 40)->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'property_id', 'media_type', 'status']);
            $table->index(['property_id', 'sort_order']);
            $table->index(['property_id', 'is_primary']);
        });

        Schema::create('property_cleaning_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();
            $table->foreignUuid('property_id')
                ->constrained('properties')
                ->restrictOnDelete();
            $table->string('name');
            $table->string('frequency', 40)
                ->default(CleaningScheduleFrequency::BetweenStays->value);
            $table->json('days_of_week')->nullable();
            $table->time('preferred_start_time')->nullable();
            $table->time('preferred_end_time')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 40)->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'property_id', 'status']);
            $table->index(['property_id', 'frequency', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_cleaning_schedules');
        Schema::dropIfExists('property_media');
        Schema::dropIfExists('property_house_rules');
        Schema::dropIfExists('property_amenities');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('properties');
    }
};
