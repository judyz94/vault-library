<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Authentication management
 *
 * APIs for managing user authentication
 */
class AuthController extends Controller
{
    use ApiResponse;

    /**
     * User login
     *
     * Authenticate a user and issue an access token for API usage.
     *
     * @bodyParam email string required The user's email address. Example: johndoe@example.com
     * @bodyParam password string required The user's password. Example: secret123
     * @throws ValidationException
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success(
            ['user' => $user, 'token' => $token],
            'Login successful.'
        );
    }

    /**
     * User logout
     *
     * Revoke the authenticated user's current access token.
     *
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'Logged out successfully.');
    }
}
