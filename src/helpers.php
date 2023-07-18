<?php

use Masroore\SocialAuth\SocialAuth;

function sa_config_key(?string $name = null): string
{
    return blank($name) ? SocialAuth::PACKAGE_NAME : SocialAuth::PACKAGE_NAME . '.' . $name;
}

function sa_config(string $key, mixed $default = null): mixed
{
    return config(sa_config_key($key), $default);
}
