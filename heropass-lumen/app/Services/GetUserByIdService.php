<?php

namespace App\Services;

use App\Models\TwitchUser;
use App\Repository\UserApiRepository;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetUserByIdService
{
    private UserRepositoryInterface $dbRepository;
    private ?UserApiRepository $apiRepository = null;

    public function __construct(UserRepositoryInterface $dbRepository)
    {
        $this->dbRepository = $dbRepository;
    }

    public function getUserData(string $userId): JsonResponse
    {
        // Primero buscamos en la base de datos
        $user = $this->dbRepository->getUserById($userId);
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
        $this->apiRepository = new UserApiRepository($credentials);

        // Obtenemos el usuario desde la API
        $user = $this->apiRepository->getUserById($userId);
        if (!$user) {
            return response()->json([
                "error" => "User not found."
            ], 404);
        }

        // Guardamos el usuario en la base de datos
        $this->dbRepository->saveUser($user);

        return response()->json($user->toArray(), 200);
    }

    // SOLUCIONAR ESTO: GESTIONAR TOKEN DE CONSULTAS A LA API DE TWITCH
    private function obtenerToken(): array
    {
        require_once __DIR__ . '/../../public/endpoints/api/crearToken.php';
        return obtenerToken();
    }
}
