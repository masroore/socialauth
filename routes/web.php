<?php

use Masroore\SocialAuth\Http\Controllers\SocialAuthController;
use Masroore\SocialAuth\SocialAuth;

Route::domain(get_config('domain', 'localhost'))
    ->middleware(get_config('middleware', ['web']))
    ->name(SocialAuth::PACKAGE_NAME . '.')
    ->group(static function (): void {
        Route::get('/oauth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
            ->name('redirect');

        Route::get('/oauth/{provider}/callback', [SocialAuthController::class, 'processCallback'])
            ->name('callback');
    });
