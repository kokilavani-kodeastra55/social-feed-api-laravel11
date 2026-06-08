<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * Authenticate a user and return token details.
     *
     * @param array $credentials
     * @return array|null
     */
    public function authenticate(array $credentials): ?array
    {
        try {
            $user = User::findByEmail($credentials['email']);

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return null;
            }

            $user->tokens()->delete();

            $token = $user->createToken('api-token')->plainTextToken;

            return [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->only(['id', 'name', 'email']),
            ];
        } catch (\Throwable $e) {
            Log::error('AuthService authenticate error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * Log out user by deleting current token.
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        try {
            $user->currentAccessToken()?->delete();
        } catch (\Throwable $e) {
            Log::error('AuthService logout error: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}
