<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeExceptionCommand
 *
 * Artisan command to generate a custom Exception with a JSON render() method
 * and automatically register it in bootstrap/app.php.
 */
class MakeExceptionCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-exception {name} {--message=} {--code=400}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a custom Exception with a JSON render() method and auto-register in bootstrap/app.php';

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

        if (!Str::endsWith($class, 'Exception')) {
            $class .= 'Exception';
        }

        $subNamespace = count($segments) ? '\\' . implode('\\', $segments) : '';
        $namespace = 'App\\Exceptions' . $subNamespace;

        $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
        $dirPath = app_path("Exceptions/{$relativeDir}");
        $filePath = "{$dirPath}{$class}.php";

        if (File::exists($filePath)) {
            $this->error("Exception [{$class}] already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($dirPath);

        $code = (int) ($this->option('code') ?: 400);
        $message = $this->option('message') ?: Str::headline(Str::replaceLast('Exception', '', $class)) . ' occurred.';

        $stub = File::get(__DIR__ . '/../stubs/Exception.stub');
        $stub = str_replace(
            ['{{namespace}}', '{{class}}', '{{code}}', '{{message}}'],
            [$namespace, $class, $code, addslashes($message)],
            $stub
        );

        File::put($filePath, $stub);
        $this->info("Exception [{$namespace}\\{$class}] created successfully.");

        $this->registerInBootstrapApp("{$namespace}\\{$class}");

        return self::SUCCESS;
    }

    /**
     * Automatically register the exception handler in bootstrap/app.php.
     *
     * @param string $fullClass
     * @return void
     */
    protected function registerInBootstrapApp(string $fullClass): void
    {
        $bootstrapApp = base_path('bootstrap/app.php');

        if (!File::exists($bootstrapApp)) {
            return;
        }

        $content = File::get($bootstrapApp);

        if (str_contains($content, $fullClass)) {
            return;
        }

        $registrationCode = "        \$exceptions->render(function (\\{$fullClass} \$e, \$request) {\n            return \$e->render(\$request);\n        });\n";

        // Pattern for Laravel 11/12 withExceptions callback
        $pattern = '/->withExceptions\s*\(\s*function\s*\(\s*Exceptions\s*\$exceptions\s*\)(?:\s*:\s*void)?\s*\{([\s\S]*?)\}\s*\)/';

        if (preg_match($pattern, $content, $matches)) {
            $existingBody = $matches[1];
            // Remove standalone comment "//" if present
            $cleanedBody = preg_replace('/^\s*\/\/\s*$/m', '', $existingBody);
            $newBody = rtrim($cleanedBody) . "\n" . $registrationCode . "    ";

            $replacement = str_replace($existingBody, $newBody, $matches[0]);
            $updatedContent = str_replace($matches[0], $replacement, $content);

            File::put($bootstrapApp, $updatedContent);
            $this->info("Exception auto-registered in [bootstrap/app.php].");
        }
    }
}
