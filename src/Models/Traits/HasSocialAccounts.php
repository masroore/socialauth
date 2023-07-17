<?php

namespace Masroore\SocialAuth\Models\Traits;

use App\Models\SocialAccount;
use App\Services\OAuth\OAuthManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property Collection $socialAccounts
 * @property int $current_social_account_id
 */
trait HasSocialAccounts
{
    /**
     * Determine if the given connected account is the current connected account.
     */
    public function isCurrentSocialAccount(SocialAccount $socialAccount): bool
    {
        return $socialAccount->id === $this->currentSocialAccount->id;
    }

    /**
     * Get the current connected account of the user's context.
     */
    public function currentSocialAccount(): BelongsTo
    {
        if (null === $this->current_social_account_id && $this->id) {
            $this->switchSocialAccount(
                $this->socialAccounts()->orderBy('created_at')->first()
            );
        }

        return $this->belongsTo(SocialAccount::class, 'current_social_account_id');
    }

    /**
     * Switch the user's context to the given connected account.
     */
    public function switchSocialAccount(SocialAccount $socialAccount): bool
    {
        if (!$this->ownsSocialAccount($socialAccount)) {
            return false;
        }

        $this->forceFill([
            'current_social_account_id' => $socialAccount->id,
        ])->save();

        $this->setRelation('currentSocialAccount', $socialAccount);

        return true;
    }

    /**
     * Determine if the user owns the given connected account.
     */
    public function ownsSocialAccount(SocialAccount $socialAccount): bool
    {
        return $this->id == optional($socialAccount)->user_id;
    }

    /**
     * Get all the connected accounts belonging to the user.
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class, 'user_id', 'id');
    }

    /**
     * Attempt to retrieve the token for a given provider.
     */
    public function getTokenFor(string $provider, mixed $default = null): mixed
    {
        $provider = OAuthManager::sanitizeProviderName($provider);
        if ($this->hasTokenFor($provider)) {
            return $this->socialAccounts
                ->firstWhere('provider', $provider)
                ->token;
        }

        return $default;
    }

    /**
     * Determine if the user has a specific account type.
     */
    public function hasTokenFor(string $provider): bool
    {
        $provider = OAuthManager::sanitizeProviderName($provider);

        return $this->socialAccounts->contains('provider', $provider);
    }

    /**
     * Attempt to find a connected account that belongs to the user,
     * for the given provider and ID.
     */
    public function getSocialAccountFor(string $provider, string $id): ?SocialAccount
    {
        $provider = OAuthManager::sanitizeProviderName($provider);

        return $this->socialAccounts
            ->where('provider', $provider)
            ->where('provider_id', $id)
            ->first();
    }
}
