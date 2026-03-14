<?php

namespace KhaledAbdalbasit\LaunchPoint\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use KhaledAbdalbasit\LaunchPoint\Commands\InstallLaunchPoint;
use KhaledAbdalbasit\LaunchPoint\Commands\MakeRepositoryCommand;
use KhaledAbdalbasit\LaunchPoint\Commands\MakeServiceCommand;

/**
 * Class LaunchPointServiceProvider
 * * Handles registration of package components and provides helper methods
 * for granular file scaffolding.
 */
class LaunchPointServiceProvider extends ServiceProvider
{
    /**
     * Register package configuration and commands.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/launchpoint.php', 'launchpoint');
    }

    /**
     * Bootstrap package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallLaunchPoint::class,
                MakeServiceCommand::class,
                MakeRepositoryCommand::class,
            ]);

            $this->registerPublishables();
        }
    }

    /**
     * Register vendor publishable resources.
     *
     * @return void
     */
    protected function registerPublishables()
    {
        $this->publishes([
            __DIR__ . '/../../config/launchpoint.php' => config_path('launchpoint.php'),
        ], 'launchpoint-config');
    }

    /**
     * Publish core authentication scaffolding.
     *
     * @return void
     */
    public function publishAuthSystem()
    {
        $map = [
            'AuthController.stub' => app_path('Http/Controllers/Api/Auth/AuthController.php'),
            'AuthService.stub' => app_path('Services/Api/Auth/AuthService.php'),
            'AuthRepository.stub' => app_path('Repositories/Api/Auth/AuthRepository.php'),
            'RegisterRequest.stub' => app_path('Http/Requests/Auth/RegisterRequest.php'),
            'LoginRequest.stub' => app_path('Http/Requests/Auth/LoginRequest.php'),
        ];

        foreach ($map as $stubName => $destPath) {
            $this->generateFile(__DIR__ . "/../stubs/{$stubName}", $destPath, [
                '{{namespace}}' => $this->resolveNamespace($destPath),
                '{{trait_namespace}}' => 'App\Traits',
                '{{repository_namespace}}' => 'App\Repositories\Api\Auth',
                '{{service_namespace}}' => 'App\Services\Api\Auth',
                '{{request_namespace}}' => 'App\Http\Requests\Auth',
                '{{helper_namespace}}' => 'App\Helpers',
            ]);
        }
    }

    /**
     * Publish the API Response Trait.
     *
     * @return void
     */
    public function publishApiResponseTrait()
    {
        $this->generateFile(
            __DIR__ . '/../stubs/ApiResponseTrait.stub',
            app_path('Traits/ApiResponseTrait.php'),
            ['{{namespace}}' => 'App\Traits']
        );
    }

    /**
     * Publish the File Helper class.
     *
     * @return void
     */
    public function publishFileHelper()
    {
        $this->generateFile(
            __DIR__ . '/../stubs/FileHelper.stub',
            app_path('Helpers/FileHelper.php'),
            ['{{namespace}}' => 'App\Helpers']
        );
    }

    /**
     * Core file generation logic.
     *
     * @param string $stubPath
     * @param string $destPath
     * @param array $replacements
     * @return void
     */
    public function generateFile($stubPath, $destPath, $replacements = [])
    {
        if (File::exists($stubPath)) {
            File::ensureDirectoryExists(dirname($destPath));
            $content = File::get($stubPath);
            foreach ($replacements as $search => $replace) {
                $content = str_replace($search, (string)$replace, $content);
            }
            File::put($destPath, $content);
        }
    }

    /**
     * Resolve PSR-4 namespace based on file path.
     *
     * @param string $path
     * @return string
     */
    public function resolveNamespace($path)
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $appBase = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, app_path());
        $relative = str_replace([$appBase, '.php'], '', $path);
        $segments = array_filter(explode(DIRECTORY_SEPARATOR, trim($relative, DIRECTORY_SEPARATOR)));
        array_pop($segments);
        return 'App' . (count($segments) ? '\\' . implode('\\', $segments) : '');
    }
}
