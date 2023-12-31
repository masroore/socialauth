<?php

namespace Masroore\SocialAuth\Support;

final class Features
{
    public static function generateMissingEmails(): bool
    {
        return self::enabled(Feature::GenerateMissingEmails->value);
    }

    /**
     * Determine if the given feature is enabled.
     */
    public static function enabled(string $feature): bool
    {
        return in_array($feature, sa_config('features', []), true);
    }

    public static function registration(): bool
    {
        return self::enabled(Feature::Registration->value);
    }

    public static function rememberSession(): bool
    {
        return self::enabled(Feature::RememberSession->value);
    }

    public static function updateProfile(): bool
    {
        return self::enabled(Feature::UpdateProfile->value);
    }

    public static function markEmailVerified(): bool
    {
        return self::enabled(Feature::MarkEmailVerified->value);
    }

    public static function managesProfilePhotos(): bool
    {
        return self::enabled(Feature::ProfilePhoto->value);
    }

    public static function resizeProfilePhoto(): bool
    {
        return self::enabled(Feature::ResizeProfilePhoto->value);
    }

    public static function createAccountOnFirstLogin(): bool
    {
        return self::enabled(Feature::CreateAccountOnFirstLogin->value);
    }

    public static function loginOnRegistration(): bool
    {
        return self::enabled(Feature::LoginOnRegistration->value);
    }

    public static function refreshOauthTokens(): bool
    {
        return self::enabled(Feature::RefreshOauthTokens->value);
    }
}
