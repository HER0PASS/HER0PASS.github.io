<?php

namespace App\Http\Middleware;

use App\Http\Controllers\TokenController;
use App\Http\Controllers\TokenValidator;
use App\Models\APISessions;
use App\Repository\DataBaseRepository;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Js;
use PHPUnit\Util\Json;

class TokenManager
{
    private DataBaseRepository $dataBaseRepository;
    public function __construct(DataBaseRepository $dataBaseRepository)
    {
        $this->dataBaseRepository = $dataBaseRepository;
    }

    public function checkUser($email, $api_key): \App\Models\APIUser
    {
        return $this->dataBaseRepository->checkAPIUserExistence($email, $api_key);
    }

    public function getToken($user_id): JsonResponse
    {
        $session = $this->dataBaseReposistory->getSessionByUserId($user_id);
        $token = $session->getToken();
        $expires_at = $session->getExpireDate($token);
        return new JsonResponse(['token' => $token, 'expires_at' => $expires_at]);
    }

    public function generateToken(): JsonResponse
    {
        $token = bin2hex(random_bytes(16));
        $expires_at = date('Y-m-d H:i:s', time() + (3 * 24 * 60 * 60));

        return new JsonResponse(['token' => $token, 'expires_at' => $expires_at]);
    }

    public function updateToken($token, $expires_at, $userId): void
    {
        if ($this->dataBaseRepository->getSessionByUserId($userId) === null) {
            $this->dataBaseRepository->registerSession($token, $expires_at, $userId);
        } else {
            $this->dataBaseRepository->updateSession($token, $expires_at, $userId);
        }
    }

    public function tokenIsActive($token): bool
    {
        $expires_at = $this->dataBaseRepository->getSessionByToken($token)->getExpiresAt();

        return $expires_at->getTimestamp() > time();
    }
}
