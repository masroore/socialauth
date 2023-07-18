<?php

use Masroore\SocialAuth\SocialAuth;

function config_key(?string $name = null): string
{
    return blank($name) ? SocialAuth::PACKAGE_NAME : SocialAuth::PACKAGE_NAME . '.' . $name;
}

function get_config(string $key, mixed $default = null): mixed
{
    return config(config_key($key), $default);
}
