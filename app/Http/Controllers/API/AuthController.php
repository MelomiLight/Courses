<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->service->register($request);
        } catch (\Exception $e) {
            return response()->json(['error' => __('messages.register.fail')], 500);
        }

        return response()->json([
            'message' => __('messages.register.success'),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $response = $this->service->login($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => __('messages.login.success'),
            'data' => new UserResource($response['user']),
            'access_token' => $response['access_token'],
            'token_type' => $response['token_type'],
            'expires_at' => $response['expires_at'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->token()->revoke();
        } catch (\Exception $e) {
            return response()->json(['error' => __('messages.logout.fail')], 500);
        }
        return response()->json([
            'message' => __('messages.logout.success')
        ]);
    }
}
