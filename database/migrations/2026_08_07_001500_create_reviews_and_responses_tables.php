<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->foreignUuid('guest_user_id')->constrained('users')->restrictOnDelete();
            $table->string('token_hash', 128)->unique();
            $table->timestamp('sent_at')->nullable();
            $table->dateTime('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('status', 40)->default('pending');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique(['booking_id', 'guest_user_id']);
            $table->index(['status', 'expires_at']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id');
            $table->foreignUuid('guest_user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->unsignedTinyInteger('cleanliness_rating')->nullable();
            $table->unsignedTinyInteger('communication_rating')->nullable();
            $table->unsignedTinyInteger('location_rating')->nullable();
            $table->unsignedTinyInteger('value_rating')->nullable();
            $table->unsignedTinyInteger('accuracy_rating')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_verified_stay')->default(false);
            $table->string('moderation_status', 40)->default('pending');
            $table->string('sentiment')->nullable();
            $table->decimal('sentiment_score', 5, 4)->nullable();
            $table->dateTime('submitted_at');
            $table->timestamp('published_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->foreign(['business_id', 'booking_id'])->references(['business_id', 'id'])->on('bookings')->restrictOnDelete();
            $table->unique(['booking_id', 'guest_user_id']);
            $table->unique(['business_id', 'property_id', 'id']);
            $table->index(['business_id', 'property_id', 'moderation_status', 'published_at'], 'reviews_property_publication_index');
        });

        Schema::create('review_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('review_id')->constrained('reviews')->restrictOnDelete();
            $table->foreignUuid('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('content');
            $table->string('moderation_status', 40)->default('pending');
            $table->dateTime('submitted_at');
            $table->timestamp('published_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique('review_id');
            $table->index(['business_id', 'moderation_status', 'published_at'], 'review_response_moderation_publish_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_responses');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('review_invitations');
    }
};
