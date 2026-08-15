<?php

use App\Enums\DiscountType;
use App\Enums\PricingAdjustmentType;
use App\Enums\PricingRuleType;
use App\Enums\PropertyPromotionType;
use App\Enums\PropertyStaffRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('property_staff_assignments')) {
            Schema::create('property_staff_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('employee_id');
            $table->string('assignment_role', 50)->default(PropertyStaffRole::Other->value);
            $table->text('responsibilities')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('assignment_status', 40)->default('active');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'employee_id'])
                ->references(['business_id', 'id'])->on('employees')->restrictOnDelete();
            $table->index(['business_id', 'property_id', 'assignment_role', 'assignment_status'], 'property_staff_property_lookup');
            $table->index(['business_id', 'employee_id', 'assignment_status'], 'property_staff_employee_lookup');
            $table->index(['property_id', 'assignment_role', 'is_primary', 'assignment_status'], 'property_primary_staff_lookup');
            $table->index(['starts_on', 'ends_on']);
            });
        }

        if (! Schema::hasTable('property_marketplace_listings')) {
            Schema::create('property_marketplace_listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->string('slug', 191)->unique();
            $table->string('public_title');
            $table->string('short_summary', 500)->nullable();
            $table->longText('public_description')->nullable();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->boolean('instant_booking_enabled')->default(false);
            $table->unsignedSmallInteger('minimum_advance_notice_hours')->default(0);
            $table->unsignedSmallInteger('maximum_advance_booking_days')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->json('seo_keywords')->nullable();
            $table->unsignedTinyInteger('seo_score')->nullable();
            $table->json('seo_score_details')->nullable();
            $table->unsignedTinyInteger('listing_quality_score')->nullable();
            $table->json('listing_quality_details')->nullable();
            $table->string('publication_status', 40)->default('draft');
            $table->boolean('is_publication_eligible')->default(false);
            $table->json('publication_eligibility_details')->nullable();
            $table->timestamp('eligibility_checked_at')->nullable();
            $table->foreignUuid('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->foreignUuid('unpublished_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('unpublished_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique('property_id');
            $table->index(['business_id', 'publication_status', 'status'], 'property_listing_publication_idx');
            $table->index(['is_publication_eligible', 'publication_status'], 'property_listing_eligibility_idx');
            });
        }

        Schema::create('property_pricing_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->string('name');
            $table->string('rule_type', 40)->default(PricingRuleType::DateRange->value);
            $table->string('adjustment_type', 40)->default(PricingAdjustmentType::FixedPrice->value);
            $table->decimal('adjustment_value', 19, 4);
            $table->char('currency', 3)->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->json('applicable_weekdays')->nullable();
            $table->unsignedSmallInteger('minimum_stay_nights')->nullable();
            $table->unsignedSmallInteger('maximum_stay_nights')->nullable();
            $table->unsignedSmallInteger('minimum_advance_booking_days')->nullable();
            $table->unsignedSmallInteger('maximum_advance_booking_days')->nullable();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_stackable')->default(false);
            $table->timestamp('effective_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->index(['business_id', 'property_id', 'status', 'starts_on', 'ends_on'], 'property_pricing_date_lookup');
            $table->index(['property_id', 'rule_type', 'priority', 'status'], 'property_pricing_rule_idx');
            $table->index(['effective_at', 'expires_at', 'status'], 'property_pricing_effective_idx');
        });

        Schema::create('property_promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->string('name');
            $table->text('public_description')->nullable();
            $table->string('promotion_type', 40)->default(PropertyPromotionType::Manual->value);
            $table->string('discount_type', 20)->default(DiscountType::Percentage->value);
            $table->decimal('discount_value', 19, 4);
            $table->string('coupon_code', 100)->nullable();
            $table->date('booking_window_starts_on')->nullable();
            $table->date('booking_window_ends_on')->nullable();
            $table->date('stay_window_starts_on')->nullable();
            $table->date('stay_window_ends_on')->nullable();
            $table->json('applicable_weekdays')->nullable();
            $table->unsignedSmallInteger('minimum_stay_nights')->nullable();
            $table->decimal('minimum_booking_amount', 19, 4)->nullable();
            $table->decimal('maximum_discount_amount', 19, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->unsignedInteger('total_usage_limit')->nullable();
            $table->unsignedInteger('per_guest_usage_limit')->nullable();
            $table->boolean('is_stackable')->default(false);
            $table->string('publication_status', 40)->default('draft');
            $table->timestamp('effective_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['business_id', 'coupon_code']);
            $table->index(['business_id', 'property_id', 'publication_status', 'status'], 'property_promotions_publication_lookup');
            $table->index(['booking_window_starts_on', 'booking_window_ends_on'], 'property_promo_booking_window_idx');
            $table->index(['stay_window_starts_on', 'stay_window_ends_on'], 'property_promo_stay_window_idx');
        });

        Schema::create('property_health_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->date('snapshot_date');
            $table->string('period_type', 30)->default('daily');
            $table->unsignedTinyInteger('score');
            $table->unsignedTinyInteger('occupancy_score')->nullable();
            $table->unsignedTinyInteger('guest_rating_score')->nullable();
            $table->unsignedTinyInteger('maintenance_score')->nullable();
            $table->unsignedTinyInteger('cleaning_score')->nullable();
            $table->unsignedTinyInteger('revenue_growth_score')->nullable();
            $table->unsignedTinyInteger('asset_condition_score')->nullable();
            $table->unsignedTinyInteger('inspection_score')->nullable();
            $table->unsignedTinyInteger('booking_conversion_score')->nullable();
            $table->json('component_details')->nullable();
            $table->json('supporting_metrics')->nullable();
            $table->text('ai_explanation')->nullable();
            $table->json('recommended_improvements')->nullable();
            $table->string('calculation_version', 80);
            $table->timestamp('calculated_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['business_id', 'property_id', 'period_type', 'snapshot_date'], 'property_health_period_unique');
            $table->index(['business_id', 'property_id', 'snapshot_date', 'score'], 'property_health_history_lookup');
        });

        Schema::create('asset_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('asset_id');
            $table->string('media_type', 20)->default('image');
            $table->string('storage_disk', 40)->nullable();
            $table->string('storage_path', 2048)->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'property_id', 'asset_id'])
                ->references(['business_id', 'property_id', 'id'])->on('assets')->restrictOnDelete();
            $table->index(['business_id', 'property_id', 'asset_id', 'status'], 'asset_media_asset_lookup');
            $table->index(['asset_id', 'sort_order'], 'asset_media_sort_idx');
            $table->index(['asset_id', 'is_primary'], 'asset_media_primary_idx');
        });

        Schema::create('review_analyses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('review_id');
            $table->string('sentiment', 40)->nullable();
            $table->decimal('sentiment_score', 5, 4)->nullable();
            $table->text('summary')->nullable();
            $table->json('positive_themes')->nullable();
            $table->json('complaint_themes')->nullable();
            $table->json('keywords')->nullable();
            $table->string('follow_up_priority', 20)->nullable();
            $table->boolean('follow_up_recommended')->default(false);
            $table->string('model_provider', 80)->nullable();
            $table->string('model_name', 120)->nullable();
            $table->string('model_version', 80)->nullable();
            $table->string('analysis_version', 80);
            $table->timestamp('analysed_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id', 'review_id'])
                ->references(['business_id', 'property_id', 'id'])->on('reviews')->restrictOnDelete();
            $table->unique(['review_id', 'analysis_version']);
            $table->index(['business_id', 'property_id', 'analysed_at'], 'review_analysis_property_lookup');
            $table->index(['sentiment', 'follow_up_priority', 'status'], 'review_analysis_followup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_analyses');
        Schema::dropIfExists('asset_media');
        Schema::dropIfExists('property_health_snapshots');
        Schema::dropIfExists('property_promotions');
        Schema::dropIfExists('property_pricing_rules');
        Schema::dropIfExists('property_marketplace_listings');
        Schema::dropIfExists('property_staff_assignments');
    }
};
