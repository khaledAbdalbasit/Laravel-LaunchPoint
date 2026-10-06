<?php

namespace Tests\Unit;

use App\Traits\ApiResponseTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ApiResponseTraitTest extends TestCase
{
    use ApiResponseTrait;

    public function test_api_response_returns_basic_structure()
    {
        $response = $this->apiResponse([
            'message' => 'Success',
            'data' => ['id' => 1, 'name' => 'Test'],
            'code' => 200,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);

        $this->assertEquals(200, $data['code']);
        $this->assertEquals('Success', $data['message']);
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $data['data']);
    }

    public function test_api_response_supports_length_aware_paginator()
    {
        $items = collect([
            ['id' => 1, 'title' => 'Item 1'],
            ['id' => 2, 'title' => 'Item 2'],
        ]);

        $paginator = new LengthAwarePaginator(
            $items,
            10, // total
            2,  // per page
            1,  // current page
            ['path' => 'http://localhost/api/items']
        );

        $response = $this->apiResponse([
            'message' => 'Items retrieved',
            'data' => $paginator,
        ]);

        $payload = $response->getData(true);

        $this->assertArrayHasKey('meta', $payload);
        $this->assertArrayHasKey('links', $payload);

        $this->assertEquals(1, $payload['meta']['current_page']);
        $this->assertEquals(10, $payload['meta']['total']);
        $this->assertEquals(2, $payload['meta']['per_page']);
        $this->assertEquals(5, $payload['meta']['last_page']);

        $this->assertNotNull($payload['links']['first']);
        $this->assertNotNull($payload['links']['last']);
        $this->assertNotNull($payload['links']['next']);
        $this->assertNull($payload['links']['prev']);

        $this->assertCount(2, $payload['data']);
    }

    public function test_api_response_supports_paginator_via_pagination_key()
    {
        $items = collect([['id' => 1]]);
        $paginator = new LengthAwarePaginator($items, 1, 1, 1, ['path' => 'http://localhost/api/test']);

        $response = $this->apiResponse([
            'data' => $items,
            'pagination' => $paginator,
        ]);

        $payload = $response->getData(true);

        $this->assertArrayHasKey('meta', $payload);
        $this->assertArrayHasKey('links', $payload);
        $this->assertEquals(1, $payload['meta']['total']);
    }
}
