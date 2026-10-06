<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LaunchPointCommandsTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up test generated files
        File::delete(app_path('Models/TestPost.php'));
        File::delete(app_path('Http/Requests/StoreTestPostRequest.php'));
        File::delete(app_path('Http/Resources/TestPostResource.php'));
        File::delete(app_path('Http/Resources/TestPostCollection.php'));
        File::delete(app_path('Enums/TestStatus.php'));
        File::delete(app_path('Actions/CreateTestPostAction.php'));
        File::delete(app_path('Traits/HasTestSlug.php'));
        File::delete(app_path('Exceptions/TestCustomException.php'));
        File::delete(app_path('Filters/TestPostFilter.php'));
        File::delete(app_path('Models/Scopes/TestActiveScope.php'));
        File::delete(app_path('Traits/Scopes/TestActiveScope.php'));

        parent::tearDown();
    }

    public function test_make_model_command()
    {
        $this->artisan('launchpoint:make-model', [
            'name' => 'TestPost',
            '--fillable' => 'title,slug,body',
        ])->assertSuccessful();

        $path = app_path('Models/TestPost.php');
        $this->assertTrue(File::exists($path));

        $content = File::get($path);
        $this->assertStringContainsString('class TestPost extends Model', $content);
        $this->assertStringContainsString("'title',", $content);
        $this->assertStringContainsString("'slug',", $content);
        $this->assertStringContainsString("'body',", $content);
    }

    public function test_make_request_command()
    {
        $this->artisan('launchpoint:make-request', [
            'name' => 'StoreTestPost',
            '--model' => 'TestPost',
        ])->assertSuccessful();

        $path = app_path('Http/Requests/StoreTestPostRequest.php');
        $this->assertTrue(File::exists($path));

        $content = File::get($path);
        $this->assertStringContainsString('class StoreTestPostRequest extends FormRequest', $content);
        $this->assertStringContainsString('public function authorize(): bool', $content);
        $this->assertStringContainsString('public function rules(): array', $content);
        $this->assertStringContainsString('public function messages(): array', $content);
    }

    public function test_make_resource_command_with_collection()
    {
        $this->artisan('launchpoint:make-resource', [
            'name' => 'TestPost',
            '--collection' => true,
        ])->assertSuccessful();

        $resourcePath = app_path('Http/Resources/TestPostResource.php');
        $collectionPath = app_path('Http/Resources/TestPostCollection.php');

        $this->assertTrue(File::exists($resourcePath));
        $this->assertTrue(File::exists($collectionPath));

        $resourceContent = File::get($resourcePath);
        $this->assertStringContainsString('class TestPostResource extends JsonResource', $resourceContent);

        $collectionContent = File::get($collectionPath);
        $this->assertStringContainsString('class TestPostCollection extends ResourceCollection', $collectionContent);
    }

    public function test_make_enum_command()
    {
        $this->artisan('launchpoint:make-enum', [
            'name' => 'TestStatus',
            '--cases' => 'DRAFT=draft,PUBLISHED=published,ARCHIVED=archived',
        ])->assertSuccessful();

        $path = app_path('Enums/TestStatus.php');
        $this->assertTrue(File::exists($path));

        $content = File::get($path);
        $this->assertStringContainsString('enum TestStatus: string', $content);
        $this->assertStringContainsString("case DRAFT = 'draft';", $content);
        $this->assertStringContainsString("case PUBLISHED = 'published';", $content);
        $this->assertStringContainsString('public function label(): string', $content);
        $this->assertStringContainsString('public static function options(): array', $content);
    }

    public function test_make_action_command()
    {
        $this->artisan('launchpoint:make-action', [
            'name' => 'CreateTestPostAction',
        ])->assertSuccessful();

        $path = app_path('Actions/CreateTestPostAction.php');
        $this->assertTrue(File::exists($path));

        $content = File::get($path);
        $this->assertStringContainsString('class CreateTestPostAction', $content);
        $this->assertStringContainsString('public function handle(mixed ...$args): mixed', $content);
        $this->assertStringContainsString('public static function run(mixed ...$args): mixed', $content);
    }

    public function test_make_trait_command()
    {
        $this->artisan('launchpoint:make-trait', [
            'name' => 'HasTestSlug',
        ])->assertSuccessful();

        $path = app_path('Traits/HasTestSlug.php');
        $this->assertTrue(File::exists($path));

        $content = File::get($path);
        $this->assertStringContainsString('trait HasTestSlug', $content);
    }

    public function test_make_exception_command()
    {
        $this->artisan('launchpoint:make-exception', [
            'name' => 'TestCustomException',
            '--message' => 'Custom error message',
            '--code' => '422',
        ])->assertSuccessful();

        $path = app_path('Exceptions/TestCustomException.php');
        $this->assertTrue(File::exists($path));

        $content = File::get($path);
        $this->assertStringContainsString('class TestCustomException extends Exception', $content);
        $this->assertStringContainsString('protected $code = 422;', $content);
        $this->assertStringContainsString('public function render(?Request $request = null): JsonResponse', $content);
    }

    public function test_make_filter_command()
    {
        $this->artisan('launchpoint:make-filter', [
            'name' => 'TestPostFilter',
        ])->assertSuccessful();

        $path = app_path('Filters/TestPostFilter.php');
        $this->assertTrue(File::exists($path));

        $content = File::get($path);
        $this->assertStringContainsString('class TestPostFilter', $content);
        $this->assertStringContainsString('public function apply(Builder $query): Builder', $content);
    }

    public function test_make_scope_command_global_and_local()
    {
        // Global scope
        $this->artisan('launchpoint:make-scope', [
            'name' => 'TestActiveScope',
        ])->assertSuccessful();

        $scopePath = app_path('Models/Scopes/TestActiveScope.php');
        $this->assertTrue(File::exists($scopePath));
        $scopeContent = File::get($scopePath);
        $this->assertStringContainsString('class TestActiveScope implements Scope', $scopeContent);

        // Local scope trait
        $this->artisan('launchpoint:make-scope', [
            'name' => 'TestActiveScope',
            '--local' => true,
        ])->assertSuccessful();

        $traitPath = app_path('Traits/Scopes/TestActiveScope.php');
        $this->assertTrue(File::exists($traitPath));
        $traitContent = File::get($traitPath);
        $this->assertStringContainsString('trait TestActiveScope', $traitContent);
        $this->assertStringContainsString('public function scopeTestActive(Builder $query', $traitContent);
    }

    public function test_launchpoint_list_command()
    {
        $this->artisan('launchpoint:list')
            ->assertSuccessful()
            ->expectsOutputToContain('Available LaunchPoint Commands');
    }
}
