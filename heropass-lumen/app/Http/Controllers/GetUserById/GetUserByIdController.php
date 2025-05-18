<?php

namespace App\Http\Controllers\GetUserById;

use App\Services\GetUserByIdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetUserByIdController extends BaseController
{
    private $validator;
    private $service;

    public function __construct(GetUserByIdValidator $validator, GetUserByIdService $service)
    {
        $this->validator = $validator;
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        // 1. Verificación del token (cláusula de guarda)
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
        }

        $token = $matches[1];
        $userId = $this->service->verificarToken($token);
        if (!$userId) {
            return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
        }

        // 2. Validación de parámetros (cláusula de guarda)
        $isValidationOk = $this->validator->validate($request->input('id'));
        if (!$isValidationOk) {
            return response()->json(["error" => "Invalid or missing 'id' parameter."], 400);
        }

        // 3. Lógica de negocio (happy path)
        $twitchUserId = $request->input('id');
        return $this->service->getUserData($twitchUserId);
    }
}
