<?php

namespace App\Modules\Bridesmaid\Providers;

use Caffeinated\Modules\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/Lang', 'bridesmaid');
        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'bridesmaid');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations', 'bridesmaid');
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
