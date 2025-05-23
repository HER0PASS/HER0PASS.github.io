<?php

namespace App\Services;

use App\Repository\DataBaseRepository;
use Illuminate\Http\JsonResponse;
use App\Models\APIUser;

class RegisterService
{
    public function __construct(
        private DataBaseRepository $dataBaseRepository,
    ) {
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
