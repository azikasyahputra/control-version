<?php

namespace App\Providers;

use App\Interfaces\VersionRepositoryInterface;
use App\Repositories\VersionRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(VersionRepositoryInterface::class, VersionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
