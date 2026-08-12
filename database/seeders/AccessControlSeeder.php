<?php

namespace Database\Seeders;

use App\Enums\RoleScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        $workspaceIds = $this->seedWorkspaces();
        $permissionIds = $this->seedPermissions($workspaceIds);
        $roleIds = $this->seedRoles();

        $this->seedRolePermissions($roleIds, $permissionIds);
        $this->seedRoleWorkspaces($roleIds, $workspaceIds);
        $this->seedSeparationRules($permissionIds);
        $this->seedPasswordPolicy();
    }

    /** @return array<string, string> */
    private function seedWorkspaces(): array
    {
        $workspaces = [
            'marketplace' => ['Marketplace', 10],
            'dashboard' => ['Dashboard', 20],
            'properties' => ['Properties', 30],
            'bookings' => ['Bookings', 40],
            'calendar' => ['Calendar', 50],
            'finance' => ['Finance', 60],
            'operations' => ['Operations', 70],
            'reports' => ['Reports', 80],
            'ai_insights' => ['AI Insights', 90],
            'settings' => ['Settings', 100],
            'platform_admin' => ['Platform Administration', 110],
        ];

        $ids = [];
        foreach ($workspaces as $key => [$name, $sortOrder]) {
            $ids[$key] = $this->upsertWithId('workspaces', ['key' => $key], [
                'name' => $name,
                'sort_order' => $sortOrder,
                'status' => 'active',
            ]);
        }

        return $ids;
    }

    /** @param array<string, string> $workspaceIds
     * @return array<string, string>
     */
    private function seedPermissions(array $workspaceIds): array
    {
        $permissions = [
            'marketplace.search' => ['marketplace', 'marketplace', 'search', 'normal', false],
            'marketplace.favourite' => ['marketplace', 'marketplace', 'favourite', 'normal', false],
            'profile.update' => ['settings', 'profile', 'update', 'sensitive', true],
            'review.create' => ['marketplace', 'review', 'create', 'normal', false],
            'business.view' => ['dashboard', 'business', 'view', 'normal', false],
            'business.edit' => ['settings', 'business', 'edit', 'sensitive', true],
            'business.archive' => ['settings', 'business', 'archive', 'critical', true],
            'subscription.manage' => ['settings', 'subscription', 'manage', 'critical', true],
            'property.create' => ['properties', 'property', 'create', 'sensitive', true],
            'property.view' => ['properties', 'property', 'view', 'normal', false],
            'property.edit' => ['properties', 'property', 'edit', 'sensitive', true],
            'property.assign_staff' => ['properties', 'property_staff', 'assign', 'sensitive', true],
            'property.manage_pricing' => ['properties', 'property_pricing', 'manage', 'critical', true],
            'property.manage_promotions' => ['properties', 'property_promotion', 'manage', 'sensitive', true],
            'property.manage_listing' => ['marketplace', 'property_listing', 'manage', 'sensitive', true],
            'property.preview_listing' => ['marketplace', 'property_listing', 'preview', 'normal', false],
            'property.publish' => ['properties', 'property', 'publish', 'critical', true],
            'property.view_finance' => ['properties', 'property_finance', 'view', 'sensitive', true],
            'property.view_analytics' => ['properties', 'property_analytics', 'view', 'sensitive', true],
            'property.view_history' => ['properties', 'property_history', 'view', 'sensitive', true],
            'property.view_health' => ['properties', 'property_health', 'view', 'normal', false],
            'property.manage_assets' => ['properties', 'property_asset', 'manage', 'sensitive', true],
            'property.rotate_asset_qr' => ['properties', 'property_asset', 'rotate_qr', 'critical', true],
            'property.manage_documents' => ['properties', 'property_document', 'manage', 'sensitive', true],
            'property.respond_reviews' => ['properties', 'property_review', 'respond', 'sensitive', true],
            'property.archive' => ['properties', 'property', 'archive', 'critical', true],
            'property.delete' => ['properties', 'property', 'delete', 'critical', true],
            'booking.create' => ['bookings', 'booking', 'create', 'sensitive', true],
            'booking.modify' => ['bookings', 'booking', 'modify', 'sensitive', true],
            'booking.cancel' => ['bookings', 'booking', 'cancel', 'critical', true],
            'booking.approve' => ['bookings', 'booking', 'approve', 'critical', true],
            'booking.view_history' => ['bookings', 'booking', 'view_history', 'sensitive', true],
            'booking.check_in' => ['bookings', 'booking', 'check_in', 'sensitive', true],
            'booking.check_out' => ['bookings', 'booking', 'check_out', 'sensitive', true],
            'booking.manage_guests' => ['bookings', 'booking_guest', 'manage', 'sensitive', true],
            'booking.assign_staff' => ['bookings', 'booking_staff', 'assign', 'sensitive', true],
            'booking.manage_financial_documents' => ['bookings', 'booking_financial_document', 'manage', 'critical', true],
            'booking.communicate' => ['bookings', 'booking_communication', 'send', 'sensitive', true],
            'booking.manage_disputes' => ['bookings', 'booking_dispute', 'manage', 'critical', true],
            'booking.configure_automation' => ['bookings', 'booking_automation', 'configure', 'critical', true],
            'calendar.view' => ['calendar', 'calendar', 'view', 'normal', false],
            'calendar.manage_availability' => ['calendar', 'availability', 'manage', 'critical', true],
            'calendar.manage_blocks' => ['calendar', 'availability_block', 'manage', 'sensitive', true],
            'calendar.manage_connections' => ['calendar', 'calendar_connection', 'manage', 'critical', true],
            'calendar.sync' => ['calendar', 'calendar_connection', 'sync', 'sensitive', true],
            'payment.record' => ['finance', 'payment', 'record', 'sensitive', true],
            'payment.verify' => ['finance', 'payment', 'verify', 'critical', true],
            'expense.create' => ['finance', 'expense', 'create', 'sensitive', true],
            'report.export' => ['reports', 'report', 'export', 'sensitive', true],
            'profit.view' => ['reports', 'profit', 'view', 'sensitive', true],
            'refund.process' => ['finance', 'refund', 'process', 'critical', true],
            'finance.view_transactions' => ['finance', 'financial_transaction', 'view', 'sensitive', true],
            'finance.manage_accounts' => ['finance', 'financial_account', 'manage', 'critical', true],
            'finance.reconcile' => ['finance', 'financial_reconciliation', 'manage', 'critical', true],
            'finance.manage_revenue' => ['finance', 'revenue', 'manage', 'critical', true],
            'finance.manage_expenses' => ['finance', 'expense', 'manage', 'sensitive', true],
            'finance.approve_expenses' => ['finance', 'expense', 'approve', 'critical', true],
            'finance.request_refund' => ['finance', 'refund', 'request', 'sensitive', true],
            'finance.approve_refund' => ['finance', 'refund', 'approve', 'critical', true],
            'finance.manage_tax' => ['finance', 'tax', 'manage', 'critical', true],
            'finance.view_forecasts' => ['finance', 'financial_forecast', 'view', 'sensitive', true],
            'finance.manage_reports' => ['finance', 'financial_report', 'manage', 'sensitive', true],
            'task.assign' => ['operations', 'task', 'assign', 'sensitive', true],
            'task.view_assigned' => ['operations', 'task', 'view_assigned', 'normal', false],
            'task.complete' => ['operations', 'task', 'complete', 'sensitive', true],
            'task.verify' => ['operations', 'task', 'verify', 'sensitive', true],
            'task.reassign' => ['operations', 'task', 'reassign', 'sensitive', true],
            'workflow.manage' => ['operations', 'workflow', 'manage', 'critical', true],
            'inventory.view' => ['operations', 'inventory', 'view', 'normal', false],
            'inventory.manage' => ['operations', 'inventory', 'manage', 'sensitive', true],
            'inventory.adjust' => ['operations', 'inventory', 'adjust', 'critical', true],
            'employee.manage_capabilities' => ['settings', 'employee_capability', 'manage', 'sensitive', true],
            'approval.request' => ['settings', 'approval', 'request', 'sensitive', true],
            'approval.view' => ['settings', 'approval', 'view', 'sensitive', true],
            'approval.act' => ['settings', 'approval', 'act', 'critical', true],
            'approval.configure' => ['settings', 'approval', 'configure', 'critical', true],
            'approval.delegate' => ['settings', 'approval', 'delegate', 'critical', true],
            'governance.view_audit' => ['settings', 'governance', 'view_audit', 'critical', true],
            'governance.manage_retention' => ['settings', 'governance', 'manage_retention', 'critical', true],
            'governance.manage_privacy' => ['settings', 'governance', 'manage_privacy', 'critical', true],
            'maintenance.create' => ['operations', 'maintenance', 'create', 'sensitive', true],
            'inspection.perform' => ['operations', 'inspection', 'perform', 'sensitive', true],
            'inspection.approve' => ['operations', 'inspection', 'approve', 'critical', true],
            'employee.invite' => ['settings', 'employee', 'invite', 'sensitive', true],
            'employee.remove' => ['settings', 'employee', 'remove', 'critical', true],
            'employee.change_role' => ['settings', 'employee', 'change_role', 'critical', true],
            'employee.reset_password' => ['settings', 'employee', 'reset_password', 'critical', true],
            'document.upload' => ['operations', 'document', 'upload', 'sensitive', true],
            'document.download' => ['operations', 'document', 'download', 'sensitive', true],
            'document.archive' => ['operations', 'document', 'archive', 'critical', true],
            'document.delete' => ['operations', 'document', 'delete', 'critical', true],
            'document.share' => ['operations', 'document', 'share', 'critical', true],
            'ai.view' => ['ai_insights', 'ai', 'view', 'normal', false],
            'ai.accept' => ['ai_insights', 'ai', 'accept', 'sensitive', true],
            'ai.reject' => ['ai_insights', 'ai', 'reject', 'sensitive', true],
            'ai.configure' => ['ai_insights', 'ai', 'configure', 'critical', true],
            'ai.chat' => ['ai_insights', 'ai_conversation', 'create', 'normal', false],
            'ai.view_predictions' => ['ai_insights', 'ai_prediction', 'view', 'sensitive', true],
            'ai.manage_knowledge' => ['ai_insights', 'ai_knowledge', 'manage', 'critical', true],
            'ai.automate' => ['ai_insights', 'ai_automation', 'execute', 'critical', true],
            'platform.business.manage' => ['platform_admin', 'platform', 'business_manage', 'critical', true],
            'platform.business.verify' => ['platform_admin', 'platform', 'business_verify', 'critical', true],
            'platform.property.verify' => ['platform_admin', 'platform', 'property_verify', 'critical', true],
            'platform.analytics.view' => ['platform_admin', 'platform', 'analytics_view', 'sensitive', true],
            'platform.configure' => ['platform_admin', 'platform', 'configure', 'critical', true],
            'platform.moderate' => ['platform_admin', 'platform', 'moderate', 'critical', true],
            'platform.impersonate' => ['platform_admin', 'platform', 'impersonate', 'critical', true],
        ];

        $ids = [];
        foreach ($permissions as $key => [$workspace, $category, $action, $risk, $sensitive]) {
            $ids[$key] = $this->upsertWithId('permissions', ['key' => $key], [
                'workspace_id' => $workspaceIds[$workspace],
                'category' => $category,
                'action' => $action,
                'risk_level' => $risk,
                'requires_audit' => true,
                'is_sensitive' => $sensitive,
                'status' => 'active',
            ]);
        }

        return $ids;
    }

    /** @return array<string, string> */
    private function seedRoles(): array
    {
        $roles = [
            'guest' => ['Guest', RoleScope::Public->value, 100],
            'platform_super_admin' => ['Platform Super Administrator', RoleScope::Platform->value, 0],
            'business_owner' => ['Business Owner', RoleScope::Business->value, 10],
            'property_manager' => ['Property Manager', RoleScope::Business->value, 20],
            'reception_officer' => ['Reception / Reservations Officer', RoleScope::Business->value, 30],
            'accountant' => ['Accountant', RoleScope::Business->value, 30],
            'operations_manager' => ['Operations Manager', RoleScope::Business->value, 30],
            'cleaner' => ['Cleaner', RoleScope::Business->value, 50],
            'maintenance_technician' => ['Maintenance Technician', RoleScope::Business->value, 50],
            'inspector' => ['Inspector / QA Officer', RoleScope::Business->value, 40],
            'customer_support' => ['Customer Support', RoleScope::Business->value, 40],
        ];

        $ids = [];
        foreach ($roles as $key => [$name, $scope, $level]) {
            $ids[$key] = $this->upsertWithId('roles', ['system_key' => $key], [
                'business_id' => null,
                'name' => $name,
                'slug' => $key,
                'scope' => $scope,
                'hierarchy_level' => $level,
                'is_system' => true,
                'is_template' => $scope === RoleScope::Business->value,
                'status' => 'active',
            ]);
        }

        return $ids;
    }

    /** @param array<string, string> $roleIds
     * @param  array<string, string>  $permissionIds
     */
    private function seedRolePermissions(array $roleIds, array $permissionIds): void
    {
        $propertyWorkspace = [
            'property.view', 'property.assign_staff', 'property.manage_pricing',
            'property.manage_promotions', 'property.manage_listing', 'property.preview_listing',
            'property.view_finance', 'property.view_analytics', 'property.view_history',
            'property.view_health', 'property.manage_assets', 'property.rotate_asset_qr',
            'property.manage_documents', 'property.respond_reviews',
        ];
        $businessManagement = ['business.view', 'business.edit', 'property.create', 'property.edit', 'property.publish', 'property.archive', ...$propertyWorkspace];
        $bookings = [
            'booking.create', 'booking.modify', 'booking.cancel', 'booking.approve',
            'booking.view_history', 'booking.check_in', 'booking.check_out',
            'booking.manage_guests', 'booking.assign_staff',
            'booking.manage_financial_documents', 'booking.communicate',
            'booking.manage_disputes', 'booking.configure_automation',
        ];
        $finance = [
            'payment.record', 'payment.verify', 'expense.create', 'report.export',
            'profit.view', 'refund.process', 'finance.view_transactions',
            'finance.manage_accounts', 'finance.reconcile', 'finance.manage_revenue',
            'finance.manage_expenses', 'finance.approve_expenses',
            'finance.request_refund', 'finance.approve_refund', 'finance.manage_tax',
            'finance.view_forecasts', 'finance.manage_reports',
        ];
        $calendar = ['calendar.view', 'calendar.manage_availability', 'calendar.manage_blocks', 'calendar.manage_connections', 'calendar.sync'];
        $operations = ['task.assign', 'task.view_assigned', 'task.complete', 'task.verify', 'task.reassign', 'workflow.manage', 'inventory.view', 'inventory.manage', 'inventory.adjust', 'maintenance.create', 'inspection.perform', 'inspection.approve', 'document.upload', 'document.download'];
        $team = ['employee.invite', 'employee.remove', 'employee.change_role', 'employee.reset_password', 'employee.manage_capabilities'];
        $approvals = ['approval.request', 'approval.view', 'approval.act', 'approval.configure', 'approval.delegate'];
        $governance = ['governance.view_audit', 'governance.manage_retention', 'governance.manage_privacy'];
        $ai = ['ai.view', 'ai.accept', 'ai.reject', 'ai.configure', 'ai.chat', 'ai.view_predictions', 'ai.manage_knowledge', 'ai.automate'];

        $map = [
            'guest' => ['marketplace.search', 'marketplace.favourite', 'profile.update', 'review.create', 'booking.create', 'booking.view_history', 'payment.record'],
            'business_owner' => array_values(array_unique(array_merge(
                $businessManagement,
                ['business.archive', 'subscription.manage'],
                $bookings,
                $calendar,
                $finance,
                $operations,
                $team,
                $approvals,
                $governance,
                ['document.archive', 'document.delete', 'document.share'],
                $ai
            ))),
            'property_manager' => array_merge($businessManagement, $bookings, $calendar, $operations, ['payment.record', 'profit.view', 'report.export'], ['employee.invite', 'employee.manage_capabilities'], ['approval.request', 'approval.view', 'approval.act'], ['ai.view', 'ai.accept', 'ai.reject', 'ai.chat', 'ai.view_predictions']),
            'reception_officer' => ['business.view', 'property.view', 'property.edit', 'property.preview_listing', 'property.view_health', 'booking.create', 'booking.modify', 'booking.cancel', 'booking.view_history', 'booking.check_in', 'booking.check_out', 'booking.manage_guests', 'booking.assign_staff', 'booking.communicate', 'calendar.view', 'calendar.manage_availability', 'calendar.manage_blocks', 'payment.record', 'document.upload'],
            'accountant' => ['business.view', 'property.view', 'property.view_finance', 'property.view_analytics', 'booking.view_history', 'booking.manage_financial_documents', 'booking.manage_disputes', ...$finance, 'approval.request', 'approval.view', 'approval.act', 'document.upload', 'document.download', 'ai.view', 'ai.chat', 'ai.view_predictions'],
            'operations_manager' => ['business.view', 'property.view', 'property.edit', 'property.assign_staff', 'property.view_history', 'property.view_health', 'property.manage_assets', 'property.rotate_asset_qr', 'property.manage_documents', 'booking.view_history', 'calendar.view', 'calendar.manage_blocks', ...$operations, 'report.export', 'employee.invite', 'employee.manage_capabilities', 'approval.request', 'approval.view', 'approval.act', 'ai.view', 'ai.accept', 'ai.reject', 'ai.chat', 'ai.view_predictions'],
            'cleaner' => ['property.view', 'property.view_health', 'task.view_assigned', 'task.complete', 'inventory.view', 'document.upload', 'document.download'],
            'maintenance_technician' => ['property.view', 'property.view_health', 'property.manage_assets', 'task.view_assigned', 'task.complete', 'inventory.view', 'maintenance.create', 'document.upload', 'document.download'],
            'inspector' => ['property.view', 'property.view_health', 'property.manage_documents', 'task.view_assigned', 'task.complete', 'task.verify', 'inspection.perform', 'inspection.approve', 'maintenance.create', 'document.upload', 'document.download'],
            'customer_support' => ['business.view', 'property.view', 'property.preview_listing', 'booking.create', 'booking.modify', 'booking.cancel', 'booking.view_history', 'booking.manage_guests', 'booking.communicate', 'booking.manage_disputes', 'document.upload'],
            'platform_super_admin' => array_keys($permissionIds),
        ];

        foreach ($map as $roleKey => $permissionKeys) {
            foreach (array_unique($permissionKeys) as $permissionKey) {
                $this->upsertWithId('role_permissions', [
                    'role_id' => $roleIds[$roleKey],
                    'permission_id' => $permissionIds[$permissionKey],
                ], ['status' => 'active']);
            }
        }
    }

    /** @param array<string, string> $roleIds
     * @param  array<string, string>  $workspaceIds
     */
    private function seedRoleWorkspaces(array $roleIds, array $workspaceIds): void
    {
        $map = [
            'guest' => ['marketplace' => 'full', 'bookings' => 'limited', 'settings' => 'limited'],
            'business_owner' => ['marketplace' => 'full', 'dashboard' => 'full', 'properties' => 'full', 'bookings' => 'full', 'calendar' => 'full', 'finance' => 'full', 'operations' => 'full', 'reports' => 'full', 'ai_insights' => 'full', 'settings' => 'full'],
            'property_manager' => ['marketplace' => 'full', 'dashboard' => 'full', 'properties' => 'full', 'bookings' => 'full', 'calendar' => 'full', 'finance' => 'limited', 'operations' => 'full', 'reports' => 'full', 'ai_insights' => 'full', 'settings' => 'limited'],
            'reception_officer' => ['dashboard' => 'limited', 'properties' => 'limited', 'bookings' => 'full', 'calendar' => 'full', 'finance' => 'limited', 'settings' => 'limited'],
            'accountant' => ['dashboard' => 'limited', 'properties' => 'limited', 'bookings' => 'limited', 'calendar' => 'limited', 'finance' => 'full', 'reports' => 'full', 'ai_insights' => 'limited', 'settings' => 'limited'],
            'operations_manager' => ['dashboard' => 'full', 'properties' => 'full', 'bookings' => 'limited', 'calendar' => 'full', 'operations' => 'full', 'reports' => 'full', 'ai_insights' => 'full', 'settings' => 'limited'],
            'cleaner' => ['properties' => 'limited', 'bookings' => 'limited', 'calendar' => 'limited', 'operations' => 'limited', 'settings' => 'limited'],
            'maintenance_technician' => ['properties' => 'limited', 'calendar' => 'limited', 'operations' => 'limited', 'settings' => 'limited'],
            'inspector' => ['properties' => 'limited', 'calendar' => 'limited', 'operations' => 'full', 'settings' => 'limited'],
            'customer_support' => ['dashboard' => 'limited', 'properties' => 'limited', 'bookings' => 'full', 'calendar' => 'limited', 'settings' => 'limited'],
            'platform_super_admin' => array_fill_keys(array_keys($workspaceIds), 'full'),
        ];

        foreach ($map as $roleKey => $workspaces) {
            foreach ($workspaces as $workspaceKey => $accessLevel) {
                $this->upsertWithId('role_workspaces', [
                    'role_id' => $roleIds[$roleKey],
                    'workspace_id' => $workspaceIds[$workspaceKey],
                ], ['access_level' => $accessLevel, 'status' => 'active']);
            }
        }
    }

    /** @param array<string, string> $permissionIds */
    private function seedSeparationRules(array $permissionIds): void
    {
        $this->upsertWithId('permission_separation_rules', [
            'system_key' => 'payment-verification-refund-processing',
        ], [
            'business_id' => null,
            'permission_id' => $permissionIds['payment.verify'],
            'conflicting_permission_id' => $permissionIds['refund.process'],
            'enforcement' => 'warn',
            'description' => 'Payment verification and refund approval should be performed by different users where staffing permits.',
            'status' => 'active',
        ]);
    }

    private function seedPasswordPolicy(): void
    {
        $this->upsertWithId('password_policies', ['system_key' => 'platform-default'], [
            'business_id' => null,
            'name' => 'Platform Default',
            'minimum_length' => 12,
            'requires_uppercase' => true,
            'requires_lowercase' => true,
            'requires_number' => true,
            'requires_symbol' => false,
            'password_history_count' => 5,
            'maximum_failed_attempts' => 5,
            'lockout_minutes' => 15,
            'session_timeout_minutes' => 60,
            'requires_mfa' => false,
            'status' => 'active',
        ]);
    }

    /** @param array<string, mixed> $unique
     * @param  array<string, mixed>  $values
     */
    private function upsertWithId(string $table, array $unique, array $values): string
    {
        $query = DB::table($table);
        foreach ($unique as $column => $value) {
            $query->where($column, $value);
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
