<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_disputes', function (Blueprint $table): void {
            $table->string('priority', 20)->default('normal')->after('description');
            $table->foreignUuid('assigned_to')->nullable()->after('dispute_status')->constrained('users')->nullOnDelete();
            $table->timestamp('due_at')->nullable()->after('assigned_to');
            $table->decimal('approved_amount', 19, 4)->nullable()->after('resolution');
            $table->index(['dispute_status', 'priority', 'due_at'], 'platform_dispute_queue_index');
            $table->index(['assigned_to', 'dispute_status'], 'platform_dispute_assignee_index');
        });
    }

    public function down(): void
    {
        Schema::table('booking_disputes', function (Blueprint $table): void {
            $table->dropForeign(['assigned_to']);
            $table->dropIndex('platform_dispute_queue_index');
            $table->dropIndex('platform_dispute_assignee_index');
            $table->dropColumn(['priority', 'assigned_to', 'due_at', 'approved_amount']);
        });
    }
};
