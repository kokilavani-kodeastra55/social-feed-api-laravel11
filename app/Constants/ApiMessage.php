<?php

namespace App\Constants;

class ApiMessage
{
    public const SUCCESS = 'Request completed successfully.';
    public const VALIDATION_FAILED = 'Validation failed.';
    public const UNAUTHENTICATED = 'Unauthenticated.';
    public const FORBIDDEN = 'You are not allowed to perform this action.';
    public const NOT_FOUND = 'Resource not found.';
    public const SERVER_ERROR = 'Something went wrong.';
}
