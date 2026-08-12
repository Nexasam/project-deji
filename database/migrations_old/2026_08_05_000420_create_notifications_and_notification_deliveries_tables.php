<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->string('recipient_type', 100);
            $table->string('recipient_id', 191);
            $table->string('event_type', 100);
            $table->string('category', 80);
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('priority', 20)->default('normal');
            $table->string('status', 40)->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'recipient_type', 'recipient_id', 'status'], 'notifications_recipient_status_index');
            $table->index(['business_id', 'event_type', 'created_at']);
            $table->index(['business_id', 'category', 'status']);
            $table->index(['status', 'scheduled_at']);
        });

        Schema::create('notification_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('notification_id')->constrained('platform_notifications')->restrictOnDelete();
            $table->string('channel', 40);
            $table->string('destination', 255)->nullable();
            $table->string('provider', 80)->nullable();
            $table->string('provider_reference', 191)->nullable();
            $table->string('status', 40)->default('pending');
            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['business_id', 'notification_id', 'channel', 'status'], 'notification_channel_status_index');
            $table->index(['status', 'attempted_at']);
            $table->index(['provider', 'provider_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_deliveries');
        Schema::dropIfExists('platform_notifications');
    }
};
