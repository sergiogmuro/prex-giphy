<?php

namespace App\Http\Controllers;

use App\Interfaces\LoginInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private LoginInterface $loginService;

    public function __construct(LoginInterface $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            $login = $this->loginService->login(email: $credentials['email'], password: $credentials['password']);

            return response()->json([
                'id' => $login['user_id'],
                'token' => $login['token'],
                'expires_at' => $login['expires_at']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
