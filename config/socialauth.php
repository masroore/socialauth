<?php

use Masroore\SocialAuth\Enums\Feature;

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
        'avatar_url' => 'avatar_url',
        'email_verified_at' => 'email_verified_at',
    ],

    // Allow login, and registration if enabled, for users with an email for one of the following domains.
    // All domains allowed by default
    // Only use lower case
    'domains' => [
        'allowed' => [],
        'banned' => [],
    ],

    'profile_photo_disk' => 'public',

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
