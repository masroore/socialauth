<?php

namespace Masroore\SocialAuth\Services;

final class Features
{
    /**
     * Determine if the application has the generates missing emails feature enabled.
     */
    public static function generatesMissingEmails(): bool
    {
        return self::enabled(self::generateMissingEmails());
    }

    /**
     * Determine if the given feature is enabled.
     */
    public static function enabled(string $feature): bool
    {
        return in_array($feature, config('oauth.features', []));
    }

    /**
     * Enabled the generate missing emails feature.
     */
    public static function generateMissingEmails(): string
    {
        return 'generate-missing-emails';
    }

    /**
     * Determine if the application supports creating accounts
     * when logging in for the first time via a provider.
     */
    public static function hasCreateAccountOnFirstLoginFeatures(): bool
    {
        return self::enabled(self::createAccountOnFirstLogin());
    }

    /**
     * Enable the create account on first login feature.
     */
    public static function createAccountOnFirstLogin(): string
    {
        return 'create-account-on-first-login';
    }

    /**
     * Determine if the application supports logging into existing
     * accounts when registering with a provider who's email address
     * is already registered.
     */
    public static function hasLoginOnRegistrationFeatures(): bool
    {
        return self::enabled(self::loginOnRegistration());
    }

    /**
     * Enable the login on registration feature.
     */
    public static function loginOnRegistration(): string
    {
        return 'login-on-registration';
    }

    /**
     * Determine if the application should use provider avatars when registering.
     */
    public static function hasProviderAvatarsFeature(): bool
    {
        return self::enabled(self::providerAvatars());
    }

    /**
     * Enable the provider avatars feature.
     */
    public static function providerAvatars(): string
    {
        return 'provider-avatars';
    }

    /**
     * Determine if the application should remember the users session om login.
     */
    public static function hasRememberSessionFeatures(): bool
    {
        return self::enabled(self::rememberSession());
    }

    /**
     * Enable the remember session feature for logging in.
     */
    public static function rememberSession(): string
    {
        return 'remember-session';
    }

    /**
     * Determine if the application should refresh the tokens on retrieval.
     */
    public static function refreshesOauthTokens(): bool
    {
        return self::enabled(self::refreshOauthTokens());
    }

    /**
     * Enable the automatic refresh token update on token retrieval.
     */
    public static function refreshOauthTokens(): string
    {
        return 'refresh-oauth-tokens';
    }
}
