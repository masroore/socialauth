<?php

namespace Masroore\SocialAuth\Support;

use Masroore\SocialAuth\SocialAuth;

final class Routes
{
    public static function redirect(string $routeName, string $default): string
    {
        return config(SocialAuth::PACKAGE_NAME . '.redirects.' . $routeName) ?? $default;
    }

    public static function for(string $routeName, string $default): string
    {
        return config(SocialAuth::PACKAGE_NAME . '.routes.' . $routeName) ?? $default;
    }
}
