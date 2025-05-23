<?php

namespace App\Services;

use App\Http\Middleware\TokenManager;
use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISessions;
use App\Models\APIUser;
use Illuminate\Http\JsonResponse;

class TokenService
{
    public function __construct(private DataBaseRepositoryInterface $dataBaseRepository)
    {
    }

    public function createToken($email, $api_key): ?APISessions
    {
        $user = new APIUser(null, $email, $api_key);

        $validUser = $this->dataBaseRepository->getAPIUserByEmail($email, $api_key);

        if (!$validUser) {
            return null;
        }

        $session = $this->dataBaseRepository->getSessionByUserId($validUser->getId());

        if (!$session) {
            $session = new APISessions($validUser->getId());
            $session->generateToken();
            $this->dataBaseRepository->registerSession($session);
        } else {
            $session->generateToken();
            $this->dataBaseRepository->updateSession($session);
        }

        return $session;
    }
}
