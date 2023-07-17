<?php

use Masroore\SocialAuth\Http\Controllers\SocialAuthController;

Route::domain(config('socialauth.domain'))
    ->middleware(config('socialauth.middleware', ['web']))
    ->name('socialauth.')
    ->group(static function (): void {
        Route::get('/oauth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
            ->name('redirect');

        Route::get('/oauth/{provider}/callback', [SocialAuthController::class, 'processCallback'])
            ->name('callback');
    });
