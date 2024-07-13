<?php

namespace App\Providers;

use App\Interfaces\GifInterfaceService;
use App\Interfaces\LoginInterface;
use App\Services\GiphyService;
use App\Services\PassportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginInterface::class, PassportService::class);

        $this->app->singleton(GifInterfaceService::class, GiphyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::personalAccessTokensExpireIn(Carbon::now()->addMinutes(30));
    }
}
