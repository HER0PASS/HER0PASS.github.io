<?php

namespace App\Interfaces;

interface TwitchApiRepositoryInterface
{
    public function getUserByDisplayName(string $name): ?User;
}
