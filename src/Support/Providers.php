<?php

namespace Masroore\SocialAuth\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Masroore\SocialAuth\SocialAuth;

final class Providers
{
    private static ?Collection $providers = null;

    /**
     * @return array<array<string, string>>
     */
    public static function getProviderButtons(): array
    {
        $buttons = [];

        if (self::getProviders()->isNotEmpty()) {
            foreach (self::getProviders()->keys() as $provider) {
                $buttons[] = self::getProviderButton($provider);
            }
        }

        return $buttons;
    }

    public static function getProviders(): Collection
    {
        if (null === self::$providers) {
            self::$providers = collect(config(SocialAuth::PACKAGE_NAME . '.providers', []));
        }

        return self::$providers;
    }

    /**
     * @return array<string, string>
     */
    public static function getProviderButton(string $provider): array
    {
        $provider = SocialAuth::sanitizeProviderName($provider);
        $button = [];

        if (self::enabled($provider)) {
            $button = [
                'provider' => $provider,
                'label' => Arr::get(self::getProviders(), "{$provider}.label"),
                'icon' => Arr::get(self::getProviders(), "{$provider}.icon"),
            ];
        }

        return $button;
    }

    /**
     * Determine if the given provider is enabled.
     */
    public static function enabled(string $provider): bool
    {
        return self::getProviders()->has(SocialAuth::sanitizeProviderName($provider));
    }
}
