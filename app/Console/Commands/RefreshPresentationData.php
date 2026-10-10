<?php

namespace App\Console\Commands;

use Database\Seeders\PresentationDemoSeeder;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class RefreshPresentationData extends Command
{
    protected $signature = 'presentation:refresh {--execute : Delete sandbox records} {--clear-only : Do not rebuild after cleanup} {--confirm= : Required confirmation phrase}';

    protected $description = 'Safely remove only recognized presentation data and rebuild it without touching live records';

    private const CONFIRMATION = 'REFRESH-PRESENTATION-DATA';
    private const BUSINESS_EMAILS = ['owner@lagoonstays.test', 'owner@coastlineresidences.test'];
    private const PROPERTY_CODES = ['LAG-001', 'LAG-002', 'LAG-003', 'LAG-004', 'LAG-005', 'CSR-001', 'CSR-002', 'CSR-003', 'CSR-004', 'CSR-005', 'DEMO-REVIEW-001'];

    public function handle(): int
    {
        $businessIds = DB::table('businesses')->where(function ($query): void {
            $query->where('is_test', true)->orWhereIn('email', self::BUSINESS_EMAILS);
        })->pluck('id');
        $propertyIds = DB::table('properties')->where(function ($query): void {
            $query->where('is_test', true)->orWhereIn('code', self::PROPERTY_CODES);
        })->pluck('id');
        $userIds = DB::table('users')->where(function ($query): void {
            $query->whereIn('email', self::BUSINESS_EMAILS)
                ->orWhere('email', 'like', '%.demo@verifiedshortlet.test')
                ->orWhereIn('email', ['guest.demo@verifiedshortlet.test', 'verification.admin@verifiedshortlet.test', 'disputes.admin@verifiedshortlet.test']);
        })->pluck('id');

        $this->table(['Scope', 'Records'], [
            ['Recognized businesses', $businessIds->count()],
            ['Recognized properties', $propertyIds->count()],
            ['Recognized presentation users', $userIds->count()],
        ]);

        $violations = $this->safetyViolations($businessIds->all(), $propertyIds->all(), $userIds->all());
        if ($violations !== []) {
            $this->error('Cleanup stopped because recognized seed records are now connected to data outside the presentation namespace.');
            foreach ($violations as $violation) $this->line(' - '.$violation);
            return self::FAILURE;
        }

        if (! $this->option('execute')) {
            $this->info('Safety check passed. This was a dry run; no records were changed.');
            $this->line('Run with --execute --confirm='.self::CONFIRMATION.' to refresh presentation data.');
            return self::SUCCESS;
        }
        if (! hash_equals(self::CONFIRMATION, (string) $this->option('confirm'))) {
            $this->error('The exact confirmation phrase is required.');
            return self::FAILURE;
        }

        try {
            DB::transaction(function () use ($businessIds, $userIds): void {
                $this->withoutForeignKeyChecks(function () use ($businessIds, $userIds): void {
                    foreach ($this->tables() as $table) {
                        if (in_array($table, ['migrations', 'platform_settings', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'], true)) continue;
                        $columns = Schema::getColumnListing($table);
                        $query = DB::table($table);
                        $query->where(function ($where) use ($columns, $businessIds, $userIds): void {
                            $hasCondition = false;
                            if (in_array('business_id', $columns, true) && $businessIds->isNotEmpty()) {
                                $where->whereIn('business_id', $businessIds); $hasCondition = true;
                            }
                            foreach (['user_id', 'guest_user_id', 'created_by', 'updated_by', 'assigned_by', 'verified_by', 'published_by', 'requested_by', 'completed_by'] as $column) {
                                if (in_array($column, $columns, true) && $userIds->isNotEmpty()) {
                                    $hasCondition ? $where->orWhereIn($column, $userIds) : $where->whereIn($column, $userIds);
                                    $hasCondition = true;
                                }
                            }
                            if (! $hasCondition) $where->whereRaw('1 = 0');
                        })->delete();
                    }
                    DB::table('properties')->whereIn('code', self::PROPERTY_CODES)->delete();
                    DB::table('businesses')->whereIn('email', self::BUSINESS_EMAILS)->delete();
                    DB::table('users')->whereIn('id', $userIds)->delete();
                });

                if (! $this->option('clear-only')) {
                    $this->callSilent('db:seed', ['--class' => ServicedApartmentMarketplaceSeeder::class, '--force' => true]);
                    $this->callSilent('db:seed', ['--class' => PresentationDemoSeeder::class, '--force' => true]);
                }
            }, 3);
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Presentation refresh failed and was rolled back: '.$exception->getMessage());
            return self::FAILURE;
        }

        $this->info($this->option('clear-only')
            ? 'Sandbox data was safely cleared. Live records were not selected.'
            : 'Sandbox data was safely rebuilt. Live records were not selected.');
        return self::SUCCESS;
    }

    private function safetyViolations(array $businessIds, array $propertyIds, array $userIds): array
    {
        $violations = [];
        if ($businessIds === []) return $violations;
        $unknownProperties = DB::table('properties')->whereIn('business_id', $businessIds)->whereNotIn('code', self::PROPERTY_CODES)->count();
        if ($unknownProperties) $violations[] = "{$unknownProperties} property record(s) are not recognized presentation properties.";
        $unknownBookings = DB::table('bookings')->whereIn('business_id', $businessIds)->where('reference', 'not like', 'VS-DEMO-%')->count();
        if ($unknownBookings) $violations[] = "{$unknownBookings} booking record(s) are not presentation bookings.";
        $unknownMembers = DB::table('business_memberships')->whereIn('business_id', $businessIds)->whereNotIn('user_id', $userIds)->count();
        if ($unknownMembers) $violations[] = "{$unknownMembers} business member(s) are not recognized presentation users.";

        foreach ($this->tables() as $table) {
            if (in_array($table, ['migrations', 'platform_settings', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'], true)) continue;
            $columns = Schema::getColumnListing($table);
            if (! in_array('business_id', $columns, true)) continue;
            foreach (['user_id', 'guest_user_id', 'created_by', 'updated_by', 'assigned_by', 'verified_by', 'published_by', 'requested_by', 'completed_by'] as $column) {
                if (! in_array($column, $columns, true) || $userIds === []) continue;
                $external = DB::table($table)->whereIn($column, $userIds)
                    ->where(fn ($query) => $query->whereNull('business_id')->orWhereNotIn('business_id', $businessIds))
                    ->count();
                if ($external) $violations[] = "{$external} {$table} record(s) connect a presentation user through {$column} to a non-presentation business.";
            }
        }
        return $violations;
    }

    private function tables(): array
    {
        return match (DB::getDriverName()) {
            'mysql', 'mariadb' => collect(DB::select('SELECT TABLE_NAME AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_type = ?', ['BASE TABLE']))->pluck('name')->all(),
            'sqlite' => collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"))->pluck('name')->all(),
            default => throw new RuntimeException('Presentation refresh supports MySQL, MariaDB, and SQLite.'),
        };
    }

    private function withoutForeignKeyChecks(callable $callback): void
    {
        Schema::disableForeignKeyConstraints();
        try { $callback(); } finally { Schema::enableForeignKeyConstraints(); }
    }
}
