<?php

namespace App\Providers;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Repository\DataBaseRepository;
use App\Repository\TwitchAPIRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DataBaseRepositoryInterface::class, DataBaseRepository::class);
        $this->app->bind(TwitchApiRepositoryInterface::class, function () {
            require_once base_path('public/endpoints/api/crearToken.php');
            $credentials = obtenerToken();

            return new TwitchAPIRepository($credentials);
        });
    }
}
