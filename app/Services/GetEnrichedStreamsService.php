<?php

namespace App\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetEnrichedStreamsService
{
    private TwitchApiRepositoryInterface $apiRepository;

    public function __construct(TwitchApiRepositoryInterface $apiRepository)
    {
        $this->apiRepository = $apiRepository;
    }
    public function getEnrichedStreamsData(): JsonResponse
    {
        $streams = $this->apiRepository->getStreams();
        if (!$streams) {
            return response()->json([
                "error" => "Streams not found."
            ], 404);
        }

        return response()->json(array_map(fn ($s) => $s->toArray(), $streams), 200);
    }
}
