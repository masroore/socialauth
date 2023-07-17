<?php

namespace Masroore\SocialAuth;

final class RedirectPath
{
    /**
     * Get the route path for the given route name.
     */
    public static function for(string $routeName, string $default): string
    {
        return config(SocialAuth::PACKAGE_NAME . '.redirects.' . $routeName) ?? $default;
    }
}
