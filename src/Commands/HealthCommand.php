<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class HealthCommand
 *
 * Artisan command to check the health and environment configuration of the application.
 */
class HealthCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check project health: database connection, .env configuration, and essential folders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->displayLogo();
        $this->components->info('LaunchPoint Project Health Diagnostic');

        $rows = [];
        $hasErrors = false;

        // 1. Database Check
        $dbStatus = $this->checkDatabase();
        $rows[] = ['Database Connection', $dbStatus['status'], $dbStatus['detail']];
        if (!$dbStatus['ok']) {
            $hasErrors = true;
        }

        // 2. .env File Check
        $envStatus = $this->checkEnvFile();
        $rows[] = ['.env Configuration', $envStatus['status'], $envStatus['detail']];
        if (!$envStatus['ok']) {
            $hasErrors = true;
        }

        // 3. Essential Directories Check
        $dirStatus = $this->checkDirectories();
        $rows[] = ['Directory Structure', $dirStatus['status'], $dirStatus['detail']];
        if (!$dirStatus['ok']) {
            $hasErrors = true;
        }

        $this->table(['Check', 'Status', 'Details'], $rows);

        if ($hasErrors) {
            $this->components->warn('Health check finished with issues. Please review details above.');
            return self::FAILURE;
        }

        $this->components->info('All health checks passed! Project is properly configured.');
        return self::SUCCESS;
    }

    /**
     * Check database connectivity.
     *
     * @return array{ok: bool, status: string, detail: string}
     */
    protected function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            $driver = DB::connection()->getDriverName();
            $dbName = DB::connection()->getDatabaseName();
            return [
                'ok' => true,
                'status' => '<fg=green>PASS</>',
                'detail' => "Connected successfully to [{$driver}: {$dbName}]",
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'status' => '<fg=red>FAIL</>',
                'detail' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check .env file existence and required keys.
     *
     * @return array{ok: bool, status: string, detail: string}
     */
    protected function checkEnvFile(): array
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            return [
                'ok' => false,
                'status' => '<fg=red>FAIL</>',
                'detail' => 'Missing .env file in project root.',
            ];
        }

        $requiredKeys = ['APP_KEY', 'APP_ENV', 'DB_CONNECTION'];
        $missingKeys = [];

        foreach ($requiredKeys as $key) {
            if (empty(env($key))) {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            return [
                'ok' => false,
                'status' => '<fg=yellow>WARN</>',
                'detail' => 'Missing/empty variables: ' . implode(', ', $missingKeys),
            ];
        }

        return [
            'ok' => true,
            'status' => '<fg=green>PASS</>',
            'detail' => 'Found .env with all core keys defined',
        ];
    }

    /**
     * Check essential directories existence and writability.
     *
     * @return array{ok: bool, status: string, detail: string}
     */
    protected function checkDirectories(): array
    {
        $dirsToCheck = [
            'storage' => true,
            'storage/logs' => true,
            'storage/framework' => true,
            'bootstrap/cache' => true,
            'app' => false,
            'config' => false,
            'routes' => false,
        ];

        $missing = [];
        $notWritable = [];

        foreach ($dirsToCheck as $dir => $mustBeWritable) {
            $path = base_path($dir);
            if (!File::exists($path)) {
                $missing[] = $dir;
            } elseif ($mustBeWritable && !is_writable($path)) {
                $notWritable[] = $dir;
            }
        }

        if (!empty($missing)) {
            return [
                'ok' => false,
                'status' => '<fg=red>FAIL</>',
                'detail' => 'Missing directories: ' . implode(', ', $missing),
            ];
        }

        if (!empty($notWritable)) {
            return [
                'ok' => false,
                'status' => '<fg=yellow>WARN</>',
                'detail' => 'Not writable: ' . implode(', ', $notWritable),
            ];
        }

        return [
            'ok' => true,
            'status' => '<fg=green>PASS</>',
            'detail' => 'All core directories exist and are writable',
        ];
    }
}
