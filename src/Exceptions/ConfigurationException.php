<?php

namespace Masroore\SocialAuth\Exceptions;

use Exception;

class ConfigurationException extends Exception
{
    public static function make(string $option): static
    {
        return new static(__('Option ":option" is not configured.', compact('option')));
    }
}
