<?php

namespace Masroore\SocialAuth\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as OAuthUserContract;
use Masroore\SocialAuth\Exceptions\ConfigurationException;
use Masroore\SocialAuth\SocialAuth;
use Masroore\SocialAuth\Support\Features;

final class UserManager
{
    public static function getUserModel(): Model
    {
        return new (self::getUserModelClass());
    }

    public static function getUserModelClass(): string
    {
        return (string) sa_config('user_model', \App\Models\User::class);
    }

    public static function findByEmail(string $email): ?Model
    {
        return self::getUserModelClass()::firstWhere(
            sa_config('columns.email', 'email'),
            self::sanitizeEmail($email)
        );
    }

    public static function sanitizeEmail(string $email): string
    {
        return trim(Str::lower($email));
    }

    public static function emailExists(string $email): bool
    {
        return DB::table(sa_config('users_table'))
            ->where(sa_config('columns.email', 'email'), self::sanitizeEmail($email))
            ->exists();
    }

    public static function createUserFromSocialite(OAuthUserContract $providerUser, array $extraAttributes = []): Model
    {
        $columns = sa_config('columns', []);
        if (blank($columns)) {
            throw ConfigurationException::make('columns');
        }

        /** @var array<string, mixed> $attributes */
        $attributes = [
            $columns['name'] => SocialAuth::getSocialUserName($providerUser),
            $columns['email'] => $providerUser->getEmail(),
            $columns['password'] => Hash::make(Str::random()),
        ];

        if (Features::markEmailVerified()) {
            $attributes[$columns['email_verified_at']] = Date::now();
        }

        $attributes = array_merge($attributes, $extraAttributes);

        return self::getUserModelClass()::forceCreate($attributes);
    }
}
