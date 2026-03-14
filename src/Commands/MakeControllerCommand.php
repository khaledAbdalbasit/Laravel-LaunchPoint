<?php

namespace KhaledAbdalbasit\LaunchPoint\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Class MakeControllerCommand
 *
 * Artisan command to generate a controller class.
 * Supports optional service injection and uses ApiResponseTrait for responses.
 */
class MakeControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpoint:make-controller {name} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller with optional service injection';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name');
        $service = $this->option('service');

        // If a service is specified but does not exist, auto-generate it with its repository
        if ($service) {
            $servicePath = app_path("Services/{$service}.php");
            if (!File::exists($servicePath)) {
                $this->call('launchpoint:make-service', ['name' => $service]);
                $this->info("Service {$service} auto-generated with its Repository.");
            }
        }

        $path = app_path("Http/Controllers/{$name}.php");

        if (File::exists($path)) {
            $this->error("Controller {$name} already exists.");
            return;
        }

        if (!File::exists(app_path('Http/Controllers'))) {
            File::makeDirectory(app_path('Http/Controllers'), 0755, true);
        }

        $stub = $service ? $this->serviceStub($name, $service) : $this->basicStub($name);
        File::put($path, $stub);

        $this->info("Controller {$name} created successfully.");
    }

    /**
     * Generate a basic controller stub without a service.
     *
     * @param string $class
     * @return string
     */
    protected function basicStub($class)
    {
        return <<<PHP
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

/**
 * Class {$class}
 *
 * Basic controller without service injection.
 */
class {$class} extends Controller
{

}
PHP;
    }

    /**
     * Generate a controller stub connected to a service.
     *
     * @param string \$class
     * @param string \$service
     * @return string
     */
    protected function serviceStub($class, $service)
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
 * Controller connected to {$service} service.
 * Provides standard CRUD methods using ApiResponseTrait for consistent JSON responses.
 */
class {$class} extends Controller
{
    use ApiResponseTrait;

    /**
     * The injected service instance.
     *
     * @var {$service}
     */
    protected {$service} \$service;

    /**
     * {$class} constructor.
     *
     * @param {$service} \$service
     */
    public function __construct({$service} \$service)
    {
        \$this->service = \$service;
    }

    /**
     * List all records.
     *
     * @return \\Illuminate\Http\JsonResponse
     */
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

    /**
     * Show a single record by ID.
     *
     * @param int \$id
     * @return \\Illuminate\Http\JsonResponse
     */
    public function show(\$id)
    {
        try {
            \$data = \$this->service->findOrFail(\$id);
            return \$this->apiResponse(['data' => \$data, 'message' => 'Record found.']);
        } catch (ModelNotFoundException \$e) {
            return \$this->apiResponse(['message' => 'Resource not found.', 'code' => 404]);
        } catch (QueryException \$e) {
            return \$this->apiResponse(['message' => 'Database error: ' . \$e->getMessage(), 'code' => 500]);
        } catch (Exception \$e) {
            return \$this->apiResponse(['message' => 'Unexpected error: ' . \$e->getMessage(), 'code' => 500]);
        }
    }

    /**
     * Store a new record.
     *
     * @param \\Illuminate\Http\Request \$request
     * @return \\Illuminate\Http\JsonResponse
     */
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

    /**
     * Update an existing record.
     *
     * @param \\Illuminate\Http\Request \$request
     * @param int \$id
     * @return \\Illuminate\Http\JsonResponse
     */
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

    /**
     * Delete a record by ID.
     *
     * @param int \$id
     * @return \\Illuminate\Http\JsonResponse
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
