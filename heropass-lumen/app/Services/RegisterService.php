<?php

namespace App\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APIUser;

class RegisterService
{
    private DataBaseRepositoryInterface $dataBaseRepository;

    public function __construct(DataBaseRepositoryInterface $dataBaseRepository)
    {
        $this->dataBaseRepository = $dataBaseRepository;
    }
    public function registerUser(APIUser $user): APIUser
    {
        $existing = $this->dataBaseRepository->getAPIUserByEmail($user->getEmail());

        $user->generateApiKey();

        if ($existing) {
            $this->dataBaseRepository->updateAPIUserAPIKey($user);
        } else {
            $this->dataBaseRepository->registerAPIUser($user);
        }

        return $user;
    }
}
