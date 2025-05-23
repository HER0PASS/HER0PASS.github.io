<?php

namespace Tests\Fakes;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISessions;
use App\Models\APIUser;
use App\Models\TwitchUser;

class FakeDataBaseRepository implements DataBaseRepositoryInterface
{
    private array $fakeTwitchUsers = [
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
        foreach ($this->fakeTwitchUsers as $data) {
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

    private array $fakeAPIUsers = [
        [
            'id' => '1',
            'email' => 'user1@example.com',
            'api_key' => '6288f213b19339919569e8b43f1ad852'
        ],
        [
            'id' => '2',
            'email' => 'user2@example.com',
            'api_key' => 'bab8ea158c16e2741c1b7ec1ec14febc'
        ]
    ];

    public function getAPIUserByEmail($email): ?APIUser
    {
        foreach ($this->fakeAPIUsers as $userData) {
            if ($userData['email'] === $email) {
                return APIUser::fromArray($userData);
            }
        }
        return null;
    }

    public function checkAPIUserExistence(APIUser $apiUser): ?APIUser
    {
        // TODO: Implement checkAPIUserExistence() method.
    }

    public function updateAPIUserAPIKey(APIUser $apiUser): void
    {
        foreach ($this->fakeAPIUsers as &$userData) {
            if ($userData['email'] === $apiUser->getEmail()) {
                $userData['api_key'] = $apiUser->getApiKey();
                return;
            }
        }
    }

    public function registerAPIUser(APIUser $apiUser): void
    {
        // TODO: Implement registerAPIUser() method.
    }

    public function getSessionByToken($token): ?APISessions
    {
        // TODO: Implement getSessionByToken() method.
    }

    public function getSessionByUserId($user_id): ?APISessions
    {
        // TODO: Implement getSessionByUserId() method.
    }

    public function registerSession(APISessions $apiSession): void
    {
        // TODO: Implement registerSession() method.
    }

    public function updateSession(APISessions $apiSession): void
    {
        // TODO: Implement updateSession() method.
    }
}
