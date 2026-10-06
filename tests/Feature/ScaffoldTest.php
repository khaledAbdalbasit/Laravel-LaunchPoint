<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ScaffoldTest extends TestCase
{
    protected function tearDown(): void
    {
        File::delete(app_path('Enums/InvoiceStatus.php'));
        File::delete(app_path('Exceptions/PaymentFailedException.php'));
        File::delete(app_path('Actions/CalculateTotalAction.php'));

        parent::tearDown();
    }

    public function test_generated_enum_methods_work_in_runtime()
    {
        $this->artisan('launchpoint:make-enum', [
            'name' => 'InvoiceStatus',
            '--cases' => 'PAID=paid,UNPAID=unpaid',
        ])->assertSuccessful();

        require_once app_path('Enums/InvoiceStatus.php');

        $this->assertEquals(['paid', 'unpaid'], \App\Enums\InvoiceStatus::values());
        $this->assertEquals(['PAID', 'UNPAID'], \App\Enums\InvoiceStatus::names());
        $this->assertEquals('Paid', \App\Enums\InvoiceStatus::PAID->label());
        $this->assertEquals(['paid' => 'Paid', 'unpaid' => 'Unpaid'], \App\Enums\InvoiceStatus::options());
    }

    public function test_generated_exception_render_returns_expected_json()
    {
        $this->artisan('launchpoint:make-exception', [
            'name' => 'PaymentFailedException',
            '--code' => 402,
            '--message' => 'Payment required to proceed',
        ])->assertSuccessful();

        require_once app_path('Exceptions/PaymentFailedException.php');

        $exception = new \App\Exceptions\PaymentFailedException();
        $response = $exception->render(Request::create('/test', 'GET'));

        $this->assertEquals(402, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals(402, $data['code']);
        $this->assertEquals('Payment required to proceed', $data['message']);
        $this->assertEquals('PaymentFailedException', $data['error']);
    }

    public function test_generated_action_run_and_handle()
    {
        $this->artisan('launchpoint:make-action', [
            'name' => 'CalculateTotalAction',
        ])->assertSuccessful();

        require_once app_path('Actions/CalculateTotalAction.php');

        $action = new \App\Actions\CalculateTotalAction();
        $this->assertTrue($action->handle());
        $this->assertTrue(\App\Actions\CalculateTotalAction::run());
    }
}
