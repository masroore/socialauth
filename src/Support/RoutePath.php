<?php

namespace Masroore\SocialAuth\Support;

use Masroore\SocialAuth\SocialAuth;

final class RoutePath
{
    /**
     * Get the route path for the given route name.
     */
    public static function for(string $routeName, string $default): string
    {
        return config(SocialAuth::PACKAGE_NAME . '.paths.' . $routeName) ?? $default;
    }
}
