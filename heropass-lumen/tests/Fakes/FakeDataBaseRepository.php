<?php

namespace Tests\Fakes;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\TwitchUser;

class FakeDataBaseRepository implements DataBaseRepositoryInterface
{
    private array $fakeUsers = [
        [
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
        ],
        [
            'id' => '67890',
            'login' => 'pokimane',
            'display_name' => 'Pokimane',
            'type' => '',
            'broadcaster_type' => 'partner',
            'description' => 'Variety Streamer & Content Creator',
            'profile_image_url' => 'https://example.com/pokimane.jpg',
            'offline_image_url' => 'https://example.com/pokimane-offline.jpg',
            'view_count' => 400000,
            'created_at' => '2013-03-15T00:00:00Z'
        ]
    ];

    public function getTwitchUserById(string $id): ?TwitchUser
    {
        foreach ($this->fakeUsers as $data) {
            if ($data['id'] === $id) {
                return TwitchUser::fromArray($data);
            }
        }
        return null;
    }

    public function saveTwitchUser(TwitchUser $user): void
    {
        // TODO: Implement saveUser() method.
    }
}
