<?php

namespace Masroore\SocialAuth\Exceptions;

use LogicException;

class ProviderNotConfigured extends LogicException
{
    public static function make(string $provider): static
    {
        return new static(__('Provider ":Provider" is not configured.', ['provider' => $provider]));
    }
}
