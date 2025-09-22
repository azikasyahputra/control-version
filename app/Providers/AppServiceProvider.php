<?php

namespace App\Providers;

use App\Interfaces\ObjectRepositoryInterface;
use App\Repositories\ObjectRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ObjectRepositoryInterface::class, ObjectRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
