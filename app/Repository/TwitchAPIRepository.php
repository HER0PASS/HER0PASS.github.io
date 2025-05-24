<?php

namespace App\Repository;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Models\TwitchUser;

class TwitchAPIRepository implements TwitchApiRepositoryInterface
{
    private array $credentials;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    public function getTwitchUserById(string $userId): ?TwitchUser
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

        if ($http_code !== 200 || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!isset($data["data"][0])) {
            return null;
        }

        return TwitchUser::fromArray($data["data"][0]);
    }

    public function getStreams(): ?array
    {
        $client_id = $this->credentials['client_id'];
        $access_token = $this->credentials['access_token'];

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

        if ($http_code !== 200 || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!isset($data["data"])) {
            return null;
        }

        $streams_filtrados = array_map(function ($stream) {
            return [
                "title" => $stream["title"],
                "user_name" => $stream["user_name"],
            ];
        }, $data["data"]);

        return $streams_filtrados;
    }
}
