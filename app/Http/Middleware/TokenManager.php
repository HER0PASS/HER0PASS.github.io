<?php

namespace App\Http\Middleware;

use App\Interfaces\DataBaseRepositoryInterface;

class TokenManager
{
    public function __construct(private DataBaseRepositoryInterface $dataBaseRepository)
    {
    }

    public function tokenIsActive($token): bool
    {
        $session = $this->dataBaseRepository->getSessionByToken($token);
        return $session && $session->getExpiresAt()->getTimestamp() > time();
    }
}
