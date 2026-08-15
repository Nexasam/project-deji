<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_setup_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->string('step_key', 80);
            $table->unsignedTinyInteger('sort_order');
            $table->boolean('is_required')->default(false);
            $table->string('state', 40)->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->foreignUuid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['property_id', 'step_key']);
            $table->index(['business_id', 'property_id', 'state', 'sort_order'], 'property_setup_resume_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_setup_steps');
    }
};
