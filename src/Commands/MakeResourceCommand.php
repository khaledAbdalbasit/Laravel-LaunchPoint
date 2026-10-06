<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeResourceCommand
 *
 * Artisan command to generate an API Resource and optional ResourceCollection.
 */
class MakeResourceCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-resource {name} {--c|collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API Resource class (and optional ResourceCollection)';

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
        $rawClass = array_pop($segments);

        $baseName = Str::endsWith($rawClass, 'Resource')
            ? Str::replaceLast('Resource', '', $rawClass)
            : $rawClass;

        $resourceClass = "{$baseName}Resource";
        $subNamespace = count($segments) ? '\\' . implode('\\', $segments) : '';
        $namespace = 'App\\Http\\Resources' . $subNamespace;

        $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
        $dirPath = app_path("Http/Resources/{$relativeDir}");
        $resourcePath = "{$dirPath}{$resourceClass}.php";

        if (File::exists($resourcePath)) {
            $this->error("Resource [{$resourceClass}] already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($dirPath);

        $resourceStub = File::get(__DIR__ . '/../stubs/Resource.stub');
        $resourceStub = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $resourceClass],
            $resourceStub
        );

        File::put($resourcePath, $resourceStub);
        $this->info("Resource [{$namespace}\\{$resourceClass}] created successfully.");

        if ($this->option('collection') || $this->option('c')) {
            $collectionClass = "{$baseName}Collection";
            $collectionPath = "{$dirPath}{$collectionClass}.php";

            if (!File::exists($collectionPath)) {
                $collectionStub = File::get(__DIR__ . '/../stubs/ResourceCollection.stub');
                $collectionStub = str_replace(
                    ['{{namespace}}', '{{class}}'],
                    [$namespace, $collectionClass],
                    $collectionStub
                );

                File::put($collectionPath, $collectionStub);
                $this->info("ResourceCollection [{$namespace}\\{$collectionClass}] created successfully.");
            }
        }

        return self::SUCCESS;
    }
}
