<?php

use Masroore\SocialAuth\Http\Controllers\SocialAuthController;
use Masroore\SocialAuth\SocialAuth;

Route::middleware(get_config('middleware', ['web']))
    // ->domain(get_config('domain', 'localhost'))
    ->name(SocialAuth::PACKAGE_NAME . '.')
    ->group(static function (): void {
        Route::get('/oauth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
            ->name('redirect');

        Route::get('/oauth/{provider}/callback', [SocialAuthController::class, 'processCallback'])
            ->name('callback');
    });
