<?php

namespace App\Http\Controllers\GetStreams;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetStreamsController extends BaseController
{
    private $validator;

    public function __construct(GetStreamsValidator $validator)
    {
        $this->validator = $validator;
    }

    public function index(Request $request)
    {
        // Verificar token
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
        }

        $token = $matches[1];
        $user_id = $this->verificarToken($token);
        if (!$user_id) {
            return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
        }

        // Validar parámetros
        $isValidationOk = $this->validator->validate();
        if (!$isValidationOk) {
            return response()->json(["error" => "Parámetros inválidos."], 400);
        }

        // Obtener credenciales de Twitch
        $credentials = $this->obtenerToken();

        if (isset($credentials['error'])) {
            return response()->json(["error" => "Failed to obtain access token", "details" => $credentials], 500);
        }

        // Obtener datos de los streams desde la API de Twitch
        $result = $this->getStreamsDataFromApi($credentials);
        if (isset($result['error'])) {
            return response()->json($result, $result['status'] ?? 500);
        }

        return response()->json($result, 200);
    }

    protected function verificarToken($token)
    {
        require_once base_path('public/endpoints/verificarToken.php');
        return verificarToken($token);
    }

    protected function obtenerToken()
    {
        require_once base_path('public/endpoints/api/crearToken.php');
        return obtenerToken();
    }

    protected function getStreamsDataFromApi($credentials)
    {
        $client_id = $credentials['client_id'];
        $access_token = $credentials['access_token'];

        $api_url = "https://api.twitch.tv/helix/streams?first=10";

        $headers = [
            "Client-ID: $client_id",
            "Authorization: Bearer $access_token"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $data = json_decode($response, true);

            if (!isset($data["data"])) {
                return ["error" => "No se encontraron streams", "status" => 404];
            }

            $streams_filtrados = array_map(function ($stream) {
                return [
                    "title" => $stream["title"],
                    "user_name" => $stream["user_name"],
                ];
            }, $data["data"]);

            return $streams_filtrados;
        } elseif ($http_code == 401) {
            return ["error" => "RESPONSE 401: Unauthorized. Twitch access token is invalid or has expired.", "status" => 401];
        } elseif ($http_code == 500) {
            return ["error" => "RESPONSE 500: Internal Server Error", "status" => 500];
        } else {
            return ["error" => "Unexpected error", "status" => $http_code];
        }
    }
}
