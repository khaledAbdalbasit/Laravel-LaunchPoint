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
        // 1. نشر ملف الإعدادات
        $this->publishes([
            __DIR__ . '/../../config/launchpoint.php' => config_path('launchpoint.php'),
        ], 'config');

        // 2. نشر ApiResponse Trait (تعديل المسار لاستخدام __DIR__)
        $traitPath = app_path('Traits/ApiResponseTrait.stub');
        if (!File::exists($traitPath)) {
            File::ensureDirectoryExists(app_path('Traits'));
            // المسار الآن نسبي بالنسبة لملف الـ Provider الحالي
            $stubPath = __DIR__ . '/../stubs/ApiResponseTrait.stub';
            if (File::exists($stubPath)) {
                File::put($traitPath, file_get_contents($stubPath));
            }
        }

        // 3. نشر المتحكمات (Controllers)
        $controllers = ['SettingsController', 'AuthController', 'ProfileController'];
        foreach ($controllers as $controller) {
            $controllerPath = app_path("Http/Controllers/Api/{$controller}.php");
            if (!File::exists($controllerPath)) {
                File::ensureDirectoryExists(app_path('Http/Controllers/Api'));
                $stubPath = __DIR__ . "/../stubs/{$controller}.stub";
                if (File::exists($stubPath)) {
                    File::put($controllerPath, file_get_contents($stubPath));
                }
            }
        }

        // 4. نشر مسارات API
        $routesPath = base_path('routes/api.php');
        $stubRoutesPath = __DIR__ . '/../stubs/api_routes.stub';
        if (File::exists($routesPath) && File::exists($stubRoutesPath)) {
            if (!str_contains(file_get_contents($routesPath), 'LaunchPoint Routes')) {
                File::append($routesPath, file_get_contents($stubRoutesPath));
            }
        }

        // 5. تحديث ملف الـ Exception Handler
        $handlerPath = app_path('Exceptions/Handler.php');
        $renderableStub = __DIR__ . '/../stubs/handler_renderable.stub';

        if (File::exists($handlerPath) && File::exists($renderableStub)) {
            $content = File::get($handlerPath);
            if (!str_contains($content, 'LaunchPoint Exception Handling')) {
                $renderableContent = file_get_contents($renderableStub);
                $content = str_replace('// register renderable here', $renderableContent, $content);
                File::put($handlerPath, $content);
            }
        }
    }
}
