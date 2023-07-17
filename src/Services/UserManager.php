<?php

namespace Masroore\SocialAuth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as OAuthUserContract;

final class UserManager
{
    public static function findByEmail(string $email): ?User
    {
        return User::firstWhere('email', self::sanitizeEmail($email));
    }

    public static function sanitizeEmail(string $email): string
    {
        return trim(Str::lower($email));
    }

    public static function emailExists(string $email): bool
    {
        return DB::table('users')
            ->where('email', self::sanitizeEmail($email))
            ->exists();
    }

    public static function createVerifiedUser(OAuthUserContract $providerUser, array $extraAttributes = []): User
    {
        /** @var array<string, string> $attributes */
        $attributes = [
            'name' => SocialAuth::getSocialUserName($providerUser),
            'email' => $providerUser->getEmail(),
            'password' => Hash::make(Str::random()),
        ];
        $attributes = array_merge($attributes, $extraAttributes);

        return User::forceCreate($attributes);
    }
}
