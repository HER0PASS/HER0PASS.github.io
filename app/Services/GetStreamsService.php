<?php

namespace App\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetStreamsService
{
    private DataBaseRepositoryInterface $dbRepository;
    private TwitchApiRepositoryInterface $apiRepository;

    public function __construct(DataBaseRepositoryInterface $dbRepository, TwitchApiRepositoryInterface $apiRepository)
    {
        $this->dbRepository = $dbRepository;
        $this->apiRepository = $apiRepository;
    }    
      public function getStreamsData(): JsonResponse
    {
        try {
            // Obtener credenciales de Twitch
            $credentials = $this->obtenerToken();
            if (isset($credentials['error'])) {
                return response()->json([
                    "error" => "Internal Server Error"
                ], 500);
            }

            // Establecer las credenciales para el repositorio de la API
            $this->apiRepository = new \App\Repository\TwitchAPIRepository($credentials);
            
            // Obtener datos de los streams desde el repositorio
            $streams = $this->apiRepository->getStreams();
            
            if (!$streams) {
                return response()->json([
                    "error" => "Unauthorized. Twitch access token is invalid or has expired."
                ], 401);
            }

            return response()->json($streams, 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Internal Server Error"
            ], 500);
        }
    }

    // SOLUCIONAR ESTO: GESTIONAR TOKEN DE CONSULTAS A LA API DE TWITCH
    public function obtenerToken(): array
    {
        require_once __DIR__ . '/../../public/endpoints/api/crearToken.php';
        return obtenerToken();
    }
}
