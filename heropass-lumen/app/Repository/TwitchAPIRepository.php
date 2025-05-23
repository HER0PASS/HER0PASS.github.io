<?php

namespace App\Repository;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Models\TwitchUser;

class DataBaseApiRepository implements TwitchApiRepositoryInterface
{
    private array $credentials;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    public function getTwitchUserById(string $userId): ?TwitchUser
    {
        $userData = $this->getUserDataFromApi($userId);
        if (isset($userData['error'])) {
            return null;
        }

        return TwitchUser::fromArray($userData);
    }


    private function getUserDataFromApi(string $userId): array
    {
        $client_id = $this->credentials['client_id'];
        $access_token = $this->credentials['access_token'];

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
}
