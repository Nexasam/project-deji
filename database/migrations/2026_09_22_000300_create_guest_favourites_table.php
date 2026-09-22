<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_favourites', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('property_id')->constrained('properties')->cascadeOnDelete();
            $table->timestamp('favourited_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'property_id']);
            $table->index(['user_id', 'favourited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_favourites');
    }
};
