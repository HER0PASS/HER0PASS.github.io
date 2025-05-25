<?php

namespace App\Providers;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Repository\DataBaseRepository;
use App\Repository\TwitchAPIRepository;
use App\Services\TwitchCredentialManager;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DataBaseRepositoryInterface::class, DataBaseRepository::class);
        $this->app->bind(TwitchApiRepositoryInterface::class, function ($app) {
            $credentials = $app->make(TwitchCredentialManager::class)->getCredentials();
            return new TwitchAPIRepository($credentials);
        });
    }
}
