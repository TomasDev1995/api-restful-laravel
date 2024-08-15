<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Devuelve una respuesta JSON de éxito.
     *
     * @param string $message
     * @param mixed $resource
     * @param int $statusCode
     * @return JsonResponse
     */
    public function successResponse(string $message, $resource = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'code' => $statusCode,
            'resource' => $resource
        ], $statusCode);
    }

    /**
     * Devuelve una respuesta JSON de error.
     *
     * @param string $error
     * @param string|null $details
     * @param int $statusCode
     * @return JsonResponse
     */
    public function errorResponse(string $error, string $details = null, int $statusCode = 422): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'details' => $details
        ], $statusCode);
    }
}
