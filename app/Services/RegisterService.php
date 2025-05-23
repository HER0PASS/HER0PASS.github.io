<?php

namespace App\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APIUser;

class RegisterService
{
    public function __construct(private DataBaseRepositoryInterface $dataBaseRepository)
    {
    }
    public function registerUser(APIUser $user): APIUser
    {
        $existing = $this->dataBaseRepository->getAPIUserByEmail($user->getEmail(), $user->getApiKey());

        $user->generateApiKey();

        if ($existing) {
            $this->dataBaseRepository->updateAPIUserAPIKey($user);
        } else {
            $this->dataBaseRepository->registerAPIUser($user);
        }

        return $user;
    }
}
