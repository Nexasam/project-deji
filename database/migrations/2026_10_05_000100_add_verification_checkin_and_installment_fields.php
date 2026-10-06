<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'account_type')) {
                $table->string('account_type', 30)->default('individual')->after('phone_number');
            }
            if (! Schema::hasColumn('users', 'identity_verification')) {
                $table->json('identity_verification')->nullable()->after('identity_verification_status');
            }
        });

        Schema::table('businesses', function (Blueprint $table): void {
            if (! Schema::hasColumn('businesses', 'verification_payload')) {
                $table->json('verification_payload')->nullable()->after('verification_status');
            }
        });

        Schema::table('properties', function (Blueprint $table): void {
            if (! Schema::hasColumn('properties', 'check_in_instructions')) {
                $table->json('check_in_instructions')->nullable()->after('description');
            }
            if (! Schema::hasColumn('properties', 'host_phone_number')) {
                $table->string('host_phone_number', 40)->nullable()->after('manager_name');
            }
            if (! Schema::hasColumn('properties', 'superhost_badge_enabled')) {
                $table->boolean('superhost_badge_enabled')->default(false)->after('host_phone_number');
            }
        });

        Schema::table('bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('bookings', 'payment_plan')) {
                $table->json('payment_plan')->nullable()->after('source_metadata');
            }
            if (! Schema::hasColumn('bookings', 'payment_due_at')) {
                $table->dateTime('payment_due_at')->nullable()->after('payment_plan');
            }
            if (! Schema::hasColumn('bookings', 'check_in_instructions_snapshot')) {
                $table->json('check_in_instructions_snapshot')->nullable()->after('payment_due_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            foreach (['check_in_instructions_snapshot', 'payment_due_at', 'payment_plan'] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('properties', function (Blueprint $table): void {
            foreach (['superhost_badge_enabled', 'host_phone_number', 'check_in_instructions'] as $column) {
                if (Schema::hasColumn('properties', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('businesses', function (Blueprint $table): void {
            if (Schema::hasColumn('businesses', 'verification_payload')) {
                $table->dropColumn('verification_payload');
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            foreach (['identity_verification', 'account_type'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
