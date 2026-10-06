<?php

namespace LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaunchPoint\Traits\CanDisplayLogo;
use Illuminate\Support\Str;

/**
 * Class MakeControllerCommand
 *
 * Artisan command to generate a controller class.
 *
 * Supports:
 *  - Basic controller (no options)
 *  - Service-injected controller (--service)
 *  - Full stack scaffold with --all:
 *      Repository → Service → Controller + Request + Resource
 */
class MakeControllerCommand extends Command
{
    use CanDisplayLogo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-controller
                            {name}
                            {--service=}
                            {--model=}
                            {--request=}
                            {--resource=}
                            {--all}
                            {--a}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller — use --all to scaffold Repository, Service, Request & Resource together';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name     = $this->argument('name');
        $service  = $this->option('service');
        $model    = $this->option('model');
        $request  = $this->option('request');
        $resource = $this->option('resource');
        $all      = $this->option('all') || $this->option('a');

        // Derive all names from the base when --all is used
        if ($all) {
            $base = basename(str_replace(['/', '\\'], '/', $name));
            if (str_ends_with($base, 'Controller')) {
                $base = substr($base, 0, -10);
            }

            $service  = $service  ?: "{$base}Service";
            $model    = $model    ?: $base;
            $request  = $request  ?: "{$base}Request";
            $resource = $resource ?: "{$base}Resource";
        }

        // Ensure Controller suffix
        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        // Auto-generate Service (and its Repository) if needed
        if ($service && !File::exists(app_path("Services/{$service}.php"))) {
            $args = ['name' => $service];
            if ($model) {
                $args['--model'] = $model;
            }
            $this->call('launchpoint:make-service', $args);
        }

        // Auto-generate Request if needed
        if ($request && !File::exists(app_path("Http/Requests/{$request}.php"))) {
            $this->call('launchpoint:make-request', ['name' => $request]);
        }

        // Auto-generate Resource if needed
        if ($resource && !File::exists(app_path("Http/Resources/{$resource}.php"))) {
            $this->call('launchpoint:make-resource', ['name' => $resource]);
        }

        // Build the controller
        $path = app_path("Http/Controllers/{$name}.php");

        if (File::exists($path)) {
            $this->error("Controller [{$name}] already exists.");
            return;
        }

        File::ensureDirectoryExists(app_path('Http/Controllers'));

        $stub = match (true) {
            (bool)$service && (bool)$request && (bool)$resource => $this->fullStub($name, $service, $request, $resource),
            (bool)$service                                       => $this->serviceStub($name, $service),
            default                                              => $this->basicStub($name),
        };

        File::put($path, $stub);

