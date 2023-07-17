<?php

use DutchCodingCompany\FilamentSocialite\Http\Controllers\SocialiteLoginController;

Route::domain(config('socialauth.domain'))
    ->middleware(config('socialauth.middleware', ['web']))
    ->name('socialauth.')
    ->group(static function (): void {
        Route::get('/oauth/{provider}', [
            SocialiteLoginController::class,
            'redirectToProvider',
        ])
            ->name('redirect');

        Route::get('/oauth/callback/{provider}', [
            SocialiteLoginController::class,
            'processCallback',
        ])
            ->name('callback');
    });
