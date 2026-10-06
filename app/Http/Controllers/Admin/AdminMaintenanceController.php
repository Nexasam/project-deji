<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Platform\PlatformAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\Process\Process;

class AdminMaintenanceController extends Controller
{
    /** @var array<string, string> */
    private array $logs = [
        'laravel' => 'storage/logs/laravel.log',
        'scheduler' => 'storage/logs/scheduler.log',
    ];

    /** @var array<string, string> */
    private array $commands = [
        'calendars:sync' => 'Sync external calendars',
        'calendars:notify-stale' => 'Notify stale calendar connections',
        'operations:notify-overdue' => 'Notify overdue operations',
        'bookings:notify-upcoming-arrivals' => 'Notify upcoming arrivals',
    ];

    public function __construct(private readonly PlatformAudit $audit) {}

    public function index(Request $request): View
    {
        $selectedLog = array_key_exists($request->query('log', 'laravel'), $this->logs)
            ? $request->query('log', 'laravel')
            : 'laravel';

        return view('admin.maintenance.index', [
            'logs' => $this->logs,
            'selectedLog' => $selectedLog,
            'logPath' => $this->logs[$selectedLog],
            'logContent' => $this->tailLog($this->logs[$selectedLog]),
            'commands' => $this->commands,
        ]);
    }

    public function clearLog(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'log' => ['required', 'string', 'in:'.implode(',', array_keys($this->logs))],
            'confirm' => ['accepted'],
        ]);

        $path = base_path($this->logs[$data['log']]);
        File::ensureDirectoryExists(dirname($path));
        File::put($path, '');

        $this->record($request, 'platform.maintenance.log_cleared', 'Cleared '.$data['log'].' log.', [
            'log' => $data['log'],
            'path' => $this->logs[$data['log']],
            'confirmed' => true,
        ]);

        return back()->with('status', str($data['log'])->title().' log cleared.');
    }

    public function optimizeClear(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => ['accepted']]);

        Artisan::call('optimize:clear');

        $this->record($request, 'platform.maintenance.optimize_cleared', 'Ran php artisan optimize:clear.', [
            'confirmed' => true,
            'output' => trim(Artisan::output()),
        ]);

        return back()->with('status', 'Application caches cleared.');
    }

    public function migrate(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => ['accepted']]);

        $exitCode = Artisan::call('migrate', ['--force' => true]);
        $output = trim(Artisan::output());

        $this->record($request, 'platform.maintenance.migrations_run', 'Ran php artisan migrate --force.', [
            'confirmed' => true,
            'exit_code' => $exitCode,
            'output' => mb_substr($output, 0, 4000),
        ]);

        return back()
            ->with($exitCode === 0 ? 'status' : 'maintenance_error', $output ?: 'php artisan migrate --force finished.');
    }

    public function gitPull(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => ['accepted']]);

        $process = new Process(['git', 'pull', 'origin', 'dev'], base_path(), null, null, 120);
        $process->run();

        $output = trim($process->getOutput()."\n".$process->getErrorOutput());

        $this->record($request, 'platform.maintenance.git_pull', 'Ran git pull origin dev.', [
            'confirmed' => true,
            'successful' => $process->isSuccessful(),
            'exit_code' => $process->getExitCode(),
            'output' => mb_substr($output, 0, 4000),
        ]);

        return back()
            ->with($process->isSuccessful() ? 'status' : 'maintenance_error', $output ?: 'git pull origin dev finished with no output.');
    }

    public function runCommand(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'command' => ['required', 'string', 'in:'.implode(',', array_keys($this->commands))],
            'confirm' => ['accepted'],
        ]);

        $exitCode = Artisan::call($data['command']);
        $output = trim(Artisan::output());

        $this->record($request, 'platform.maintenance.command_run', 'Ran '.$data['command'].'.', [
            'command' => $data['command'],
            'exit_code' => $exitCode,
            'confirmed' => true,
            'output' => mb_substr($output, 0, 4000),
        ]);

        return back()
            ->with($exitCode === 0 ? 'status' : 'maintenance_error', $output ?: $data['command'].' finished.');
    }

    private function tailLog(string $relativePath, int $lines = 220): string
    {
        $path = base_path($relativePath);

        if (! File::exists($path)) {
            return 'Log file does not exist yet.';
        }

        $content = File::get($path);
        $rows = preg_split('/\R/', $content) ?: [];

        return implode(PHP_EOL, array_slice($rows, -$lines));
    }

    /** @param array<string, mixed> $metadata */
    private function record(Request $request, string $event, string $description, array $metadata): void
    {
        $this->audit->record($request->user(), $event, $request->user(), $description, [], [], $metadata);
    }
}
