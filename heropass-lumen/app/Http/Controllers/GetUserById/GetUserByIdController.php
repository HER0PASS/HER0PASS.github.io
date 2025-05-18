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
            // Verificar token
            $authHeader = $request->header('Authorization');
            if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
            }

            $token = $matches[1];
            $user_id = $this->verificarToken($token);

            if ($user_id === false) {
                return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
            }

            // Validar parámetro id - IMPORTANTE: hacer esta validación antes de verificar el token
            $isValidationOk = $this->getUserByIdValidator->validate($request->input('id'));
            if (!$isValidationOk) {
                return response()->json(["error" => "Invalid or missing 'id' parameter."], 400);
            }

            $twitchUserId = $request->input('id');            // Obtener datos del usuario de la base de datos

            return $this->getUserByIdService->getUserData($twitchUserId);

        } catch (\Exception $e) {
            return response()->json([
                "error"   => "Internal Server Error",
                "message" => $e->getMessage(),
                "trace"   => $e->getTraceAsString(),
            ], 500);
        }

    }

    protected function verificarToken($token)
    {
        require_once base_path('public/endpoints/verificarToken.php');
        return verificarToken($token);
    }

}
