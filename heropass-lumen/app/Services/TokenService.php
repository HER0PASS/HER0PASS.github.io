<?php

namespace App\Services;

use App\Http\Controllers\TokenValidator;
use App\Http\Middleware\TokenManager;
use App\Repository\DataBaseRepository;
use Illuminate\Http\JsonResponse;

class TokenService
{
    private TokenManager $tokenManager;

    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function createToken($email, $api_key): JsonResponse
    {
        $userId = $this->tokenManager->checkUser($email, $api_key);
        [$token, $expires_at] = $this->tokenManager->getToken($userId);
        if ($token === null | $expires_at < time()) {
            [$token , $expires_at] = $this->tokenManager->generateToken();
            $this->updateToken($token, $userId);
        }

        return new JsonResponse(['userId' => $userId]);
    }
}
