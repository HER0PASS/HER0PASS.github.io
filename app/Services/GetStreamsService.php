<?php

namespace App\Services;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Http\JsonResponse;

class GetStreamsService
{
    private TwitchApiRepositoryInterface $apiRepository;

    public function __construct(TwitchApiRepositoryInterface $apiRepository)
    {
        $this->apiRepository = $apiRepository;
    }
    public function getStreamsData(): JsonResponse
    {
        $streams = $this->apiRepository->getStreams();
        if (!$streams) {
            return response()->json([
                "error" => "Streams not found."
            ], 404);
        }

        return response()->json($streams->toArray(), 200);
    }
}
