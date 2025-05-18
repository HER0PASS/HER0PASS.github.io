<?php

namespace App\Services;

use App\Repository\DataBaseRepository;
use Illuminate\Http\JsonResponse;

class RegisterService
{
    public function __construct(
        private DataBaseRepository $dataBaseRepository,
    ) {
        $this->dataBaseRepository = new DataBaseRepository();
    }
    public function registerUser($email): JsonResponse
    {
        $api_key = bin2hex(random_bytes(16));
        $user = $this->dataBaseRepository->getUserByEmail($email);
        if ($user) {
            $this->dataBaseRepository->updateApiKey($email, $api_key);
        } else {
            $this->dataBaseRepository->registerEmailAndApiKey($email, $api_key);
        }
        return new JsonResponse(['api_key' => $api_key], 200);
    }
}
