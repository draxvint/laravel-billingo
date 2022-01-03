<?php

namespace Polynar\Billingo;

use Illuminate\Support\ServiceProvider;
use function config_path;

class BillingoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/billingo.php' => config_path('billingo.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/billingo.php', 'billingo');

        // Register the main class to use with the facade
        $this->app->singleton('billingo', function () {
            return new Billingo;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Billingo::class];
    }
}
