<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Services\CacheService;
use App\Services\CoreService;
use App\Services\MultiTenancyService;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MultiTenancyService::class, function ($app) {
            return new MultiTenancyService();
        });

        $this->app->singleton(CoreService::class, function ($app) {
            return new CoreService();
        });

        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
