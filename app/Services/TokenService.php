<?php

namespace App\Services;

use App\Http\Middleware\TokenManager;
use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISession;
use App\Models\APIUser;
use Illuminate\Http\JsonResponse;

class TokenService
{
    public function __construct(private DataBaseRepositoryInterface $dataBaseRepository)
    {
    }

    public function createToken($email, $api_key): ?APISession
    {
        $user = new APIUser(null, $email, $api_key);

        $validUser = $this->dataBaseRepository->getAPIUserByEmail($user);

        if (!$validUser) {
            return null;
        }

        $session = $this->dataBaseRepository->getSessionByUserId($validUser->getId());

        if (!$session) {
            $session = new APISession($validUser->getId());
            $session->generateToken();
            $this->dataBaseRepository->registerSession($session);
        } elseif ($session->isExpired()) {
            $session->generateToken();
            $this->dataBaseRepository->updateSession($session);
        }

        return $session;
    }
}
