<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_calendar_exports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->text('plain_token');
            $table->char('token_hash', 64)->unique('calendar_export_token_hash_unique');
            $table->timestamp('generated_at');
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('active_key', 20)->nullable()->default('active');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreign(['business_id', 'property_id'], 'calendar_export_property_fk')
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['property_id', 'active_key'], 'property_active_calendar_export_unique');
        });

        Schema::table('property_availability_blocks', function (Blueprint $table) {
            $table->unique(['external_calendar_connection_id', 'source_reference'], 'calendar_block_source_unique');
        });
    }

    public function down(): void
    {
        Schema::table('property_availability_blocks', fn (Blueprint $table) => $table->dropUnique('calendar_block_source_unique'));
        Schema::dropIfExists('property_calendar_exports');
    }
};
