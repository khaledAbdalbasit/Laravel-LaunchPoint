<?php

namespace KhaledAbdalbasit\LaunchPoint\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

/**
 * Class LaunchPointServiceProvider
 * * This service provider is responsible for bootstrapping the LaunchPoint
 * authentication system, publishing necessary components, and configuring
 * the application environment.
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
        $this->mergeConfigFrom(__DIR__ . '/../../config/launchpoint.php', 'launchpoint');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishables();
        $this->publishApiResponseTrait();
        $this->publishHelpers();
        $this->publishAuthSystem();
        $this->appendApiRoutes();
        $this->updateExceptionHandler();
    }

    /**
     * Register the package's publishable resources.
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
     * Generate and publish the ApiResponseTrait.
     *
     * @return void
     */
    protected function publishApiResponseTrait()
    {
        $this->generateFile(
            __DIR__ . '/../stubs/ApiResponseTrait.stub',
            app_path('Traits/ApiResponseTrait.php'),
            ['{{namespace}}' => 'App\Traits']
        );
    }

    /**
     * Generate and publish helper classes.
     *
     * @return void
     */
    protected function publishHelpers()
    {
        $this->generateFile(
            __DIR__ . '/../stubs/FileHelper.stub',
            app_path('Helpers/FileHelper.php'),
            ['{{namespace}}' => 'App\Helpers']
        );
    }

    /**
     * Orchestrate the publication of the complete authentication system.
     *
     * @return void
     */
    protected function publishAuthSystem()
    {
        $map = [
            'AuthController.stub' => app_path('Http/Controllers/Api/Auth/AuthController.php'),
            'AuthService.stub' => app_path('Services/Api/Auth/AuthService.php'),
            'AuthRepository.stub' => app_path('Repositories/Api/Auth/AuthRepository.php'),
            'RegisterRequest.stub' => app_path('Http/Requests/Auth/RegisterRequest.php'),
            'LoginRequest.stub' => app_path('Http/Requests/Auth/LoginRequest.php'),
        ];

        foreach ($map as $stubName => $destPath) {
            $stubPath = __DIR__ . "/../stubs/{$stubName}";

            $this->generateFile($stubPath, $destPath, [
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
     * Append package-specific routes to the application's API routes.
     *
     * @return void
     */
    protected function appendApiRoutes()
    {
        $path = base_path('routes/api.php');
        $stub = __DIR__ . '/../stubs/api_routes.stub';

        if (File::exists($path) && File::exists($stub)) {
            $content = File::get($path);
            if (!str_contains($content, 'LaunchPoint Routes')) {
                File::append($path, "\n" . File::get($stub));
            }
        }
    }

    /**
     * Update the application's Exception Handler for custom API responses.
     * Supports Laravel versions prior to 11.
     *
     * @return void
     */
    protected function updateExceptionHandler()
    {
        $path = app_path('Exceptions/Handler.php');
        if (!File::exists($path)) return;

        $stub = __DIR__ . '/../stubs/handler_renderable.stub';

        if (File::exists($stub)) {
            $content = File::get($path);
            if (!str_contains($content, 'LaunchPoint Exception Handling')) {
                $renderable = File::get($stub);
                $content = str_replace('// register renderable here', $renderable, $content);
                File::put($path, $content);
            }
        }
    }

    /**
     * Helper method to generate files from stubs with variable replacements.
     *
     * @param string $stubPath
     * @param string $destPath
     * @param array $replacements
     * @return void
     */
    protected function generateFile($stubPath, $destPath, $replacements = [])
    {
        if (!File::exists($destPath) && File::exists($stubPath)) {
            File::ensureDirectoryExists(dirname($destPath));

            $content = File::get($stubPath);
            foreach ($replacements as $search => $replace) {
                $content = str_replace($search, $replace, $content);
            }

            File::put($destPath, $content);
        }
    }

    /**
     * Resolve the PHP namespace dynamically based on the file destination.
     *
     * @param string $path
     * @return string
     */
    protected function resolveNamespace($path)
    {
        // Remove app path and .php extension
        $relative = str_replace([app_path(), '.php'], '', $path);

        // Normalize directory separators for cross-platform compatibility
        $relative = str_replace(['/', '\\'], '\\', $relative);

        // Clean leading/trailing backslashes
        $relative = trim($relative, '\\');

        // Explode path and remove the class name (last element)
        $parts = explode('\\', $relative);
        array_pop($parts);

        // Return the fully qualified namespace
        return 'App\\' . implode('\\', $parts);
    }
}
