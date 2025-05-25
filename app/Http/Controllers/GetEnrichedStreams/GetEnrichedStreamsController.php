<?php

namespace App\Http\Controllers\GetEnrichedStreams;

use App\Services\GetEnrichedStreamsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetEnrichedStreamsController extends BaseController
{
    private GetEnrichedStreamsValidator $getEnrichedStreamsValidator;
    private GetEnrichedStreamsService $getEnrichedStreamsService;


    public function __construct(
        GetEnrichedStreamsValidator $getEnrichedStreamsValidator,
        GetEnrichedStreamsService $getEnrichedStreamsService
    ) {
        $this->getEnrichedStreamsValidator = $getEnrichedStreamsValidator;
        $this->getEnrichedStreamsService = $getEnrichedStreamsService;
    }


    public function getEnrichedStream(Request $request): JsonResponse
    {
        try {
            $validation = $this->getEnrichedStreamsValidator->validateRequest($request);

            if (!$validation['isValid']) {
                return response()->json(["error" => $validation['error']], $validation['status']);
            }

            return $this->getEnrichedStreamsService->getEnrichedStreamsData($validation['limit']);
        } catch (\Exception $e) {
            return response()->json([
                "error"   => "Internal Server Error",
                "message" => $e->getMessage(),
                "trace"   => $e->getTraceAsString(),
            ], 500);
        }
    }
}
