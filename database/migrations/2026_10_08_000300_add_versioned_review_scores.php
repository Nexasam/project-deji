<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::table('reviews', function (Blueprint $table): void { $table->unsignedInteger('questionnaire_version')->nullable()->after('rating'); $table->decimal('weighted_score',5,2)->nullable()->after('questionnaire_version'); $table->json('score_breakdown')->nullable()->after('weighted_score'); }); }
    public function down(): void { Schema::table('reviews', fn (Blueprint $table) => $table->dropColumn(['questionnaire_version','weighted_score','score_breakdown'])); }
};
