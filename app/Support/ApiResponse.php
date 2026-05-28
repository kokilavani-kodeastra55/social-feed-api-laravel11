<?php

namespace App\Support;

use App\Support\Api\ApiResponder;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return ApiResponder::success($data, $message, $status);
    }

    public static function error(string $message, mixed $errors = null, int $status = 400): JsonResponse
    {
        return ApiResponder::error($message, $errors, $status);
    }
}
