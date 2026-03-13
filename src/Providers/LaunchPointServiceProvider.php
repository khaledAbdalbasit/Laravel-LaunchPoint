<?php

namespace KhaledAbdalbasit\LaunchPoint\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

/**
 * Class LaunchPointServiceProvider
 * Responsible for bootstrapping the LaunchPoint package components,
 * including authentication systems, helpers, and exception handling.
 */
class LaunchPointServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/launchpoint.php', 'launchpoint');
    }

    public function boot()
    {
        $this->registerPublishables();
        $this->publishApiResponseTrait();
        $this->publishHelpers();
        $this->publishAuthSystem();
        $this->appendApiRoutes();
        $this->updateExceptionHandler();
    }

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

    protected function publishApiResponseTrait()
    {
        $this->generateFile(
            __DIR__ . '/../stubs/ApiResponseTrait.stub',
            app_path('Traits/ApiResponseTrait.php'),
            ['{{namespace}}' => 'App\Traits']
        );
    }

    protected function publishHelpers()
    {
        $this->generateFile(
            __DIR__ . '/../stubs/FileHelper.stub',
            app_path('Helpers/FileHelper.php'),
            ['{{namespace}}' => 'App\Helpers']
        );
    }

    protected function publishAuthSystem()
    {
        $map = [
            // Controllers
            'AuthController.stub' => app_path('Http/Controllers/Api/Auth/AuthController.php'),

            // Services & Repositories
            'AuthService.stub' => app_path('Services/Api/Auth/AuthService.php'),
            'AuthRepository.stub' => app_path('Repositories/Api/Auth/AuthRepository.php'),

            // Requests (جميعها داخل Auth folder)
            'RegisterRequest.stub' => app_path('Http/Requests/Auth/RegisterRequest.php'),
            'LoginRequest.stub' => app_path('Http/Requests/Auth/LoginRequest.php'),
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

    protected function resolveNamespace($path)
    {
        $relative = str_replace([app_path(), '.php'], '', $path);
        $relative = trim($relative, DIRECTORY_SEPARATOR);
        $parts = explode(DIRECTORY_SEPARATOR, $relative);
        array_pop($parts);
        return 'App\\' . implode('\\', $parts);
    }
}
