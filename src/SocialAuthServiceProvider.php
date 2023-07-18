<?php

namespace Masroore\SocialAuth;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SocialAuthServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name(SocialAuth::PACKAGE_NAME)
            ->hasConfigFile()
            // ->hasTranslations()
            // ->hasViews()
            // ->hasRoute('web')
            ->hasMigration('create_social_accounts_table');
    }

    /**
     * Register the application services.
     */
    public function packageRegistered(): void
    {
        // Automatically apply the package configuration
        // $this->mergeConfigFrom(__DIR__ . '/../config/socialauth.php', SocialAuth::PACKAGE_NAME);

        // Register the main class to use with the facade
        $this->app->singleton(SocialAuth::PACKAGE_NAME, fn () => new SocialAuth());
        $this->app->bind(SocialAuth::PACKAGE_NAME, SocialAuth::class);
    }
}
