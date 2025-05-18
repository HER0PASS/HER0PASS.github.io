<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GetUserByIdService
{

    public function getUserData(string $userId): JsonResponse
    {
        $userData = $this->getUserDataFromDB($userId);
        if ($userData) {
            return response()->json(json_decode($userData), 200);
        }

        $credentials = $this->obtenerToken();
        if (isset($credentials['error'])) {
            return response()->json(["error" => "Failed to obtain access token", "details" => $credentials], 500);
        }

        $result = $this->getUserDataFromApi($userId, $credentials);
        if (isset($result['error'])) {
            return response()->json($result, $result['status'] ?? 500);
        }

        $this->saveUserDataToDB($result);

        return response()->json($result, 200);
    }


    // Más adelante hay que crear el token manager
    private function obtenerToken(): array
    {
        require_once base_path('public/endpoints/api/crearToken.php');
        return obtenerToken();
    }


    // Más adelante hay que crear el repositorio para hacer las peticiones a la bbdd y convertir a modelos
    private function getUserDataFromDB(string $userId): ?string
    {
        $row = DB::table('TwitchUsers')
            ->where('idUser', $userId)
            ->first();

        return $row?->data ?? null;

    }


    // Más adelante hay que hacer un repositorio para peticiones a la api
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


    // Más adelante irá en el repositorio DB
    private function saveUserDataToDB(array $userData): void
    {
        DB::table('TwitchUsers')->insert([
            'idUser' => $userData['id'],
            'data'   => json_encode($userData),
        ]);
    }

}
