<?php

namespace App\Http\Middleware;

use App\Http\Controllers\TokenController;
use App\Http\Controllers\TokenValidator;
use App\Repository\DataBaseRepository;
use App\Services\TokenService;

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
}
