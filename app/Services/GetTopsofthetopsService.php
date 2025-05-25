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
        $now = new \DateTime();
        $timeStamp = $this->dbRepository->getTimestampCache();

        // Si no hay timestamp o si los datos son muy antiguos, ir a la API
        if (!$timeStamp || $timeStamp->diff($now)->days >= 10 && $timeStamp->diff($now)->s >= $since) {
            if (isset($credentials['error'])) {
                return response()->json([
                    "error" => "Internal server error."
                ], 500);
            }

            // Guardamos los tops obtenidos desde la API
            $tops = $this->apiRepository->getTopsofthetops();
            $i = 1;
            foreach ($tops as $top) {
                $this->dbRepository->saveTopsofthetops($top, $i);
                $i++;
            }

            return response()->json(array_map(fn ($top) => $top->toArray(), $tops), 200);
        }

        // Si la caché es reciente, usar los datos guardados
        $tops = [];
        for ($i = 1; $i <= 3; $i++) {
            $top = $this->dbRepository->getTopsofthetops($i);
            if ($top) {
                $tops[] = $top;
            }
        }

        return response()->json(array_map(fn ($top) => $top->toArray(), $tops), 200);
    }
}
