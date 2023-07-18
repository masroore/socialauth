<?php

namespace Masroore\SocialAuth\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as OAuthUserContract;
use Masroore\SocialAuth\SocialAuth;

final class UserManager
{
    public static function getUserModel(): Model
    {
        return new (self::getUserModelClass());
    }

    public static function getConfig(): array
    {
        return config(SocialAuth::PACKAGE_NAME, []);
    }

    public static function config(string $key, $default): mixed
    {
        return config(implode('.', [SocialAuth::PACKAGE_NAME, $key]), $default);
    }

    public static function getUserModelClass(): string
    {
        return self::getConfig()['user_model'] ?? \App\Models\User::class;
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
            ->where(self::config('email_column'), self::sanitizeEmail($email))
            ->exists();
    }

    public static function createUserFromSocialite(OAuthUserContract $providerUser, array $extraAttributes = []): Model
    {
        /** @var array<string, mixed> $attributes */
        $attributes = [
            'name' => SocialAuth::getSocialUserName($providerUser),
            'email' => $providerUser->getEmail(),
            'password' => Hash::make(Str::random()),
        ];
        $attributes = array_merge($attributes, $extraAttributes);

        return self::getUserModelClass()::forceCreate($attributes);
    }
}
