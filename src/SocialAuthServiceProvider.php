<?php

namespace Masroore\SocialAuth;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;

class SocialAuthServiceProvider extends ServiceProvider
{
    public const PACKAGE_NAME = 'socialauth';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::PACKAGE_NAME)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_social_accounts_table');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        // Optional methods to load your package assets
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'socialauth');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'socialauth');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path(self::PACKAGE_NAME . '.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/socialauth'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/socialauth'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/socialauth'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', self::PACKAGE_NAME);

        // Register the main class to use with the facade
        $this->app->singleton(self::PACKAGE_NAME, fn () => new SocialAuth());
    }
}
