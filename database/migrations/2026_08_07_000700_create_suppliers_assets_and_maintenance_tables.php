<?php

use App\Enums\AssetCondition;
use App\Enums\MaintenanceRecordStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->json('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'name', 'status']);
            $table->unique(['business_id', 'id']);
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('property_id');
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->string('asset_code', 100);
            $table->string('name');
            $table->string('category', 80);
            $table->string('serial_number', 191)->nullable();
            $table->string('qr_identifier', 120)->unique();
            $table->string('qr_token_hash', 128)->nullable();
            $table->timestamp('qr_token_rotated_at')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expires_on')->nullable();
            $table->string('condition', 40)->default(AssetCondition::Good->value);
            $table->decimal('replacement_value', 19, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'property_id'])
                ->references(['business_id', 'id'])->on('properties')->restrictOnDelete();
            $table->unique(['business_id', 'asset_code']);
            $table->unique(['business_id', 'id']);
            $table->unique(['business_id', 'property_id', 'id']);
            $table->index(['business_id', 'property_id', 'category', 'status']);
            $table->index(['business_id', 'condition', 'status']);
            $table->index(['serial_number', 'status']);
            $table->index('warranty_expires_on');
        });

        Schema::create('asset_maintenance_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('asset_id');
            $table->string('name');
            $table->string('frequency', 40);
            $table->unsignedSmallInteger('interval')->default(1);
            $table->date('starts_on')->nullable();
            $table->date('next_due_on')->nullable();
            $table->text('instructions')->nullable();
            $table->string('status', 40)->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['business_id', 'asset_id'])
                ->references(['business_id', 'id'])->on('assets')->restrictOnDelete();
            $table->index(['business_id', 'next_due_on', 'status']);
        });

        Schema::create('asset_maintenance_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('business_id')->constrained('businesses')->restrictOnDelete();
            $table->uuid('asset_id');
            $table->foreignUuid('maintenance_schedule_id')->nullable()
                ->constrained('asset_maintenance_schedules')->restrictOnDelete();
            $table->foreignUuid('operational_task_id')->nullable()
                ->constrained('operational_tasks')->restrictOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->string('status', 40)->default(MaintenanceRecordStatus::Scheduled->value);
            $table->date('scheduled_for')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('work_performed')->nullable();
            $table->decimal('cost_amount', 19, 4)->nullable();
            $table->char('currency', 3)->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign(['business_id', 'asset_id'])
                ->references(['business_id', 'id'])->on('assets')->restrictOnDelete();
            $table->index(['business_id', 'asset_id', 'status']);
            $table->index(['business_id', 'scheduled_for', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance_records');
        Schema::dropIfExists('asset_maintenance_schedules');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('suppliers');
    }
};
