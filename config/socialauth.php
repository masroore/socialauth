<?php

use Masroore\SocialAuth\Support\Feature;

return [

    'domain' => env('APP_DOMAIN', 'localhost'),

    'middleware' => ['web'],

    'auth_middleware' => 'auth',

    'user_model' => \App\Models\User::class,

    'users_table' => 'users',

    'columns' => [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'profile_photo_path' => 'profile_photo_path',
        'email_verified_at' => 'email_verified_at',
    ],

    // Allow login, and registration if enabled, for users with an email for one of the following domains.
    // All domains allowed by default
    // Only use lower case
    'domains' => [
        'allowed' => [],
        'banned' => [],
    ],

    'profile_photo' => [
        'disk' => 'public',
        'dimensions' => 180,
        'quality' => 70,
        'color' => 'fff',
        'background' => '0D8ABC',
        'length' => 2,
    ],

    'providers' => [
        'github' => [
            'label' => 'GitHub',
            'icon' => 'fa-github',
        ],
    ],

    'features' => [
        Feature::Registration->value,
        Feature::GenerateMissingEmails->value,
        Feature::MarkEmailVerified->value,
        Feature::CreateAccountOnFirstLogin->value,
        Feature::LoginOnRegistration->value,
        Feature::ProfilePhoto->value,
        Feature::ResizeProfilePhoto->value,
        Feature::UpdateProfile->value,
        Feature::RefreshOauthTokens->value,
        Feature::RememberSession->value,
    ],

    'routes' => [
        // Specify the route name for the login page
        'login' => 'login',
        'register' => 'register',
    ],

    'redirects' => [
        // Specify the default redirect route for successful logins
        'login' => 'home',
    ],

];
