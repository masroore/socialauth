<?php

namespace Masroore\SocialAuth\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Masroore\SocialAuth\SocialAuth
 */
class SocialAuthFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'socialauth';
    }
}
