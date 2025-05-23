<?php

namespace App\Interfaces;

use App\Models\APISessions;
use App\Models\APIUser;
use App\Models\TwitchUser;

interface DataBaseRepositoryInterface
{
    public function getTwitchUserById(string $userId): ?TwitchUser;

    public function saveTwitchUser(TwitchUser $user): void;

    public function getAPIUserByEmail(string $email, string $apiKey): ?APIUser;

    public function updateAPIUserAPIKey(APIUser $apiUser): void;

    public function registerAPIUser(APIUser $apiUser): void;

    public function getSessionByToken($token): ?APISessions;

    public function getSessionByUserId($user_id): ?APISessions;

    public function registerSession(APISessions $apiSession): void;

    public function updateSession(APISessions $apiSession): void;
}
