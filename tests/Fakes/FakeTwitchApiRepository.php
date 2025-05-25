<?php

namespace Tests\Fakes;

use App\Interfaces\TwitchApiRepositoryInterface;
use App\Models\Stream;
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

    public function getStreams(): ?array
    {
        $stream1 = new Stream('Analizando la nueva plantilla del FC Barcelona', 'messi');
        $stream2 = new Stream('Jugando al Fortnite con amigos', 'miketroke');
        $stream3 = new Stream('Las aventuras del peiro', 'dembele');
        return [$stream1, $stream2, $stream3];
    }

    public function getEnrichedStreams(string $limit): ?array
    {
        $streams = [
            [
                "stream_id" => "987654321",
                "user_id" => "12345",
                "user_name" => "ninja",
                "viewer_count" => 34567,
                "title" => "Epic Gaming Session",
                "user_display_name" => "Ninja",
                "profile_image_url" => "https://example.com/ninja.jpg"
            ],
            [
                "stream_id" => "123456789",
                "user_id" => "22222",
                "user_name" => "pokimane",
                "viewer_count" => 28900,
                "title" => "Let’s Go!",
                "user_display_name" => "Pokimane",
                "profile_image_url" => "https://example.com/pokimane.jpg"
            ],
            [
                "stream_id" => "555555555",
                "user_id" => "33333",
                "user_name" => "shroud",
                "viewer_count" => 15000,
                "title" => "Playing with viewers",
                "user_display_name" => "Shroud",
                "profile_image_url" => "https://example.com/shroud.jpg"
            ]
        ];

        return array_slice($streams, 0, (int)$limit);
    }
}
