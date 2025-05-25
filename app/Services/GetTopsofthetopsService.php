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
        $diff = $timeStamp->diff($now);

        if ($diff->days < 10 || $diff->s < $since) {
            $tops = [];
            for ($i = 1; $i <= 3; $i++) {
                $tops[] = $this->dbRepository->getTopsofthetopsData($i);
            }

            return response()->json(array_map(fn ($top) => $top->toArray(), $tops), 200);
        }

        // Si no lo encontramos, consultamos a la API
        $credentials = $this->obtenerToken();
        if (isset($credentials['error'])) {
            return response()->json([
                "error" => "Internal server error."
            ], 500);
        }

        // Guardamos el usuario en la base de datos
        $tops = $this->apiRepository->getTopsofthetops();
        $i = 1;
        foreach ($tops as $top) {
            // Asegúrate de que sea instancia de TwitchTopsofthetops. Si no, usa TwitchTopsofthetops::fromArray($top)
            $this->dbRepository->saveTopsofthetops($top, $i);
            $i++;
        }

        return response()->json(array_map(fn ($top) => $top->toArray(), $tops), 200);
    }

    // SOLUCIONAR ESTO: GESTIONAR TOKEN DE CONSULTAS A LA API DE TWITCH
    public function obtenerToken(): array
    {
        require_once __DIR__ . '/../../public/endpoints/api/crearToken.php';
        return obtenerToken();
    }
}
