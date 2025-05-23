<?php

namespace App\Providers;

use App\Interfaces\DataBaseRepositoryInterface;
use App\Repository\DataBaseDBRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DataBaseRepositoryInterface::class, DataBaseDBRepository::class);
    }
}
