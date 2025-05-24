<?php

namespace App\Http\Controllers\GetStreams;

use App\Exceptions\TwitchApiException;
use App\Services\GetStreamsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetStreamsController extends BaseController
{
    private GetStreamsService $getStreamsService;

    public function __construct(GetStreamsService $getStreamsService)
    {
        $this->getStreamsService = $getStreamsService;
    }

    public function getStreams(): JsonResponse
    {
        try {
            return $this->getStreamsService->getStreamsData();
        } catch (TwitchApiException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Internal Server Error.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
