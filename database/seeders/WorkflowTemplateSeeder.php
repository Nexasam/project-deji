<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkflowTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $catalogue = [
            'guest_journey' => ['Guest Journey', 'booking.confirmed', [
                ['pre_arrival', 'Prepare for arrival', 'guest_welcome', 'reception_officer', false],
                ['check_in', 'Complete guest check-in', 'guest_welcome', 'reception_officer', false],
                ['stay_support', 'Monitor guest stay', 'guest_welcome', 'customer_support', false],
                ['check_out', 'Complete guest check-out', 'inspection', 'reception_officer', false],
                ['review_request', 'Request guest review', null, 'customer_support', false],
            ]],
            'checkout_cleaning' => ['Checkout Cleaning', 'booking.checked_out', [
                ['clean', 'Clean property after checkout', 'cleaning', 'cleaner', false],
                ['inspect', 'Verify cleaning quality', 'inspection', 'inspector', true],
            ]],
            'daily_cleaning' => ['Daily Cleaning', null, [['clean', 'Complete daily cleaning', 'cleaning', 'cleaner', true]]],
            'deep_cleaning' => ['Deep Cleaning', null, [['deep_clean', 'Complete deep cleaning', 'deep_cleaning', 'cleaner', true]]],
            'linen_replacement' => ['Linen Replacement', null, [['replace_linen', 'Replace and count linen', 'cleaning', 'cleaner', true]]],
            'preventive_maintenance' => ['Preventive Maintenance', null, [['maintain', 'Perform scheduled maintenance', 'maintenance', 'maintenance_technician', true]]],
            'reactive_maintenance' => ['Reactive Maintenance', 'maintenance.issue.created', [['repair', 'Investigate and repair issue', 'repair', 'maintenance_technician', true]]],
            'emergency_maintenance' => ['Emergency Maintenance', 'maintenance.issue.emergency', [['respond', 'Respond to emergency issue', 'repair', 'maintenance_technician', true]]],
            'property_onboarding' => ['Property Onboarding', 'property.created', [
                ['prepare_profile', 'Complete property profile', 'inspection', 'property_manager', false],
                ['verify_property', 'Verify property readiness', 'inspection', 'inspector', true],
            ]],
            'property_verification' => ['Property Verification', 'property.verification_requested', [['verify', 'Verify property', 'inspection', 'inspector', true]]],
            'property_publication' => ['Property Publication', 'property.verified', [['publish', 'Approve and publish property', null, 'property_manager', true]]],
            'refund_processing' => ['Refund Processing', 'refund.requested', [['approve_refund', 'Review refund request', null, 'accountant', true]]],
            'expense_approval' => ['Expense Approval', 'expense.submitted', [['approve_expense', 'Review submitted expense', null, 'accountant', true]]],
            'fire_safety_inspection' => ['Fire Safety Inspection', null, [['inspect', 'Complete fire safety inspection', 'inspection', 'inspector', true]]],
            'insurance_renewal' => ['Insurance Renewal', 'document.expiring', [['renew', 'Renew property insurance', null, 'property_manager', true]]],
            'property_licence_renewal' => ['Property Licence Renewal', 'document.expiring', [['renew', 'Renew property licence', null, 'property_manager', true]]],
            'asset_inspection' => ['Asset Inspection', null, [['inspect', 'Inspect asset condition', 'inspection', 'inspector', true]]],
        ];

        foreach ($catalogue as $key => [$name, $event, $steps]) {
            $templateId = $this->upsert('workflow_templates', ['business_id' => null, 'template_key' => $key, 'version' => 1], [
                'name' => $name,
                'description' => "System workflow template for {$name}.",
                'trigger_type' => $event === null ? 'manual' : 'domain_event',
                'trigger_event' => $event,
                'trigger_configuration' => null,
                'is_system' => true,
                'is_active' => true,
                'status' => 'active',
            ]);

            foreach ($steps as $index => [$stepKey, $stepName, $taskType, $roleKey, $requiresVerification]) {
                $roleId = DB::table('roles')->where('system_key', $roleKey)->value('id');
                $this->upsert('workflow_template_steps', ['workflow_template_id' => $templateId, 'step_key' => $stepKey], [
                    'name' => $stepName,
                    'description' => null,
                    'step_order' => $index + 1,
                    'step_type' => $taskType === null ? 'action' : 'task',
                    'task_type' => $taskType,
                    'responsible_role_id' => $roleId,
                    'responsible_department_id' => null,
                    'estimated_duration_minutes' => null,
                    'sla_minutes' => null,
                    'requires_verification' => $requiresVerification,
                    'requires_approval' => $taskType === null,
                    'checklist_template' => null,
                    'failure_configuration' => null,
                    'escalation_configuration' => null,
                    'configuration' => null,
                    'status' => 'active',
                ]);
            }
        }
    }

    /** @param array<string, mixed> $unique
     * @param  array<string, mixed>  $values
     */
    private function upsert(string $table, array $unique, array $values): string
    {
        $query = DB::table($table);
        foreach ($unique as $column => $value) {
            $value === null ? $query->whereNull($column) : $query->where($column, $value);
        }
        $id = $query->value('id');
        $now = now();
        if ($id !== null) {
            DB::table($table)->where('id', $id)->update([...$values, 'updated_at' => $now]);

            return $id;
        }
        $id = (string) Str::uuid();
        DB::table($table)->insert([...$unique, ...$values, 'id' => $id, 'created_at' => $now, 'updated_at' => $now]);

        return $id;
    }
}
