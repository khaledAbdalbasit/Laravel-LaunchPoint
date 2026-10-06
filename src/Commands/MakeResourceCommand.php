<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeResourceCommand
 *
 * Artisan command to generate a JSON API Resource class.
 * Appends "Resource" suffix automatically if not provided.
 */
class MakeResourceCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-resource {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API Resource class';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (!str_ends_with($name, 'Resource')) {
            $name .= 'Resource';
        }

        $path = app_path("Http/Resources/{$name}.php");

        if (File::exists($path)) {
            $this->error("Resource [{$name}] already exists.");
            return;
        }

        File::ensureDirectoryExists(app_path('Http/Resources'));
        File::put($path, $this->stub($name));

        $this->info("Resource [{$name}] created successfully.");
    }

    /**
     * Generate the JsonResource stub.
     *
     * @param string $class
     * @return string
     */
    protected function stub(string $class): string
    {
        return <<<PHP
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class {$class}
 *
 * Transforms the model into a JSON-friendly array for API responses.
 */
class {$class} extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request \$request): array
    {
        return parent::toArray(\$request);
    }
}
PHP;
    }
}
