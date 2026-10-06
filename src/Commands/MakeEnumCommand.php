<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeEnumCommand
 *
 * Artisan command to generate a PHP 8.1+ Backed Enum with label() and option helpers.
 */
class MakeEnumCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-enum {name} {--cases=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a PHP 8.1 Backed Enum with label() and helper methods';

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
        $namespace = 'App\\Enums' . $subNamespace;

        $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
        $dirPath = app_path("Enums/{$relativeDir}");
        $filePath = "{$dirPath}{$class}.php";

        if (File::exists($filePath)) {
            $this->error("Enum [{$class}] already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($dirPath);

        $casesInput = $this->option('cases');
        $parsedCases = $this->parseCases($casesInput);

        $casesLines = [];
        $labelLines = [];

        foreach ($parsedCases as $caseName => $caseValue) {
            $casesLines[] = "    case {$caseName} = '{$caseValue}';";
            $humanLabel = Str::headline($caseValue);
            $labelLines[] = "            self::{$caseName} => '{$humanLabel}',";
        }

        $stub = File::get(__DIR__ . '/../stubs/Enum.stub');
        $stub = str_replace(
            ['{{namespace}}', '{{class}}', '{{cases}}', '{{labels}}'],
            [$namespace, $class, implode("\n", $casesLines), implode("\n", $labelLines)],
            $stub
        );

        File::put($filePath, $stub);
        $this->info("Enum [{$namespace}\\{$class}] created successfully.");

        return self::SUCCESS;
    }

    /**
     * Parse the --cases option string into key-value pairs.
     *
     * @param string|null $input
     * @return array<string, string>
     */
    protected function parseCases(?string $input): array
    {
        if (empty($input)) {
            return [
                'ACTIVE' => 'active',
                'INACTIVE' => 'inactive',
                'PENDING' => 'pending',
            ];
        }

        $result = [];
        $items = array_map('trim', explode(',', $input));

        foreach ($items as $item) {
            if (str_contains($item, '=')) {
                [$k, $v] = explode('=', $item, 2);
                $key = $this->formatCaseKey($k);
                $val = trim($v, " '\"");
                $result[$key] = $val;
            } else {
                $key = $this->formatCaseKey($item);
                $val = Str::lower(Str::snake($item));
                $result[$key] = $val;
            }
        }

        return $result;
    }

    /**
     * Format case constant name into valid UPPER_SNAKE_CASE.
     *
     * @param string $key
     * @return string
     */
    protected function formatCaseKey(string $key): string
    {
        $key = trim($key);
        if (strtoupper($key) === $key) {
            return preg_replace('/[^A-Z0-9_]/', '_', $key);
        }

        return Str::upper(Str::snake($key));
    }
}
