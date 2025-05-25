<?php

namespace App\Repository;

use App\Exceptions\TwitchApiException;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Models\EnrichedStream;
use App\Models\Stream;
use App\Models\TwitchGetTopsofthetops;
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
        $rawStreams = $this->getStreamsJson(10);
        if (!$rawStreams) {
            return null;
        }

        $streams = [];
        foreach ($rawStreams as $streamData) {
            $streams[] = new Stream(
                $streamData["title"] ?? '',
                $streamData["user_name"] ?? ''
            );
        }

        return $streams;
    }

    public function getEnrichedStreams(string $limit): ?array
    {
        $streams_raw = $this->getStreamsJson($limit);
        if (!$streams_raw) {
            return null;
        }

        usort($streams_raw, fn ($streamA, $streamB) => $streamB['viewer_count'] <=> $streamA['viewer_count']);

        $user_ids = array_column($streams_raw, 'user_id');
        $user_ids_str = implode('&id=', $user_ids);

        [$client_id, $access_token] = $this->getCredentials();
        $users_api_url = "https://api.twitch.tv/helix/users?id=" . $user_ids_str;

        [$users_response, $users_code] = $this->getApiResponse($client_id, $access_token, $users_api_url);

        if ($users_code !== 200) {
            return null;
        }

        $users_data = json_decode($users_response, true);
        if (!isset($users_data['data'])) {
            return null;
        }

        $user_map = EnrichedStream::buildUserMap($users_data['data']);
        return EnrichedStream::enrichStreams($streams_raw, $user_map);
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

    private function getCredentials(): array
    {
        return [
            $this->credentials['client_id'],
            $this->credentials['access_token'],
        ];
    }

    private function getStreamsJson(string $limit): ?array
    {
        [$client_id, $access_token] = $this->getCredentials();

        $api_url = "https://api.twitch.tv/helix/streams?first=$limit";

        [$response, $http_code] = $this->getApiResponse($client_id, $access_token, $api_url);

        if ($http_code === 401) {
            throw new TwitchApiException();
        }

        $data = json_decode($response, true);

        if ($http_code !== 200 || !$response || !isset($data["data"])) {
            return null;
        }

        return $data["data"];
    }
    public function getTopsofthetops(): array
    {
        $client_id = $this->credentials['client_id'];
        $access_token = $this->credentials['access_token'];

        $headers = [
            "Client-ID: $client_id",
            "Authorization: Bearer $access_token"
        ];

        // Obtener los 3 juegos más populares
        $games = $this->getTopGames($headers, 3);
        $tops = [];

        foreach ($games as $game) {
            $topUserData = $this->getTopUserForGame($game['id'], $game['name'], $headers);
            if ($topUserData) {
                $tops[] = TwitchGetTopsofthetops::fromArray($topUserData);
            }
        }

        return $tops;
    }

    private function getTopGames(array $headers, int $count): array
    {
        $url = "https://api.twitch.tv/helix/games/top?first=$count";

        $response = $this->makeRequest($url, $headers);

        return $response['data'] ?? [];
    }
    private function makeRequest(string $url, array $headers): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        return json_decode($response, true);
    }

    private function getTopUserForGame(string $gameId, string $gameName, array $headers): ?array
    {
        $url = "https://api.twitch.tv/helix/videos?game_id=$gameId&sort=views&first=40";
        $response = $this->makeRequest($url, $headers);
        $videos = $response['data'] ?? [];

        if (empty($videos)) {
            return null;
        }

        $usuarios = [];
        foreach ($videos as $video) {
            $user = $video['user_name'];
            if (!isset($usuarios[$user])) {
                $usuarios[$user] = [
                    'total_videos' => 0,
                    'total_views' => 0,
                    'most_viewed' => $video
                ];
            }

            $usuarios[$user]['total_videos']++;
            $usuarios[$user]['total_views'] += $video['view_count'];

            if ($video['view_count'] > $usuarios[$user]['most_viewed']['view_count']) {
                $usuarios[$user]['most_viewed'] = $video;
            }
        }

        $topUser = array_reduce(array_keys($usuarios), function ($carry, $user) use ($usuarios) {
            if (!$carry || $usuarios[$user]['most_viewed']['view_count'] > $usuarios[$carry]['most_viewed']['view_count']) {
                return $user;
            }
            return $carry;
        });

        return [
            'game_id' => $gameId,
            'game_name' => $gameName,
            'user_name' => $topUser,
            'total_videos' => $usuarios[$topUser]['total_videos'],
            'total_views' => $usuarios[$topUser]['total_views'],
            'most_viewed_title' => $usuarios[$topUser]['most_viewed']['title'],
            'most_viewed_views' => $usuarios[$topUser]['most_viewed']['view_count'],
            'most_viewed_duration' => $usuarios[$topUser]['most_viewed']['duration'],
            'most_viewed_created_at' => $usuarios[$topUser]['most_viewed']['created_at']
        ];
    }
}
