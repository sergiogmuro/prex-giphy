<?php

namespace App\Services;

use App\Interfaces\LoginInterface;
use Exception;
use Illuminate\Support\Facades\Auth;

class PassportService implements LoginInterface
{
    public function login(string $email, string $password): array
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            $token = $user->createToken('prex_access_token');

            return [
                'user_id' => $user->id,
                'token' => $token->accessToken,
                'expires_at' => $token->token->expires_at
            ];
        } else {
            throw new Exception('Unauthorized', 401);
        }
    }
}
