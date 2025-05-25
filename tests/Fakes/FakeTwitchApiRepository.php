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
        // TODO: Implement getEnrichedStreams() method.
    }
}
