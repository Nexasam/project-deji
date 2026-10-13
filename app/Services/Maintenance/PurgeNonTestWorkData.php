<?php

namespace App\Services\Maintenance;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

final class PurgeNonTestWorkData
{
    /** Tables that are infrastructure or global configuration, never work records. */
    private const PROTECTED_TABLES = [
        'migrations', 'platform_settings', 'permissions', 'amenities',
        'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
    ];

    /** @return array{targets: array<string, array<string>>, counts: array<string, int>, protected_users: int, test_businesses: int} */
    public function preview(): array
    {
        $testBusinessIds = DB::table('businesses')->where('is_test', true)->pluck('id');
        $protectedUserIds = $this->protectedUserIds($testBusinessIds);

        $targets = [
            'businesses' => DB::table('businesses')->where('is_test', false)->pluck('id')->all(),
            'users' => DB::table('users')->whereNotIn('id', $protectedUserIds)->pluck('id')->all(),
        ];

        $targets = $this->expandTargets($targets);
        $counts = collect($targets)->map(fn (array $ids): int => count($ids))->filter()->sortDesc()->all();

        return [
            'targets' => $targets,
            'counts' => $counts,
            'protected_users' => $protectedUserIds->count(),
            'test_businesses' => $testBusinessIds->count(),
        ];
    }

    /** @return array{counts: array<string, int>, deleted: int} */
    public function execute(): array
    {
        $plan = $this->preview();

        return $this->executePlan($plan);
    }

    /** @return array{targets: array<string, array<string>>, counts: array<string, int>, owned_businesses: int} */
    public function previewUser(User $user): array
    {
        $ownedBusinessIds = $this->soleOwnerBusinessIds($user);
        $targets = $this->expandTargets([
            'users' => [$user->id],
            'businesses' => $ownedBusinessIds->all(),
        ]);

        return [
            'targets' => $targets,
            'counts' => collect($targets)->map(fn (array $ids): int => count($ids))->filter()->sortDesc()->all(),
            'owned_businesses' => $ownedBusinessIds->count(),
        ];
    }

    /** @return array{counts: array<string, int>, deleted: int} */
    public function executeUser(User $user): array
    {
        return $this->executePlan($this->previewUser($user));
    }

    /** @param array{targets: array<string, array<string>>, counts: array<string, int>} $plan @return array{counts: array<string, int>, deleted: int} */
    private function executePlan(array $plan): array
    {

        return DB::transaction(function () use ($plan): array {
            Schema::disableForeignKeyConstraints();

            try {
                $deleted = 0;
                foreach ($plan['targets'] as $table => $ids) {
                    if ($ids === [] || in_array($table, self::PROTECTED_TABLES, true)) {
                        continue;
                    }

                    foreach (array_chunk($ids, 500) as $chunk) {
                        $deleted += DB::table($table)->whereIn('id', $chunk)->delete();
                    }
                }
            } finally {
                Schema::enableForeignKeyConstraints();
            }

            return ['counts' => $plan['counts'], 'deleted' => $deleted];
        }, 3);
    }

    private function soleOwnerBusinessIds(User $user): Collection
    {
        $candidateIds = DB::table('business_memberships')
            ->join('user_roles', 'user_roles.business_membership_id', '=', 'business_memberships.id')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('business_memberships.user_id', $user->id)
            ->where('business_memberships.status', 'active')
            ->where('user_roles.status', 'active')
            ->whereNull('user_roles.revoked_at')
            ->where('roles.system_key', 'business_owner')
            ->pluck('business_memberships.business_id')
            ->unique();

        return $candidateIds->filter(function (string $businessId) use ($user): bool {
            return ! DB::table('business_memberships')
                ->join('user_roles', 'user_roles.business_membership_id', '=', 'business_memberships.id')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('business_memberships.business_id', $businessId)
                ->where('business_memberships.user_id', '!=', $user->id)
                ->where('business_memberships.status', 'active')
                ->where('user_roles.status', 'active')
                ->whereNull('user_roles.revoked_at')
                ->where('roles.system_key', 'business_owner')
                ->exists();
        })->values();
    }

    private function protectedUserIds(Collection $testBusinessIds): Collection
    {
        $ids = DB::table('users')->where('email', 'like', '%.test')->pluck('id');

        $platformAdmins = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.status', 'active')
            ->whereNull('user_roles.revoked_at')
            ->where('roles.scope', 'platform')
            ->where('roles.status', 'active')
            ->pluck('user_roles.user_id');

        $testMembers = $testBusinessIds->isEmpty()
            ? collect()
            : DB::table('business_memberships')->whereIn('business_id', $testBusinessIds)->pluck('user_id');

        $testGuests = $testBusinessIds->isEmpty()
            ? collect()
            : DB::table('bookings')->whereIn('business_id', $testBusinessIds)->pluck('guest_user_id');

        return $ids->merge($platformAdmins)->merge($testMembers)->merge($testGuests)
            ->filter()->unique()->values();
    }

    /** @param array<string, array<string>> $targets @return array<string, array<string>> */
    private function expandTargets(array $targets): array
    {
        $foreignKeys = $this->foreignKeys();
        $targets = collect($targets)->map(fn (array $ids) => array_values(array_unique($ids)))->all();

        do {
            $changed = false;

            foreach ($foreignKeys as $foreignKey) {
                $parent = $foreignKey['parent_table'];
                $child = $foreignKey['child_table'];

                if ($foreignKey['parent_column'] !== 'id'
                    || in_array($child, self::PROTECTED_TABLES, true)
                    || empty($targets[$parent])
                    || ! Schema::hasColumn($child, 'id')) {
                    continue;
                }

                $childIds = collect();
                foreach (array_chunk($targets[$parent], 500) as $chunk) {
                    $childIds = $childIds->merge(
                        DB::table($child)->whereIn($foreignKey['child_column'], $chunk)->pluck('id')
                    );
                }

                $existing = $targets[$child] ?? [];
                $merged = array_values(array_unique([...$existing, ...$childIds->filter()->all()]));
                if (count($merged) > count($existing)) {
                    $targets[$child] = $merged;
                    $changed = true;
                }
            }
        } while ($changed);

        return $targets;
    }

    /** @return array<int, array{child_table:string,child_column:string,parent_table:string,parent_column:string}> */
    private function foreignKeys(): array
    {
        return match (DB::getDriverName()) {
            'mysql', 'mariadb' => collect(DB::select(<<<'SQL'
                SELECT TABLE_NAME AS child_table, COLUMN_NAME AS child_column,
                       REFERENCED_TABLE_NAME AS parent_table, REFERENCED_COLUMN_NAME AS parent_column
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL
                SQL))->map(fn ($row) => (array) $row)->all(),
            'sqlite' => $this->sqliteForeignKeys(),
            default => throw new RuntimeException('Work-data cleanup supports MySQL, MariaDB, and SQLite.'),
        };
    }

    private function sqliteForeignKeys(): array
    {
        $foreignKeys = [];
        $tables = collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"))->pluck('name');
        foreach ($tables as $table) {
            foreach (DB::select('PRAGMA foreign_key_list("'.str_replace('"', '""', $table).'")') as $key) {
                $foreignKeys[] = [
                    'child_table' => $table,
                    'child_column' => $key->from,
                    'parent_table' => $key->table,
                    'parent_column' => $key->to,
                ];
            }
        }

        return $foreignKeys;
    }
}
