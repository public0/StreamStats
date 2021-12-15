<?php

namespace App\Providers;

use App\Domain\Services\Twitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Twitch::class, function($app) {
            return new Twitch();
        });
    }
}
