<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Website;
use App\Team;
use App\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
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

        User::created(function ($user) {
            $user->onCreate();
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton('ItemManager', function ($app) {
            return new \App\ItemTypes\ItemTypes(auth()->check() ? auth()->user() : null);
        });
        $this->app->singleton('EquipManager', function ($app) {
            return new \App\EquipTypes\EquipTypes();
        });
    }
}
