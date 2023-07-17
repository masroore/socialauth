<?php

namespace Masroore\SocialAuth\Services;

use Masroore\SocialAuth\SocialAuth;

final class Features
{
    public static function generateMissingEmails(): bool
    {
        return self::enabled('generate-missing-emails');
    }

    /**
     * Determine if the given feature is enabled.
     */
    public static function enabled(string $feature): bool
    {
        return in_array($feature, config(SocialAuth::PACKAGE_NAME . '.features', []));
    }

    public static function registration(): bool
    {
        return self::enabled('registration');
    }

    public static function rememberSession(): bool
    {
        return self::enabled('remember-session');
    }

    public static function updateProfile(): bool
    {
        return self::enabled('update-profile');
    }

    public static function auth_2fa(): bool
    {
        return self::enabled('auth-2fa');
    }

    public static function emailVerification(): bool
    {
        return self::enabled('email-verification');
    }

    public static function profilePhoto(): bool
    {
        return self::enabled('profile-photo');
    }

    public static function createAccountOnFirstLogin(): bool
    {
        return self::enabled('create-account-on-first-login');
    }

    public static function loginOnRegistration(): bool
    {
        return self::enabled('login-on-registration');
    }

    public static function refreshOauthTokens(): bool
    {
        return self::enabled('refresh-oauth-tokens');
    }
}
