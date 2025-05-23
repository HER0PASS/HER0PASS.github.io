<?php

namespace App\Http\Middleware;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISessions;
use App\Models\APIUser;
use Illuminate\Http\JsonResponse;

class TokenManager
{
    private DataBaseRepositoryInterface $dataBaseRepository;

    public function __construct(DataBaseRepositoryInterface $dataBaseRepository)
    {
        $this->dataBaseRepository = $dataBaseRepository;
    }

    public function checkUser($email, $api_key): APIUser
    {
        $apiUser = new APIUser("null", $email, $api_key);
        return $this->dataBaseRepository->checkAPIUserExistence($apiUser);
    }

    public function getToken($user_id): JsonResponse
    {
        $session = $this->dataBaseRepository->getSessionByUserId($user_id);
        $token = $session->getToken();
        $expires_at = $session->getExpiresAt()->format('Y-m-d H:i:s');

        return new JsonResponse([
            'token' => $token,
            'expires_at' => $expires_at,
        ]);
    }

    public function generateToken(): JsonResponse
    {
        $token = bin2hex(random_bytes(16));
        $expires_at = (new \DateTime('+3 days'))->format('Y-m-d H:i:s');

        return new JsonResponse(['token' => $token, 'expires_at' => $expires_at]);
    }

    public function updateToken($token, $expires_at, $userId): void
    {
        $session = new APISessions($userId, $token, $expires_at);

        if ($this->dataBaseRepository->getSessionByUserId($userId) === null) {
            $this->dataBaseRepository->registerSession($session);
        } else {
            $this->dataBaseRepository->updateSession($session);
        }
    }

    public function tokenIsActive($token): bool
    {
        $session = $this->dataBaseRepository->getSessionByToken($token);
        return $session && $session->getExpiresAt()->getTimestamp() > time();
    }
}
