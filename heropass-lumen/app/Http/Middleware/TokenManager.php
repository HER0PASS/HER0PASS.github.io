<?php

namespace App\Http\Middleware;

use App\Http\Controllers\TokenController;
use App\Http\Controllers\TokenValidator;
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
    public function checkUser($email, $api_key): ?string
    {
        return $this->dataBaseRepository->checkUserExistence($email, $api_key);
    }
    public function getToken($userId): JsonResponse
    {
        $token =  $this->dataBaseRepository->getTokenFromDataBase($userId);
        $expires_at = $this->dataBaseRepository->getExpireDate($token);
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
        if ($this->dataBaseRepository->getTokenFromDataBase($userId) === null) {
            $this->dataBaseRepository->registerTokenInDatabase($token, $expires_at, $userId);
        } else {
            $this->dataBaseRepository->updateTokenInDatabase($token, $expires_at, $userId);
        }
    }

    public function tokenIsActive($token): bool
    {
        $expires_at = $this->dataBaseRepository->getExpireDate($token);

        if (!$expires_at) {
            return false;
        }

        return strtotime($expires_at) > time();
    }
}
