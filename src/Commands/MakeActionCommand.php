<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeActionCommand
 *
 * Artisan command to generate a Single Action Class.
 */
class MakeActionCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-action {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Single Action class with a handle() method';

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
        $namespace = 'App\\Actions' . $subNamespace;

        $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
        $dirPath = app_path("Actions/{$relativeDir}");
        $filePath = "{$dirPath}{$class}.php";

        if (File::exists($filePath)) {
            $this->error("Action [{$class}] already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($dirPath);

        $stub = File::get(__DIR__ . '/../stubs/Action.stub');
        $stub = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $class],
            $stub
        );

        File::put($filePath, $stub);
        $this->info("Action [{$namespace}\\{$class}] created successfully.");

        return self::SUCCESS;
    }
}
