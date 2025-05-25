<?php

namespace Tests\Fakes;

use App\Interfaces\TwitchApiRepositoryInterface;
use App\Models\TwitchGetTopsofthetops;
use App\Models\TwitchUser;

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
                'game_name' => 'Just Chatting',
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
                'game_name' => 'League of Legends',
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
                'game_name' => 'Fortnite',
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
}
