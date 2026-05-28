<?php

namespace App\Support\Api;

use App\Constants\ApiMessage;
use App\Constants\ApiStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiExceptionRenderer
{
    public static function validation(ValidationException $exception): JsonResponse
    {
        return ApiResponder::error(
            ApiMessage::VALIDATION_FAILED,
            $exception->errors(),
            ApiStatusCode::UNPROCESSABLE_ENTITY
        );
    }

    public static function unauthenticated(): JsonResponse
    {
        return ApiResponder::error(
            ApiMessage::UNAUTHENTICATED,
            ['auth' => ['Authentication required.']],
            ApiStatusCode::UNAUTHORIZED
        );
    }

    public static function notFound(): JsonResponse
    {
        return ApiResponder::error(
            ApiMessage::NOT_FOUND,
            ['resource' => ['The requested endpoint was not found.']],
            ApiStatusCode::NOT_FOUND
        );
    }

    public static function serverError(): JsonResponse
    {
        return ApiResponder::error(
            ApiMessage::SERVER_ERROR,
            ['server' => ['An unexpected error occurred.']],
            ApiStatusCode::INTERNAL_SERVER_ERROR
        );
    }

    public static function shouldRenderApi(Request $request): bool
    {
        return $request->is('api/*');
    }
}
