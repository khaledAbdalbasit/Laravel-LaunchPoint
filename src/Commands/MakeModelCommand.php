<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeModelCommand
 *
 * Artisan command to generate an Eloquent Model with pre-configured $fillable.
 * Supports --migration, --factory, --seeder, and --all options.
 */
class MakeModelCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-model {name} {--m|migration} {--f|factory} {--s|seeder} {--a|all} {--fillable=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model with pre-configured fillable attributes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->displayLogo();

        $name = str_replace('/', '\\', $this->argument('name'));
        $segments = array_filter(explode('\\', trim($name, '\\')));
        $class = array_pop($segments);
        $subNamespace = count($segments) ? '\\' . implode('\\', $segments) : '';
        $namespace = 'App\\Models' . $subNamespace;

        $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
        $dirPath = app_path("Models/{$relativeDir}");
        $filePath = "{$dirPath}{$class}.php";

        if (File::exists($filePath)) {
            $this->error("Model [{$name}] already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($dirPath);

        // Parse fillable attributes
        $fillableInput = $this->option('fillable');
        if ($fillableInput) {
            $fields = array_map('trim', explode(',', $fillableInput));
            $formattedFillable = implode("\n", array_map(fn($f) => "        '{$f}',", array_filter($fields)));
        } else {
            $formattedFillable = "        // 'title',\n        // 'description',";
        }

        $stubPath = __DIR__ . '/../stubs/Model.stub';
        $stub = File::get($stubPath);
        $stub = str_replace(
            ['{{namespace}}', '{{class}}', '{{fillable}}'],
            [$namespace, $class, $formattedFillable],
            $stub
        );

        File::put($filePath, $stub);
        $this->info("Model [App\\Models\\{$name}] created successfully.");

        $all = (bool) $this->option('all');
        $migration = $all || (bool) $this->option('migration');
        $factory = $all || (bool) $this->option('factory');
        $seeder = $all || (bool) $this->option('seeder');

        $fullModelClass = "App\\Models\\{$name}";

        if ($migration) {
            $table = Str::snake(Str::pluralStudly($class));
            $this->call('make:migration', [
                'name' => "create_{$table}_table",
                '--create' => $table,
            ]);
            $this->info("Migration for [{$table}] created.");
        }

        if ($factory) {
            $this->call('make:factory', [
                'name' => "{$class}Factory",
                '--model' => $fullModelClass,
            ]);
            $this->info("Factory [{$class}Factory] created.");
        }

        if ($seeder) {
            $this->call('make:seeder', [
                'name' => "{$class}Seeder",
            ]);
            $this->info("Seeder [{$class}Seeder] created.");
        }

        return self::SUCCESS;
    }
}
