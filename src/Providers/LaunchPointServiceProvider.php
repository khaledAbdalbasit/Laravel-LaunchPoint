<?php

namespace KhaledAbdalbasit\LaunchPoint\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

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
            'AuthController.stub'    => app_path('Http/Controllers/Api/Auth/AuthController.php'),
            'AuthService.stub'       => app_path('Services/Api/Auth/AuthService.php'),
            'AuthRepository.stub'    => app_path('Repositories/Api/Auth/AuthRepository.php'),
            'RegisterRequest.stub'   => app_path('Http/Requests/Auth/RegisterRequest.php'),
            'LoginRequest.stub'      => app_path('Http/Requests/Auth/LoginRequest.php'),
        ];

        foreach ($map as $stubName => $destPath) {
            $stubPath = __DIR__ . "/../stubs/{$stubName}";

            // تأكد إننا بنستخدم placeholders فقط
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

    protected function appendApiRoutes()
    {
        $path = base_path('routes/api.php');
        $stub = __DIR__ . '/../stubs/api_routes.stub';

        if (File::exists($path) && File::exists($stub)) {
            $stubContent = File::get($stub);
            $currentContent = File::get($path);

            if (!str_contains($currentContent, 'LaunchPoint Routes')) {
                File::append($path, "\n" . $stubContent);
            }
        }
    }

    protected function updateExceptionHandler()
    {
        $path = app_path('Exceptions/Handler.php');
        if (!File::exists($path)) return;

        $stub = __DIR__ . '/../stubs/handler_renderable.stub';
        if (File::exists($stub)) {
            $content = File::get($path);
            if (!str_contains($content, 'LaunchPoint Exception Handling')) {
                $content = str_replace('// register renderable here', File::get($stub), $content);
                File::put($path, $content);
            }
        }
    }

    protected function generateFile($stubPath, $destPath, $replacements = [])
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

    protected function resolveNamespace($path)
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $appBase = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, app_path());
        $relative = str_replace([$appBase, '.php'], '', $path);
        $segments = array_filter(explode(DIRECTORY_SEPARATOR, trim($relative, DIRECTORY_SEPARATOR)));
        array_pop($segments);
        return 'App' . (count($segments) ? '\\' . implode('\\', $segments) : '');
    }
}
