<?php

namespace Tests\Fakes;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Models\APISessions;
use App\Models\APIUser;
use App\Models\TwitchUser;

class FakeDataBaseRepository implements DataBaseRepositoryInterface
{
    private array $fakeSessions;
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

    public function __construct()
    {
        $this->fakeSessions = [
            new APISessions('2', 'ab7ecdeaa06336505d1781576c805f47', new \DateTime('2025-02-16 16:20:49')),
            new APISessions('4', '41d2562ddc215251d5c6dfd86c44d16b', new \DateTime('2025-04-26 17:12:02')),
        ];
    }

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

    public function getAPIUserByEmail($email, $apiKey): ?APIUser
    {
        foreach ($this->fakeAPIUsers as $userData) {
            if ($userData['email'] === $email) {
                return APIUser::fromArray($userData);
            }
        }
        return null;
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
        if ($token === 'valid_token') {
            return new APISessions('12345', 'valid_token', new \DateTime('+3 days'));
        }

        return null;
    }

    public function getSessionByUserId($user_id): ?APISessions
    {
        foreach ($this->fakeSessions as $session) {
            if ($session->getUserId() === $user_id) {
                return $session;
            }
        }
        return null;
    }

    public function registerSession(APISessions $apiSession): void
    {
        $this->fakeSessions[] = $apiSession;
    }

    public function updateSession(APISessions $apiSession): void
    {
        foreach ($this->fakeSessions as $i => $session) {
            if ($session->getUserId() === $apiSession->getUserId()) {
                $this->fakeSessions[$i] = $apiSession;
                return;
            }
        }
        $this->fakeSessions[] = $apiSession;
    }

    public function storeUser(APIUser $user): void
    {
        $this->fakeAPIUsers[] = $user->toArray();
    }
}
