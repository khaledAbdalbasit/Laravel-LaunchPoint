<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\AbstractPaginator;

trait ApiResponseTrait
{
    /**
     * Return a standardized API JSON response.
     *
     * @param array $options
     * @return JsonResponse
     */
    public function apiResponse(array $options = []): JsonResponse
    {
        $code = $options['code'] ?? 200;
        $message = $options['message'] ?? null;
        $data = $options['data'] ?? null;
        $pagination = $options['pagination'] ?? null;

        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];

        // Handle LengthAwarePaginator passed via $data or $pagination
        $paginator = null;
        if ($data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator) {
            $paginator = $data;
            $response['data'] = $data->items();
        } elseif ($pagination instanceof LengthAwarePaginator || $pagination instanceof AbstractPaginator) {
            $paginator = $pagination;
        }

        if ($paginator) {
            $response['meta'] = [
                'current_page' => $paginator->currentPage(),
                'from' => method_exists($paginator, 'firstItem') ? $paginator->firstItem() : null,
                'last_page' => method_exists($paginator, 'lastPage') ? $paginator->lastPage() : null,
                'per_page' => $paginator->perPage(),
                'to' => method_exists($paginator, 'lastItem') ? $paginator->lastItem() : null,
                'total' => method_exists($paginator, 'total') ? $paginator->total() : null,
            ];

            $response['links'] = [
                'first' => $paginator->url(1),
                'last' => method_exists($paginator, 'lastPage') ? $paginator->url($paginator->lastPage()) : null,
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ];
        } elseif ($pagination && is_array($pagination)) {
            $response['pagination'] = $pagination;
        }

        if (isset($options['meta']) && is_array($options['meta'])) {
            $response['meta'] = array_merge($response['meta'] ?? [], $options['meta']);
        }

        if (isset($options['links']) && is_array($options['links'])) {
            $response['links'] = array_merge($response['links'] ?? [], $options['links']);
        }

        return response()->json($response, $code);
    }
}
