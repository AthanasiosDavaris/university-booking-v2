<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Authenticate the user and return the Sanctum token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Attempt to auth with the given email and password
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->load(['role', 'department'])->append('name');

        // Create Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Revoke the user's current token
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the token that was used to auth the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }

    /**
     * Get the current auth user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'department'])->append('name');

        return response()->json($user);
    }
}
