<?php

namespace Masroore\SocialAuth;

use Illuminate\Database\Eloquent\Model;

final class SocialAuth
{
    public const PACKAGE_NAME = 'socialauth';

    public function getUserModel(): Model
    {
        return new ($this->getUserModelClass());
    }

    public function getUserModelClass(): string
    {
        return $this->getConfig()['user_model'] ?? \App\Models\User::class;
    }

    public function getConfig(): array
    {
        return config(self::PACKAGE_NAME, []);
    }
}
