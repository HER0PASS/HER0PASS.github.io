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

        $response = $this->tokenManager->getToken($userId);

        $data = json_decode($response->getContent(), true);

        $token = $data['token'] ?? null;
        $expires_at = $data['expires_at'] ?? null;

        if ($token === null || $expires_at < date('Y-m-d H:i:s')) {
            $response = $this->tokenManager->generateToken();

            $data = json_decode($response->getContent(), true);

            $token = $data['token'] ?? null;
            $expires_at = $data['expires_at'] ?? null;

            $this->tokenManager->updateToken($token, $expires_at, $userId);
        }

        return new JsonResponse(['token' => $token]);
    }
}
