<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('tax_enabled')->default(false)->after('pricing_currency');
            $table->decimal('tax_rate', 8, 4)->default(0)->after('tax_enabled');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('service_fee_amount', 19, 4)->default(0)->after('discount_amount');
            $table->decimal('tax_amount', 19, 4)->default(0)->after('service_fee_amount');
            $table->json('pricing_policy_snapshot')->nullable()->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', fn (Blueprint $table) => $table->dropColumn(['service_fee_amount', 'tax_amount', 'pricing_policy_snapshot']));
        Schema::table('properties', fn (Blueprint $table) => $table->dropColumn(['tax_enabled', 'tax_rate']));
    }
};
