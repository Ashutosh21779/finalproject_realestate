<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RecommendationService;
use App\Services\CollaborativeFilteringService;
use App\Services\ContentBasedService;

class RecommendationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the collaborative filtering service
        $this->app->singleton(CollaborativeFilteringService::class, function ($app) {
            return new CollaborativeFilteringService();
        });

        // Register the content-based service
        $this->app->singleton(ContentBasedService::class, function ($app) {
            return new ContentBasedService();
        });

        // Register the main recommendation service
        $this->app->singleton(RecommendationService::class, function ($app) {
            return new RecommendationService(
                $app->make(CollaborativeFilteringService::class),
                $app->make(ContentBasedService::class)
            );
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
