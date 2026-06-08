<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\LogoutRequest;
use App\Services\AuthService;
use App\Helpers\HTTPResponse;
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
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->authenticate($request->validated());

        if (! $result) {
            return HTTPResponse::unauthorized('Invalid credentials.', [
                'auth' => ['The provided credentials are incorrect.'],
            ]);
        }

        return HTTPResponse::ok($result, 'Login successful.');
    }

    /**
     * Revoke current user Sanctum access token.
     *
     * @param LogoutRequest $request
     * @return JsonResponse
     */
    public function logout(LogoutRequest $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return HTTPResponse::ok(null, 'Logout successful.');
    }

    /**
     * Get authenticated user profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return HTTPResponse::ok($request->user(), 'Authenticated user details.');
    }
}
