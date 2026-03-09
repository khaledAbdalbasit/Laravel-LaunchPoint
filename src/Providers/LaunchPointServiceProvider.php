<?php

namespace KhaledAbdalbasit\LaunchPoint\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

/**
 * Class LaunchPointServiceProvider
 * * Responsible for bootstrapping the LaunchPoint package components,
 * including authentication systems, helpers, and exception handling.
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
        // Register assets for manual publishing via artisan
        $this->registerPublishables();

        // Automatic generation of core components
        $this->publishApiResponseTrait();
        $this->publishHelpers();
        $this->publishAuthSystem();

        // Inject application hooks and custom configurations
        $this->appendApiRoutes();
        $this->updateExceptionHandler();
    }

    /**
     * Define files available for manual publishing.
     *
     * @return void
     */
    protected function registerPublishables()
    {
        $this->publishes([
            __DIR__ . '/../../config/launchpoint.php' => config_path('launchpoint.php'),
        ], 'launchpoint-config');

        $this->publishes([
            __DIR__ . '/../stubs/ApiResponseTrait.stub' => app_path('Traits/ApiResponseTrait.php'),
            __DIR__ . '/../stubs/FileHelper.stub' => app_path('Helpers/FileHelper.php'),
        ], 'launchpoint-helpers');
    }

    /**
     * Publish the standardized API response trait.
     *
     * @return void
     */
    protected function publishApiResponseTrait()
    {
        $path = app_path('Traits/ApiResponseTrait.php');
        $stub = __DIR__ . '/../stubs/ApiResponseTrait.stub';

        $this->generateFile($stub, $path, [
            '{{namespace}}' => 'App\Traits'
        ]);
    }

    /**
     * Publish the global file management helper.
     *
     * @return void
     */
    protected function publishHelpers()
    {
        $path = app_path('Helpers/FileHelper.php');
        $stub = __DIR__ . '/../stubs/FileHelper.stub';

        $this->generateFile($stub, $path, [
            '{{namespace}}' => 'App\Helpers'
        ]);
    }

    /**
     * Deploy the authentication boilerplate in the specified directories.
     *
     * @return void
     */
    protected function publishAuthSystem()
    {
        $map = [
            // Controllers
            'AuthController.stub' => app_path('Http/Controllers/Api/Auth/AuthController.php'),

            // Services & Repositories
            'AuthService.stub' => app_path('Services/Api/Auth/AuthService.php'),
            'AuthRepository.stub' => app_path('Repositories/Api/Auth/AuthRepository.php'),

            // Requests (As per your specific directory requirements)
            'RegisterRequest.stub' => app_path('Http/Requests/Auth/RegisterRequest.php'),
            'LoginRequest.stub' => app_path('Http/Requests/Api/LoginRequest.php'),
        ];

        foreach ($map as $stubName => $destPath) {
            $stubPath = __DIR__ . "/../stubs/{$stubName}";
            $namespace = $this->resolveNamespace($destPath);

            $this->generateFile($stubPath, $destPath, [
                '{{namespace}}' => $namespace,
                '{{trait_namespace}}' => 'App\Traits'
            ]);
        }
    }

    /**
     * Append package routes to the host application's api.php file.
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
     * Inject custom exception handling logic into the application's Handler.
     *
     * @return void
     */
    protected function updateExceptionHandler()
    {
        $path = app_path('Exceptions/Handler.php');
        $stub = __DIR__ . '/../stubs/handler_renderable.stub';

        if (File::exists($path) && File::exists($stub)) {
            $content = File::get($path);
            if (!str_contains($content, 'LaunchPoint Exception Handling')) {
                $renderable = File::get($stub);
                $content = str_replace('// register renderable here', $renderable, $content);
                File::put($path, $content);
            }
        }
    }

    /**
     * Internal helper for file generation and dynamic replacement.
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
     * Resolve the PSR-4 namespace based on the absolute file path.
     *
     * @param string $path
     * @return string
     */
    protected function resolveNamespace($path)
    {
        $relative = str_replace([app_path(), '.php'], '', $path);
        $relative = trim($relative, DIRECTORY_SEPARATOR);
        $parts = explode(DIRECTORY_SEPARATOR, $relative);
        array_pop($parts);

        return 'App\\' . implode('\\', $parts);
    }
}
