<?php

namespace Masroore\SocialAuth\Services;

use Illuminate\Support\MessageBag;

class OAuthMessageBag extends MessageBag
{
    public static function make(string $message): self
    {
        $self = new self();
        $self->add('oauth', $message);

        return $self;
    }
}
