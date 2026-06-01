<?php

use Illuminate\Http\JsonResponse;

if (! function_exists('api_success')) {
    /**
     * Return a standardized success JSON response.
     */
    function api_success(mixed $data = null, string $message = 'Request completed successfully.', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $status);
    }
}

if (! function_exists('api_error')) {
    /**
     * Return a standardized error JSON response.
     */
    function api_error(string $message, mixed $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }
}
