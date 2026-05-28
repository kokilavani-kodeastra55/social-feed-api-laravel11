<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\Api\ApiResponder;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Request completed successfully.',
        int $status = 200
    ): JsonResponse {
        return ApiResponder::success($data, $message, $status);
    }

    protected function errorResponse(
        string $message,
        mixed $errors = null,
        int $status = 400
    ): JsonResponse {
        return ApiResponder::error($message, $errors, $status);
    }
}
