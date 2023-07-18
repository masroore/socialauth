<?php

namespace Masroore\SocialAuth\Support;

use Illuminate\Support\MessageBag;
use Masroore\SocialAuth\SocialAuth;

class OAuthMessageBag extends MessageBag
{
    public static function make(string $message): self
    {
        $self = new self();
        $self->add(SocialAuth::PACKAGE_NAME, $message);

        return $self;
    }
}
