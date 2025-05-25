<?php

namespace App\Interfaces;

use App\Models\TwitchGetTopsofthetops;
use App\Models\TwitchUser;

interface TwitchApiRepositoryInterface
{
    public function getTwitchUserById(string $userId): ?TwitchUser;
    public function getTopsofthetops(): array;
    public function getStreams(): ?array;
    public function getEnrichedStreams(string $limit): ?array;
}
