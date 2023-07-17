<?php

namespace Masroore\SocialAuth\Enums;

enum Feature: string
{
    case Registration = 'registration';
    case RefreshOauthTokens = 'refresh-oauth-tokens';
    case RememberSession = 'remember-session';
    case UpdateProfile = 'update-profile';
    case EmailVerification = 'email-verification';
    case ProfilePhoto = 'profile-photo';
    case CreateAccountOnFirstLogin = 'create-account-on-first-login';
    case LoginOnRegistration = 'login-on-registration';
    case GenerateMissingEmails = 'generate-missing-emails';
}
