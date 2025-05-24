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
        // Primero buscamos en la base de datos
        $streams = $this->dbRepository->getStreams();
        if ($streams) {
            return response()->json($streams, 200);
        }

        // Si no lo encontramos, consultamos a la API
        $streams = $this->apiRepository->getStreams();
        if (!$streams) {
            return response()->json([
                "error" => "Streams not found."
            ], 404);
        }

        return response()->json($streams, 200);
    }

    // SOLUCIONAR ESTO: GESTIONAR TOKEN DE CONSULTAS A LA API DE TWITCH
    public function obtenerToken(): array
    {
        require_once __DIR__ . '/../../public/endpoints/api/crearToken.php';
        return obtenerToken();
    }
}
