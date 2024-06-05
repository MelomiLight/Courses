<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = DB::transaction(function () use ($validatedData) {
            return User::create($validatedData);
        });

        event(new Registered($user));

        return $user;
    }

    /**
     * @throws Exception
     */
    public function login(LoginRequest $request): array
    {
        $credentials = $request->only('username', 'iin', 'email', 'password');
        foreach (['username', 'iin', 'email'] as $field) {
            if (isset($credentials[$field])) {
                $attempt = Auth::attempt([$field => $credentials[$field], 'password' => $credentials['password']]);
                if ($attempt) {
                    break;
                }
            }
        }

        $user = $request->user();

        if (!$user->hasVerifiedEmail()) {
            throw new Exception('Email not verified.');
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $token->save();

        return [
            'user' => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at->toDateTimeString()
        ];
    }
}
