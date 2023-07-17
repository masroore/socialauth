<?php

return [

    'domain' => env('APP_DOMAIN', 'localhost'),

    'middleware' => ['web'],

    'auth_middleware' => 'auth',

    'user_model' => \App\Models\User::class,

    // Allow login, and registration if enabled, for users with an email for one of the following domains.
    // All domains allowed by default
    // Only use lower case
    'allowed_domains' => [],

    'profile_photo_disk' => 'public',

    'providers' => [
        'github' => [
            'label' => 'GitHub',
            'icon' => 'fa-github',
            'scopes' => [],
        ],
    ],

    'features' => [
        'registration',
        'profile_photo',
        'xxx',
    ],

    'paths' => [
        // Specify the route name for the login page
        'login' => 'login',
    ],

    'redirects' => [
        // Specify the default redirect route for successful logins
        'login' => 'home',
    ],

];
