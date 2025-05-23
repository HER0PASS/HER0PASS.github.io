<?php

namespace App\Interfaces;

use App\Models\TwitchUser;

interface TwitchApiRepositoryInterface
{
    public function getTwitchUserById(string $userId): ?TwitchUser;
}
