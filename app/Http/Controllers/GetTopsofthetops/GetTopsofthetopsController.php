<?php

namespace App\Http\Controllers\GetTopsofthetops;

use App\Http\Controllers\GetTopsofthetops\GetTopsofthetopsValidator;
use App\Services\GetTopsofthetopsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetTopsofthetopsController extends BaseController
{
    private GetTopsofthetopsValidator $getTopsofthetopsValidator;
    private GetTopsofthetopsService $getTopsofthetopsService;

    public function __construct(GetTopsofthetopsValidator $getTopsofthetopsValidator, GetTopsofthetopsService $getTopsofthetopsService)
    {
        $this->getTopsofthetopsValidator = $getTopsofthetopsValidator;
        $this->getTopsofthetopsService = $getTopsofthetopsService;
    }

    public function getTopsofthetops(Request $request): JsonResponse
    {
        try {
            // Validar la solicitud (incluye validación de since  y token)
            $validation = $this->getTopsofthetopsValidator->validateRequest($request);

            if (!$validation['isValid']) {
                return response()->json(["error" => $validation['error']], $validation['status']);
            }

            // Obtener datos del usuario
            return $this->getTopsofthetopsService->getTopsofthetopsData($validation['id']);
        } catch (\Exception $e) {
            return response()->json([
                "error"   => "Internal Server Error",
                "message" => $e->getMessage(),
                "trace"   => $e->getTraceAsString(),
            ], 500);
        }
    }
}
