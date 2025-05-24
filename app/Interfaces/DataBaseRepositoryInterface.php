<?php

namespace App\Interfaces;

use App\Models\APISession;
use App\Models\APIUser;
use App\Models\TwitchUser;

interface DataBaseRepositoryInterface
{
    public function getTwitchUserById(string $userId): ?TwitchUser;

    public function saveTwitchUser(TwitchUser $user): void;

    public function getAPIUserByEmail(APIUser $apiApiUser): ?APIUser;

    public function updateAPIUserAPIKey(APIUser $apiUser): void;

    public function registerAPIUser(APIUser $apiUser): void;

    public function getSessionByToken($token): ?APISession;

    public function getSessionByUserId($user_id): ?APISession;

    public function registerSession(APISession $apiSession): void;

    public function updateSession(APISession $apiSession): void;
}
