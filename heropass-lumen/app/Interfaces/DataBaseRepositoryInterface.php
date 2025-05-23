<?php

namespace App\Interfaces;

use App\Models\TwitchUser;

interface DataBaseRepositoryInterface
{
    public function getUserById(string $userId): ?TwitchUser;
    public function saveUser(TwitchUser $user): void;
}
