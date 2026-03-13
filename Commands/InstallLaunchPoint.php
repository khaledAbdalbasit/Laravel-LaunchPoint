<?php

namespace KhaledAbdalbasit\LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;

/**
 * Class InstallLaunchPoint
 * * This command handles the complete setup of the LaunchPoint Starter Kit,
 * including dependency installation, API scaffolding, and asset publication.
 */
class InstallLaunchPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install LaunchPoint API system, dependencies and publish assets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->components->info('LaunchPoint Installation Wizard');

        // 1. Check and install Laravel API scaffolding
        $this->ensureApiIsInstalled();

        // 2. Install required third-party packages
        $this->installDependencies();

        // 3. Publish package resources (Controllers, Services, etc.)
        $this->publishResources();

        $this->components->info('LaunchPoint has been installed successfully.');

        return self::SUCCESS;
    }

    /**
     * Ensure that the Laravel API routes and configuration are present.
     * Required specifically for Laravel 11+ applications.
     *
     * @return void
     */
    protected function ensureApiIsInstalled()
    {
        if (!File::exists(base_path('routes/api.php'))) {
            $this->comment('Configuring Laravel API scaffolding...');
            $this->call('install:api', ['--no-interaction' => true]);
        }
    }

    /**
     * Install necessary composer dependencies for the package to function.
     *
     * @return void
     */
    protected function installDependencies()
    {
        $package = 'fisal/laravel-otp';

        $this->comment("Installing dependency: {$package}...");

        // Using a shell process to execute composer require
        $process = Process::run("composer require {$package}");

        if ($process->successful()) {
            $this->components->info("Package [{$package}] installed successfully.");
        } else {
            $this->error("Failed to install [{$package}]. Please run 'composer require {$package}' manually.");
        }
    }

    /**
     * Publish the package assets and starter kit components.
     *
     * @return void
     */
    /**
     * Publish the package assets and starter kit components.
     *
     * @return void
     */
    protected function publishResources()
    {
        $this->components->task('Publishing authentication components', function () {
            /** @var \KhaledAbdalbasit\LaunchPoint\Providers\LaunchPointServiceProvider $provider */
            $provider = $this->laravel->getProvider(\KhaledAbdalbasit\LaunchPoint\Providers\LaunchPointServiceProvider::class);

            if ($provider) {
                // Execute the core authentication system publication
                $provider->publishAuthSystem();

                // Publish configuration and trigger fallback tag-based publishing
                $this->callSilent('vendor:publish', [
                    '--provider' => 'KhaledAbdalbasit\LaunchPoint\Providers\LaunchPointServiceProvider',
                    '--tag' => 'launchpoint-config',
                    '--force' => true
                ]);

                return true;
            }

            return false;
        });

        $this->components->info('Core assets and authentication logic published successfully.');
    }
}
