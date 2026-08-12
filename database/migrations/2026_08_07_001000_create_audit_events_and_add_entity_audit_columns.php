<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'businesses',
            'properties',
            'amenities',
            'property_amenities',
            'property_house_rules',
            'property_media',
            'property_cleaning_schedules',
            'users',
            'business_memberships',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        foreach (['bookings', 'payments'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        Schema::table('properties', function (Blueprint $table) {
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('published_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('audit_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->nullable()->constrained('businesses')->restrictOnDelete();
            $table->nullableUuidMorphs('auditable');
            $table->string('event_type', 120);
            $table->text('description')->nullable();
            $table->json('before_values')->nullable();
            $table->json('after_values')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at');
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['business_id', 'event_type', 'occurred_at']);
            $table->index(['created_by', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['published_by']);
        });

        foreach (['bookings', 'payments'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('updated_by');
            });
        }

        foreach ([
            'businesses',
            'properties',
            'amenities',
            'property_amenities',
            'property_house_rules',
            'property_media',
            'property_cleaning_schedules',
            'users',
            'business_memberships',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('updated_by');
                $table->dropConstrainedForeignId('created_by');
            });
        }
    }
};
