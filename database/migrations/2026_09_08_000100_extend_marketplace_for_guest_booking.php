<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->unsignedSmallInteger('beds')->default(1)->after('bedrooms');
        });

        Schema::table('property_marketplace_listings', function (Blueprint $table): void {
            $table->json('stay_categories')->nullable()->after('public_description');
        });
    }

    public function down(): void
    {
        Schema::table('property_marketplace_listings', function (Blueprint $table): void {
            $table->dropColumn('stay_categories');
        });

        Schema::table('properties', function (Blueprint $table): void {
            $table->dropColumn('beds');
        });
    }
};
