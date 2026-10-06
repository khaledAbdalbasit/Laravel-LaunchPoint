<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeScopeCommand
 *
 * Artisan command to generate an Eloquent Scope class or Local Scope Trait.
 */
class MakeScopeCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-scope {name} {--local} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an Eloquent Scope class (or Local Scope Trait with --local)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->displayLogo();

        $rawName = str_replace('/', '\\', $this->argument('name'));
        $segments = array_filter(explode('\\', trim($rawName, '\\')));
        $class = array_pop($segments);

        if (!Str::endsWith($class, 'Scope')) {
            $class .= 'Scope';
        }

        $isLocal = (bool) $this->option('local');

        if ($isLocal) {
            $subNamespace = count($segments) ? '\\' . implode('\\', $segments) : '';
            $namespace = 'App\\Traits\\Scopes' . $subNamespace;
            $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
            $dirPath = app_path("Traits/Scopes/{$relativeDir}");
            $filePath = "{$dirPath}{$class}.php";

            if (File::exists($filePath)) {
                $this->error("Scope Trait [{$class}] already exists.");
                return self::FAILURE;
            }

            File::ensureDirectoryExists($dirPath);

            $methodName = Str::studly(Str::replaceLast('Scope', '', $class));

            $stub = File::get(__DIR__ . '/../stubs/ScopeTrait.stub');
            $stub = str_replace(
                ['{{namespace}}', '{{class}}', '{{method}}'],
                [$namespace, $class, $methodName],
                $stub
            );

            File::put($filePath, $stub);
            $this->info("Local Scope Trait [{$namespace}\\{$class}] created successfully.");
        } else {
            $subNamespace = count($segments) ? '\\' . implode('\\', $segments) : '';
            $namespace = 'App\\Models\\Scopes' . $subNamespace;
            $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
            $dirPath = app_path("Models/Scopes/{$relativeDir}");
            $filePath = "{$dirPath}{$class}.php";

            if (File::exists($filePath)) {
                $this->error("Scope [{$class}] already exists.");
                return self::FAILURE;
            }

            File::ensureDirectoryExists($dirPath);

            $stub = File::get(__DIR__ . '/../stubs/Scope.stub');
            $stub = str_replace(
                ['{{namespace}}', '{{class}}'],
                [$namespace, $class],
                $stub
            );

            File::put($filePath, $stub);
            $this->info("Eloquent Scope [{$namespace}\\{$class}] created successfully.");
        }

        return self::SUCCESS;
    }
}
