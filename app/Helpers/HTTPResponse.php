<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HTTPResponse
{
    /**
     * Return a standardized success JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public static function success(mixed $data = null, string $message = 'Request completed successfully.', int $status = Response::HTTP_OK): JsonResponse
    {
        return api_success($data, $message, $status);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $status
     * @return JsonResponse
     */
    public static function error(string $message, mixed $errors = null, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return api_error($message, $errors, $status);
    }

    /**
     * Return a 200 OK success response.
     */
    public static function ok(mixed $data = null, string $message = 'Success'): JsonResponse
    {
        return self::success($data, $message, Response::HTTP_OK);
    }

    /**
     * Return a 201 Created success response.
     */
    public static function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return self::success($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Return a 204 No Content success response.
     */
    public static function noContent(string $message = 'No Content'): JsonResponse
    {
        return self::success(null, $message, Response::HTTP_NO_CONTENT);
    }

    /**
     * Return a 400 Bad Request error response.
     */
    public static function badRequest(string $message = 'Bad Request', mixed $errors = null): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Return a 401 Unauthorized error response.
     */
    public static function unauthorized(string $message = 'Unauthorized', mixed $errors = null): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Return a 403 Forbidden error response.
     */
    public static function forbidden(string $message = 'Forbidden', mixed $errors = null): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_FORBIDDEN);
    }

    /**
     * Return a 404 Not Found error response.
     */
    public static function notFound(string $message = 'Not Found', mixed $errors = null): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_NOT_FOUND);
    }

    /**
     * Return a 422 Unprocessable Entity validation error response.
     */
    public static function unprocessable(mixed $errors = null, string $message = 'Validation errors occurred'): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Return a 500 Internal Server Error response.
     */
    public static function internalServerError(string $message = 'Internal Server Error', mixed $errors = null): JsonResponse
    {
        return self::error($message, $errors, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

