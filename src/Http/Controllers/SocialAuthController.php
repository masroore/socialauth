<?php

namespace Masroore\SocialAuth\Http\Controllers;

use App\Http\Responses\LoginResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Masroore\SocialAuth\Exceptions\SocialAuthProviderNotConfigured;
use Masroore\SocialAuth\Services\OAuth\OAuthManager;
use Masroore\SocialAuth\Services\OAuth\OAuthMessageBag;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialAuthController extends Controller
{
    private function checkProviderConfiguration(string $provider): void
    {
        if (!OAuthManager::isProviderConfigured($provider)) {
            throw SocialAuthProviderNotConfigured::make($provider);
        }
    }

    public function redirectToProvider(string $provider): SymfonyRedirectResponse
    {
        $provider = OAuthManager::sanitizeProviderName($provider);
        $this->checkProviderConfiguration($provider);

        session()->put('oauth.previous_url', back()->getTargetUrl());

        /*
        if ($provider == 'facebook') {
            return Socialite::driver($provider)->with(['auth_type' => 'reauthenticate'])->redirect();
        }
        */

        $scopes = OAuthManager::getProviderScopes($provider);
        if (!blank($scopes)) {
            return Socialite::with($provider)
                ->scopes($scopes)
                ->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider): Response|RedirectResponse|LoginResponse
    {
        $provider = OAuthManager::sanitizeProviderName($provider);
        $this->checkProviderConfiguration($provider);

        $redirect = $this->checkErrors($request);

        if ($redirect instanceof RedirectResponse) {
            return $redirect;
        }

        try {
            $providerAccount = OAuthManager::retrieveOauthUser($provider);
        } catch (InvalidStateException $e) {
            return $this->handleInvalidState($e);
        }

        return OAuthManager::authenticate($provider, $providerAccount);
    }

    private function checkErrors(Request $request): ?RedirectResponse
    {
        if (!$request->has('error')) {
            return null;
        }

        if (Auth::check()) {
            flash()->warning($request->get('error_description'));

            return redirect(RouteServiceProvider::HOME);
        }

        $messageBag = OAuthMessageBag::make($request->get('error_description'));

        return redirect()->route(AuthSettings::instance()->registration ? 'register' : 'login')->withErrors($messageBag);
    }

    /**
     * Handle an invalid state exception from a Socialite provider.
     */
    private function handleInvalidState(InvalidStateException $exception, ?callable $callback = null): Response
    {
        if ($callback) {
            return $callback($exception);
        }

        throw $exception;
    }
}
