<?php

namespace Masroore\SocialAuth\Services;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Masroore\SocialAuth\SocialAuth;

final class Providers
{
    /**
     * Determine if the application has support for the Bitbucket provider.
     */
    public static function hasBitbucketSupport(): bool
    {
        return self::enabled(self::bitbucket());
    }

    /**
     * Determine if the given provider is enabled.
     */
    public static function enabled(string $provider): bool
    {
        $provider = SocialAuth::sanitizeProviderName($provider);

        return collect(self::getProviders())->has($provider);
    }

    /**
     * @return array<string, string>
     */
    public static function getProviderButton(string $provider): array
    {
        $provider = SocialAuth::sanitizeProviderName($provider);
        $providers = collect(self::getProviders());
        $button = [];
        if (!$providers->isEmpty() && $providers->has($provider)) {
            $button = [
                'provider' => $provider,
                'label' => Arr::get($providers, "{$provider}.label"),
                'icon' => Arr::get($providers, "{$provider}.icon"),
            ];
        }

        return $button;
    }

    /**
     * @return array<array<string, string>>
     */
    public static function getProviderButtons(): array
    {
        $providers = collect(self::getProviders());
        $buttons = [];

        if (!$providers->isEmpty()) {
            foreach ($providers->keys() as $provider) {
                $buttons[] = self::getProviderButton($provider);
            }
        }

        return $buttons;
    }

    public static function getProviders(): array
    {
        return config(SocialAuth::PACKAGE_NAME . '.providers', []);
    }

    /**
     * Enable the Bitbucket provider.
     */
    public static function bitbucket(): string
    {
        return 'bitbucket';
    }

    /**
     * Determine if the application has support for the Facebook provider.
     */
    public static function hasFacebookSupport(): bool
    {
        return self::enabled(self::facebook());
    }

    /**
     * Enable the Facebook provider.
     */
    public static function facebook(): string
    {
        return 'facebook';
    }

    /**
     * Determine if the application has support for the GitLab provider.
     */
    public static function hasGitlabSupport(): bool
    {
        return self::enabled(self::gitlab());
    }

    /**
     * Enable the GitLab provider.
     */
    public static function gitlab(): string
    {
        return 'gitlab';
    }

    /**
     * Determine if the application has support for the GitHub provider.
     */
    public static function hasGithubSupport(): bool
    {
        return self::enabled(self::github());
    }

    /**
     * Enable the GitHub provider.
     */
    public static function github(): string
    {
        return 'github';
    }

    /**
     * Determine if the application has support for the Google provider.
     */
    public static function hasGoogleSupport(): bool
    {
        return self::enabled(self::google());
    }

    /**
     * Enable the Google provider.
     */
    public static function google(): string
    {
        return 'google';
    }

    /**
     * Determine if the application has support for the LinkedIn provider.
     */
    public static function hasLinkedInSupport(): bool
    {
        return self::enabled(self::linkedin());
    }

    /**
     * Enable the LinkedIn provider.
     */
    public static function linkedin(): string
    {
        return 'linkedin';
    }

    /**
     * Determine if the application has support for the Twitter provider.
     */
    public static function hasTwitterSupport(): bool
    {
        return self::enabled(self::twitterOAuth1())
            || self::enabled(self::twitterOAuth2());
    }

    /**
     * Enable the Twitter OAuth 1.0 provider.
     */
    public static function twitterOAuth1(): string
    {
        return 'twitter';
    }

    /**
     * Enable the Twitter OAuth 2.0 provider.
     */
    public static function twitterOAuth2(): string
    {
        return 'twitter-oauth-2';
    }

    /**
     * Determine if the application has support for the Twitter OAuth 1.0 provider.
     */
    public static function hasTwitterOAuth1Support(): bool
    {
        return self::enabled(self::twitterOAuth1());
    }

    /**
     * Determine if the application has support for the Twitter OAuth 2.0 provider.
     */
    public static function hasTwitterOAuth2Support(): bool
    {
        return self::enabled(self::twitterOAuth2());
    }

    /**
     * Enable the Twitter provider.
     */
    public static function twitter(): string
    {
        return 'twitter';
    }

    /**
     * Dynamically handle static calls.
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        // If the method exists on the class, call it. Otherwise, attempt to
        // determine the provider from the method name being called.
        if (method_exists(self::class, $name)) {
            return self::$name(...$arguments);
        }

        /** @example $name = "HasMyCustomProviderSupport" */
        if (preg_match('/^has.*Support$/', $name)) {
            $provider = Str::remove('Support', Str::remove('has', $name));

            return self::enabled(Str::kebab($provider)) || self::enabled(Str::lower($provider));
        }

        self::throwBadMethodCallException($name);
    }

    /**
     * Throw a bad method call exception for the given method.
     */
    private static function throwBadMethodCallException(string $method): void
    {
        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            self::class,
            $method
        ));
    }
}
