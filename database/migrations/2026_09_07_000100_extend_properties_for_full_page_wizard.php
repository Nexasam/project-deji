<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->string('booking_mode', 20)->nullable()->after('property_type');
            $table->decimal('floor_area_sqm', 10, 2)->nullable()->after('bathrooms');
        });

        Schema::create('property_channel_connections', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->string('provider', 40);
            $table->string('external_reference', 2048)->nullable();
            $table->string('connection_status', 40)->default('pending');
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['property_id', 'provider']);
            $table->index(['business_id', 'connection_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_channel_connections');
        Schema::table('properties', fn (Blueprint $table) => $table->dropColumn(['booking_mode', 'floor_area_sqm']));
    }
};
