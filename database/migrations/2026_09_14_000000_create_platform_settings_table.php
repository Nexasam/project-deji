<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('group_key', 80);
            $table->string('key', 150)->unique();
            $table->json('value');
            $table->string('value_type', 30);
            $table->string('label');
            $table->text('description')->nullable();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['group_key', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
