<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Website;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Website::updated(function ($site) {
            $site->updateUserSiteCount();
        });
        Website::saved(function ($site) {
            $site->updateUserSiteCount();
        });
        Website::deleted(function ($site) {
            $site->updateUserSiteCount();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
