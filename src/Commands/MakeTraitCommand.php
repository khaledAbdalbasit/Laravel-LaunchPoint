<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeTraitCommand
 *
 * Artisan command to generate a Trait with proper PSR-4 namespace.
 */
class MakeTraitCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-trait {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Trait with proper namespace';

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

        $subNamespace = count($segments) ? '\\' . implode('\\', $segments) : '';
        $namespace = 'App\\Traits' . $subNamespace;

        $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
        $dirPath = app_path("Traits/{$relativeDir}");
        $filePath = "{$dirPath}{$class}.php";

        if (File::exists($filePath)) {
            $this->error("Trait [{$class}] already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($dirPath);

        $stub = File::get(__DIR__ . '/../stubs/Trait.stub');
        $stub = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $class],
            $stub
        );

        File::put($filePath, $stub);
        $this->info("Trait [{$namespace}\\{$class}] created successfully.");

        return self::SUCCESS;
    }
}
