<?php

namespace App\Providers;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Repository\DataBaseRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DataBaseRepositoryInterface::class, DataBaseRepository::class);
    }
}
