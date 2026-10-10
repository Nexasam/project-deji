<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class GoLivePreflight extends Command
{
    protected $signature = 'app:go-live-preflight';

    protected $description = 'Fail unless the application meets the production deployment baseline.';

    public function handle(): int
    {
        $checks = [
            ['Production environment', app()->environment('production'), (string) app()->environment()],
            ['Debug disabled', ! config('app.debug'), config('app.debug') ? 'enabled' : 'disabled'],
            ['HTTPS application URL', str_starts_with((string) config('app.url'), 'https://'), (string) config('app.url')],
            ['Application key configured', filled(config('app.key')), filled(config('app.key')) ? 'configured' : 'missing'],
            ['Secure session cookie', config('session.secure') === true, config('session.secure') ? 'enabled' : 'disabled'],
            ['HTTP-only session cookie', config('session.http_only') === true, config('session.http_only') ? 'enabled' : 'disabled'],
            ['Persistent session driver', config('session.driver') !== 'array', (string) config('session.driver')],
            ['Persistent cache store', config('cache.default') !== 'array', (string) config('cache.default')],
            ['Queue is production-compatible', ! in_array(config('queue.default'), ['sync', 'null'], true) || config('platform.allow_sync_queue'), config('queue.default').(config('platform.allow_sync_queue') ? ' (explicit shared-host exception)' : '')],
            ['Storage is writable', is_writable(storage_path()), storage_path()],
            ['Bootstrap cache is writable', is_writable(base_path('bootstrap/cache')), base_path('bootstrap/cache')],
            ['Production assets exist', is_file(public_path('build/manifest.json')), public_path('build/manifest.json')],
        ];

        try {
            DB::connection()->getPdo();
            $checks[] = ['Database connection', true, DB::connection()->getDatabaseName()];
            foreach (['migrations', 'sessions', 'cache', 'jobs', 'failed_jobs'] as $table) {
                $checks[] = ["Database table: {$table}", Schema::hasTable($table), Schema::hasTable($table) ? 'present' : 'missing'];
            }
        } catch (Throwable $exception) {
            $checks[] = ['Database connection', false, $exception->getMessage()];
        }

        $failed = false;
        $rows = array_map(function (array $check) use (&$failed): array {
            [$name, $passed, $detail] = $check;
            $failed = $failed || ! $passed;

            return [$passed ? 'PASS' : 'FAIL', $name, $detail];
        }, $checks);

        $this->table(['Result', 'Check', 'Detail'], $rows);

        if ($failed) {
            $this->error('Go-live preflight failed. Correct every failed check before opening production traffic.');

            return self::FAILURE;
        }

        $this->info('Go-live deployment baseline passed.');

        return self::SUCCESS;
    }
}
