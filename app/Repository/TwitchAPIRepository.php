<?php

namespace App\Repository;

use App\Exceptions\TwitchApiException;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Models\Stream;
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
        [$client_id, $access_token] = $this->getCredentials();

        $api_url = "https://api.twitch.tv/helix/users?id=$userId";

        [$response, $http_code] = $this->getApiResponse($client_id, $access_token, $api_url);

        $data = json_decode($response, true);

        if ($http_code === 401) {
            throw new TwitchApiException();
        }

        if ($http_code !== 200 || !$response || !isset($data["data"][0])) {
            return null;
        }

        return TwitchUser::fromArray($data["data"][0]);
    }

    public function getStreams(): ?array
    {
        [$client_id, $access_token] = $this->getCredentials();

        $api_url = "https://api.twitch.tv/helix/streams?first=10";

        [$response, $http_code] = $this->getApiResponse($client_id, $access_token, $api_url);

        if ($http_code === 401) {
            throw new TwitchApiException();
        }

        $data = json_decode($response, true);

        if ($http_code !== 200 || !$response || !isset($data["data"])) {
            return null;
        }

        $streams = [];
        foreach ($data["data"] as $streamData) {
            $streams[] = new Stream(
                $streamData["title"] ?? '',
                $streamData["user_name"] ?? ''
            );
        }

        return $streams;
    }

    public function getApiResponse(mixed $client_id, mixed $access_token, string $api_url): array
    {
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
        return [$response, $http_code];
    }

    public function getCredentials(): array
    {
        return [
            $this->credentials['client_id'],
            $this->credentials['access_token'],
        ];
    }
}
