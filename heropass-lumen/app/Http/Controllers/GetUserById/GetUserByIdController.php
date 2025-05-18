<?php

namespace App\Http\Controllers\GetUserById;

use App\Services\GetUserByIdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetUserByIdController extends BaseController
{
    private GetUserByIdValidator $getUserByIdValidator;
    private GetUserByIdService $getUserByIdService;

    public function __construct(GetUserByIdValidator $getUserByIdValidator, GetUserByIdService $getUserByIdService)
    {
        $this->getUserByIdValidator = $getUserByIdValidator;
        $this->getUserByIdService = $getUserByIdService;
    }

    public function getUser(Request $request): JsonResponse
    {
        try {
            // Validar la solicitud (incluye validación de ID y token)
            $validation = $this->getUserByIdValidator->validateRequest($request);

            if (!$validation['isValid']) {
                return response()->json(["error" => $validation['error']], $validation['status']);
            }

            // Verificar token
            $user_id = $this->getUserByIdValidator->verificarToken($validation['token']);
            if ($user_id === false) {
                return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
            }

            // Obtener datos del usuario
            return $this->getUserByIdService->getUserData($validation['id']);
        } catch (\Exception $e) {
            return response()->json([
                "error"   => "Internal Server Error",
                "message" => $e->getMessage(),
                "trace"   => $e->getTraceAsString(),
            ], 500);
        }
    }
}
