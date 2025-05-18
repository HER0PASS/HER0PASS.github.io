<?php

namespace App\Repository;

use App\Models\TwitchUser;

interface UserRepositoryInterface
{
    public function getUserById(string $userId): ?TwitchUser;
    public function saveUser(TwitchUser $user): void;
}
