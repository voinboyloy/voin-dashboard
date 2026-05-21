<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NotionSyncService;

class NotionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NotionSyncService::class, function ($app) {
            return new NotionSyncService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
