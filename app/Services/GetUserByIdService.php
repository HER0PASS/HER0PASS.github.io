<?php

namespace App\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetUserByIdService
{
    private DataBaseRepositoryInterface $dbRepository;
    private TwitchApiRepositoryInterface $apiRepository;

    public function __construct(DataBaseRepositoryInterface $dbRepository, TwitchApiRepositoryInterface $apiRepository)
    {
        $this->dbRepository = $dbRepository;
        $this->apiRepository = $apiRepository;
    }

    public function getUserData(string $userId): JsonResponse
    {
        // Primero buscamos en la base de datos
        $user = $this->dbRepository->getTwitchUserById($userId);
        if ($user) {
            return response()->json($user->toArray(), 200);
        }

        // Obtenemos el usuario desde la API
        $user = $this->apiRepository->getTwitchUserById($userId);
        if (!$user) {
            return response()->json([
                "error" => "User not found."
            ], 404);
        }

        // Guardamos el usuario en la base de datos
        $this->dbRepository->saveTwitchUser($user);

        return response()->json($user->toArray(), 200);
    }
}
