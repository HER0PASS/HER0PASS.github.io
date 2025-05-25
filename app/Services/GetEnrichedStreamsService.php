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

    public function getEnrichedStreamsData(string $limit): JsonResponse
    {
        $streams = $this->apiRepository->getEnrichedStreams($limit);
        if (!$streams) {
            return response()->json([
                "error" => "Enriched Streams not found."
            ], 404);
        }

        return response()->json($streams, 200);
    }
}
