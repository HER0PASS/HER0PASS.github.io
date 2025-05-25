<?php

namespace Tests\Fakes;

use App\Interfaces\TwitchApiRepositoryInterface;
use App\Models\TwitchEnrichedStream;
use App\Models\TwitchGetTopsofthetops;
use App\Models\TwitchStream; // Asumiendo que esta clase existe
use App\Models\TwitchUser;

// Asumiendo que esta clase existe

class FakeTwitchApiRepository implements TwitchApiRepositoryInterface
{
    public function getTwitchUserById(string $userId): ?TwitchUser
    {
        if ($userId === '12345') {
            return TwitchUser::fromArray([
                'id' => '12345',
                'login' => 'ninja',
                'display_name' => 'Ninja',
                'type' => '',
                'broadcaster_type' => 'partner',
                'description' => 'Professional Gamer and Streamer',
                'profile_image_url' => 'https://example.com/ninja.jpg',
                'offline_image_url' => 'https://example.com/ninja-offline.jpg',
                'view_count' => 500000,
                'created_at' => '2011-11-20T00:00:00Z'
            ]);
        }
        return null;
    }

    public function getTopsofthetops(): array
    {
        return [
            TwitchGetTopsofthetops::fromArray([
                'game_id' => '509658',
                'game_name' => 'Dota',
                'user_name' => 'LCK',
                'total_videos' => 4,
                'total_views' => 1000000000,
                'most_viewed_title' => 'DK vs T1 | 2021 LCK Summer FINALS',
                'most_viewed_views' => 5550000,
                'most_viewed_duration' => '5h52m8s',
                'most_viewed_created_at' => '2021-08-28T12:00:00Z'
            ]),
            TwitchGetTopsofthetops::fromArray([
                'game_id' => '21779',
                'game_name' => 'Elden Ring',
                'user_name' => 'Faker',
                'total_videos' => 7,
                'total_views' => 600000000,
                'most_viewed_title' => 'Faker SoloQ Masterclass',
                'most_viewed_views' => 4300000,
                'most_viewed_duration' => '3h12m5s',
                'most_viewed_created_at' => '2022-04-10T18:30:00Z'
            ]),
            TwitchGetTopsofthetops::fromArray([
                'game_id' => '33214',
                'game_name' => 'PUBG',
                'user_name' => 'Ninja',
                'total_videos' => 10,
                'total_views' => 800000000,
                'most_viewed_title' => 'Victory Royale with Drake',
                'most_viewed_views' => 9000000,
                'most_viewed_duration' => '2h47m32s',
                'most_viewed_created_at' => '2018-03-14T21:00:00Z'
            ])
        ];
    }

    public function getStreams(): array
    {
        // Devuelve un array simulado de streams
        return [
            TwitchStream::fromArray([
                'id' => '1',
                'user_id' => '12345',
                'user_name' => 'Ninja',
                'game_id' => '21779',
                'title' => 'Epic Stream',
                'viewer_count' => 5000,
                'started_at' => '2024-01-01T12:00:00Z'
            ])
        ];
    }

    public function getEnrichedStreams(): array
    {
        // Devuelve un array simulado de streams enriquecidos
        return [
            TwitchEnrichedStream::fromArray([
                'stream_id' => '1',
                'user_name' => 'Ninja',
                'game_name' => 'Elden Ring',
                'title' => 'Epic Stream',
                'viewer_count' => 5000,
                'started_at' => '2024-01-01T12:00:00Z',
                'profile_image_url' => 'https://example.com/ninja.jpg'
            ])
        ];
    }
}
