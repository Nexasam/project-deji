<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table): void {
            $table->boolean('is_test')->default(false)->index();
        });

        Schema::table('properties', function (Blueprint $table): void {
            $table->boolean('is_test')->default(false)->index();
        });
    }

    public function down(): void
    {
        Schema::table('properties', fn (Blueprint $table) => $table->dropColumn('is_test'));
        Schema::table('businesses', fn (Blueprint $table) => $table->dropColumn('is_test'));
    }
};
