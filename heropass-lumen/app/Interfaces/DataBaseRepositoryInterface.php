<?php

namespace App\Interfaces;

use App\Models\TwitchUser;

interface DataBaseRepositoryInterface
{
    public function getTwitchUserById(string $userId): ?TwitchUser;
    public function saveTwitchUser(TwitchUser $user): void;
}
