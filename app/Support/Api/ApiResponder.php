<?php

namespace App\Support\Api;

use App\Constants\ApiStatusCode;
use Illuminate\Http\JsonResponse;

class ApiResponder
{
    public static function success(
        mixed $data = null,
        string $message = 'Request completed successfully.',
        int $status = ApiStatusCode::OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    public static function error(
        string $message,
        mixed $errors = null,
        int $status = ApiStatusCode::BAD_REQUEST
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }
}
