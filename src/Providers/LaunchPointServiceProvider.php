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

        $this->publishes([
            __DIR__ . '/../../config/launchpoint.php' => config_path('launchpoint.php'),
        ], 'config');

        $traitPath = app_path('Traits/ApiResponseTrait.php');
        if (!File::exists($traitPath)) {
            File::ensureDirectoryExists(app_path('Traits'));
            File::put($traitPath, file_get_contents(__DIR__ . '/../../../../../stubs/ApiResponseTrait.php'));
        }

        $controllers = ['SettingsController', 'AuthController', 'ProfileController'];
        foreach ($controllers as $controller) {
            $controllerPath = app_path("Http/Controllers/Api/{$controller}.php");
            if (!File::exists($controllerPath)) {
                File::ensureDirectoryExists(app_path('Http/Controllers/Api'));
                File::put($controllerPath, file_get_contents(__DIR__ . "/../stubs/{$controller}.stub"));
            }
        }

        $routesPath = base_path('routes/api.php');
        if (!str_contains(file_get_contents($routesPath), 'LaunchPoint Routes')) {
            File::append($routesPath, file_get_contents(__DIR__ . '/../stubs/api_routes.stub'));
        }

        $handlerPath = app_path('Exceptions/Handler.php');
        if (File::exists($handlerPath)) {
            $content = File::get($handlerPath);
            if (!str_contains($content, 'LaunchPoint Exception Handling')) {
                $renderable = __DIR__ . '/../stubs/handler_renderable.stub';
                if (File::exists($renderable)) {
                    $renderableContent = file_get_contents($renderable);
                    $content = str_replace('// register renderable here', $renderableContent, $content);
                    File::put($handlerPath, $content);
                }
            }
        }
    }
}
