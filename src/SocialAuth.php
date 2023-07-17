<?php

namespace Masroore\SocialAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Contracts\User as OAuthUserContract;
use Laravel\Socialite\Facades\Socialite;
use Masroore\SocialAuth\Exceptions\ProviderNotConfigured;
use Masroore\SocialAuth\Http\Responses\LoginResponse;
use Masroore\SocialAuth\Models\SocialAccount;
use Masroore\SocialAuth\Services\Features;
use Masroore\SocialAuth\Services\OAuthMessageBag;
use Masroore\SocialAuth\Services\UserManager;
use Masroore\SocialAuth\Traits\ManagesSocialAvatarSize;

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

    use ManagesSocialAvatarSize;

    public static function retrieveOauthUser(string $provider): OAuthUserContract
    {
        $socialUser = Socialite::driver($provider)->user();

        return self::generateMissingEmails($provider, $socialUser);
    }

    private static function generateMissingEmails(string $provider, OAuthUserContract $socialUser): OAuthUserContract
    {
        if (Features::generateMissingEmails()) {
            $socialUser->email = $socialUser->getEmail() ?? ("{$socialUser->id}@{$provider}." . config('app.domain'));
        }

        return $socialUser;
    }

    public static function authenticate(string $provider, OAuthUserContract $providerUser): Response|RedirectResponse|LoginResponse
    {
        $provider = self::sanitizeProviderName($provider);
        $socialAccount = self::findSocialAccountForProviderAndId($provider, $providerUser->getId());

        // already logged in...
        if (null !== ($user = Auth::user())) {
            return self::alreadyAuthenticated($user, $socialAccount, $provider, $providerUser);
        }

        // new account registration...
        $previousUrl = session()->get('oauth.previous_url');

        if (Features::registration() && !$socialAccount
            && (
                $previousUrl === route('register')
                || (Features::createAccountOnFirstLogin() && $previousUrl === route('login'))
            )
        ) {
            $user = UserManager::findByEmail($providerUser->getEmail());

            if ($user) {
                return self::alreadyRegistered($user, $socialAccount, $provider, $providerUser);
            }

            return self::registerNewUser($provider, $providerUser);
        }

        if (!Features::createAccountOnFirstLogin() && !$socialAccount) {
            $messageBag = OAuthMessageBag::make(
                __('An account with this :Provider sign in was not found. Please register or try a different sign in method.', ['provider' => $provider])
            );

            return redirect()->route('login')->withErrors($messageBag);
        }

        if (Features::createAccountOnFirstLogin() && !$socialAccount) {
            if (UserManager::emailExists($providerUser->getEmail())) {
                $messageBag = OAuthMessageBag::make(
                    __('An account with that email address already exists. Please login to connect your :Provider account.', ['provider' => $provider])
                );

                return redirect()->route('login')->withErrors($messageBag);
            }

            $user = self::createNewUserFromProvider($provider, $providerUser);

            return self::loginUser($user);
        }

        $user = $socialAccount->user;

        self::updateSocialAccount($socialAccount, $provider, $providerUser);

        $user->forceFill([
            'current_social_account_id' => $socialAccount->id,
        ])->save();

        return self::loginUser($user);
    }

    public static function sanitizeProviderName(string $provider): string
    {
        return strtolower(trim($provider));
    }

    /**
     * Find a connected account instance fot a given provider and provider ID.
     */
    public static function findSocialAccountForProviderAndId(string $provider, string $providerId): ?SocialAccount
    {
        return SocialAccount::query()
            ->where('provider', self::sanitizeProviderName($provider))
            ->where('provider_id', $providerId)
            ->first();
    }

    /**
     * Handle connection of accounts for an already authenticated user.
     */
    private static function alreadyAuthenticated(Authenticatable $user, ?SocialAccount $account, string $provider, OAuthUserContract $socialUser): RedirectResponse
    {
        // if ($account && $account->user_id !== $user->id) {
        if ($account && !$user->ownsSocialAccount($account)) {
            flash()->warning(
                __('This :Provider sign in account is already associated with another user. Please try a different account.', ['provider' => $provider])
            );

            return redirect()->route('profile.show');
        }

        if (!$account) {
            self::createSocialAccountForUser($user, $provider, $socialUser);
            flash()->success(__('You have successfully connected :Provider to your account.', ['provider' => $provider]));

            return redirect()->route('profile.show');
        }

        flash()->info(__('This :Provider sign in account is already associated with your user.', ['provider' => $provider]));

        return redirect()->route('profile.show');
    }

    public static function createSocialAccountForUser(Authenticatable $user, string $provider, OAuthUserContract $socialUser): SocialAccount
    {
        $attributes = array_merge(self::getFillableAttributes($provider, $socialUser), ['user_id' => $user->getAuthIdentifier()]);

        return SocialAccount::forceCreate($attributes);
    }

    private static function getFillableAttributes(string $provider, OAuthUserContract $socialUser): array
    {
        return [
            'provider' => self::sanitizeProviderName($provider),
            'provider_id' => $socialUser->getId(),
            'name' => self::getSocialUserName($socialUser),
            'nickname' => $socialUser->getNickname(),
            'email' => $socialUser->getEmail(),
            'avatar_path' => $socialUser->getAvatar(),
            'token' => $socialUser->token,
            'secret' => $socialUser->tokenSecret ?? null,
            'refresh_token' => $socialUser->refreshToken ?? null,
            'expires_at' => property_exists($socialUser, 'expiresIn') ? now()->addSeconds($socialUser->expiresIn) : null,
        ];
    }

    public static function getSocialUserName(OAuthUserContract $socialUser): ?string
    {
        $name = $socialUser->getName() ?? $socialUser->getNickname();

        if (blank($name)) {
            // facebook
            if (isset($socialUser->user['first_name']) || isset($socialUser->user['last_name'])) {
                $name = trim(implode(' ', [$socialUser->user['first_name'], $socialUser->user['last_name']]));
            }
        }

        if (blank($name)) {
            // linkedin
            $name = $socialUser->user['formattedName'] ?? '';
        }

        if (blank($name)) {
            if (isset($socialUser->user['firstName']) || isset($socialUser->user['lastName'])) {
                $name = trim(implode(' ', [$socialUser->user['firstName'], $socialUser->user['lastName']]));
            }
        }

        return $name;
    }

    /**
     * Handle when a user is already registered.
     */
    private static function alreadyRegistered(Authenticatable $user, ?SocialAccount $account, string $provider, OAuthUserContract $socialUser): RedirectResponse|LoginResponse
    {
        if (Features::createAccountOnFirstLogin()) {
            // The user exists, but they're not registered with the given provider.
            if (!$account) {
                self::createSocialAccountForUser($user, $provider, $socialUser);
            }

            return self::loginUser($user);
        }

        flash()->warning(__('This :Provider sign in account is already associated with your user.', ['provider' => $provider]));
        $messageBag = OAuthMessageBag::make(__('An account with that :Provider sign in already exists, please login.', ['provider' => $provider]));

        return redirect()->route('register')->withErrors($messageBag);
    }

    /**
     * Authenticate the given user and return a login response.
     */
    private static function loginUser(Authenticatable $user): LoginResponse
    {
        Auth::login($user, Features::rememberSession());

        return app(LoginResponse::class);
    }

    /**
     * Handle the registration of a new user.
     */
    private static function registerNewUser(string $provider, OAuthUserContract $providerAccount): RedirectResponse|LoginResponse
    {
        $provider_email = $providerAccount->getEmail();

        if (!$provider_email) {
            $messageBag = OAuthMessageBag::make(
                __('No email address is associated with this :Provider account. Please try a different account.', ['provider' => $provider])
            );

            return redirect()->route('register')->withErrors($messageBag);
        }

        if (UserManager::emailExists($provider_email)) {
            $messageBag = OAuthMessageBag::make(
                __('An account with that email address already exists. Please login to connect your :Provider account.', ['provider' => $provider])
            );

            return redirect()->route('login')->withErrors($messageBag);
        }

        $user = self::createNewUserFromProvider($provider, $providerAccount);

        return self::loginUser($user);
    }

    public static function createNewUserFromProvider(string $provider, OAuthUserContract $providerUser): User
    {
        return DB::transaction(static function () use ($provider, $providerUser) {
            return tap(
                UserManager::createVerifiedUser($providerUser),
                static function (User $user) use ($provider, $providerUser): void {
                    if (Features::profilePhoto() && $providerUser->getAvatar()) {
                        $user->setProfilePhotoFromUrl($providerUser->getAvatar());
                    }

                    $user->switchSocialAccount(
                        self::createSocialAccountForUser($user, $provider, $providerUser)
                    );
                }
            );
        });
    }

    public static function updateSocialAccount(SocialAccount $socialAccount, string $provider, OAuthUserContract $socialUser): void
    {
        $attributes = self::getFillableAttributes($provider, $socialUser);
        $socialAccount->forceFill($attributes)->save();
    }

    public static function getProviderScopes(string $provider): string|array
    {
        return self::getProviderConfig($provider)['scopes'] ?? [];
    }

    public static function getProviderConfig(string $provider): array
    {
        if (!self::isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }

        return config()->get('services.' . $provider);
    }

    /**
     * Check if provider is configured.
     */
    public static function isProviderConfigured(string $provider): bool
    {
        // return in_array(self::sanitizeProviderName($provider), self::providers(), true);
        return config()->has('services.' . self::sanitizeProviderName($provider));
    }

    /**
     * Determine which providers the application supports.
     */
    public static function providers(): array
    {
        return config(self::PACKAGE_NAME . '.providers', []);
    }
}
