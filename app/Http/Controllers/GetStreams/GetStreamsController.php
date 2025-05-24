<?php

namespace App\Http\Controllers\GetStreams;

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

    public function getStreams(Request $request): JsonResponse
    {
        try {
            // Obtener datos de los streams
            return $this->getStreamsService->getStreamsData();
        } catch (\Exception $e) {
            return response()->json([
                "error"   => "Internal Server Error.",
                "message" => $e->getMessage(),
                "trace"   => $e->getTraceAsString(),
            ], 500);
        }
    }
}
