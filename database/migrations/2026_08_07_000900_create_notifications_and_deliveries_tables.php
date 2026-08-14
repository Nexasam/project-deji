<?php

use App\Enums\NotificationDeliveryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('type', 100);
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'read_at', 'created_at']);
            $table->index(['business_id', 'type', 'created_at']);
        });

        Schema::create('notification_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->foreignUuid('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->string('channel', 20);
            $table->string('destination')->nullable();
            $table->string('provider', 80)->nullable();
            $table->string('provider_reference', 191)->nullable();
            $table->string('status', 40)->default(NotificationDeliveryStatus::Pending->value);
            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('provider_metadata')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['notification_id', 'channel', 'destination'], 'notification_delivery_target_unique');
            $table->index(['status', 'last_attempted_at']);
            $table->index(['provider', 'provider_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_deliveries');
        Schema::dropIfExists('notifications');
    }
};
