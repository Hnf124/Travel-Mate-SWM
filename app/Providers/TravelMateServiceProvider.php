<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\TravelMateServiceInterface;
use App\Services\TravelMateService;

class TravelMateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Binding interface ke implementasi
        $this->app->bind(TravelMateServiceInterface::class, TravelMateService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}