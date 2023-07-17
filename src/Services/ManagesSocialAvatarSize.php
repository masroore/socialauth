<?php

namespace Masroore\SocialAuth\Services;

use Laravel\Socialite\Contracts\User as SocialUser;

trait ManagesSocialAvatarSize
{
    /**
     * Get appropriate image size for a specific provider.
     */
    protected static function getAvatarForProvider(string $provider, SocialUser $socialUser): ?string
    {
        if ($provider == 'facebook') {
            return str_replace('width=1920', 'width=150', $socialUser->avatar_original);
        }

        if ($provider == 'google') {
            return $socialUser->avatar_original . '?sz=150';
        }

        if ($provider == 'twitter') {
            return str_replace('_normal', '_200x200', $socialUser->getAvatar());
        }

        return $socialUser->getAvatar();
    }
}
