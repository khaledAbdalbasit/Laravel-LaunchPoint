<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeRequestCommand
 *
 * Artisan command to generate a structured FormRequest.
 * Supports auto-generating validation rules and messages based on an optional --model.
 */
class MakeRequestCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-request {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an organized FormRequest with authorize(), rules(), and messages()';

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

        if (!Str::endsWith($class, 'Request')) {
            $class .= 'Request';
        }

        $subNamespace = count($segments) ? '\\' . implode('\\', $segments) : '';
        $namespace = 'App\\Http\\Requests' . $subNamespace;

        $relativeDir = count($segments) ? implode('/', $segments) . '/' : '';
        $dirPath = app_path("Http/Requests/{$relativeDir}");
        $filePath = "{$dirPath}{$class}.php";

        if (File::exists($filePath)) {
            $this->error("Request [{$class}] already exists.");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($dirPath);

        $modelName = $this->option('model');
        [$rulesContent, $messagesContent] = $this->generateRulesAndMessages($modelName);

        $stubPath = __DIR__ . '/../stubs/Request.stub';
        $stub = File::get($stubPath);
        $stub = str_replace(
            ['{{namespace}}', '{{class}}', '{{rules}}', '{{messages}}'],
            [$namespace, $class, $rulesContent, $messagesContent],
            $stub
        );

        File::put($filePath, $stub);
        $this->info("FormRequest [{$namespace}\\{$class}] created successfully.");

        return self::SUCCESS;
    }

    /**
     * Generate starter rules and messages based on model.
     *
     * @param string|null $modelName
     * @return array{0: string, 1: string}
     */
    protected function generateRulesAndMessages(?string $modelName): array
    {
        $fields = [];

        if ($modelName) {
            $modelClass = 'App\\Models\\' . str_replace('/', '\\', $modelName);
            if (class_exists($modelClass)) {
                try {
                    $modelInstance = new $modelClass();
                    $fillable = $modelInstance->getFillable();
                    if (!empty($fillable)) {
                        $fields = $fillable;
                    }
                } catch (\Throwable $e) {
                    // Fallback to name-based guess
                }
            }

            if (empty($fields)) {
                $fields = ['name', 'email', 'status'];
            }
        }

        if (empty($fields)) {
            $rules = [
                "            // 'title' => ['required', 'string', 'max:255'],",
                "            // 'description' => ['nullable', 'string'],",
            ];
            $messages = [
                "            // 'title.required' => 'The title field is required.',",
            ];
            return [implode("\n", $rules), implode("\n", $messages)];
        }

        $ruleLines = [];
        $messageLines = [];

        foreach ($fields as $field) {
            $rules = $this->inferRulesForField($field);
            $ruleLines[] = "            '{$field}' => [" . implode(', ', array_map(fn($r) => "'{$r}'", $rules)) . "],";
            if (in_array('required', $rules)) {
                $humanField = str_replace('_', ' ', $field);
                $messageLines[] = "            '{$field}.required' => 'The {$humanField} field is required.',";
            }
        }

        return [implode("\n", $ruleLines), implode("\n", $messageLines)];
    }

    /**
     * Infer validation rules for a given attribute name.
     *
     * @param string $field
     * @return array<string>
     */
    protected function inferRulesForField(string $field): array
    {
        if (Str::endsWith($field, '_id')) {
            return ['required', 'integer'];
        }

        return match ($field) {
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'phone', 'mobile' => ['nullable', 'string', 'max:20'],
            'age' => ['nullable', 'integer', 'min:1'],
            'is_active', 'status' => ['nullable', 'string', 'max:50'],
            'description', 'content', 'body', 'notes' => ['nullable', 'string'],
            default => ['required', 'string', 'max:255'],
        };
    }
}
