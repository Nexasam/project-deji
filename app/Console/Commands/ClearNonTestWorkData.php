<?php

namespace App\Console\Commands;

use App\Services\Maintenance\PurgeNonTestWorkData;
use Illuminate\Console\Command;
use Throwable;

final class ClearNonTestWorkData extends Command
{
    protected $signature = 'work-data:clear {--execute : Delete the previewed non-test work records} {--confirm= : Required confirmation phrase}';

    protected $description = 'Remove non-test work data while preserving test data, platform administrators and global configuration';

    private const CONFIRMATION = 'DELETE-NON-TEST-WORK-DATA';

    public function handle(PurgeNonTestWorkData $purge): int
    {
        try {
            $plan = $purge->preview();
            $this->table(['Scope', 'Records'], [
                ['Protected test businesses', $plan['test_businesses']],
                ['Protected users', $plan['protected_users']],
                ['Non-test businesses to remove', $plan['counts']['businesses'] ?? 0],
                ['Non-protected users to remove', $plan['counts']['users'] ?? 0],
                ['All connected records to remove', array_sum($plan['counts'])],
            ]);

            if (! $this->option('execute')) {
                $this->info('Dry run complete. No records were changed.');
                $this->line('Run with --execute --confirm='.self::CONFIRMATION.' only after reviewing the counts.');
                return self::SUCCESS;
            }

            if (! hash_equals(self::CONFIRMATION, (string) $this->option('confirm'))) {
                $this->error('The exact confirmation phrase is required.');
                return self::FAILURE;
            }

            $result = $purge->execute();
            $this->info("Removed {$result['deleted']} non-test work record(s). Test data and global configuration were preserved.");
            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Cleanup failed and was rolled back: '.$exception->getMessage());
            return self::FAILURE;
        }
    }
}
