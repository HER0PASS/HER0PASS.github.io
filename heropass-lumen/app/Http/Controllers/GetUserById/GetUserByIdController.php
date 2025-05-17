<?php

namespace App\Http\Controllers\GetUserById;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GetUserByIdController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        // Verificar token
        $headers = getallheaders();
        if (
            !isset($headers['Authorization']) || !preg_match(
                '/Bearer\s(\S+)/',
                $headers['Authorization'],
                $matches
            )
        ) {
            return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
        }

        $token = $matches[1];
        $user_id = $this->verificarToken($token);
        if (!$user_id) {
            return response()->json(["error" => "Unauthorized. Twitch access token is invalid or has expired."], 401);
        }

        // Validar parámetro id
        if (empty($request->input('id'))) {
            return response()->json(["error" => "Invalid or missing 'id' parameter."], 400);
        }

        $user_id = $request->input('id');

        // Obtener credenciales de Twitch
        $credentials = $this->obtenerToken();

        if (isset($credentials['error'])) {
            return response()->json(["error" => "Failed to obtain access token", "details" => $credentials], 500);
        }

        $userData = $this->getUserDataFromDB($user_id);
        if ($userData) {
            return response()->json(json_decode($userData), 200);
        }

        // Si no existe en la base de datos, obtener desde la API de Twitch
        $result = $this->getUserDataFromApi($user_id, $credentials);
        if (isset($result['error'])) {
            return response()->json($result, $result['status'] ?? 500);
        }

        // Guardar los datos en la base de datos
        $this->saveUserDataToDB($result);

        return response()->json($result, 200);
    }

    private function verificarToken($token)
    {
        require_once base_path('public/endpoints/verificarToken.php');
        return verificarToken($token);
    }

    private function obtenerToken()
    {
        require_once base_path('public/endpoints/api/crearToken.php');
        return obtenerToken();
    }

    private function getUserDataFromDB($userId)
    {
        require_once base_path('public/endpoints/bbdd/conexion.php');
        global $conn;

        $sql = "SELECT * FROM TwitchUsers WHERE idUser = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $conn->close();
            return $user_data["data"];
        }

        return null;
    }

    private function getUserDataFromApi($userId, $credentials)
    {
        $client_id = $credentials['client_id'];
        $access_token = $credentials['access_token'];

        $api_url = "https://api.twitch.tv/helix/users?id=$userId";

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
            if (!isset($data["data"][0])) {
                return ["error" => "User not found.", "status" => 404];
            }

            $streamer = $data["data"][0];
            return [
                "id" => $streamer["id"],
                "login" => $streamer["login"],
                "display_name" => $streamer["display_name"],
                "type" => $streamer["type"],
                "broadcaster_type" => $streamer["broadcaster_type"],
                "description" => $streamer["description"],
                "profile_image_url" => $streamer["profile_image_url"],
                "offline_image_url" => $streamer["offline_image_url"],
                "view_count" => $streamer["view_count"],
                "created_at" => $streamer["created_at"]
            ];
        } elseif ($http_code == 400) {
            return ["error" => "RESPONSE 400: Invalid or missing 'id' parameter.", "status" => 400];
        } elseif ($http_code == 401) {
            return ["error" => "RESPONSE 401: Unauthorized. Twitch access token is invalid or has expired.", "status" => 401];
        } elseif ($http_code == 404) {
            return ["error" => "RESPONSE 404: User not found.", "status" => 404];
        } elseif ($http_code == 500) {
            return ["error" => "RESPONSE 500: Internal Server Error", "status" => 500];
        } else {
            return ["error" => "Unexpected error", "status" => $http_code];
        }
    }

    private function saveUserDataToDB($userData)
    {
        require_once base_path('public/endpoints/bbdd/conexion.php');
        global $conn;

        $data_json = json_encode($userData);
        $insert_sql = "INSERT INTO TwitchUsers (idUser, data) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ss", $userData["id"], $data_json);
        $insert_stmt->execute();
        $conn->close();
    }
}
