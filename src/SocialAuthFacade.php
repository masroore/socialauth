<?php

namespace Masroore\SocialAuth;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Masroore\SocialAuth\Skeleton\SkeletonClass
 */
class SocialAuthFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'socialauth';
    }
}
