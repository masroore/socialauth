<?php

namespace Masroore\SocialAuth\Support;

use Masroore\SocialAuth\SocialAuth;

final class Routes
{
    public static function redirect(string $routeName, ?string $default = null): string
    {
        return self::config('redirects', $routeName, $default);
    }

    private static function config(string $section, string $routeName, ?string $default = null): string
    {
        $default ??= $routeName;
        $key = implode('.', [SocialAuth::PACKAGE_NAME, $section, $routeName]);

        return (string) (config($key) ?? $default);
    }

    public static function for(string $routeName, ?string $default = null): string
    {
        return self::config('routes', $routeName, $default);
    }
}