        $this->info("Controller [{$name}] created successfully.");
    }

    /**
     * Generate a basic controller stub without any dependency.
     *
     * @param string $class
     * @return string
     */
    protected function basicStub(string $class): string
    {
        return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

/**
 * Class {$class}
 */
class {$class} extends Controller
{

}
PHP;
    }

    /**
     * Generate a controller stub injecting only a Service.
     *
     * @param string $class
     * @param string $service
     * @return string
     */
    protected function serviceStub(string $class, string $service): string
    {
        return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\\{$service};
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * Class {$class}
 *
 * Controller connected to {$service}.
 */
class {$class} extends Controller
{
    use ApiResponseTrait;

    protected {$service} \$service;

    public function __construct({$service} \$service)
    {
        \$this->service = \$service;
    }

    public function index()
    {
        try {
            \$data = \$this->service->getAll();
            return \$this->apiResponse(['data' => \$data, 'message' => 'Records retrieved successfully.']);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    public function show(\$id)
    {
        try {
            \$data = \$this->service->findOrFail(\$id);
            return \$this->apiResponse(['data' => \$data, 'message' => 'Record found.']);
        } catch (ModelNotFoundException \$e) {
            return \$this->apiResponse(['message' => 'Resource not found.', 'code' => 404]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    public function store(Request \$request)
    {
        try {
            \$data = \$this->service->create(\$request->all());
            return \$this->apiResponse(['data' => \$data, 'message' => 'Record created successfully.', 'code' => 201]);
        } catch (ValidationException \$e) {
            return \$this->apiResponse(['message' => 'Validation failed.', 'data' => \$e->errors(), 'code' => 422]);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    public function update(Request \$request, \$id)
    {
        try {
            \$data = \$this->service->update(\$id, \$request->all());
            return \$this->apiResponse(['data' => \$data, 'message' => 'Record updated successfully.']);
        } catch (ModelNotFoundException \$e) {
            return \$this->apiResponse(['message' => 'Resource not found.', 'code' => 404]);
        } catch (ValidationException \$e) {
            return \$this->apiResponse(['message' => 'Validation failed.', 'data' => \$e->errors(), 'code' => 422]);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    public function destroy(\$id)
    {
        try {
            \$this->service->delete(\$id);
            return \$this->apiResponse(['message' => 'Record deleted successfully.']);
        } catch (ModelNotFoundException \$e) {
            return \$this->apiResponse(['message' => 'Resource not found.', 'code' => 404]);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }
}
PHP;
    }

    /**
     * Generate a full controller stub with Service, FormRequest, and API Resource.
     * Used when --all (or --request + --resource) flags are provided.
     *
     * Chain: Request → Controller → Service → Repository → Model
     *                                       ↓
     *                                   Resource (response)
     *
     * @param string $class
     * @param string $service
     * @param string $request
     * @param string $resource
     * @return string
     */
    protected function fullStub(string $class, string $service, string $request, string $resource): string
    {
        return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\\{$service};
use App\Http\Requests\\{$request};
use App\Http\Resources\\{$resource};
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

/**
 * Class {$class}
 *
 * Full-stack controller generated by LaunchPoint.
 * Chain: {$request} → {$class} → {$service} → Repository → Model → {$resource}
 */
class {$class} extends Controller
{
    use ApiResponseTrait;

    protected {$service} \$service;

    public function __construct({$service} \$service)
    {
        \$this->service = \$service;
    }

    /**
     * List all records wrapped in a Resource collection.
     */
    public function index()
    {
        try {
            \$data = \$this->service->getAll();
            return \$this->apiResponse([
                'data'    => {$resource}::collection(\$data),
                'message' => 'Records retrieved successfully.',
            ]);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Show a single record wrapped in a Resource.
     */
    public function show(\$id)
    {
        try {
            \$data = \$this->service->findOrFail(\$id);
            return \$this->apiResponse([
                'data'    => new {$resource}(\$data),
                'message' => 'Record found.',
            ]);
        } catch (ModelNotFoundException \$e) {
            return \$this->apiResponse(['message' => 'Resource not found.', 'code' => 404]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Store a new record using validated Request data.
     */
    public function store({$request} \$request)
    {
        try {
            \$data = \$this->service->create(\$request->validated());
            return \$this->apiResponse([
                'data'    => new {$resource}(\$data),
                'message' => 'Record created successfully.',
                'code'    => 201,
            ]);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Update an existing record using validated Request data.
     */
    public function update({$request} \$request, \$id)
    {
        try {
            \$data = \$this->service->update(\$id, \$request->validated());
            return \$this->apiResponse([
                'data'    => new {$resource}(\$data),
                'message' => 'Record updated successfully.',
            ]);
        } catch (ModelNotFoundException \$e) {
            return \$this->apiResponse(['message' => 'Resource not found.', 'code' => 404]);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Delete a record by ID.
     */
    public function destroy(\$id)
    {
        try {
            \$this->service->delete(\$id);
            return \$this->apiResponse(['message' => 'Record deleted successfully.']);
        } catch (ModelNotFoundException \$e) {
            return \$this->apiResponse(['message' => 'Resource not found.', 'code' => 404]);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }
}
PHP;
    }
}
