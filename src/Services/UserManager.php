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
        return (string) self::config('user_model', \App\Models\User::class);
    }

    public static function config(string $key, mixed $default): mixed
    {
        return config(self::configKey($key), $default);
    }

    public static function configKey(?string $name = null): string
    {
        return blank($name) ? SocialAuth::PACKAGE_NAME : SocialAuth::PACKAGE_NAME . '.' . $name;
    }

    public static function findByEmail(string $email): ?Model
    {
        return self::getUserModelClass()::firstWhere(self::config('email_column'), self::sanitizeEmail($email));
    }

    public static function sanitizeEmail(string $email): string
    {
        return trim(Str::lower($email));
    }

    public static function emailExists(string $email): bool
    {
        return DB::table(self::config('users_table'))
            ->where(self::config('columns.email', 'email'), self::sanitizeEmail($email))
            ->exists();
    }

    public static function createUserFromSocialite(OAuthUserContract $providerUser, array $extraAttributes = []): Model
    {
        $columns = self::config('columns', []);
        if (blank($columns)) {
            throw ConfigurationException::make('columns');
        }

        /** @var array<string, mixed> $attributes */
        $attributes = [
            $columns['name'] => SocialAuth::getSocialUserName($providerUser),
            $columns['email'] => $providerUser->getEmail(),
            $columns['password'] => Hash::make(Str::random()),
        ];

        if (Features::emailVerification()) {
            $attributes[$columns['email_verified_at']] = Date::now();
        }

        $attributes = array_merge($attributes, $extraAttributes);

        return self::getUserModelClass()::create($attributes);
    }
}
