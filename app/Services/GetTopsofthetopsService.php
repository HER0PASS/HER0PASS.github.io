<?php

namespace App\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetTopsofthetopsService
{
    private DataBaseRepositoryInterface $dbRepository;
    private TwitchApiRepositoryInterface $apiRepository;

    public function __construct(DataBaseRepositoryInterface $dbRepository, TwitchApiRepositoryInterface $apiRepository)
    {
        $this->dbRepository = $dbRepository;
        $this->apiRepository = $apiRepository;
    }

    public function getTopsofthetopsData(int $since): JsonResponse
    {
        // Primero buscamos en la base de datos
        $now = new \DateTime();
        $timeStamp = $this->dbRepository->getTimestampCache();
        if ($timeStamp->diff($now)->days < 10 | $timeStamp->diff($now)->seconds < $since) {
            $top1 = $this->dbRepository->getTopsofthetopsData(1);
            $top2 = $this->dbRepository->getTopsofthetopsData(2);
            $top3 = $this->dbRepository->getTopsofthetopsData(3);

            $tops = [$top1, $top2, $top3];

            return response()->json(array_map(fn ($top) => $top->toArray(), $tops), 200);
        }

        // Si no lo encontramos, consultamos a la API
        $credentials = $this->obtenerToken();
        if (isset($credentials['error'])) {
            return response()->json([
                "error" => "Internal server error."
            ], 500);
        }

        // Obtenemos el usuario desde la API
        $top1 = $this->apiRepository->getTopsofthetops(1);
        $top2 = $this->apiRepository->getTopsofthetops(2);
        $top3 = $this->apiRepository->getTopsofthetops(3);
        $tops = [$top1, $top2, $top3];

        // Guardamos el usuario en la base de datos
        $this->dbRepository->saveTopsofthetops($tops);

        return response()->json(array_map(fn ($top) => $top->toArray(), $tops), 200);
    }

    // SOLUCIONAR ESTO: GESTIONAR TOKEN DE CONSULTAS A LA API DE TWITCH
    public function obtenerToken(): array
    {
        require_once __DIR__ . '/../../public/endpoints/api/crearToken.php';
        return obtenerToken();
    }
}
