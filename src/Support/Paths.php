<?php

namespace Masroore\SocialAuth\Support;

use Masroore\SocialAuth\SocialAuth;

final class Paths
{
    public static function redirect(string $routeName, string $default): string
    {
        return config(SocialAuth::PACKAGE_NAME . '.redirects.' . $routeName) ?? $default;
    }

    public static function path(string $routeName, string $default): string
    {
        return config(SocialAuth::PACKAGE_NAME . '.paths.' . $routeName) ?? $default;
    }
}
