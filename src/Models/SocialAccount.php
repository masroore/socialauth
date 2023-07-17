<?php

namespace Masroore\SocialAuth\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    use HasTimestamps;

    protected $fillable = [
        'provider',
        'provider_id',
        'name',
        'nickname',
        'email',
        'avatar_path',
        'token',
        'secret',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the data that should be shared.
     *
     * @return array<string, mixed>
     */
    public function getSharedData(): array
    {
        return [
            'id' => $this->id,
            'provider' => $this->provider,
            'avatar_path' => $this->avatar_path,
            'created_at' => optional($this->created_at)->diffForHumans(),
        ];
    }
}
