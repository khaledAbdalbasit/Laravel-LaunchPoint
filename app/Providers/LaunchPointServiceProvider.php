<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class LaunchPointServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/launchpoint.php', 'launchpoint');
    }

    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../../config/launchpoint.php' => config_path('launchpoint.php'),
        ], 'config');

        //  Publish ApiResponse Trait
        $traitPath = app_path('Traits/ApiResponseTrait.stub');
        if (!File::exists($traitPath)) {
            File::ensureDirectoryExists(app_path('Traits'));
            File::put($traitPath, file_get_contents(__DIR__.'/stubs/ApiResponseTrait.stub'));
        }

        //  Publish Controllers
        $controllers = ['SettingsController', 'AuthController', 'ProfileController'];
        foreach ($controllers as $controller) {
            $controllerPath = app_path("Http/Controllers/Api/{$controller}.php");
            if (!File::exists($controllerPath)) {
                File::ensureDirectoryExists(app_path('Http/Controllers/Api'));
                File::put($controllerPath, file_get_contents(__DIR__."/stubs/{$controller}.stub"));
            }
        }

        // Publish API routes
        $routesPath = base_path('routes/api.php');
        if (!str_contains(file_get_contents($routesPath), 'LaunchPoint Routes')) {
            File::append($routesPath, file_get_contents(__DIR__.'/stubs/api_routes.stub'));
        }

        // Publish Global Exception Handler
        $handlerPath = app_path('Exceptions/Handler.php');
        if (File::exists($handlerPath)) {
            $content = File::get($handlerPath);
            if (!str_contains($content, 'LaunchPoint Exception Handling')) {
                // append renderable logic
                $renderable = file_get_contents(__DIR__.'/stubs/handler_renderable.stub');
                $content = str_replace('// register renderable here', $renderable, $content);
                File::put($handlerPath, $content);
            }
        }

        $this->loadRoutesFrom(__DIR__.'/stubs/api_routes.stub');
    }
}
