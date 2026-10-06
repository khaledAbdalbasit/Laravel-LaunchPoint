<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaunchPoint\Traits\CanDisplayLogo;

/**
 * Class MakeRequestCommand
 *
 * Artisan command to generate a FormRequest class.
 * Appends "Request" suffix automatically if not provided.
 */
class MakeRequestCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-request {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Form Request class';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (!str_ends_with($name, 'Request')) {
            $name .= 'Request';
        }

        $path = app_path("Http/Requests/{$name}.php");

        if (File::exists($path)) {
            $this->error("Request [{$name}] already exists.");
            return;
        }

        File::ensureDirectoryExists(app_path('Http/Requests'));
        File::put($path, $this->stub($name));

        $this->info("Request [{$name}] created successfully.");
    }

    /**
     * Generate the FormRequest stub.
     *
     * @param string $class
     * @return string
     */
    protected function stub(string $class): string
    {
        return <<<PHP
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class {$class}
 *
 * Validates incoming request data.
 */
class {$class} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
PHP;
    }
}
