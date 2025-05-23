<?php

namespace App\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Repository\DataBaseRepository;
use App\Repository\TwitchAPIRepository;
use Illuminate\Http\JsonResponse;

class GetUserByIdService
{
    private DataBaseRepositoryInterface $dbRepository;
    private ?TwitchAPIRepository $apiRepository = null;

    public function __construct(DataBaseRepositoryInterface $dbRepository)
    {
        $this->dbRepository = $dbRepository;
    }

    public function getUserData(string $userId): JsonResponse
    {
        // Primero buscamos en la base de datos
        $user = $this->dbRepository->getTwitchUserById($userId);
        if ($user) {
            return response()->json($user->toArray(), 200);
        }

        // Si no lo encontramos, consultamos a la API
        $credentials = $this->obtenerToken();
        if (isset($credentials['error'])) {
            return response()->json([
                "error" => "Internal server error."
            ], 500);
        }

        // Creamos el repositorio de la API con las credenciales obtenidas
        $this->apiRepository = new TwitchAPIRepository($credentials);

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

    // SOLUCIONAR ESTO: GESTIONAR TOKEN DE CONSULTAS A LA API DE TWITCH
    public function obtenerToken(): array
    {
        require_once __DIR__ . '/../../public/endpoints/api/crearToken.php';
        return obtenerToken();
    }
}
