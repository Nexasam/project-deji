<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table): void {
            $table->string('receipt_disk', 40)->nullable()->after('description');
            $table->string('receipt_path', 2048)->nullable()->after('receipt_disk');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', fn (Blueprint $table) => $table->dropColumn(['receipt_disk', 'receipt_path']));
    }
};
