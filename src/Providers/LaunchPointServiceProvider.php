<?php

namespace KhaledAbdalbasit\LaunchPoint\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class LaunchPointServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // دمج ملف الإعدادات
        $this->mergeConfigFrom(__DIR__ . '/../../config/launchpoint.php', 'launchpoint');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // 1. نشر ملف الإعدادات (Config)
        $this->publishes([
            __DIR__ . '/../../config/launchpoint.php' => config_path('launchpoint.php'),
        ], 'launchpoint-config');

        // 2. نشر الـ Trait (ApiResponseTrait)
        $this->publishApiResponseTrait();

        // 3. نشر الـ Controllers
        $this->publishControllers();

        // 4. إضافة مسارات الـ API (Routes)
        $this->appendApiRoutes();

        // 5. تعديل معالج الاستثناءات (Global Exception Handler)
        $this->registerExceptionHandlerSnippet();
    }

    protected function publishApiResponseTrait()
    {
        $traitPath = app_path('Traits/ApiResponseTrait.php');
        $stubPath = __DIR__ . '/../stubs/ApiResponseTrait.stub';

        if (!File::exists($traitPath) && File::exists($stubPath)) {
            File::ensureDirectoryExists(app_path('Traits'));
            File::put($traitPath, File::get($stubPath));
        }
    }

    protected function publishControllers()
    {
        $controllers = ['SettingsController', 'AuthController', 'ProfileController'];
        $destinationDir = app_path('Http/Controllers/Api');

        foreach ($controllers as $controller) {
            $controllerPath = "{$destinationDir}/{$controller}.php";
            $stubPath = __DIR__ . "/../stubs/{$controller}.stub";

            if (!File::exists($controllerPath) && File::exists($stubPath)) {
                File::ensureDirectoryExists($destinationDir);
                File::put($controllerPath, File::get($stubPath));
            }
        }
    }

    protected function appendApiRoutes()
    {
        $routesPath = base_path('routes/api.php');
        $stubPath = __DIR__ . '/../stubs/api_routes.stub';

        if (File::exists($routesPath) && File::exists($stubPath)) {
            $currentRoutes = File::get($routesPath);
            if (!str_contains($currentRoutes, 'LaunchPoint Routes')) {
                File::append($routesPath, "\n" . File::get($stubPath));
            }
        }
    }

    protected function registerExceptionHandlerSnippet()
    {
        $handlerPath = app_path('Exceptions/Handler.php');
        $renderableStub = __DIR__ . '/../stubs/handler_renderable.stub';

        if (File::exists($handlerPath) && File::exists($renderableStub)) {
            $content = File::get($handlerPath);

            if (!str_contains($content, 'LaunchPoint Exception Handling')) {
                $renderableContent = File::get($renderableStub);
                // بنحاول ندور على مكان التسجيل الافتراضي في Laravel
                $search = 'public function register(): void';

                if (str_contains($content, $search)) {
                    $newContent = str_replace($search, $search . "\n    " . $renderableContent, $content);
                    File::put($handlerPath, $newContent);
                }
            }
        }
    }
}
