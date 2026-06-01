<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\LogoutRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Authenticate user credentials and return Sanctum access token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->authenticate($request->validated());

        if (! $result) {
            return api_error('Invalid credentials.', [
                'auth' => ['The provided credentials are incorrect.'],
            ], 401);
        }

        return api_success($result, 'Login successful.');
    }

    /**
     * Revoke current user Sanctum access token.
     */
    public function logout(LogoutRequest $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return api_success(null, 'Logout successful.');
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return api_success($request->user(), 'Authenticated user details.');
    }
}
