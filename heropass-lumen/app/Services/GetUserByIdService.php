<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class GetUserByIdService
{
    /**
     * Verifica si el token proporcionado es válido
     * 
     * @param string $token Token a verificar
     * @return string|bool ID del usuario si el token es válido, false en caso contrario
     */
    public function verificarToken(string $token)
    {
        require_once base_path('public/endpoints/verificarToken.php');
        return verificarToken($token);
    }

    /**
     * Obtiene los datos de un usuario por su ID
     * 
     * @param string $userId ID del usuario
     * @return JsonResponse Respuesta JSON con los datos del usuario
     */
    public function getUserData(string $userId): JsonResponse
    {
        // 1. Intentar obtener datos de la base de datos
        $userData = $this->getUserDataFromDB($userId);
        if ($userData) {
            return response()->json(json_decode($userData), 200);
        }

        // 2. Si no existen en la base de datos, obtener de la API
        $credentials = $this->obtenerToken();
        if (isset($credentials['error'])) {
            return response()->json(["error" => "Failed to obtain access token", "details" => $credentials], 500);
        }

        $result = $this->getUserDataFromApi($userId, $credentials);
        if (isset($result['error'])) {
            return response()->json($result, $result['status'] ?? 500);
        }

        // 3. Guardar los datos en la base de datos
        $this->saveUserDataToDB($result);

        return response()->json($result, 200);
    }

    /**
     * Obtiene un token de acceso para la API de Twitch
     * 
     * @return array Credenciales de acceso a la API
     */
    private function obtenerToken(): array
    {
        require_once base_path('public/endpoints/api/crearToken.php');
        return obtenerToken();
    }

    /**
     * Obtiene los datos de un usuario desde la base de datos
     * 
     * @param string $userId ID del usuario
     * @return string|null Datos del usuario en formato JSON o null si no existe
     */
    private function getUserDataFromDB(string $userId): ?string
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

    /**
     * Obtiene los datos de un usuario desde la API de Twitch
     * 
     * @param string $userId ID del usuario
     * @param array $credentials Credenciales de acceso a la API
     * @return array Datos del usuario o error
     */
    private function getUserDataFromApi(string $userId, array $credentials): array
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
        }

        // Manejar diferentes códigos de error HTTP
        return match ($http_code) {
            400 => ["error" => "Invalid or missing 'id' parameter.", "status" => 400],
            401 => ["error" => "Unauthorized. Twitch access token is invalid or has expired.", "status" => 401],
            404 => ["error" => "User not found.", "status" => 404],
            500 => ["error" => "Internal Server Error", "status" => 500],
            default => ["error" => "Unexpected error", "status" => $http_code]
        };
    }

    /**
     * Guarda los datos de un usuario en la base de datos
     * 
     * @param array $userData Datos del usuario
     * @return void
     */
    private function saveUserDataToDB(array $userData): void
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
