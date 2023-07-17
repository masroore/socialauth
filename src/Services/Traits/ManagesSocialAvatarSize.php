<?php

namespace Masroore\SocialAuth\Services\Traits;

use Laravel\Socialite\Contracts\User as OAuthUserContract;
use Masroore\SocialAuth\Services\Providers;
use Masroore\SocialAuth\SocialAuth;

trait ManagesSocialAvatarSize
{
    /**
     * Get appropriate image size for a specific provider.
     */
    protected static function getAvatarForProvider(string $provider, OAuthUserContract $socialUser): ?string
    {
        $provider = SocialAuth::sanitizeProviderName($provider);

        if ($provider == Providers::facebook()) {
            return str_replace('width=1920', 'width=150', $socialUser->avatar_original);
        }

        if ($provider == Providers::google()) {
            return $socialUser->avatar_original . '?sz=150';
        }

        if ($provider == Providers::twitter()) {
            return str_replace('_normal', '_200x200', $socialUser->getAvatar());
        }

        return $socialUser->getAvatar();
    }
}
