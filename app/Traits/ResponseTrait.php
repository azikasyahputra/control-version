<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Return a standard success response.
     *
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public function success($data, int $statusCode = 200): JsonResponse
    {
        return response()->json($data, $statusCode);
    }

    /**
     * Return a standard error response.
     *
     * @param array $errors
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error(array $errors, int $statusCode): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'errors' => $errors,
        ], $statusCode);
    }
}