<?php

namespace KhaledAbdalbasit\LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Class InstallLaunchPoint
 * * Interactive command to scaffold LaunchPoint components based on user selection.
 */
class InstallLaunchPoint extends Command
{
    /**
     * @var string
     */
    protected $signature = 'launchpoint:install';

    /**
     * @var string
     */
    protected $description = 'Interactively install LaunchPoint components';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->components->info('LaunchPoint Installation Wizard');

        $this->ensureApiIsInstalled();

        /** @var \KhaledAbdalbasit\LaunchPoint\Providers\LaunchPointServiceProvider $provider */
        $provider = $this->laravel->getProvider(\KhaledAbdalbasit\LaunchPoint\Providers\LaunchPointServiceProvider::class);

        if (!$provider) {
            $this->error('Provider not found.');
            return self::FAILURE;
        }

        // 1. Interactive Auth System Selection
        if ($this->confirm('Do you want to install the Authentication Scaffolding?', true)) {
            $provider->publishAuthSystem();
            $provider->publishFileHelper();
            $provider->publishApiResponseTrait();
            $this->info('Auth system, FileHelper, and ApiResponseTrait installed.');
        } else {
            // 2. Individual selection if Auth is skipped
            if ($this->confirm('Do you want to install the FileHelper individually?', false)) {
                $provider->publishFileHelper();
                $this->info('FileHelper installed.');
            }

            if ($this->confirm('Do you want to install the ApiResponseTrait individually?', false)) {
                $provider->publishApiResponseTrait();
                $this->info('ApiResponseTrait installed.');
            }
        }

        // Always publish config
        $this->callSilent('vendor:publish', [
            '--provider' => 'KhaledAbdalbasit\LaunchPoint\Providers\LaunchPointServiceProvider',
            '--tag' => 'launchpoint-config',
            '--force' => true
        ]);

        $this->components->info('Installation completed successfully.');

        return self::SUCCESS;
    }

    /**
     * @return void
     */
    protected function ensureApiIsInstalled()
    {
        if (!File::exists(base_path('routes/api.php'))) {
            $this->comment('Configuring Laravel API scaffolding...');
            $this->call('install:api', ['--no-interaction' => true]);
        }
    }
}
