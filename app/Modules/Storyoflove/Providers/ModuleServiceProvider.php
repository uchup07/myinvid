<?php

namespace App\Modules\Storyoflove\Providers;

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
        $this->loadTranslationsFrom(__DIR__.'/../Resources/Lang', 'storyoflove');
        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'storyoflove');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations', 'storyoflove');
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
