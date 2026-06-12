<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! defined('LINKHUB_COMM_MODE')) {
            define('LINKHUB_COMM_MODE', 'CURL');
        }

        $this->app->singleton(\App\Services\PopbillService::class);
        $this->app->singleton(\App\Services\AligoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\FloorRoom::observe(\App\Observers\FloorRoomObserver::class);
    }
}
