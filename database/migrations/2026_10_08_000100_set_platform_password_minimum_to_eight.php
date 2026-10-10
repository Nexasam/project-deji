<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('password_policies')
            ->where('system_key', 'platform-default')
            ->update(['minimum_length' => 8, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('password_policies')
            ->where('system_key', 'platform-default')
            ->update(['minimum_length' => 12, 'updated_at' => now()]);
    }
};
