<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_availability_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->uuid('booking_id')->nullable();
            $table->string('block_type', 40);
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('state', 40)->default('active');
            $table->string('source_provider', 80)->nullable();
            $table->string('source_reference', 191)->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->foreignUuid('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('release_reason')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])
                ->on('properties')
                ->restrictOnDelete();
            $table->foreign(['business_id', 'property_id', 'booking_id'])
                ->references(['business_id', 'property_id', 'id'])
                ->on('bookings')
                ->restrictOnDelete();
            $table->index(
                ['business_id', 'property_id', 'state', 'starts_on', 'ends_on'],
                'property_availability_lookup'
            );
            $table->index(['booking_id', 'state']);
            $table->index(['source_provider', 'source_reference']);
        });

        DB::statement(
            'ALTER TABLE property_availability_blocks '
            .'ADD CONSTRAINT property_availability_dates_check CHECK (ends_on > starts_on)'
        );

        Schema::create('booking_channel_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->string('channel', 80);
            $table->string('external_booking_reference', 191);
            $table->string('external_property_reference', 191)->nullable();
            $table->string('external_account_reference', 191)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('synchronized_at')->nullable();
            $table->string('synchronization_state', 40)->default('pending');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])
                ->on('bookings')
                ->restrictOnDelete();
            $table->unique(['channel', 'external_booking_reference']);
            $table->index(['business_id', 'booking_id', 'status']);
        });

        Schema::create('booking_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->string('previous_status', 40)->nullable();
            $table->string('new_status', 40);
            $table->text('reason')->nullable();
            $table->string('source', 80)->default('system');
            $table->foreignUuid('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])
                ->on('bookings')
                ->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'occurred_at'], 'booking_status_timeline_index');
            $table->index(['business_id', 'new_status', 'occurred_at']);
        });

        Schema::create('booking_date_changes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('booking_id');
            $table->date('old_arrival_date');
            $table->date('old_departure_date');
            $table->date('new_arrival_date');
            $table->date('new_departure_date');
            $table->text('reason')->nullable();
            $table->foreignUuid('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('availability_state', 40)->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->foreign(['business_id', 'booking_id'])
                ->references(['business_id', 'id'])
                ->on('bookings')
                ->restrictOnDelete();
            $table->index(['business_id', 'booking_id', 'occurred_at'], 'booking_date_change_timeline_index');
            $table->index(['business_id', 'availability_state', 'occurred_at']);
        });

        DB::statement(
            'ALTER TABLE booking_date_changes '
            .'ADD CONSTRAINT booking_date_changes_old_dates_check '
            .'CHECK (old_departure_date > old_arrival_date)'
        );
        DB::statement(
            'ALTER TABLE booking_date_changes '
            .'ADD CONSTRAINT booking_date_changes_new_dates_check '
            .'CHECK (new_departure_date > new_arrival_date)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_date_changes');
        Schema::dropIfExists('booking_status_history');
        Schema::dropIfExists('booking_channel_links');
        Schema::dropIfExists('property_availability_blocks');
    }
};
