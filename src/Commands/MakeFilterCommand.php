<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeFilterCommand
 *
 * Artisan command to generate an Eloquent Query Filter class.
 */
class MakeFilterCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-filter {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an Eloquent Query Filter class for request filtering';

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

        if (!Str::endsWith($class, 'Filter')) {
            $class .= 'Filter';
        }

        $subNamespace = count($segments) ? '\\' . implode('\\', $segments) : '';
        $namespace = 'App\\Filters' . $subNamespace;

        $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
        $dirPath = app_path("Filters/{$relativeDir}");
        $filePath = "{$dirPath}{$class}.php";

        if (File::exists($filePath)) {
            $this->error("Filter [{$class}] already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($dirPath);

        $stub = File::get(__DIR__ . '/../stubs/Filter.stub');
        $stub = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $class],
            $stub
        );

        File::put($filePath, $stub);
        $this->info("Filter [{$namespace}\\{$class}] created successfully.");

        return self::SUCCESS;
    }
}
