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

    public static function createVerifiedUser(OAuthUserContract $providerUser): User
    {
        return User::create([
            'name' => OAuthManager::getSocialUserName($providerUser),
            'email' => $providerUser->getEmail(),
            'password' => Hash::make(Str::random()),
            'status' => UserStatus::Active->value,
            'role' => UserRole::Member->value,
            'email_verified_at' => Date::now(),
            'last_login_at' => Date::now(),
            'ip_address' => Ip::get(),
        ]);
    }

    public static function createUnverifiedUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => UserStatus::Unconfirmed->value,
            'role' => UserRole::Member->value,
            'last_login_at' => Date::now(),
            'ip_address' => Ip::get(),
        ]);

    }
}
