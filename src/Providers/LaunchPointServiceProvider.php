<?php

namespace KhaledAbdalbasit\LaunchPoint\Providers;

use Illuminate\Support\ServiceProvider;
use KhaledAbdalbasit\LaunchPoint\Commands\InstallLaunchPoint;

/**
 * Class LaunchPointServiceProvider
 * * This provider handles the registration of package configurations and commands.
 * The actual file generation is now triggered via the Artisan command.
 */
class LaunchPointServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge package configuration
        $this->mergeConfigFrom(__DIR__ . '/../../config/launchpoint.php', 'launchpoint');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the installation command if running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallLaunchPoint::class,
            ]);

            $this->registerPublishables();
        }
    }

    /**
     * Define the files that can be published via the vendor:publish command.
     *
     * @return void
     */
    protected function registerPublishables()
    {
        $this->publishes([
            __DIR__ . '/../../config/launchpoint.php' => config_path('launchpoint.php'),
        ], 'launchpoint-config');

        // Note: We keep the tag-based publishing option as a fallback
        // but the main logic is now inside our Install Command.
    }

    /**
     * Public methods to be accessed by the Install Command.
     * These methods replace the protected logic that was running on boot.
     */

    public function publishAuthSystem()
    {
        $map = [
            'AuthController.stub'    => app_path('Http/Controllers/Api/Auth/AuthController.php'),
            'AuthService.stub'       => app_path('Services/Api/Auth/AuthService.php'),
            'AuthRepository.stub'    => app_path('Repositories/Api/Auth/AuthRepository.php'),
            'RegisterRequest.stub'   => app_path('Http/Requests/Auth/RegisterRequest.php'),
            'LoginRequest.stub'      => app_path('Http/Requests/Auth/LoginRequest.php'),
            'ApiResponseTrait.stub'  => app_path('Traits/ApiResponseTrait.php'),
            'FileHelper.stub'        => app_path('Helpers/FileHelper.php'),
        ];

        foreach ($map as $stubName => $destPath) {
            $stubPath = __DIR__ . "/../stubs/{$stubName}";

            $this->generateFile($stubPath, $destPath, [
                '{{namespace}}'            => $this->resolveNamespace($destPath),
                '{{trait_namespace}}'      => 'App\Traits',
                '{{repository_namespace}}' => 'App\Repositories\Api\Auth',
                '{{service_namespace}}'    => 'App\Services\Api\Auth',
                '{{request_namespace}}'    => 'App\Http\Requests\Auth',
                '{{helper_namespace}}'     => 'App\Helpers',
            ]);
        }
    }

    public function generateFile($stubPath, $destPath, $replacements = [])
    {
        if (\Illuminate\Support\Facades\File::exists($stubPath)) {
            \Illuminate\Support\Facades\File::ensureDirectoryExists(dirname($destPath));
            $content = \Illuminate\Support\Facades\File::get($stubPath);
            foreach ($replacements as $search => $replace) {
                $content = str_replace($search, (string)$replace, $content);
            }
            \Illuminate\Support\Facades\File::put($destPath, $content);
        }
    }

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
