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
        // استخدام realpath لضمان المسار الصحيح 100%
        $stubPath = realpath(__DIR__ . '/../stubs/ApiResponseTrait.stub');
        $this->generateFile(
            $stubPath,
            app_path('Traits/ApiResponseTrait.php'),
            ['{{namespace}}' => 'App\Traits']
        );
    }

    protected function publishHelpers()
    {
        $stubPath = realpath(__DIR__ . '/../stubs/FileHelper.stub');
        $this->generateFile(
            $stubPath,
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

        // تحديد مجلد الـ stubs بشكل مطلق
        $stubsDir = realpath(__DIR__ . '/../stubs');

        foreach ($map as $stubName => $destPath) {
            $stubPath = $stubsDir . DIRECTORY_SEPARATOR . $stubName;

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

    protected function generateFile($stubPath, $destPath, $replacements = [])
    {
        // تأكد أن الـ stub موجود وأنه ليس هو نفسه ملف الـ Provider
        if ($stubPath && File::exists($stubPath) && $stubPath !== __FILE__) {
            File::ensureDirectoryExists(dirname($destPath));

            $content = File::get($stubPath);

            // تنفيذ التبديل
            foreach ($replacements as $search => $replace) {
                $content = str_replace($search, $replace, $content);
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

    // ... باقي الميثودز (appendApiRoutes, updateExceptionHandler) تبقى كما هي
}
