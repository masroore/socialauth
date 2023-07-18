<?php

namespace Masroore\SocialAuth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Masroore\SocialAuth\Exceptions\ProviderNotConfigured;
use Masroore\SocialAuth\Http\Responses\LoginResponse;
use Masroore\SocialAuth\SocialAuth;
use Masroore\SocialAuth\Support\Features;
use Masroore\SocialAuth\Support\OAuthMessageBag;
use Masroore\SocialAuth\Support\Providers;
use Masroore\SocialAuth\Support\Routes;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialAuthController extends Controller
{
    public function redirectToProvider(string $provider): SymfonyRedirectResponse
    {
        $provider = SocialAuth::sanitizeProviderName($provider);
        $this->checkProviderConfiguration($provider);

        session()->put('oauth.previous_url', back()->getTargetUrl());

        /*
        if ($provider == 'facebook') {
            return Socialite::driver($provider)->with(['auth_type' => 'reauthenticate'])->redirect();
        }
        */

        $scopes = Providers::getProviderScopes($provider);
        if (!blank($scopes)) {
            return Socialite::with($provider)
                ->scopes($scopes)
                ->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    private function checkProviderConfiguration(string $provider): void
    {
        if (!Providers::isProviderConfigured($provider)) {
            throw ProviderNotConfigured::make($provider);
        }
    }

    public function handleProviderCallback(Request $request, string $provider): Response|RedirectResponse|LoginResponse
    {
        $provider = SocialAuth::sanitizeProviderName($provider);
        $this->checkProviderConfiguration($provider);
        $redirect = $this->checkErrors($request);

        if ($redirect instanceof RedirectResponse) {
            return $redirect;
        }

        try {
            $providerAccount = SocialAuth::retrieveOauthUser($provider);
        } catch (InvalidStateException $e) {
            return $this->handleInvalidState($e);
        }

        return SocialAuth::authenticate($provider, $providerAccount);
    }

    private function checkErrors(Request $request): ?RedirectResponse
    {
        if (!$request->has('error')) {
            return null;
        }

        $errors = OAuthMessageBag::make($request->get('error_description'));

        if (Auth::check()) {
            $redirectRoute = 'login';
        } else {
            $redirectRoute = Features::registration() ? 'register' : 'login';
        }

        return redirect()->route(Routes::redirect($redirectRoute, $redirectRoute))->withErrors($errors);
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
